<?php

namespace App\Models\DevTalk;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — Thread model
|--------------------------------------------------------------------------
| withCount('posts')
|   → Can be used in the controller: Thread::withCount('posts')->get()
|   → Adds a posts_count attribute without loading all posts into memory.
|   → Only loads a COUNT(*) subquery. Very efficient.
|
| scopeSearch($query, $term)
|   → Local scope. Called as Thread::search($term)->...
|   → Searches title OR body using LIKE %term%.
|   → where() with closure groups the OR inside AND conditions:
|       WHERE (title LIKE ? OR body LIKE ?) AND category_id = ?
|
| incrementViews()
|   → Uses DB::table()->increment() rather than loading the model.
|   → More efficient: one UPDATE query, no PHP model hydration.
|   → Alternatively: $this->increment('views') which also skips events.
|--------------------------------------------------------------------------
*/
class Thread extends Model
{
    protected $table = 'devtalk_threads';

    protected $fillable = [
        'user_id', 'category_id', 'title', 'body', 'is_locked', 'views',
    ];

    protected $casts = [
        'is_locked' => 'boolean',
    ];

    // ── Relationships ────────────────────────────────────────────────────────
    public function user(): BelongsTo     { return $this->belongsTo(User::class); }
    public function category(): BelongsTo { return $this->belongsTo(Category::class, 'category_id'); }
    public function posts(): HasMany      { return $this->hasMany(Post::class, 'thread_id'); }

    // ── Local scope ──────────────────────────────────────────────────────────
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (! $term) return $query;

        return $query->where(function (Builder $q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
              ->orWhere('body', 'like', "%{$term}%");
        });
    }

    // ── Helpers ──────────────────────────────────────────────────────────────
    public function incrementViews(): void
    {
        $this->increment('views');
    }
}
