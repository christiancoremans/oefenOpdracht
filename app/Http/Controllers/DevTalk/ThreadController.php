<?php

namespace App\Http\Controllers\DevTalk;

use App\Http\Controllers\Controller;
use App\Models\DevTalk\Category;
use App\Models\DevTalk\Thread;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — ThreadController
|--------------------------------------------------------------------------
| index()
|   → Eager loads category + user + posts_count for every thread.
|   → withCount('posts') adds a posts_count attribute: no N+1.
|   → Applies search + category filter from GET params.
|   → Paginated with withQueryString() so filters persist on page 2.
|
| show()
|   → Eager loads all posts WITH their user and votes.
|   → $userVotes: array of post_id → vote value for the logged-in user.
|     Passed to view so the vote buttons can show the user's current vote.
|   → incrementViews() called here → the view count goes up on each load.
|
| Ownership check pattern:
|   abort_if(
|     $thread->user_id !== auth()->id()  // not the author
|     && ! auth()->user()->isDtModerator()  // not a moderator
|     && ! auth()->user()->isDtAdmin(),     // not an admin
|     403
|   )
|   → Moderators and admins can edit/delete ANY thread for moderation.
|   → Authors can only touch their own.
|--------------------------------------------------------------------------
*/
class ThreadController extends Controller
{
    private function projectData(): array
    {
        return [
            'currentProject'     => 'devtalk',
            'projectName'        => config('projects.devtalk.name'),
            'projectDescription' => config('projects.devtalk.description'),
        ];
    }

    public function index(Request $request)
    {
        $query = Thread::with(['category', 'user'])
            ->withCount('posts')
            ->latest();

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('category')) {
            $catId = Category::where('slug', $request->category)->value('id');
            if ($catId) {
                $query->where('category_id', $catId);
            }
        }

        return view('projects.devtalk.home', array_merge($this->projectData(), [
            'threads'    => $query->paginate(15)->withQueryString(),
            'categories' => Category::orderBy('name')->get(),
        ]));
    }

    public function show(Thread $thread)
    {
        $thread->load(['category', 'user']);
        $thread->incrementViews();

        // Paginate posts (replies) with their user and votes
        $posts = $thread->posts()
            ->with(['user', 'votes'])
            ->paginate(20);

        // Build a quick lookup: post_id → vote value (1 or -1) for the logged-in user
        // EXAM NOTE: pluck('value', 'user_id') from the already-loaded votes collection
        // avoids an extra query per post.
        $userVotes = [];
        if (auth()->check()) {
            \App\Models\DevTalk\Vote::where('user_id', auth()->id())
                ->whereIn('post_id', $posts->pluck('id'))
                ->pluck('value', 'post_id')
                ->each(function ($value, $postId) use (&$userVotes) {
                    $userVotes[$postId] = $value;
                });
        }

        return view('projects.devtalk.threads.show', array_merge($this->projectData(), [
            'thread'    => $thread,
            'posts'     => $posts,
            'userVotes' => $userVotes,
        ]));
    }

    public function create()
    {
        return view('projects.devtalk.threads.create', array_merge($this->projectData(), [
            'categories' => Category::orderBy('name')->get(),
        ]));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:devtalk_categories,id',
            'title'       => 'required|string|max:255',
            'body'        => 'required|string|min:3',
        ]);

        $thread = Thread::create(array_merge($data, ['user_id' => auth()->id()]));

        return redirect()->route('devtalk.threads.show', $thread)
            ->with('success', 'Thread created.');
    }

    public function edit(Thread $thread)
    {
        abort_if(
            $thread->user_id !== auth()->id()
            && ! auth()->user()->isDtModerator()
            && ! auth()->user()->isDtAdmin(),
            403
        );

        return view('projects.devtalk.threads.edit', array_merge($this->projectData(), [
            'thread'     => $thread,
            'categories' => Category::orderBy('name')->get(),
        ]));
    }

    public function update(Request $request, Thread $thread)
    {
        abort_if(
            $thread->user_id !== auth()->id()
            && ! auth()->user()->isDtModerator()
            && ! auth()->user()->isDtAdmin(),
            403
        );

        $data = $request->validate([
            'category_id' => 'required|exists:devtalk_categories,id',
            'title'       => 'required|string|max:255',
            'body'        => 'required|string|min:3',
        ]);

        // is_locked can only be toggled by moderators/admins (checkbox in edit form)
        if (auth()->user()->isDtModerator() || auth()->user()->isDtAdmin()) {
            $data['is_locked'] = $request->boolean('is_locked');
        }

        $thread->update($data);

        return redirect()->route('devtalk.threads.show', $thread)
            ->with('success', 'Thread updated.');
    }

    public function destroy(Thread $thread)
    {
        abort_if(
            $thread->user_id !== auth()->id()
            && ! auth()->user()->isDtModerator()
            && ! auth()->user()->isDtAdmin(),
            403
        );

        $thread->delete();

        return redirect()->route('devtalk.home')
            ->with('success', 'Thread deleted.');
    }
}
