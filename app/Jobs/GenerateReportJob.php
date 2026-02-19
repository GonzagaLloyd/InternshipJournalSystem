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

            Log::info("Calling AIService for job #{$this->jobId}");
            
            $aiService = app(\App\Services\AIService::class);
            $reportContent = $aiService->generateReport($formattedEntries);

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
            
            // Re-throw to trigger backoff/retry if it's not the last attempt
            if ($this->attempts() < $this->tries) {
                throw $e;
            }

            // If last attempt, mark as failed
            $jobRecord->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'completed_at' => now()
            ]);
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
