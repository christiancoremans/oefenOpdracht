<?php

namespace App\Http\Controllers\DevTalk;

use App\Http\Controllers\Controller;
use App\Models\DevTalk\Post;
use App\Models\DevTalk\Report;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — ReportController (submit a flag/report on a post)
|--------------------------------------------------------------------------
| Users can report a post for rule violations. When they do:
|   1. A Report row is inserted with reporter_id, post_id, reason.
|   2. The post's is_flagged column is set to true.
|
| post->update(['is_flagged' => true]) is intentional: moderators can see
| flagged posts even without checking the reports table. It acts as a
| fast "this post has been reported before" marker.
|
| Preventing duplicate reports — the unique constraint approach is one
| option, but here we just allow multiple reports (different reasons may
| be relevant). Moderators see all reports on a post.
|
| The route is POST only (no form page needed — it's a modal/inline form
| on the thread-show page).
|--------------------------------------------------------------------------
*/
class ReportController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'post_id' => 'required|exists:devtalk_posts,id',
            'reason'  => 'required|string|max:1000',
        ]);

        Report::create([
            'reporter_id' => auth()->id(),
            'post_id'     => $data['post_id'],
            'reason'      => $data['reason'],
        ]);

        $post = Post::findOrFail($data['post_id']);
        $post->update(['is_flagged' => true]);

        return redirect()
            ->route('devtalk.threads.show', $post->thread_id)
            ->with('success', 'Report submitted. A moderator will review it.');
    }
}
