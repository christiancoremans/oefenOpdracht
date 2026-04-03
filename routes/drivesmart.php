<?php

use App\Http\Controllers\DriveSmart\Admin\DashboardController as DsAdminDashboard;
use App\Http\Controllers\DriveSmart\Admin\UserController as DsAdminUser;
use App\Http\Controllers\DriveSmart\Instructor\LessonController as InstructorLesson;
use App\Http\Controllers\DriveSmart\Instructor\ProgressReportController;
use App\Http\Controllers\DriveSmart\LessonController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — DriveSmart route file
|--------------------------------------------------------------------------
| Three access tiers:
|   Public:              GET / (landing page)
|   Authenticated:       dashboard + student lesson management
|   Instructor + Admin:  lesson CRUD, progress reports
|   Admin only:          user management, admin dashboard
|
| Why separate LessonController (student-facing) and
| Instructor\LessonController?
|   → Student: read-only + cancel/sick actions on THEIR OWN lessons.
|   → Instructor: create lessons, assign students, update all statuses.
|   → Separating them avoids a bloated controller with 8+ methods and
|     complex ownership logic mixed with different role concerns.
|
| .whereNumber('lesson') / .whereNumber('report')
|   → Prevents 'create' from matching {lesson} or {report} as an ID.
|   Same fix applied for DevTalk {thread} and EventEase {event}.
|--------------------------------------------------------------------------
*/

Route::prefix('project/drivesmart')->name('drivesmart.')->group(function () {

    // ── Public ───────────────────────────────────────────────────────────────
    Route::get('/', fn () => view('projects.drivesmart.home', [
        'currentProject'     => 'drivesmart',
        'projectName'        => config('projects.drivesmart.name'),
        'projectDescription' => config('projects.drivesmart.description'),
    ]))->name('home');

    // ── All authenticated users ──────────────────────────────────────────────
    Route::middleware('auth')->group(function () {

        // Dashboard hub
        Route::get('/dashboard', fn () => view('projects.drivesmart.dashboard', [
            'currentProject'     => 'drivesmart',
            'projectName'        => config('projects.drivesmart.name'),
            'projectDescription' => config('projects.drivesmart.description'),
        ]))->name('dashboard');

        // Student: my lesson list + cancel/sick actions
        Route::get('/lessons', [LessonController::class, 'index'])->name('lessons.index');
        Route::patch('/lessons/{lesson}', [LessonController::class, 'update'])->name('lessons.update')->whereNumber('lesson');

        // ── Instructor + Admin ─────────────────────────────────────────────────
        Route::middleware('ds_role:instructor,admin')
             ->prefix('instructor')->name('instructor.')
             ->group(function () {

                 // Lesson schedule management
                 Route::get('/lessons',                       [InstructorLesson::class, 'index']  )->name('lessons.index');
                 Route::get('/lessons/create',                [InstructorLesson::class, 'create'] )->name('lessons.create');
                 Route::post('/lessons',                      [InstructorLesson::class, 'store']  )->name('lessons.store');
                 Route::patch('/lessons/{lesson}',            [InstructorLesson::class, 'update'] )->name('lessons.update')->whereNumber('lesson');
                 Route::delete('/lessons/{lesson}',           [InstructorLesson::class, 'destroy'])->name('lessons.destroy')->whereNumber('lesson');

                 // Student progress reports
                 Route::get('/progress',                      [ProgressReportController::class, 'index'] )->name('progress.index');
                 Route::get('/progress/create',               [ProgressReportController::class, 'create'])->name('progress.create');
                 Route::post('/progress',                     [ProgressReportController::class, 'store']  )->name('progress.store');
                 Route::get('/progress/{report}/edit',        [ProgressReportController::class, 'edit']  )->name('progress.edit')->whereNumber('report');
                 Route::put('/progress/{report}',             [ProgressReportController::class, 'update'])->name('progress.update')->whereNumber('report');
             });

        // ── Admin only ────────────────────────────────────────────────────────
        Route::middleware('ds_role:admin')
             ->prefix('admin')->name('admin.')
             ->group(function () {
                 Route::get('/',           [DsAdminDashboard::class, 'index'])->name('dashboard');
                 Route::get('/users',      [DsAdminUser::class, 'index']     )->name('users.index');
                 Route::patch('/users/{user}', [DsAdminUser::class, 'update'])->name('users.update');
             });
    });
});
