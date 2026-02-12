<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIController extends Controller
{
    public function refine(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $apiKey = env('GEMINI_API_KEY');

        if (!$apiKey) {
            return response()->json([
                'error' => 'The Divine Muse is currently silent. (API key missing)',
            ], 500);
        }

        $prompt = "You are a helpful scribe's assistant. Your task is to correct grammar, spelling, and polish the following journal entry. 
        Maintain the original meaning and tone, but make it more elegant and well-written. 
        If the user's input is very short, suggest a way to expand it slightly to make it more descriptive of a day's event.
        Return ONLY the refined text without any explanations or introductory remarks.
        
        Text to refine:
        " . $request->content;

        try {
            // Using gemini-2.5-flash-lite which has higher rate limits (fastest/efficient)
            // Adding withoutVerifying() to bypass SSL issues in local development
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
                $refinedText = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

                if ($refinedText) {
                    return response()->json([
                        'refinedText' => trim($refinedText),
                    ]);
                }
            }

            $errorDetail = $response->json();
            Log::error('AI Refine Error', [
                'status' => $response->status(),
                'detail' => $errorDetail,
                'url' => "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent"
            ]);

            $errorMessage = $errorDetail['error']['message'] ?? 'The Divine Muse is troubled.';
            
            return response()->json([
                'error' => "The Muse says: {$errorMessage}",
            ], 500);

        } catch (\Exception $e) {
            Log::error('AI Refine Exception', ['message' => $e->getMessage()]);
            return response()->json([
                'error' => 'Connecting to the Scribe\'s Network failed.',
            ], 500);
        }
    }
}
