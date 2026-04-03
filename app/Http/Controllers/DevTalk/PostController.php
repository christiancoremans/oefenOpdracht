<?php

namespace App\Http\Controllers\DevTalk;

use App\Http\Controllers\Controller;
use App\Models\DevTalk\Post;
use App\Models\DevTalk\Thread;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — PostController (replies inside threads)
|--------------------------------------------------------------------------
| store()
|   → Reads thread_id from the form body (hidden input in thread show view).
|   → Aborts with 423 if the thread is locked. 423 = "Locked" HTTP status.
|   → Moderators and admins bypass the lock — they still need to be able
|     to post closure messages on locked threads.
|
| edit() → redirects back to the thread show page with the reply form
| pointing to this post's update route. The edit view is a standalone
| page for simplicity.
|
| Ownership check:
|   Must be the author OR a mod/admin to edit or delete any post.
|--------------------------------------------------------------------------
*/
class PostController extends Controller
{
    private function projectData(): array
    {
        return [
            'currentProject'     => 'devtalk',
            'projectName'        => config('projects.devtalk.name'),
            'projectDescription' => config('projects.devtalk.description'),
        ];
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'thread_id' => 'required|exists:devtalk_threads,id',
            'body'      => 'required|string|min:3',
        ]);

        $thread = Thread::findOrFail($data['thread_id']);

        // Locked threads block new posts — but mods/admins can still reply
        if ($thread->is_locked
            && ! auth()->user()->isDtModerator()
            && ! auth()->user()->isDtAdmin()
        ) {
            return back()->with('error', 'This thread is locked and no longer accepts replies.');
        }

        Post::create(array_merge($data, ['user_id' => auth()->id()]));

        return redirect()->route('devtalk.threads.show', $thread)
            ->with('success', 'Reply posted.');
    }

    public function edit(Post $post)
    {
        abort_if(
            $post->user_id !== auth()->id()
            && ! auth()->user()->isDtModerator()
            && ! auth()->user()->isDtAdmin(),
            403
        );

        $post->load('thread');

        return view('projects.devtalk.posts.edit', array_merge($this->projectData(), [
            'post' => $post,
        ]));
    }

    public function update(Request $request, Post $post)
    {
        abort_if(
            $post->user_id !== auth()->id()
            && ! auth()->user()->isDtModerator()
            && ! auth()->user()->isDtAdmin(),
            403
        );

        $data = $request->validate(['body' => 'required|string|min:3']);
        $post->update($data);

        return redirect()->route('devtalk.threads.show', $post->thread_id)
            ->with('success', 'Reply updated.');
    }

    public function destroy(Post $post)
    {
        abort_if(
            $post->user_id !== auth()->id()
            && ! auth()->user()->isDtModerator()
            && ! auth()->user()->isDtAdmin(),
            403
        );

        $threadId = $post->thread_id;
        $post->delete();

        return redirect()->route('devtalk.threads.show', $threadId)
            ->with('success', 'Reply deleted.');
    }
}
