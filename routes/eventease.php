<?php

use App\Http\Controllers\EventEase\Admin\UserController as EeAdminUser;
use App\Http\Controllers\EventEase\EventController;
use App\Http\Controllers\EventEase\OrganizerEventController;
use App\Http\Controllers\EventEase\ReservationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — EventEase route file
|--------------------------------------------------------------------------
| Pattern mirrors routes/devtalk.php:
|   prefix('project/eventease') + name('eventease.') + nested middleware groups
|
| Public routes:
|   GET /events          → list upcoming events (anyone)
|   GET /events/{event}  → view one event detail
|
| Authenticated routes (all logged-in users):
|   GET  /reservations           → my tickets
|   POST /reservations           → book an event
|   PATCH /reservations/{res}    → cancel/rebook a reservation
|
| Organizer + Admin routes:
|   GET/POST /organizer/events/create → create event form + store
|   GET/PUT  /organizer/events/{event}/edit → edit form + update
|   DELETE   /organizer/events/{event} → delete event
|
| Admin routes:
|   GET /admin/users          → list all users + their roles
|   PATCH /admin/users/{user} → change a user's ee_role
|
| Why .whereNumber('event')?
|   Without it, GET /events/create would try to route-model-bind 'create'
|   as an event ID, causing a 404 or ModelNotFoundException.
|   .whereNumber('event') restricts {event} to digits only, so 'create'
|   falls through to the explicit GET /events/create route above it.
|--------------------------------------------------------------------------
*/

Route::prefix('project/eventease')->name('eventease.')->group(function () {

    // ── Public ───────────────────────────────────────────────────────────────
    Route::get('/', [EventController::class, 'index'])->name('home');
    Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show')->whereNumber('event');

    // ── All authenticated users ──────────────────────────────────────────────
    Route::middleware('auth')->group(function () {

        // Dashboard hub
        Route::get('/dashboard', fn () => view('projects.eventease.dashboard', [
            'currentProject'     => 'eventease',
            'projectName'        => config('projects.eventease.name'),
            'projectDescription' => config('projects.eventease.description'),
        ]))->name('dashboard');

        // My tickets
        Route::get('/reservations',                        [ReservationController::class, 'index'] )->name('reservations.index');
        Route::post('/reservations',                       [ReservationController::class, 'store']  )->name('reservations.store');
        Route::patch('/reservations/{reservation}',        [ReservationController::class, 'update'] )->name('reservations.update');

        // ── Organizer + Admin ─────────────────────────────────────────────────
        Route::middleware('ee_role:organizer,admin')
             ->prefix('organizer')->name('organizer.')
             ->group(function () {
                 Route::get('/events',                          [OrganizerEventController::class, 'index']  )->name('events.index');
                 Route::get('/events/create',                   [OrganizerEventController::class, 'create'] )->name('events.create');
                 Route::post('/events',                         [OrganizerEventController::class, 'store']  )->name('events.store');
                 Route::get('/events/{event}/edit',             [OrganizerEventController::class, 'edit']   )->name('events.edit')->whereNumber('event');
                 Route::put('/events/{event}',                  [OrganizerEventController::class, 'update'] )->name('events.update')->whereNumber('event');
                 Route::delete('/events/{event}',               [OrganizerEventController::class, 'destroy'])->name('events.destroy')->whereNumber('event');
             });

        // ── Admin only ────────────────────────────────────────────────────────
        Route::middleware('ee_role:admin')
             ->prefix('admin')->name('admin.')
             ->group(function () {
                 Route::get('/users',          [EeAdminUser::class, 'index'] )->name('users.index');
                 Route::patch('/users/{user}', [EeAdminUser::class, 'update'])->name('users.update');
             });
    });
});
