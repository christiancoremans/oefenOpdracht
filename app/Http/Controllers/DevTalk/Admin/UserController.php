<?php

namespace App\Http\Controllers\DevTalk\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — Admin\UserController (DevTalk role management)
|--------------------------------------------------------------------------
| Admins can:
|   - List all users (paginated)
|   - Change a user's devtalk_role (admin / moderator / user)
|   - Delete a user account entirely
|
| Self-protection: abort_if($user->id === auth()->id(), 403)
|   Admins cannot demote or delete themselves — this prevents accidentally
|   locking yourself out of the admin panel. (Real apps use a "super-admin"
|   that is immutable, but for this casus abort_if is sufficient.)
|
| Why only modify devtalk_role and not role?
|   devtalk_role is the forum-specific role column. The global `role`
|   column (admin/seller/buyer) belongs to the TechBazaar project and
|   should not be touched here. Role isolation is a deliberate design
|   choice so the two projects don't interfere.
|
| PATCH /admin/users/{user}/role → update()  (role change)
| DELETE /admin/users/{user}     → destroy() (account deletion)
|--------------------------------------------------------------------------
*/
class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->paginate(20);

        return view('projects.devtalk.admin.users.index', [
            'users'              => $users,
            'currentProject'     => 'devtalk',
            'projectName'        => 'DevTalk',
            'projectDescription' => 'Developer discussion forum',
        ]);
    }

    public function update(Request $request, User $user)
    {
        abort_if($user->id === auth()->id(), 403, 'You cannot change your own role.');

        $data = $request->validate([
            'devtalk_role' => 'required|in:admin,moderator,user',
        ]);

        $user->update(['devtalk_role' => $data['devtalk_role']]);

        return redirect()->back()->with('success', "Role for {$user->name} updated.");
    }

    public function destroy(User $user)
    {
        abort_if($user->id === auth()->id(), 403, 'You cannot delete your own account from here.');

        $user->delete();

        return redirect()->route('devtalk.admin.users.index')
            ->with('success', "User {$user->name} deleted.");
    }
}
