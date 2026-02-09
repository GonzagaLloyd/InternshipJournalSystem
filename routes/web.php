<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\JournalController;

// ============================================================================
// PUBLIC ROUTES
// ============================================================================

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
});

// ============================================================================
// AUTHENTICATED ROUTES
// ============================================================================

Route::get('/dashboard', [JournalController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/entries', [JournalController::class, 'entries'])->middleware(['auth'])->name('journal.index');
Route::post('/journal', [JournalController::class, 'store'])->middleware(['auth'])->name('journal.store');


// ============================================================================
// AUTHENTICATION ROUTES
// ============================================================================
require __DIR__.'/auth.php';


