<?php

namespace App\Http\Controllers\DriveSmart\Instructor;

use App\Http\Controllers\Controller;
use App\Models\DriveSmart\Lesson;
use App\Models\User;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — Instructor LessonController
|--------------------------------------------------------------------------
| index()   → shows the instructor's full lesson schedule.
|   Admins see ALL lessons. Instructors see only their own.
|   Ordered by scheduled_at ascending (next lesson first).
|
| create()  → renders the "New Lesson" form.
|   Passes $students (users with ds_role='student') for the dropdown.
|
| store()   → validates and creates a new lesson.
|   Automatically sets instructor_id = auth()->id().
|   Status defaults to 'planned' (you can't schedule a completed lesson).
|
| update()  → instructor updates lesson status and/or notes.
|   Admins can update any lesson. Instructors only their own.
|   Status can move freely (e.g. planned → completed after the lesson).
|
| destroy() → delete a lesson (only planned lessons should be deletable
|   in a real system, but for exam simplicity we allow any).
|--------------------------------------------------------------------------
*/
class LessonController extends Controller
{
    private array $projectData = [
        'currentProject'     => 'drivesmart',
        'projectName'        => 'DriveSmart',
        'projectDescription' => 'Rijschoolbeheer: plan rijlessen, volg voortgang en beheer instructeurs',
    ];

    public function index()
    {
        $lessons = auth()->user()->isDsAdmin()
            ? Lesson::with(['instructor', 'student'])->orderBy('scheduled_at')->get()
            : auth()->user()->instructorLessons()->with('student')->orderBy('scheduled_at')->get();

        return view('projects.drivesmart.instructor.lessons.index', array_merge($this->projectData, [
            'lessons' => $lessons,
        ]));
    }

    public function create()
    {
        $students = User::where('ds_role', 'student')->orderBy('name')->get();

        return view('projects.drivesmart.instructor.lessons.create', array_merge($this->projectData, [
            'students' => $students,
        ]));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id'   => ['required', 'integer', 'exists:users,id'],
            'scheduled_at' => ['required', 'date', 'after:now'],
            'notes'        => ['nullable', 'string'],
        ]);

        $validated['instructor_id'] = auth()->id();
        $validated['status']        = Lesson::STATUS_PLANNED;

        Lesson::create($validated);

        return redirect()->route('drivesmart.instructor.lessons.index')
                         ->with('success', 'Lesson scheduled!');
    }

    public function update(Request $request, Lesson $lesson)
    {
        $this->ownershipCheck($lesson);

        $validated = $request->validate([
            'status' => ['required', 'in:planned,completed,cancelled,sick'],
            'notes'  => ['nullable', 'string'],
        ]);

        $lesson->update($validated);

        return back()->with('success', 'Lesson updated.');
    }

    public function destroy(Lesson $lesson)
    {
        $this->ownershipCheck($lesson);
        $lesson->delete();

        return redirect()->route('drivesmart.instructor.lessons.index')
                         ->with('success', 'Lesson deleted.');
    }

    // ── Helpers ──────────────────────────────────────────────────────────────
    private function ownershipCheck(Lesson $lesson): void
    {
        if (! auth()->user()->isDsAdmin() && $lesson->instructor_id !== auth()->id()) {
            abort(403, 'You do not own this lesson.');
        }
    }
}
