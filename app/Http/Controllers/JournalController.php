<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\JournalEntry;
use App\Services\JournalService;

class JournalController extends Controller
{
    protected $journalService;

    public function __construct(JournalService $journalService)
    {
        $this->journalService = $journalService;
    }

    public function index()
    {
        return Inertia::render('Dashboard', [
            'entryCount' => Inertia::defer(fn () => $this->journalService->getDashboardData(auth()->user())['entryCount']),
            'tasks' => Inertia::defer(fn () => $this->journalService->getDashboardData(auth()->user())['tasks']),
            'activity' => Inertia::defer(fn () => $this->journalService->getDashboardData(auth()->user())['activity']),
        ]);
    }

    public function entries(Request $request)
    {
        return Inertia::render('Entries/Index', [
            'entries' => Inertia::defer(fn () => $this->journalService->getPaginatedEntries(auth()->user(), $request->search)->withQueryString()),
            'filters' => $request->only(['search'])
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'content' => 'required|string|min:3',
            'entry_date' => 'required|date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'video' => 'nullable|file|mimes:mp4,mov,avi|max:20480',
            'audio' => 'nullable|file|mimes:mp3,wav,ogg|max:10240',
            'file' => 'nullable|file|mimes:pdf,doc,docx,zip|max:10240',
        ]);

        $this->journalService->saveEntry(auth()->user(), $request->all());
        return back()->with('success', 'New lore has been inscribed in the ledger.');
    }

    public function show($id)
    {
        return Inertia::render('Entries/Show', [
            'entry' => Inertia::defer(fn () => JournalEntry::where('user_id', auth()->id())->findOrFail($id)),
        ]);
    }
    
    public function update(Request $request, JournalEntry $entry)
    {
        if ($entry->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'title' => 'nullable|string|max:255',
            'content' => 'required|string|min:3',
            'entry_date' => 'required|date',
        ]);

        $this->journalService->saveEntry(auth()->user(), $request->all(), $entry);
        return back()->with('success', 'Lore successfully sealed in the ledger.');
    }

    public function destroy(JournalEntry $entry)
    {
        if ($entry->user_id !== auth()->id()) abort(403);
        $this->journalService->deleteEntry($entry);
        return redirect()->route('journal.index')->with('success', 'The record has been banished to the Sunken Vault.');
    }
}
