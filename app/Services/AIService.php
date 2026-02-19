<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    protected $apiKey;
    protected $baseUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent";

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key');
    }

    /**
     * Refine content using Gemini AI.
     */
    public function refineContent($content)
    {
        $prompt = "You are a professional Technical Writer and IT Documentation Assistant. 
        Your task is to refine the following journal entry into clear, professional, and grammatically correct technical documentation.
        
        Guidelines:
        - Maintain a professional and objective tone.
        - Use precise technical terminology where appropriate.
        - Ensure clarity and conciseness suitable for a daily work log or progress report.
        - If the input is brief, expand slightly to make it a complete thought, but do not invent facts.
        - Return ONLY the refined text. Do not add introductory phrases like 'Here is the refined text:'.
        
        Text to refine:
        " . $content;

        return $this->generateContent($prompt);
    }

    /**
     * Generate a full report based on journal entries.
     */
    public function generateReport($formattedEntries)
    {
        $prompt = "You are a professional IT Documentation Specialist. I am providing you with daily task entries from a BSIT student/intern's journal.

Your task is to generate a professional 'Weekly Progress Report' suitable for documentation and submission to a supervisor.

**Formatting Rules:**
- Use proper Markdown headers (e.g., '# Title', '## Section').
- Do NOT use bold keys for main sections (e.g., don't use '**1. Executive Summary**', use '## Executive Summary').
- Use bullet points for lists.
- The report must be **technical, concise, and professional**. 
- Do NOT use any fantasy, ancient, or flowery language. Refine the user's rough notes into clear, professional technical writing.

Structure the report as follows:
# Weekly Progress Report

## Executive Summary
(A high-level overview of the week's progress and main focus.)

## Technical Accomplishments
(A bulleted list of specific tasks completed, features implemented, and technologies used. Use strong action verbs like 'Implemented', 'Refactored', 'Debugged'.)

## Challenges & Resolutions
(A section detailing specific technical hurdles encountered and the solutions applied.)

## Key Learnings
(New technical skills or concepts reinforced during the week.)

## Forward Outlook
(Brief goals for the next week based on current progress.)

Journal Entries:
{$formattedEntries}";

        return $this->generateContent($prompt);
    }

    /**
     * Core method to interact with Gemini API.
     */
    protected function generateContent($prompt)
    {
        if (!$this->apiKey) {
            throw new \Exception('AI Service Unavailable (API key missing)');
        }

        try {
            $response = Http::withoutVerifying()
                ->timeout(120)
                ->post("{$this->baseUrl}?key={$this->apiKey}", [
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
                $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

                if ($text) {
                    return trim($text);
                }
            }

            $errorDetail = $response->json();
            Log::error('AIService API Error', ['detail' => $errorDetail]);
            
            $errorMessage = $errorDetail['error']['message'] ?? 'API request failed.';
            throw new \Exception("AI Error: {$errorMessage}");

        } catch (\Exception $e) {
            Log::error('AIService Exception', ['message' => $e->getMessage()]);
            throw $e;
        }
    }
}
