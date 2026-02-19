<?php

namespace App\Services;

use App\Models\JournalEntry;
use App\Models\Task;
use App\Models\Report;
use Illuminate\Support\Facades\Storage;

class VaultService
{
    /**
     * Get all trashed items for the user.
     */
    public function getTrashedItems($user)
    {
        $trashedEntries = JournalEntry::onlyTrashed()
            ->where('user_id', $user->id)
            ->latest('deleted_at')
            ->get();

        $trashedTasks = Task::onlyTrashed()
            ->where('user_id', $user->id)
            ->latest('deleted_at')
            ->get();

        $trashedReports = Report::onlyTrashed()
            ->where('user_id', $user->id)
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

        return [
            'entries' => $trashedEntries,
            'tasks' => $trashedTasks,
            'reports' => $trashedReports
        ];
    }

    /**
     * Restore a trashed item.
     */
    public function restoreItem($modelClass, $id, $user)
    {
        $item = $modelClass::onlyTrashed()
            ->where('user_id', $user->id)
            ->findOrFail($id);

        return $item->restore();
    }

    /**
     * Permanently delete a trashed item.
     */
    public function permanentlyDeleteItem($modelClass, $id, $user)
    {
        $item = $modelClass::onlyTrashed()
            ->where('user_id', $user->id)
            ->findOrFail($id);

        if ($modelClass === JournalEntry::class) {
            $files = array_filter([$item->image, $item->video, $item->audio, $item->file]);
            if (!empty($files)) {
                Storage::disk('public')->delete($files);
            }
        }

        return $item->forceDelete();
    }
}
