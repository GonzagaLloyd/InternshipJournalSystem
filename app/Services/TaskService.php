<?php

namespace App\Services;

use App\Models\Task;

class TaskService
{
    /**
     * Get all tasks for a user.
     */
    public function getUserTasks($user)
    {
        return Task::where('user_id', $user->id)->latest()->get();
    }

    /**
     * Store a new task.
     */
    public function storeTask($user, array $data)
    {
        return Task::create([
            'user_id' => $user->id,
            'name' => $data['name'],
            'completed' => false,
            'due_date' => $data['due_date'] ?? null,
            'priority' => $data['priority'] ?? 'medium',
        ]);
    }

    /**
     * Update an existing task.
     */
    public function updateTask($task, array $data)
    {
        return $task->update($data);
    }

    /**
     * Delete a task.
     */
    public function deleteTask($task)
    {
        return $task->delete();
    }

    /**
     * Toggle task completion.
     */
    public function toggleTask($task)
    {
        $task->update([
            'completed' => !$task->completed
        ]);
        return $task;
    }
}
