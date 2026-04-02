<?php

namespace App\Http\Controllers\TechBazaar\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — Admin UserController
|--------------------------------------------------------------------------
| Admins can:
|   - View all users (paginated)
|   - Change a user's role (update)
|   - Delete a user (destroy)
|
| ->where('id', '!=', auth()->id())
|   → Prevents the admin from deleting their own account.
|   → Prevents the admin from changing their own role (could lock themselves out).
|
| redirect()->route('...')->with('success', '...')
|   → ->with() flashes data to the session for ONE request.
|   → In the view: session('success') or @if(session('success'))
|--------------------------------------------------------------------------
*/

class UserController extends Controller
{
    private function projectData(): array
    {
        return [
            'currentProject'     => 'techbazaar',
            'projectName'        => config('projects.techbazaar.name'),
            'projectDescription' => config('projects.techbazaar.description'),
        ];
    }

    public function index()
    {
        return view('projects.techbazaar.admin.users.index', array_merge($this->projectData(), [
            'users' => User::latest()->paginate(20),
        ]));
    }

    public function update(Request $request, User $user)
    {
        // An admin cannot change their own role (could accidentally lock themselves out)
        abort_if($user->id === auth()->id(), 403, 'You cannot change your own role.');

        $request->validate([
            'role' => 'required|in:admin,seller,buyer',
        ]);

        $user->update(['role' => $request->role]);

        return redirect()->route('techbazaar.admin.users.index')
                         ->with('success', "Role updated for {$user->name}.");
    }

    public function destroy(User $user)
    {
        abort_if($user->id === auth()->id(), 403, 'You cannot delete your own account.');

        $user->delete();

        return redirect()->route('techbazaar.admin.users.index')
                         ->with('success', "{$user->name} has been deleted.");
    }
}
