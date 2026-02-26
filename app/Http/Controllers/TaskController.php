<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskController extends Controller
{
    protected $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    /**
     * Display a listing of the tasks.
     */
    public function index()
    {
        return Inertia::render('Tasks/Index', [
            'tasks' => Inertia::defer(fn () => $this->taskService->getUserTasks(auth()->user())->toArray())
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

        $this->taskService->storeTask(auth()->user(), $validated);

        return back()->with('success', 'Decree successfully sealed in the ledger.');
    }

    /**
     * Update the specified task in storage.
     */
    public function update(Request $request, Task $task)
    {
        if ($task->user_id !== auth()->id()) abort(403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'due_date' => 'nullable|date',
            'priority' => 'nullable|string|in:low,medium,high',
        ]);

        $this->taskService->updateTask($task, $validated);

        return back()->with('success', 'Decree successfully amended.');
    }

    /**
     * Remove the specified task from storage.
     */
    public function destroy(Task $task)
    {
        if ($task->user_id !== auth()->id()) abort(403);

        $this->taskService->deleteTask($task);

        return back()->with('success', 'The decree has been banished to the Sunken Vault.');
    }

    public function toggle(Task $task)
    {
        if ($task->user_id !== auth()->id()) abort(403);

        $this->taskService->toggleTask($task);

        return back()->with('success', "Mandate has been transitioned successfully.");
    }

    public function pause(Task $task)
    {
        if ($task->user_id !== auth()->id()) abort(403);

        $this->taskService->pauseTask($task);

        return back()->with('success', "Ritual has been paused. Progress is preserved.");
    }
}
