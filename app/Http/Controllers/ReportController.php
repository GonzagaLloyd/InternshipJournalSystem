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
                    'id' => $report->id,
                    'report' => $report->content, // Mapping 'content' to 'report' prop used in frontend
                    'period' => [
                        'start' => $report->period_start->format('M d, Y'),
                        'end' => $report->period_end->format('M d, Y'),
                    ],
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
                        'content' => $reportContent,
                        'period_start' => $startDate,
                        'period_end' => $endDate,
                        'title' => "Weekly Report: " . $startDate->format('M d') . " - " . $endDate->format('M d'),
                    ]);

                    return response()->json([
                        'report' => $reportContent,
                        'period' => [
                            'start' => $startDate->format('M d, Y'),
                            'end' => $endDate->format('M d, Y')
                        ],
                        'id' => $newReport->id, // Return ID for frontend to possibly highlight
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
}
