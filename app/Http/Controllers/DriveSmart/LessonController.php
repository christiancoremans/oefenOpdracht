<?php

namespace App\Http\Controllers\DriveSmart;

use App\Http\Controllers\Controller;
use App\Models\DriveSmart\Lesson;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — LessonController (student-facing)
|--------------------------------------------------------------------------
| index()  → shows ALL lessons for the logged-in student, sorted by date.
|   Both upcoming (planned) and history (completed/cancelled/sick) are
|   shown so the student can see their full record.
|
| update() → student cancels or reports sick on a lesson.
|   Three safety checks before allowing the update:
|   1. Authorization: $lesson->student_id === auth()->id()
|      → Student can only touch their OWN lessons (not other students').
|   2. Status check: only 'planned' lessons can be modified.
|      → Can't cancel an already-completed lesson.
|   3. Future check: lesson must be in the future.
|      → Can't retroactively report sick for yesterday's lesson.
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
        $lessons = auth()->user()
            ->studentLessons()
            ->with('instructor')
            ->orderBy('scheduled_at')
            ->get();

        $progressReports = auth()->user()
            ->progressReports()
            ->with('instructor')
            ->latest()
            ->get();

        return view('projects.drivesmart.lessons.index', array_merge($this->projectData, [
            'lessons'         => $lessons,
            'progressReports' => $progressReports,
        ]));
    }

    public function update(Request $request, Lesson $lesson)
    {
        // 1. Must own this lesson
        abort_if($lesson->student_id !== auth()->id(), 403, 'This is not your lesson.');

        // 2. Must be modifiable (planned + future)
        if (! $lesson->isModifiable()) {
            return back()->withErrors(['lesson' => 'You can only modify future planned lessons.']);
        }

        $action = $request->input('action');

        if ($action === 'cancel') {
            $lesson->update(['status' => Lesson::STATUS_CANCELLED]);
            return back()->with('success', 'Lesson cancelled successfully.');
        }

        if ($action === 'sick') {
            $lesson->update(['status' => Lesson::STATUS_SICK]);
            return back()->with('success', 'Reported sick for this lesson.');
        }

        return back();
    }
}
