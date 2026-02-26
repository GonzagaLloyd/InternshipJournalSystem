<?php

namespace App\Services;

use App\Models\JournalEntry;
use App\Models\Task;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class JournalService
{
    /**
     * Get dashboard activity and counts.
     */
    public function getDashboardData($user)
    {
        return cache()->remember("user_dashboard_{$user->id}", now()->addMinutes(15), function() use ($user) {
            // Get activity counts grouped by date more efficiently
            $activity = JournalEntry::where('user_id', $user->id)
                ->where('entry_date', '>=', now()->subDays(365)->format('Y-m-d'))
                ->get(['entry_date'])
                ->map(fn($e) => ['date' => substr($e->entry_date, 0, 10)]) // Ensure Y-m-d format
                ->groupBy('date')
                ->map(fn($group) => $group->count());

            return [
                'entryCount' => JournalEntry::where('user_id', $user->id)->count(),
                // Limit tasks to a reasonable number for dashboard overview or use latest
                'tasks' => Task::where('user_id', $user->id)
                    ->where('status', '!=', 'completed')
                    ->orderBy('status', 'desc') // Simple sort: puts in_progress/todo before others
                    ->latest()
                    ->limit(50)
                    ->get(), 
                'activity' => $activity,
            ];
        });
    }

    /**
     * Get paginated and filtered journal entries.
     */
    public function getPaginatedEntries($user, $search = null)
    {
        $query = JournalEntry::where('user_id', $user->id);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('entry_date', 'desc')
            ->paginate(12);
    }

    /**
     * Store or update a journal entry with file handling.
     */
    public function saveEntry($user, array $validated, $entry = null)
    {
        $paths = $this->handleFileUploads($validated, $entry);

        $data = [
            'user_id' => $user->id,
            'title' => $validated['title'] ?? 'Daily Entry - ' . \Illuminate\Support\Carbon::parse($validated['entry_date'])->format('M d, Y'),
            'content' => $validated['content'],
            'entry_date' => $validated['entry_date'],
            'image' => $paths['image'],
            'video' => $paths['video'],
            'audio' => $paths['audio'],
            'file' => $paths['file'],
        ];

        if ($entry) {
            $entry->update($data);
            Cache::forget("user_dashboard_{$user->id}");
            return $entry;
        }

        $newEntry = JournalEntry::create($data);
        Cache::forget("user_dashboard_{$user->id}");
        return $newEntry;
    }

    /**
     * Handle file uploads and return paths.
     */
    protected function handleFileUploads(array $data, $entry = null)
    {
        return [
            'image' => (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) 
                ? $data['image']->store('journal/images', 'public') 
                : ($entry?->image),
            'video' => (isset($data['video']) && $data['video'] instanceof \Illuminate\Http\UploadedFile) 
                ? $data['video']->store('journal/videos', 'public') 
                : ($entry?->video),
            'audio' => (isset($data['audio']) && $data['audio'] instanceof \Illuminate\Http\UploadedFile) 
                ? $data['audio']->store('journal/audio', 'public') 
                : ($entry?->audio),
            'file' => (isset($data['file']) && $data['file'] instanceof \Illuminate\Http\UploadedFile) 
                ? $data['file']->store('journal/documents', 'public') 
                : ($entry?->file),
        ];
    }

    /**
     * Delete an entry (Soft Delete).
     */
    public function deleteEntry($entry)
    {
        $deleted = $entry->delete();
        if ($deleted) {
            Cache::forget("user_dashboard_{$entry->user_id}");
        }
        return $deleted;
    }
}
