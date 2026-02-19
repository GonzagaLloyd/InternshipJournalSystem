<?php

namespace App\Http\Controllers;

use App\Models\JournalEntry;
use App\Models\Task;
use App\Models\Report;
use App\Services\VaultService;
use Inertia\Inertia;
use Illuminate\Http\Request;

class VaultController extends Controller
{
    protected $vaultService;

    public function __construct(VaultService $vaultService)
    {
        $this->vaultService = $vaultService;
    }

    /**
     * Display the Vault (soft-deleted items).
     */
    public function index()
    {
        return Inertia::render('Vault/Index', $this->vaultService->getTrashedItems(auth()->user()));
    }

    /**
     * Restore a trashed journal entry.
     */
    public function restoreEntry($id)
    {
        $this->vaultService->restoreItem(JournalEntry::class, $id, auth()->user());
        return back()->with('success', 'The record has been restored from the ashes.');
    }

    /**
     * Permanently delete a journal entry.
     */
    public function forceDeleteEntry($id)
    {
        $this->vaultService->permanentlyDeleteItem(JournalEntry::class, $id, auth()->user());
        return back()->with('success', 'The record has been permanently erased from history.');
    }

    /**
     * Restore a trashed task.
     */
    public function restoreTask($id)
    {
        $this->vaultService->restoreItem(Task::class, $id, auth()->user());
        return back()->with('success', 'The decree has been restored to the ledger.');
    }

    /**
     * Permanently delete a task.
     */
    public function forceDeleteTask($id)
    {
        $this->vaultService->permanentlyDeleteItem(Task::class, $id, auth()->user());
        return back()->with('success', 'The mandate has been permanently struck from records.');
    }

    /**
     * Restore a trashed report.
     */
    public function restoreReport($id)
    {
        $this->vaultService->restoreItem(Report::class, $id, auth()->user());
        return back()->with('success', 'The report has been restored from the vault.');
    }

    /**
     * Permanently delete a report.
     */
    public function forceDeleteReport($id)
    {
        $this->vaultService->permanentlyDeleteItem(Report::class, $id, auth()->user());
        return back()->with('success', 'The report has been permanently erased from the vault.');
    }
}
