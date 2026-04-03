<?php

namespace App\Http\Controllers\DriveSmart\Admin;

use App\Http\Controllers\Controller;
use App\Models\DriveSmart\Lesson;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — Admin DashboardController
|--------------------------------------------------------------------------
| Shows an overview of the entire DriveSmart system:
|   • Stats: instructor count, student count, planned/completed lessons
|   • Upcoming lessons: ALL planned future lessons across all instructors
|
| This is admin-only (protected by 'ds_role:admin' middleware in routes).
|
| $stats array passed to the view:
|   → Use compact() alternative: passing named variables. Here we use
|     array_merge with projectData for consistency with other controllers.
|--------------------------------------------------------------------------
*/
class DashboardController extends Controller
{
    private array $projectData = [
        'currentProject'     => 'drivesmart',
        'projectName'        => 'DriveSmart',
        'projectDescription' => 'Rijschoolbeheer: plan rijlessen, volg voortgang en beheer instructeurs',
    ];

    public function index()
    {
        $stats = [
            'instructors' => User::where('ds_role', 'instructor')->count(),
            'students'    => User::where('ds_role', 'student')->count(),
            'planned'     => Lesson::where('status', Lesson::STATUS_PLANNED)->count(),
            'completed'   => Lesson::where('status', Lesson::STATUS_COMPLETED)->count(),
            'cancelled'   => Lesson::where('status', Lesson::STATUS_CANCELLED)->count(),
            'sick'        => Lesson::where('status', Lesson::STATUS_SICK)->count(),
        ];

        $upcomingLessons = Lesson::with(['instructor', 'student'])
            ->where('status', Lesson::STATUS_PLANNED)
            ->where('scheduled_at', '>', now())
            ->orderBy('scheduled_at')
            ->get();

        return view('projects.drivesmart.admin.dashboard', array_merge($this->projectData, [
            'stats'          => $stats,
            'upcomingLessons' => $upcomingLessons,
        ]));
    }
}
