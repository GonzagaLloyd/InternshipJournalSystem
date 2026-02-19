<?php

namespace App\Services;

use App\Models\Report;
use App\Models\ReportGenerationJob;
use App\Models\JournalEntry;
use App\Jobs\GenerateReportJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use MongoDB\BSON\ObjectId;

class ReportService
{
    /**
     * Get all reports for a user with standardized mapping.
     */
    public function getUserReports($user)
    {
        return Report::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($report) use ($user) {
                return $this->formatReport($report, $user);
            });
    }

    /**
     * Find a report by ID and user, handling MongoDB ObjectId conversion.
     */
    public function findUserReport($id, $user)
    {
        $report = Report::where('user_id', $user->id)
            ->where('_id', $id)
            ->first();

        if (!$report && class_exists(ObjectId::class)) {
            try {
                $objectId = new ObjectId($id);
                $report = Report::where('user_id', $user->id)
                    ->where('_id', $objectId)
                    ->first();
            } catch (\Exception $e) {
                // Invalid ObjectId format
            }
        }

        return $report;
    }

    /**
     * Format a report model for frontend consumption.
     */
    public function formatReport($report, $user)
    {
        return [
            'id' => (string) $report->_id,
            'report' => $report->report,
            'period' => $report->period,
            'report_title' => $report->report_title ?? 'Weekly Progress Report',
            'user_name' => $report->user_name ?? $user->name,
            'user_role' => $report->user_role ?? 'IT Intern',
            'company_name' => $report->company_name ?? 'iTech Media Logic',
            'footer_text' => $report->footer_text ?? 'Generated via Internal Journal System',
            'created_at' => $report->created_at->format('M d, Y H:i'),
        ];
    }

    /**
     * Initiate background report generation.
     */
    public function startGeneration($user, array $entryIds)
    {
        $jobRecord = ReportGenerationJob::create([
            'user_id' => $user->id,
            'entry_ids' => $entryIds,
            'status' => 'pending'
        ]);

        GenerateReportJob::dispatch(
            $jobRecord->id,
            $user->id,
            $entryIds
        );

        Log::info("Report generation job #{$jobRecord->id} dispatched via Service");

        return $jobRecord;
    }

    /**
     * Get job status and results.
     */
    public function getJobStatus($jobId, $user)
    {
        $jobRecord = ReportGenerationJob::where('id', $jobId)
            ->where('user_id', $user->id)
            ->first();

        if (!$jobRecord) {
            return null;
        }

        // Safety check for zombie jobs
        if ($jobRecord->status === 'processing' && $jobRecord->created_at->diffInMinutes(now()) > 2) {
            return [
                'job_id' => $jobRecord->id,
                'status' => 'failed',
                'error' => 'Job timed out or worker failed.'
            ];
        }

        $response = [
            'job_id' => $jobRecord->id,
            'status' => $jobRecord->status,
        ];

        if ($jobRecord->status === 'completed') {
            $response['report'] = $jobRecord->report;
            $response['period'] = $jobRecord->period;
        } elseif ($jobRecord->status === 'failed') {
            $response['error'] = $jobRecord->error_message;
        }

        return $response;
    }

    /**
     * Save a new report.
     */
    public function storeReport($user, array $data)
    {
        return Report::create([
            'user_id' => $user->id,
            'report' => $data['report'],
            'period' => $data['period'],
            'report_title' => $data['report_title'] ?? 'Weekly Progress Report',
            'user_name' => $data['user_name'] ?? $user->name,
            'user_role' => $data['user_role'] ?? 'IT Intern',
            'company_name' => $data['company_name'] ?? 'iTech Media Logic',
            'footer_text' => $data['footer_text'] ?? 'Generated via Internal Journal System',
        ]);
    }

    /**
     * Update an existing report.
     */
    public function updateReport($report, array $data)
    {
        $report->update([
            'report' => $data['report'] ?? $report->report,
            'report_title' => $data['report_title'] ?? $report->report_title,
            'user_name' => $data['user_name'] ?? $report->user_name,
            'user_role' => $data['user_role'] ?? $report->user_role,
            'company_name' => $data['company_name'] ?? $report->company_name,
            'footer_text' => $data['footer_text'] ?? $report->footer_text,
        ]);

        return $report;
    }
}
