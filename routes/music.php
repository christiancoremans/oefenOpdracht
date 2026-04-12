<?php

use App\Http\Controllers\Music\ReservationController;
use App\Http\Controllers\Music\WorkshopController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — MusicHub route file structure
|--------------------------------------------------------------------------
| Route ordering matters for {workshop} wildcard routes.
| Static segments like /workshops/create must be registered BEFORE
| /workshops/{workshop} to prevent "create" being treated as an ID.
|
| Solution used here: admin routes (which include /create) are registered
| in their own group BEFORE the public /workshops/{workshop} show route.
|
| prefix('project/music')  → all URLs: /project/music/...
| name('music.')           → all route names: music.home, music.workshops.show, etc.
|--------------------------------------------------------------------------
*/

Route::prefix('project/music')->name('music.')->group(function () {

    // ── Public: Workshop overview (list) ──────────────────────────────────────
    Route::get('/', [WorkshopController::class, 'index'])->name('home');

    // ── Admin-only routes (registered BEFORE {workshop} wildcard) ─────────────
    // EXAM NOTE: 'auth' must wrap 'music_role' — the role middleware calls
    // auth()->user() which is null for guests. Always nest role inside auth.
    Route::middleware(['auth', 'music_role:admin'])->group(function () {
        Route::get('/workshops/create',          [WorkshopController::class, 'create'])->name('workshops.create');
        Route::post('/workshops',                [WorkshopController::class, 'store'] )->name('workshops.store');
        Route::get('/workshops/{workshop}/edit', [WorkshopController::class, 'edit']  )->name('workshops.edit')->whereNumber('workshop');
        Route::put('/workshops/{workshop}',      [WorkshopController::class, 'update'])->name('workshops.update')->whereNumber('workshop');
        Route::delete('/workshops/{workshop}',   [WorkshopController::class, 'destroy'])->name('workshops.destroy')->whereNumber('workshop');
    });

    // ── Public: Workshop detail (main page per workshop) ──────────────────────
    // Registered AFTER admin group so /workshops/create isn't caught here.
    Route::get('/workshops/{workshop}', [WorkshopController::class, 'show'])
         ->name('workshops.show')
         ->whereNumber('workshop');

    // ── Authenticated users ───────────────────────────────────────────────────
    Route::middleware('auth')->group(function () {

        Route::get('/dashboard', function () {
            return view('projects.music.dashboard', [
                'currentProject'     => 'music',
                'projectName'        => config('projects.music.name'),
                'projectDescription' => config('projects.music.description'),
            ]);
        })->name('dashboard');

        // Reserve a workshop
        Route::post('/reservations', [ReservationController::class, 'store'])->name('reservations.store');

        // Cancel a reservation
        Route::delete('/reservations/{reservation}', [ReservationController::class, 'destroy'])->name('reservations.destroy')->whereNumber('reservation');
    });
});
