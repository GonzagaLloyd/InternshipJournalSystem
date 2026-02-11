<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskController extends Controller
{
    /**
     * Display a listing of the tasks.
     */
    public function index()
    {
        $tasks = Task::where('user_id', auth()->id())->latest()->get();
        
        return Inertia::render('Tasks/Index', [
            'tasks' => $tasks->toArray()
        ]);
    }

    /**
     * Store a newly created task in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'due_date' => 'nullable|date',
            'priority' => 'nullable|string|in:low,medium,high',
        ]);

        Task::create([
            'user_id' => auth()->id(),
            'name' => $validated['name'],
            'completed' => false,
            'due_date' => $validated['due_date'] ?? null,
            'priority' => $validated['priority'] ?? 'medium',
        ]);

        return back()->with('success', 'Decree successfully sealed in the ledger.');
    }

    /**
     * Update the specified task in storage.
     */
    public function update(Request $request, Task $task)
    {
        // Ensure user owns the task
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }

        $validate = $request->validate([
            'name' => 'required|string|max:255',
            'due_date' => 'nullable|date',
            'priority' => 'nullable|string|in:low,medium,high',
        ]);

        $task->update($validate);

        return back()->with('success', 'Decree successfully sealed in the ledger.');
    }

    /**
     * Remove the specified task from storage.
     */
    public function destroy(Task $task)
    {
        // Ensure user owns the task
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }

        $task->delete();

        return back()->with('success', 'The decree has been banished to the Sunken Vault.');
    }

    /**
     * Toggle the completed status of the task.
     */
    public function toggle(Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }

        $task->update([
            'completed' => !$task->completed
        ]);

        $status = $task->completed ? 'completed' : 'reopened';
        return back()->with('success', "Mandate has been marked as {$status}.");
    }
}
