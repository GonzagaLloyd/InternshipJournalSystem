<?php

namespace App\Http\Controllers;

use App\Models\JournalEntry;
use App\Models\Task;
use App\Models\Report;
use Inertia\Inertia;
use Illuminate\Http\Request;

class VaultController extends Controller
{
    /**
     * Display the Vault (soft-deleted items).
     */
    public function index()
    {
        $trashedEntries = JournalEntry::onlyTrashed()
            ->where('user_id', auth()->id())
            ->latest('deleted_at')
            ->get();

        $trashedTasks = Task::onlyTrashed()
            ->where('user_id', auth()->id())
            ->latest('deleted_at')
            ->get();

        $trashedReports = Report::onlyTrashed()
            ->where('user_id', auth()->id())
            ->latest('deleted_at')
            ->get()
            ->map(function ($report) {
                return [
                    'id' => $report->id,
                    '_id' => $report->_id,
                    'period' => $report->period,
                    'deleted_at' => $report->deleted_at->format('M d, Y H:i'),
                ];
            });

        return Inertia::render('Vault/Index', [
            'entries' => $trashedEntries,
            'tasks' => $trashedTasks,
            'reports' => $trashedReports
        ]);
    }

    /**
     * Restore a trashed journal entry.
     */
    public function restoreEntry($id)
    {
        $entry = JournalEntry::onlyTrashed()
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        $entry->restore();

        return back()->with('success', 'The record has been restored from the ashes.');
    }

    /**
     * Permanently delete a journal entry.
     */
    public function forceDeleteEntry($id)
    {
        $entry = JournalEntry::onlyTrashed()
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        // Delete physical files if they exist
        $files = array_filter([$entry->image, $entry->video, $entry->audio, $entry->file]);
        if (!empty($files)) {
            \Storage::disk('public')->delete($files);
        }

        $entry->forceDelete();

        return back()->with('success', 'The record has been permanently erased from history.');
    }

    /**
     * Restore a trashed task.
     */
    public function restoreTask($id)
    {
        $task = Task::onlyTrashed()
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        $task->restore();

        return back()->with('success', 'The decree has been restored to the ledger.');
    }

    /**
     * Permanently delete a task.
     */
    public function forceDeleteTask($id)
    {
        $task = Task::onlyTrashed()
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        $task->forceDelete();

        return back()->with('success', 'The mandate has been permanently struck from records.');
    }

    /**
     * Restore a trashed report.
     */
    public function restoreReport($id)
    {
        $report = Report::onlyTrashed()
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        $report->restore();

        return back()->with('success', 'The report has been restored from the vault.');
    }

    /**
     * Permanently delete a report.
     */
    public function forceDeleteReport($id)
    {
        $report = Report::onlyTrashed()
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        $report->forceDelete();

        return back()->with('success', 'The report has been permanently erased from the vault.');
    }
}
