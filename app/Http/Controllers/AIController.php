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
                'error' => 'AI Service Unavailable (API key missing)',
            ], 500);
        }

        $prompt = "You are a professional Technical Writer and IT Documentation Assistant. 
        Your task is to refine the following journal entry into clear, professional, and grammatically correct technical documentation.
        
        Guidelines:
        - Maintain a professional and objective tone.
        - Use precise technical terminology where appropriate.
        - Ensure clarity and conciseness suitable for a daily work log or progress report.
        - If the input is brief, expand slightly to make it a complete thought, but do not invent facts.
        - Return ONLY the refined text. Do not add introductory phrases like 'Here is the refined text:'.
        
        Text to refine:
        " . $request->content;

        try {
            // Using gemini-flash-latest for best availability and performance
            // Adding withoutVerifying() to bypass SSL issues in local development
            $response = Http::withoutVerifying()
                ->timeout(60)
                ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key={$apiKey}", [
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
                'url' => "https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent"
            ]);

            $errorMessage = $errorDetail['error']['message'] ?? 'AI Service request failed.';
            
            return response()->json([
                'error' => "AI Error: {$errorMessage}",
            ], 500);

        } catch (\Exception $e) {
            Log::error('AI Refine Exception', ['message' => $e->getMessage()]);
            return response()->json([
                'error' => 'AI Service connection failed.',
            ], 500);
        }
    }
}
