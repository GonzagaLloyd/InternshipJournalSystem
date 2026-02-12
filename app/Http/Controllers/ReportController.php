<?php

namespace App\Http\Controllers;
 
use App\Models\JournalEntry;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Fetch reports sorted by latest
        $pastReports = \App\Models\Report::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($report) {
                return [
                    'id' => (string) $report->_id,
                    'report' => $report->report,
                    'period' => $report->period,
                    'created_at' => $report->created_at->format('M d, Y H:i'),
                ];
            });

        $entries = JournalEntry::where('user_id', $user->id)
            ->orderBy('entry_date', 'desc')
            ->get();

        $availableEntries = $entries->map(function ($entry) {
            return [
                'id' => $entry->id,
                'title' => $entry->title,
                'entry_date' => $entry->entry_date,
            ];
        });

        return Inertia::render('Reports/Index', [
            'availableEntries' => $availableEntries,
            'pastReports' => $pastReports
        ]);
    }

    public function generate(Request $request)
    {
        Log::info('Report Generation Initiated');

        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json(['error' => 'User not authenticated'], 401);
            }

            $request->validate([
                'entry_ids' => 'required|array|min:1',
                'entry_ids.*' => 'string'
            ]);

            $entryIds = [];
            foreach ($request->entry_ids as $id) {
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

            $entries = JournalEntry::where('user_id', $user->id)
                ->whereIn('_id', $entryIds)
                ->orderBy('entry_date', 'asc')
                ->get();

            if ($entries->isEmpty()) {
                return response()->json(['error' => 'No fragments found.'], 404);
            }

            $startDate = Carbon::parse($entries->first()->entry_date);
            $endDate = Carbon::parse($entries->last()->entry_date);

            $formattedEntries = $entries->map(function ($entry) {
                return "Date: {$entry->entry_date}\nTitle: {$entry->title}\nContent: {$entry->content}\n---";
            })->implode("\n");

            $apiKey = env('GEMINI_API_KEY');
            if (!$apiKey) {
                return response()->json(['error' => 'The Archivist is unavailable (API Key missing).'], 500);
            }

            $prompt = "You are a professional IT Documentation Specialist. I am providing you with daily task entries from a BSIT student/intern's journal.
            
            Your task is to generate a professional 'Weekly Progress Report' suitable for documentation and submission to a supervisor.
            
            The report must be **technical, concise, and professional**. Do NOT use any fantasy, ancient, or flowery language. Refine the user's rough notes into clear, professional technical writing.
            
            Structure the report as follows:
            1. **Executive Summary**: A high-level overview of the week's progress and main focus.
            2. **Technical Accomplishments**: A bulleted list of specific tasks completed, features implemented, and technologies used (e.g., Vue.js, Laravel, Database design). Use strong action verbs (e.g., 'Implemented', 'Refactored', 'Debugged').
            3. **Challenges & Resolutions**: A section detailing specific technical hurdles encountered and the solutions applied.
            4. **Key Learnings**: New technical skills or concepts reinforced during the week.
            5. **Forward Outlook**: Brief goals for the next week based on current progress.
            
            Journal Entries:
            {$formattedEntries}";

            Log::info('Sending request to Gemini API (Model: gemini-flash-latest)');

            $response = Http::withoutVerifying()->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key={$apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $reportContent = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

                if ($reportContent) {
                    // Save Report to Database
                    $newReport = \App\Models\Report::create([
                        'user_id' => $user->id,
                        'report' => $reportContent,
                        'period' => [
                            'start' => $startDate->format('M d, Y'),
                            'end' => $endDate->format('M d, Y')
                        ],
                    ]);

                    return response()->json([
                        'report' => $reportContent,
                        'period' => [
                            'start' => $startDate->format('M d, Y'),
                            'end' => $endDate->format('M d, Y')
                        ],
                        'id' => (string) $newReport->_id, // Return MongoDB _id as string
                    ]);
                }
            }

            return response()->json([
                'error' => 'The Grand Scribe is currently occupied. Please try again later.',
            ], 500);

        } catch (\Exception $e) {
            Log::error('Report Generation Fatal Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Internal Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = auth()->user();
            Log::info("Archiving Report ID: {$id}");

            // Try to find by string ID directly first (MongoDB driver often handles this)
            $report = \App\Models\Report::where('_id', $id)
                ->orWhere('id', $id)
                ->where('user_id', $user->id) 
                ->first();

            if (!$report) {
                // Try converting to ObjectId if string lookup failed
                if (class_exists('MongoDB\BSON\ObjectId')) {
                    try {
                        $objectId = new \MongoDB\BSON\ObjectId($id);
                        $report = \App\Models\Report::where('_id', $objectId)
                            ->where('user_id', $user->id)
                            ->first();
                    } catch (\Exception $e) {
                        // Invalid ObjectId format
                    }
                }
            }

            if (!$report) {
                Log::warning("Report not found for archiving: {$id}");
                return redirect()->back()->with('error', 'Report not found.');
            }

            // Soft delete the report (moves to vault)
            $report->delete();
            Log::info("Report archived successfully: {$id}");

            return redirect()->route('reports.index')->with('success', 'Report archived to vault successfully.');
        } catch (\Exception $e) {
            Log::error('Report Archive Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to archive report.');
        }
    }
}
