<?php

use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — Route types used in this file
|--------------------------------------------------------------------------
|
| Route::view('url', 'view')          → Quick route, no controller needed
| Route::get('url', [Controller::class, 'method'])  → Standard controller route
| Route::post(...)                    → For forms (login, logout, store, update)
| Route::prefix('segment')            → Groups routes under a URL prefix
| Route::name('prefix.')              → Groups route names with a prefix
| Route::middleware('auth')           → Requires the user to be logged in
|   → If not logged in, redirects to /login automatically
|
*/

// ── Homepage ──────────────────────────────────────────────────────────────
Route::view('/', 'welcome')->name('home');

// ── Default app dashboard (used after login) ──────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';

// ── TechBazaar practice project ───────────────────────────────────────────
require __DIR__.'/techbazaar.php';

// ── DevTalk practice project ──────────────────────────────────────────────
require __DIR__.'/devtalk.php';

// ── EventEase practice project ────────────────────────────────────────────
require __DIR__.'/eventease.php';

// ── DriveSmart practice project ───────────────────────────────────────────
require __DIR__.'/drivesmart.php';

// ── Practice projects ─────────────────────────────────────────────────────
//
// These routes use {project} as a dynamic URL segment.
// The ProjectController validates it against config/projects.php.
//
// URL pattern:   /project/{slug}            → project home  (public)
//                /project/{slug}/dashboard  → project dash  (auth required)
//
// IMPORTANT: This group must come AFTER specific project route files
// (techbazaar.php, devtalk.php) so their explicit routes take priority
// over the generic {project} wildcard.
//
Route::prefix('project/{project}')->name('project.')->group(function () {

    // Public: anyone can view the project landing page
    Route::get('/', [ProjectController::class, 'home'])->name('home');

    // Protected: must be logged in to see the dashboard
    Route::middleware('auth')->group(function () {
        Route::get('dashboard', [ProjectController::class, 'dashboard'])->name('dashboard');
    });
});


