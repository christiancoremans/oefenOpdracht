<?php

namespace App\Http\Controllers\DriveSmart\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — Admin UserController (DriveSmart)
|--------------------------------------------------------------------------
| Same pattern as EventEase Admin\UserController.
| Admin can change any user's ds_role.
|
| Server-side validation of 'in:admin,instructor,student' is critical —
| a user could bypass the <select> element and POST any value.
| Always validate enum values on the server, regardless of client-side
| constraints.
|--------------------------------------------------------------------------
*/
class UserController extends Controller
{
    private array $projectData = [
        'currentProject'     => 'drivesmart',
        'projectName'        => 'DriveSmart',
        'projectDescription' => 'Rijschoolbeheer: plan rijlessen, volg voortgang en beheer instructeurs',
    ];

    public function index()
    {
        $users = User::orderBy('name')->get();

        return view('projects.drivesmart.admin.users.index', array_merge($this->projectData, [
            'users' => $users,
        ]));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'ds_role' => ['required', 'in:admin,instructor,student'],
        ]);

        $user->update($validated);

        return back()->with('success', "DS role updated for {$user->name}.");
    }
}
