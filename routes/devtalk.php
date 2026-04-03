<?php

use App\Http\Controllers\DevTalk\Admin\DashboardController as DtAdminDashboard;
use App\Http\Controllers\DevTalk\Admin\UserController as DtAdminUser;
use App\Http\Controllers\DevTalk\Moderator\ReportController as ModReport;
use App\Http\Controllers\DevTalk\PostController;
use App\Http\Controllers\DevTalk\ReportController;
use App\Http\Controllers\DevTalk\ThreadController;
use App\Http\Controllers\DevTalk\VoteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — DevTalk route file
|--------------------------------------------------------------------------
| Pattern mirrors routes/techbazaar.php:
|   prefix('project/devtalk') + name('devtalk.') + nested middleware groups
|
| Why separate post edit/update/destroy routes instead of Route::resource?
|   → Posts are always shown INSIDE a thread page (devtalk.threads.show).
|   → A resource would add unwanted routes like GET /posts/{post} (show).
|   → Explicit routes give you exact control.
|
| Moderator routes use 'devtalk_role:moderator,admin'
|   → Both moderators AND admins can moderate.
|   → Admins have full access so they must be included.
|
| Admin routes use 'devtalk_role:admin' only
|   → The admin dashboard is admin-exclusive.
|--------------------------------------------------------------------------
*/

Route::prefix('project/devtalk')->name('devtalk.')->group(function () {

    // ── Public ───────────────────────────────────────────────────────────────
    Route::get('/',                 [ThreadController::class, 'index'])->name('home');
    // whereNumber ensures 'create' is never treated as a thread ID
    Route::get('/threads/{thread}', [ThreadController::class, 'show'] )->name('threads.show')->whereNumber('thread');

    // ── All authenticated users ──────────────────────────────────────────────
    Route::middleware('auth')->group(function () {

        // Dashboard hub
        Route::get('/dashboard', fn () => view('projects.devtalk.dashboard', [
            'currentProject'     => 'devtalk',
            'projectName'        => config('projects.devtalk.name'),
            'projectDescription' => config('projects.devtalk.description'),
        ]))->name('dashboard');

        // Threads — create, edit, update, destroy (index+show are public above)
        Route::get('/threads/create',           [ThreadController::class, 'create'] )->name('threads.create');
        Route::post('/threads',                 [ThreadController::class, 'store']  )->name('threads.store');
        Route::get('/threads/{thread}/edit',    [ThreadController::class, 'edit']   )->name('threads.edit');
        Route::put('/threads/{thread}',         [ThreadController::class, 'update'] )->name('threads.update');
        Route::delete('/threads/{thread}',      [ThreadController::class, 'destroy'])->name('threads.destroy');

        // Posts (replies) — store, edit, update, destroy
        Route::post('/posts',              [PostController::class, 'store']  )->name('posts.store');
        Route::get('/posts/{post}/edit',   [PostController::class, 'edit']   )->name('posts.edit');
        Route::put('/posts/{post}',        [PostController::class, 'update'] )->name('posts.update');
        Route::delete('/posts/{post}',     [PostController::class, 'destroy'])->name('posts.destroy');

        // Votes (up/downvote a post)
        Route::post('/votes', [VoteController::class, 'store'])->name('votes.store');

        // Reports (flag a post)
        Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');

        // ── Moderator + Admin ─────────────────────────────────────────────────
        Route::middleware('devtalk_role:moderator,admin')
             ->prefix('moderator')->name('moderator.')
             ->group(function () {
                 Route::get('/reports',           [ModReport::class, 'index'] )->name('reports.index');
                 Route::patch('/reports/{report}',[ModReport::class, 'update'])->name('reports.update');
             });

        // ── Admin only ────────────────────────────────────────────────────────
        Route::middleware('devtalk_role:admin')
             ->prefix('admin')->name('admin.')
             ->group(function () {
                 Route::get('/', [DtAdminDashboard::class, 'index'])->name('dashboard');
                 Route::resource('users', DtAdminUser::class)->only(['index', 'update', 'destroy']);
             });
    });
});
