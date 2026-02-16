<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\VaultController;
use App\Http\Controllers\CalendarController;

// ============================================================================
// PUBLIC ROUTES
// ============================================================================

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
});

// ============================================================================
// AUTHENTICATED ROUTES
// ============================================================================

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [JournalController::class, 'index'])->name('dashboard');
    Route::get('/entries', [JournalController::class, 'entries'])->name('journal.index');
    Route::post('/journal', [JournalController::class, 'store'])->name('journal.store');
    Route::get('/journal/{entry}', [JournalController::class, 'show'])->name('journal.show');
    Route::patch('/journal/{entry}', [JournalController::class, 'update'])->name('journal.update');
    Route::delete('/journal/{entry}', [JournalController::class, 'destroy'])->name('journal.destroy');

    // Task Routes
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::patch('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    Route::patch('/tasks/{task}/toggle', [TaskController::class, 'toggle'])->name('tasks.toggle');

    // Vault Routes
    Route::get('/vault', [VaultController::class, 'index'])->name('vault.index');
    Route::post('/vault/journal/{id}/restore', [VaultController::class, 'restoreEntry'])->name('vault.journal.restore');
    Route::delete('/vault/journal/{id}/force', [VaultController::class, 'forceDeleteEntry'])->name('vault.journal.force-delete');
    Route::post('/vault/tasks/{id}/restore', [VaultController::class, 'restoreTask'])->name('vault.tasks.restore');
    Route::delete('/vault/tasks/{id}/force', [VaultController::class, 'forceDeleteTask'])->name('vault.tasks.force-delete');

    // AI Routes
    Route::post('/journal/refine', [\App\Http\Controllers\AIController::class, 'refine'])->name('journal.refine');

    // Calendar Routes
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');

    // Report Routes
    Route::get('/reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/create', [\App\Http\Controllers\ReportController::class, 'create'])->name('reports.create');
    Route::post('/reports/draft', [\App\Http\Controllers\ReportController::class, 'previewDraft'])->name('reports.draft');
    Route::get('/reports/{id}', [\App\Http\Controllers\ReportController::class, 'show'])->name('reports.show');
    Route::post('/reports/generate', [\App\Http\Controllers\ReportController::class, 'generate'])->name('reports.generate');
    Route::get('/reports/job/{jobId}', [\App\Http\Controllers\ReportController::class, 'jobStatus'])->name('reports.job.status');
    Route::post('/reports', [\App\Http\Controllers\ReportController::class, 'store'])->name('reports.store');
    Route::patch('/reports/{id}', [\App\Http\Controllers\ReportController::class, 'update'])->name('reports.update');
    Route::delete('/reports/{id}', [\App\Http\Controllers\ReportController::class, 'destroy'])->name('reports.destroy');
    
    // Vault Report Routes
    Route::post('/vault/reports/{id}/restore', [VaultController::class, 'restoreReport'])->name('vault.reports.restore');
    Route::delete('/vault/reports/{id}/force', [VaultController::class, 'forceDeleteReport'])->name('vault.reports.force-delete');
});


// ============================================================================
// AUTHENTICATION ROUTES
// ============================================================================
require __DIR__.'/auth.php';


