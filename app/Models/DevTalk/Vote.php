<?php

namespace App\Models\DevTalk;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — Vote model
|--------------------------------------------------------------------------
| value = 1 (upvote) or -1 (downvote)
|
| Toggle logic (handled in VoteController):
|   1. User clicks upvote on a post they haven't voted on → INSERT value=1
|   2. User clicks upvote again (same) → DELETE the vote (toggle off)
|   3. User clicks downvote on an upvoted post → UPDATE value=-1
|
| This logic lives in the CONTROLLER, not the model. Models hold data
| + relationships. Business rules go in controllers or service classes.
|--------------------------------------------------------------------------
*/
class Vote extends Model
{
    protected $table = 'devtalk_votes';

    protected $fillable = ['user_id', 'post_id', 'value'];

    protected $casts = ['value' => 'integer'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function post(): BelongsTo { return $this->belongsTo(Post::class, 'post_id'); }
}
