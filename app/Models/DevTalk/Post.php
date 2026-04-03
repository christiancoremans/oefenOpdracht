<?php

namespace App\Models\DevTalk;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — Post model (replies inside threads)
|--------------------------------------------------------------------------
| voteScore() accessor
|   → Sums all vote values (1 or -1) to produce a net score (+3, -1, etc.)
|   → Uses $this->votes collection if already loaded (no extra query),
|     otherwise calls the relationship as a query ($this->votes()->sum()).
|   → EXAM TIP: Always use ->relationLoaded() before re-querying to avoid
|     the N+1 problem when calling this in a loop.
|
| is_flagged
|   → Set to true by ReportController::store() when a user reports this post.
|   → Moderators then see it in their report queue.
|--------------------------------------------------------------------------
*/
class Post extends Model
{
    protected $table = 'devtalk_posts';

    protected $fillable = ['user_id', 'thread_id', 'body', 'is_flagged'];

    protected $casts = [
        'is_flagged' => 'boolean',
    ];

    // ── Relationships ────────────────────────────────────────────────────────
    public function user(): BelongsTo   { return $this->belongsTo(User::class); }
    public function thread(): BelongsTo { return $this->belongsTo(Thread::class, 'thread_id'); }
    public function votes(): HasMany    { return $this->hasMany(Vote::class, 'post_id'); }
    public function reports(): HasMany  { return $this->hasMany(Report::class, 'post_id'); }

    // ── Accessors ────────────────────────────────────────────────────────────
    public function voteScore(): int
    {
        if ($this->relationLoaded('votes')) {
            return (int) $this->votes->sum('value');
        }
        return (int) $this->votes()->sum('value');
    }
}
