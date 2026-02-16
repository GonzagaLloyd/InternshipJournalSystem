<?php

namespace App\Jobs;

use App\Models\JournalEntry;
use App\Models\ReportGenerationJob as ReportGenerationJobModel;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GenerateReportJob implements ShouldQueue
{
    use Queueable;

    public $timeout = 300; // 5 minutes timeout
    public $tries = 3; // Retry 3 times on failure

    protected $jobId;
    protected $userId;
    protected $entryIds;

    /**
     * Calculate the number of seconds to wait before retrying the job.
     */
    public function backoff(): array
    {
        return [60, 120, 180];
    }

    /**
     * Create a new job instance.
     */
    public function __construct($jobId, $userId, $entryIds)
    {
        $this->jobId = $jobId;
        $this->userId = $userId;
        $this->entryIds = $entryIds;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Starting report generation job #{$this->jobId}");
        
        $jobRecord = ReportGenerationJobModel::find($this->jobId);
        
        if (!$jobRecord) {
            Log::error("Job record not found: #{$this->jobId}");
            return;
        }

        try {
            // Update status to processing
            $jobRecord->update(['status' => 'processing']);

            // Convert entry IDs to ObjectIds if needed
            $entryIds = [];
            foreach ($this->entryIds as $id) {
                try {
                    if (class_exists('MongoDB\BSON\ObjectId')) {
                        $entryIds[] = new \MongoDB\BSON\ObjectId($id);
                    } else {
                        $entryIds[] = $id;
                    }
                } catch (\Exception $e) {
                    $entryIds[] = $id;
                }
            }

            // Fetch journal entries (Optimized selection)
            $entries = JournalEntry::where('user_id', $this->userId)
                ->whereIn('_id', $entryIds)
                ->orderBy('entry_date', 'asc')
                ->get(['entry_date', 'title', 'content']);

            if ($entries->isEmpty()) {
                throw new \Exception('No journal entries found for the given IDs.');
            }

            $startDate = Carbon::parse($entries->first()->entry_date);
            $endDate = Carbon::parse($entries->last()->entry_date);

            // Format entries for AI
            $formattedEntries = $entries->map(function ($entry) {
                return "Date: {$entry->entry_date}\nTitle: {$entry->title}\nContent: {$entry->content}\n---";
            })->implode("\n");

            $apiKey = env('GEMINI_API_KEY');
            if (!$apiKey) {
                throw new \Exception('GEMINI_API_KEY not configured.');
            }

            $prompt = "You are a professional IT Documentation Specialist. I am providing you with daily task entries from a BSIT student/intern's journal.\n\nYour task is to generate a professional 'Weekly Progress Report' suitable for documentation and submission to a supervisor.\n\n**Formatting Rules:**\n- Use proper Markdown headers (e.g., '# Title', '## Section').\n- Do NOT use bold keys for main sections (e.g., don't use '**1. Executive Summary**', use '## Executive Summary').\n- Use bullet points for lists.\n- The report must be **technical, concise, and professional**. \n- Do NOT use any fantasy, ancient, or flowery language. Refine the user's rough notes into clear, professional technical writing.\n\nStructure the report as follows:\n# Weekly Progress Report\n\n## Executive Summary\n(A high-level overview of the week's progress and main focus.)\n\n## Technical Accomplishments\n(A bulleted list of specific tasks completed, features implemented, and technologies used. Use strong action verbs like 'Implemented', 'Refactored', 'Debugged'.)\n\n## Challenges & Resolutions\n(A section detailing specific technical hurdles encountered and the solutions applied.)\n\n## Key Learnings\n(New technical skills or concepts reinforced during the week.)\n\n## Forward Outlook\n(Brief goals for the next week based on current progress.)\n\nJournal Entries:\n{$formattedEntries}";

            Log::info("Calling Gemini API for job #{$this->jobId}");

            // Call Gemini API
            $response = Http::withoutVerifying()
                ->timeout(120)
                ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key={$apiKey}", [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ]
                ]);

            if (!$response->successful()) {
                $errorData = $response->json();
                $errorMessage = $errorData['error']['message'] ?? 'Gemini API request failed.';
                // If 429 (Too Many Requests) or 5xx, we might want to retry.
                // Http client throws exception usually, but successful() check handles non-200.
                if ($response->status() >= 500 || $response->status() === 429) {
                     // Throwing exception triggers retry
                     throw new \Exception("Gemini API Error ({$response->status()}): $errorMessage");
                }
                // For client errors (400), don't retry
                throw new \Exception($errorMessage);
            }

            $data = $response->json();
            $reportContent = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if (!$reportContent) {
                throw new \Exception('No report content returned from Gemini API.');
            }

            // Update job record with completed status
            $jobRecord->update([
                'status' => 'completed',
                'report' => $reportContent,
                'period' => [
                    'start' => $startDate->format('M d, Y'),
                    'end' => $endDate->format('M d, Y')
                ],
                'completed_at' => now()
            ]);

            Log::info("Report generation completed for job #{$this->jobId}");

        } catch (\Exception $e) {
            Log::error("Report generation exception for job #{$this->jobId}: " . $e->getMessage());
            
            // Check if we should retry or fail permanently
            if ($this->attempts() >= $this->tries) {
                $this->fail($e);
            } else {
                // Release back to queue for retry
                $this->release($this->backoff()[$this->attempts() - 1] ?? 60);
            }
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Job #{$this->jobId} failed catastrophically: " . $exception->getMessage());
        
        $jobRecord = ReportGenerationJobModel::find($this->jobId);
        if ($jobRecord) {
            $jobRecord->update([
                'status' => 'failed',
                'error_message' => $exception->getMessage(),
                'completed_at' => now()
            ]);
        }
    }
}
