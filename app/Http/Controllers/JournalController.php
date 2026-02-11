<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\JournalEntry;

class JournalController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard', [
            'entryCount' => JournalEntry::where('user_id', auth()->id())->count(),
            'tasks' => \App\Models\Task::where('user_id', auth()->id())->latest()->get(),
        ]);
    }

    public function entries()
    {
        return Inertia::render('Entries/Index', [
            'entries' => JournalEntry::where('user_id', auth()->id())->latest()->get(),
        ]);
    }

    public function store(Request $request)
    {
        // 1. Validate the data
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'content' => 'required|string|min:10',
            'entry_date' => 'required|date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB
            'video' => 'nullable|file|mimes:mp4,mov,avi|max:20480',      // 20MB
            'audio' => 'nullable|file|mimes:mp3,wav,ogg|max:10240',     // 10MB
            'file' => 'nullable|file|mimes:pdf,doc,docx,zip|max:10240', // 10MB
        ]);

        // 2. Handle File Uploads
        $paths = [
            'image' => $request->hasFile('image') ? $request->file('image')->store('journal/images', 'public') : null,
            'video' => $request->hasFile('video') ? $request->file('video')->store('journal/videos', 'public') : null,
            'audio' => $request->hasFile('audio') ? $request->file('audio')->store('journal/audio', 'public') : null,
            'file' => $request->hasFile('file') ? $request->file('file')->store('journal/documents', 'public') : null,
        ];

        // 3. Save to MongoDB
        JournalEntry::create([
            'user_id' => auth()->id(),
            'title' => $validated['title'] ?? 'Daily Entry - ' . now()->format('M d, Y'),
            'content' => $validated['content'],
            'entry_date' => $validated['entry_date'],
            'image' => $paths['image'],
            'video' => $paths['video'],
            'audio' => $paths['audio'],
            'file' => $paths['file'],
        ]);

        return back();
    }
    public function show($id)
    {
        $entry = JournalEntry::where('user_id', auth()->id())->findOrFail($id);
        
        return Inertia::render('Entries/Show', [
            'entry' => $entry,
        ]);
    }
    
    public function update(Request $request, JournalEntry $entry){
        
        if($entry->user_id !== auth()->id()){
            abort(403);
        }

           $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'content' => 'required|string|min:10',
            'entry_date' => 'required|date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB
            'video' => 'nullable|file|mimes:mp4,mov,avi|max:20480',      // 20MB
            'audio' => 'nullable|file|mimes:mp3,wav,ogg|max:10240',     // 10MB
            'file' => 'nullable|file|mimes:pdf,doc,docx,zip|max:10240', // 10MB
           ]);

           $paths = [
            'image' => $request->hasFile('image') ? $request->file('image')->store('journal/images', 'public') : $entry->image,
            'video' => $request->hasFile('video') ? $request->file('video')->store('journal/videos', 'public') : $entry->video,
            'audio' => $request->hasFile('audio') ? $request->file('audio')->store('journal/audio', 'public') : $entry->audio,
            'file' => $request->hasFile('file') ? $request->file('file')->store('journal/documents', 'public') : $entry->file,
        ];

        $entry->update([
            'title' => $validated['title'] ?? 'Daily Entry - ' . now()->format('M d, Y'),
            'content' => $validated['content'],
            'entry_date' => $validated['entry_date'],
            'image' => $paths['image'],
            'video' => $paths['video'],
            'audio' => $paths['audio'],
            'file' => $paths['file'],
        ]);

        return back()->with('success', 'Lore successfully sealed in the ledger.');
    }

    public function destroy(JournalEntry $entry)
    {
        if ($entry->user_id !== auth()->id()) {
            abort(403);
        }

        // Optional: Delete physical files from storage
        // \Storage::disk('public')->delete([$entry->image, $entry->video, $entry->audio, $entry->file]);

        $entry->delete();

        return to_route('journal.index')->with('success', 'The records has been banished to the Sunken Vault.');
    }
}
