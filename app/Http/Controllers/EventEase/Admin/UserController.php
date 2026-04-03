<?php

namespace App\Http\Controllers\EventEase\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — EventEase Admin UserController
|--------------------------------------------------------------------------
| Admins can view all users and change their ee_role.
|
| Pattern mirrors DevTalk's Admin\UserController:
|   index()  → lists all users with their current ee_role
|   update() → changes a user's ee_role (admin only)
|
| We validate that the new role is one of the allowed enum values.
| validate(['role' => 'in:admin,organizer,visitor']) ensures no invalid
| role is injected via a crafted POST request.
|--------------------------------------------------------------------------
*/
class UserController extends Controller
{
    private array $projectData = [
        'currentProject'     => 'eventease',
        'projectName'        => 'EventEase',
        'projectDescription' => 'Reserveringssysteem: boek tickets voor events, concerten en conferenties',
    ];

    public function index()
    {
        $users = User::orderBy('name')->get();

        return view('projects.eventease.admin.users.index', array_merge($this->projectData, [
            'users' => $users,
        ]));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'ee_role' => ['required', 'in:admin,organizer,visitor'],
        ]);

        $user->update($validated);

        return back()->with('success', "Role updated for {$user->name}.");
    }
}
