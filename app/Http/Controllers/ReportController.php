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
                'content' => $entry->content,
                'entry_date' => $entry->entry_date,
            ];
        });

        return Inertia::render('Reports/Index', [
            'availableEntries' => $availableEntries,
            'pastReports' => $pastReports
        ]);
    }

    public function create(Request $request)
    {
        $now = Carbon::now();
        $startOfWeek = $now->copy()->startOfWeek();
        $endOfWeek = $now->copy()->endOfWeek();

        $initialReport = "# Weekly Progress Report\n\n## Executive Summary\n \n## Technical Accomplishments\n-\n\n## Challenges & Resolutions\n-\n\n## Key Learnings\n-\n\n## Forward Outlook\n-\n";
        $initialPeriod = [
            'start' => $startOfWeek->format('M d, Y'),
            'end' => $endOfWeek->format('M d, Y')
        ];

        // Check if we are creating from a completed job
        if ($request->has('job_id')) {
            $user = auth()->user();
            $jobRecord = \App\Models\ReportGenerationJob::where('id', $request->job_id)
                ->where('user_id', $user->id) 
                ->first();

            if ($jobRecord && $jobRecord->status === 'completed') {
                $initialReport = $jobRecord->report;
                // Ensure period format matches if stored differently, but usually it's compatible
                if (isset($jobRecord->period)) {
                    $initialPeriod = $jobRecord->period;
                }
            }
        }

        return Inertia::render('Reports/Create', [
            'report' => [
                'id' => null,
                'report' => $initialReport,
                'period' => $initialPeriod,
                'created_at' => $now->format('M d, Y H:i'),
            ]
        ]);
    }

    public function previewDraft(Request $request)
    {
        $request->validate([
            'report' => 'required|string',
            'period' => 'required|array',
        ]);

        return Inertia::render('Reports/Create', [
            'report' => [
                'id' => null,
                'report' => $request->report,
                'period' => $request->period,
                'created_at' => Carbon::now()->format('M d, Y H:i'),
            ]
        ]);
    }

    public function show($id)
    {
        $user = auth()->user();
        
        $report = \App\Models\Report::where('user_id', $user->id)
            ->where('_id', $id)
            ->first();

        if (!$report && class_exists('MongoDB\BSON\ObjectId')) {
            try {
                $objectId = new \MongoDB\BSON\ObjectId($id);
                $report = \App\Models\Report::where('user_id', $user->id)
                    ->where('_id', $objectId)
                    ->first();
            } catch (\Exception $e) {}
        }

        if (!$report) {
            return redirect()->route('reports.index')->with('error', 'Report not found.');
        }

        return Inertia::render('Reports/Show', [
            'report' => [
                'id' => (string) $report->_id,
                'report' => $report->report,
                'period' => $report->period,
                'created_at' => $report->created_at->format('M d, Y H:i'),
            ]
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

            // Create a job record
            $jobRecord = \App\Models\ReportGenerationJob::create([
                'user_id' => $user->id,
                'entry_ids' => $request->entry_ids,
                'status' => 'pending'
            ]);

            // Dispatch the background job
            \App\Jobs\GenerateReportJob::dispatch(
                $jobRecord->id,
                $user->id,
                $request->entry_ids
            );

            Log::info("Report generation job #{$jobRecord->id} dispatched");

            return response()->json([
                'job_id' => $jobRecord->id,
                'status' => 'pending',
                'message' => 'Report generation started in background'
            ]);

        } catch (\Exception $e) {
            Log::error('Report Generation Fatal Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to start report generation: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function jobStatus($jobId)
    {
        try {
            $user = auth()->user();
            
            $jobRecord = \App\Models\ReportGenerationJob::where('id', $jobId)
                ->where('user_id', $user->id)
                ->first();

            if (!$jobRecord) {
                return response()->json(['error' => 'Job not found'], 404);
            }

            $response = [
                'job_id' => $jobRecord->id,
                'status' => $jobRecord->status,
            ];

            // Safety check for zombie jobs
            if ($jobRecord->status === 'processing' && $jobRecord->created_at->diffInMinutes(now()) > 2) {
                return response()->json([
                    'job_id' => $jobRecord->id,
                    'status' => 'failed',
                    'error' => 'Job timed out or worker failed.'
                ]);
            }

            if ($jobRecord->status === 'completed') {
                $response['report'] = $jobRecord->report;
                $response['period'] = $jobRecord->period;
            } elseif ($jobRecord->status === 'failed') {
                $response['error'] = $jobRecord->error_message;
            }

            return response()->json($response);

        } catch (\Exception $e) {
            Log::error('Job Status Check Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to check job status'], 500);
        }
    }


    public function store(Request $request)
    {
        try {
            $user = auth()->user();
            
            $request->validate([
                'report' => 'required|string',
                'period' => 'required|array',
                'period.start' => 'required|string',
                'period.end' => 'required|string',
            ]);

            $newReport = \App\Models\Report::create([
                'user_id' => $user->id,
                'report' => $request->report,
                'period' => $request->period,
            ]);

            return response()->json([
                'id' => (string) $newReport->_id,
                'report' => $newReport->report,
                'period' => $newReport->period,
                'created_at' => $newReport->created_at->format('M d, Y H:i'),
                'message' => 'Decree sealed and recorded in the library.'
            ]);

        } catch (\Exception $e) {
            Log::error('Report Save Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to save report.'], 500);
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
                return redirect()->back()->with('error', 'Chronicle not found.');
            }

            // Soft delete the report (moves to vault)
            $report->delete();
            Log::info("Report archived successfully: {$id}");

            return redirect()->route('reports.index')->with('success', 'Chronicle banished to the Sunken Vault.');
        } catch (\Exception $e) {
            Log::error('Report Archive Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Banishment failed: The chronicle resists.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $user = auth()->user();
            
            $request->validate([
                'report' => 'required|string',
            ]);

            // Find report
            $report = \App\Models\Report::where('_id', $id)->where('user_id', $user->id)->first();
            
            if (!$report && class_exists('MongoDB\BSON\ObjectId')) {
                 try {
                    $objectId = new \MongoDB\BSON\ObjectId($id);
                    $report = \App\Models\Report::where('_id', $objectId)->where('user_id', $user->id)->first();
                } catch (\Exception $e) {}
            }

            if (!$report) {
                 return response()->json(['error' => 'Report not found.'], 404);
            }

            $report->update([
                'report' => $request->report
            ]);

            return response()->json([
                'id' => (string) $report->_id,
                'report' => $report->report,
                'period' => $report->period,
                'created_at' => $report->created_at->format('M d, Y H:i'),
                'message' => 'Chronicle updated in the library vault.'
            ]);

        } catch (\Exception $e) {
            Log::error('Report Update Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update report.'], 500);
        }
    }
}
