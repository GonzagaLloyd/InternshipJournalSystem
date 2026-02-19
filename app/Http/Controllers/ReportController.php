<?php

namespace App\Http\Controllers;
 
use App\Models\JournalEntry;
use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function index()
    {
        $user = auth()->user();
        $pastReports = $this->reportService->getUserReports($user);

        $availableEntries = JournalEntry::where('user_id', $user->id)
            ->orderBy('entry_date', 'desc')
            ->get(['id', 'title', 'content', 'entry_date']);

        return Inertia::render('Reports/Index', [
            'availableEntries' => $availableEntries,
            'pastReports' => $pastReports
        ]);
    }

    public function create(Request $request)
    {
        $now = Carbon::now();
        $user = auth()->user();
        
        $initialReport = "# Weekly Progress Report\n\n## Executive Summary\n \n## Technical Accomplishments\n-\n\n## Challenges & Resolutions\n-\n\n## Key Learnings\n-\n\n## Forward Outlook\n-\n";
        $initialPeriod = [
            'start' => $now->copy()->startOfWeek()->format('M d, Y'),
            'end' => $now->copy()->endOfWeek()->format('M d, Y')
        ];

        $jobRecord = null;
        if ($request->has('job_id')) {
            $jobRecord = \App\Models\ReportGenerationJob::where('id', $request->job_id)
                ->where('user_id', $user->id) 
                ->first();

            if ($jobRecord && $jobRecord->status === 'completed') {
                $initialReport = $jobRecord->report;
                $initialPeriod = $jobRecord->period ?? $initialPeriod;
            }
        }

        return Inertia::render('Reports/Create', [
            'report' => [
                'id' => null,
                'report' => $initialReport,
                'period' => $initialPeriod,
                'report_title' => $jobRecord?->report_title ?? 'Weekly Progress Report',
                'user_name' => $jobRecord?->user_name ?? $user->name,
                'user_role' => $jobRecord?->user_role ?? 'IT Intern',
                'company_name' => $jobRecord?->company_name ?? 'iTech Media Logic',
                'footer_text' => $jobRecord?->footer_text ?? 'Generated via Internal Journal System',
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
                'report_title' => $request->report_title ?? 'Weekly Progress Report',
                'user_name' => $request->user_name ?? auth()->user()->name,
                'user_role' => $request->user_role ?? 'IT Intern',
                'company_name' => $request->company_name ?? 'iTech Media Logic',
                'footer_text' => $request->footer_text ?? 'Generated via Internal Journal System',
                'created_at' => Carbon::now()->format('M d, Y H:i'),
            ]
        ]);
    }

    public function show($id)
    {
        $user = auth()->user();
        $report = $this->reportService->findUserReport($id, $user);

        if (!$report) {
            return redirect()->route('reports.index')->with('error', 'Report not found.');
        }

        return Inertia::render('Reports/Show', [
            'report' => $this->reportService->formatReport($report, $user)
        ]);
    }

    public function generate(Request $request)
    {
        try {
            $request->validate([
                'entry_ids' => 'required|array|min:1',
                'entry_ids.*' => 'string'
            ]);

            $jobRecord = $this->reportService->startGeneration(auth()->user(), $request->entry_ids);

            return response()->json([
                'job_id' => $jobRecord->id,
                'status' => 'pending',
                'message' => 'Report generation started in background'
            ]);

        } catch (\Exception $e) {
            Log::error('Report Generation Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to start report generation.'], 500);
        }
    }

    public function jobStatus($jobId)
    {
        $status = $this->reportService->getJobStatus($jobId, auth()->user());

        if (!$status) {
            return response()->json(['error' => 'Job not found'], 404);
        }

        return response()->json($status);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'report' => 'required|string',
                'period' => 'required|array',
                'period.start' => 'required|string',
                'period.end' => 'required|string',
            ]);

            $user = auth()->user();
            $newReport = $this->reportService->storeReport($user, $request->all());

            return response()->json(array_merge(
                $this->reportService->formatReport($newReport, $user),
                ['message' => 'Decree sealed and recorded in the library.']
            ));

        } catch (\Exception $e) {
            Log::error('Report Save Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to save report.'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate(['report' => 'required|string']);
            $user = auth()->user();
            
            $report = $this->reportService->findUserReport($id, $user);
            if (!$report) return response()->json(['error' => 'Report not found.'], 404);

            $this->reportService->updateReport($report, $request->all());

            return response()->json(array_merge(
                $this->reportService->formatReport($report, $user),
                ['message' => 'Chronicle updated in the library vault.']
            ));

        } catch (\Exception $e) {
            Log::error('Report Update Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update report.'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = auth()->user();
            $report = $this->reportService->findUserReport($id, $user);

            if (!$report) return redirect()->back()->with('error', 'Chronicle not found.');

            $report->delete();
            return redirect()->route('reports.index')->with('success', 'Chronicle banished to the Sunken Vault.');
        } catch (\Exception $e) {
            Log::error('Report Archive Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Banishment failed: The chronicle resists.');
        }
    }
}
