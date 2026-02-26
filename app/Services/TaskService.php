<?php

namespace App\Services;

use App\Models\Task;
use Illuminate\Support\Facades\Cache;

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
        $task = Task::create([
            'user_id' => $user->id,
            'name' => $data['name'],
            'status' => 'todo',
            'completed' => false,
            'due_date' => $data['due_date'] ?? null,
            'priority' => $data['priority'] ?? 'medium',
            'total_time_ms' => 0,
        ]);

        Cache::forget("user_dashboard_{$user->id}");
        return $task;
    }

    /**
     * Update an existing task.
     */
    public function updateTask($task, array $data)
    {
        $updated = $task->update($data);
        if ($updated) {
            Cache::forget("user_dashboard_{$task->user_id}");
        }
        return $updated;
    }

    /**
     * Delete a task.
     */
    public function deleteTask($task)
    {
        $userId = $task->user_id;
        $deleted = $task->delete();
        if ($deleted) {
            Cache::forget("user_dashboard_{$userId}");
        }
        return $deleted;
    }

    /**
     * Pause a task (Stop timer, retain progress).
     */
    public function pauseTask($task)
    {
        if ($task->status !== 'in_progress') return $task;

        // Calculate time spent in this session
        $sessionTime = 0;
        if ($task->started_at) {
            $sessionTime = (int) abs(\Illuminate\Support\Carbon::parse($task->started_at)->diffInMilliseconds(now()));
        }

        $task->update([
            'status' => 'todo',
            'started_at' => null,
            'total_time_ms' => ($task->total_time_ms ?? 0) + $sessionTime,
        ]);

        Cache::forget("user_dashboard_{$task->user_id}");
        return $task;
    }

    /**
     * Toggle task progress state.
     * Cycle: todo -> in_progress -> completed
     */
    public function toggleTask($task)
    {
        $currentStatus = $task->status ?? 'todo';
        $updateData = [];

        if ($currentStatus === 'todo') {
            $updateData = [
                'status' => 'in_progress',
                'completed' => false,
                'started_at' => now(),
            ];
        } elseif ($currentStatus === 'in_progress') {
            // Complete the task
            $sessionTime = 0;
            if ($task->started_at) {
                $sessionTime = (int) abs(\Illuminate\Support\Carbon::parse($task->started_at)->diffInMilliseconds(now()));
            }
            
            $updateData = [
                'status' => 'completed',
                'completed' => true,
                'completed_at' => now(),
                'total_time_ms' => ($task->total_time_ms ?? 0) + $sessionTime,
                'started_at' => null
            ];
        } else {
            // Reset completed task to todo
            $updateData = [
                'status' => 'todo',
                'completed' => false,
                'completed_at' => null,
                'started_at' => null,
                'total_time_ms' => 0 
            ];
        }

        $task->update($updateData);
        Cache::forget("user_dashboard_{$task->user_id}");
        return $task;
    }
}
