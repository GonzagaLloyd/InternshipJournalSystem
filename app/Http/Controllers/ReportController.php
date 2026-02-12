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
        $entries = JournalEntry::where('user_id', auth()->id())
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
            'availableEntries' => $availableEntries
        ]);
    }

    public function generate(Request $request)
    {
        $request->validate([
            'entry_ids' => 'required|array|min:1',
            'entry_ids.*' => 'string'
        ]);

        $entries = JournalEntry::where('user_id', auth()->id())
            ->whereIn('_id', $request->entry_ids)
            ->orderBy('entry_date', 'asc')
            ->get();

        if ($entries->isEmpty()) {
            return response()->json([
                'error' => 'No journal entries found for the selected fragments.',
            ], 404);
        }

        $startDate = Carbon::parse($entries->first()->entry_date);
        $endDate = Carbon::parse($entries->last()->entry_date);

        $formattedEntries = $entries->map(function ($entry) {
            return "Date: {$entry->entry_date}\nTitle: {$entry->title}\nContent: {$entry->content}\n---";
        })->implode("\n");

        $apiKey = env('GEMINI_API_KEY');

        if (!$apiKey) {
            return response()->json([
                'error' => 'The Archivist is unavailable. (API key missing)',
            ], 500);
        }

        $prompt = "You are an elite Grand Scribe. I am giving you a series of journal entries for a week. 
        Your task is to create a 'Weekly Chronicle Report'. 
        
        The report should include:
        1. A compelling Executive Summary (The Gilded Overview).
        2. Key Themes and Accomplishments (The Great Deeds).
        3. Areas for Growth or Reflection (The Path Ahead).
        4. A motivational 'Scriptor's Wisdom' for the next week.
        
        Maintain a premium, elegant, and slightly sophisticated tone. Use formatting like bolding for highlights.
        
        Journal Entries:
        {$formattedEntries}";

        try {
            $response = Http::withoutVerifying()->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}", [
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
                    return response()->json([
                        'report' => $reportContent,
                        'period' => [
                            'start' => $startDate->format('M d, Y'),
                            'end' => $endDate->format('M d, Y')
                        ]
                    ]);
                }
            }

            return response()->json([
                'error' => 'The Grand Scribe is currently occupied. Please try again later.',
            ], 500);

        } catch (\Exception $e) {
            Log::error('Report Generation Exception', ['message' => $e->getMessage()]);
            return response()->json([
                'error' => 'The connection to the Grand Archives was severed.',
            ], 500);
        }
    }
}
