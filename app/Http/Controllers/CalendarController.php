<?php

namespace App\Http\Controllers;

use App\Models\JournalEntry;
use App\Models\Task;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();
        
        // Fetch all entries for the user
        $entries = JournalEntry::where('user_id', $userId)
            ->get(['id', 'title', 'entry_date'])
            ->map(function ($entry) {
                return [
                    'id' => $entry->id,
                    'title' => $entry->title,
                    'date' => Carbon::parse($entry->entry_date)->format('Y-m-d'),
                    'type' => 'entry'
                ];
            });

        // Fetch all tasks for the user
        $tasks = Task::where('user_id', $userId)
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
        $events = $entries->concat($tasks)->groupBy('date');

        return Inertia::render('Calendar/Index', [
            'events' => $events
        ]);
    }
}
