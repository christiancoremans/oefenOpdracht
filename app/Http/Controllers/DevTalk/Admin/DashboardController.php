<?php

namespace App\Http\Controllers\DevTalk\Admin;

use App\Http\Controllers\Controller;
use App\Models\DevTalk\Post;
use App\Models\DevTalk\Report;
use App\Models\DevTalk\Thread;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — Admin\DashboardController
|--------------------------------------------------------------------------
| The admin dashboard gives a bird's-eye view of the community health:
|   - Totals: users, threads, posts, open (unresolved) reports
|   - Most active users: sort by how many forum posts they have written
|   - Popular threads: sort by how many replies they have received
|
| withCount() — adds a {relation}_count attribute without an extra loop.
|   User::withCount('forumPosts') → adds `forum_posts_count` to each User.
|   Thread::withCount('posts')    → adds `posts_count` to each Thread.
|
| These use the relationship method names defined on the model, not table
| names. User::forumPosts() → HasMany, Thread::posts() → HasMany.
|
| Separation of concerns: keeping all query logic in the controller (not
| the view) makes it easy to add caching later:
|   Cache::remember('devtalk.admin.stats', 300, fn() => [...])
|--------------------------------------------------------------------------
*/
class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers   = User::count();
        $totalThreads = Thread::count();
        $totalPosts   = Post::count();
        $openReports  = Report::unresolved()->count();

        $mostActiveUsers = User::withCount('forumPosts')
            ->orderByDesc('forum_posts_count')
            ->limit(5)
            ->get();

        $popularThreads = Thread::withCount('posts')
            ->with('category')
            ->orderByDesc('posts_count')
            ->limit(5)
            ->get();

        return view('projects.devtalk.admin.dashboard', [
            'totalUsers'         => $totalUsers,
            'totalThreads'       => $totalThreads,
            'totalPosts'         => $totalPosts,
            'openReports'        => $openReports,
            'mostActiveUsers'    => $mostActiveUsers,
            'popularThreads'     => $popularThreads,
            'currentProject'     => 'devtalk',
            'projectName'        => 'DevTalk',
            'projectDescription' => 'Developer discussion forum',
        ]);
    }
}
