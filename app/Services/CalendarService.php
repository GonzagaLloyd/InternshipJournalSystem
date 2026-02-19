<?php

namespace App\Services;

use App\Models\JournalEntry;
use App\Models\Task;
use Carbon\Carbon;

class CalendarService
{
    /**
     * Get all calendar events (entries and tasks) for a user.
     */
    public function getCalendarEvents($user)
    {
        $entries = JournalEntry::where('user_id', $user->id)
            ->get(['id', 'title', 'entry_date'])
            ->map(function ($entry) {
                return [
                    'id' => $entry->id,
                    'title' => $entry->title,
                    'date' => Carbon::parse($entry->entry_date)->format('Y-m-d'),
                    'type' => 'entry'
                ];
            });

        $tasks = Task::where('user_id', $user->id)
            ->get(['id', 'name', 'due_date', 'priority', 'completed'])
            ->map(function ($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->name,
                    'date' => $task->due_date ? Carbon::parse($task->due_date)->format('Y-m-d') : null,
                    'type' => 'task',
                    'priority' => $task->priority,
                    'completed' => $task->completed
                ];
            })->filter(fn($task) => $task['date'] !== null);

        // Combine and group by date
        return $entries->concat($tasks)->groupBy('date');
    }
}
