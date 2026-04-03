<?php

namespace App\Http\Controllers\DevTalk;

use App\Http\Controllers\Controller;
use App\Models\DevTalk\Post;
use App\Models\DevTalk\Vote;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — VoteController (toggle upvote / downvote)
|--------------------------------------------------------------------------
| Toggle logic — three cases:
|   1. No existing vote     → CREATE new vote (value from request)
|   2. Same value as before → DELETE the vote (user un-voted)
|   3. Different value      → UPDATE the vote (switched up ↔ down)
|
| This is the Reddit/Stack Overflow UX pattern. Users don't need a
| separate "remove vote" button — clicking the same arrow again un-votes.
|
| firstOrNew() → finds existing or returns a new (unsaved) model instance.
|   Useful here because we check $vote->exists to decide create vs update.
|   Alternative: Vote::where('user_id', ...)->where('post_id', ...)->first()
|                then branch on null.
|
| Security: the post_id comes from a validated form field, not a URL param.
| Still validates it exists so no phantom votes on deleted posts.
|--------------------------------------------------------------------------
*/
class VoteController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'post_id' => 'required|exists:devtalk_posts,id',
            'value'   => 'required|in:1,-1',
        ]);

        $value    = (int) $data['value'];
        $userId   = auth()->id();
        $postId   = (int) $data['post_id'];

        $existing = Vote::where('user_id', $userId)
                        ->where('post_id', $postId)
                        ->first();

        if ($existing) {
            if ($existing->value === $value) {
                // Same vote → remove it (toggle off)
                $existing->delete();
            } else {
                // Different vote → switch direction
                $existing->update(['value' => $value]);
            }
        } else {
            // No previous vote → create
            Vote::create([
                'user_id' => $userId,
                'post_id' => $postId,
                'value'   => $value,
            ]);
        }

        $post   = Post::findOrFail($postId);
        $thread = $post->thread_id;

        return redirect()->route('devtalk.threads.show', $thread);
    }
}
