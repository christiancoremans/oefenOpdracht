<?php

namespace App\Http\Controllers\DriveSmart\Instructor;

use App\Http\Controllers\Controller;
use App\Models\DriveSmart\ProgressReport;
use App\Models\User;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — ProgressReportController
|--------------------------------------------------------------------------
| Instructors write progress reports for their students. Reports track:
|   • lessons_completed → how many lessons the student has done
|   • skill_level       → beginner / intermediate / advanced
|   • notes             → free-text instructor assessment
|
| index()  → lists all reports written by this instructor (or all, for admin).
|
| create() → renders the form. Passes $students dropdown and the
|   auto-calculated completed lesson count as a reference value
|   (the instructor fills in the field manually, but we show a hint).
|
| store()  → validates and saves. Sets instructor_id automatically.
|
| edit() / update() → instructor can correct a previously written report.
|   Ownership check: only the author or admin can edit.
|
| Privacy note (exam):
|   Only the student's own instructor or an admin can read/write these.
|   The student dashboard shows the student's OWN reports (read-only).
|--------------------------------------------------------------------------
*/
class ProgressReportController extends Controller
{
    private array $projectData = [
        'currentProject'     => 'drivesmart',
        'projectName'        => 'DriveSmart',
        'projectDescription' => 'Rijschoolbeheer: plan rijlessen, volg voortgang en beheer instructeurs',
    ];

    public function index()
    {
        $reports = auth()->user()->isDsAdmin()
            ? ProgressReport::with(['student', 'instructor'])->latest()->get()
            : auth()->user()->writtenReports()->with('student')->latest()->get();

        return view('projects.drivesmart.instructor.progress.index', array_merge($this->projectData, [
            'reports' => $reports,
        ]));
    }

    public function create()
    {
        $students = User::where('ds_role', 'student')->orderBy('name')->get();

        return view('projects.drivesmart.instructor.progress.create', array_merge($this->projectData, [
            'students' => $students,
        ]));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id'        => ['required', 'integer', 'exists:users,id'],
            'lessons_completed' => ['required', 'integer', 'min:0'],
            'skill_level'       => ['required', 'in:beginner,intermediate,advanced'],
            'notes'             => ['nullable', 'string'],
        ]);

        $validated['instructor_id'] = auth()->id();

        ProgressReport::create($validated);

        return redirect()->route('drivesmart.instructor.progress.index')
                         ->with('success', 'Progress report saved!');
    }

    public function edit(ProgressReport $report)
    {
        $this->ownershipCheck($report);

        $students = User::where('ds_role', 'student')->orderBy('name')->get();

        return view('projects.drivesmart.instructor.progress.edit', array_merge($this->projectData, [
            'report'   => $report,
            'students' => $students,
        ]));
    }

    public function update(Request $request, ProgressReport $report)
    {
        $this->ownershipCheck($report);

        $validated = $request->validate([
            'student_id'        => ['required', 'integer', 'exists:users,id'],
            'lessons_completed' => ['required', 'integer', 'min:0'],
            'skill_level'       => ['required', 'in:beginner,intermediate,advanced'],
            'notes'             => ['nullable', 'string'],
        ]);

        $report->update($validated);

        return redirect()->route('drivesmart.instructor.progress.index')
                         ->with('success', 'Report updated!');
    }

    // ── Helpers ──────────────────────────────────────────────────────────────
    private function ownershipCheck(ProgressReport $report): void
    {
        if (! auth()->user()->isDsAdmin() && $report->instructor_id !== auth()->id()) {
            abort(403, 'You did not write this report.');
        }
    }
}
