<?php

namespace App\Http\Controllers;

use App\Services\AIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AIController extends Controller
{
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function refine(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        try {
            $refinedText = $this->aiService->refineContent($request->content);

            return response()->json([
                'refinedText' => $refinedText,
            ]);

        } catch (\Exception $e) {
            Log::error('AI Refine Controller Error', ['message' => $e->getMessage()]);
            
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
