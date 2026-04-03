<?php

namespace App\Models\DevTalk;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — Report model
|--------------------------------------------------------------------------
| reporter_id (not user_id)
|   → Custom FK name. Must tell Laravel: belongsTo(User::class, 'reporter_id')
|   → Without the second argument, Laravel would look for 'user_id' which
|     doesn't exist on this table → error or null result.
|
| scopeUnresolved($query)
|   → whereNull('resolved_at') — finds reports not yet acted on.
|   → Used in Moderator/ReportController::index().
|   → Called as: Report::unresolved()->with([...])->paginate()
|
| resolved_at (timestamp, nullable)
|   → NULL = open. A value = closed.
|   → Moderator sets it by calling: $report->update(['resolved_at' => now()])
|--------------------------------------------------------------------------
*/
class Report extends Model
{
    protected $table = 'devtalk_reports';

    protected $fillable = ['reporter_id', 'post_id', 'reason'];

    protected $casts = ['resolved_at' => 'datetime'];

    // ── Relationships ────────────────────────────────────────────────────────
    public function reporter(): BelongsTo
    {
        // EXAM NOTE: second arg = foreign key column name when it doesn't follow convention
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    // ── Scopes ───────────────────────────────────────────────────────────────
    public function scopeUnresolved(Builder $query): Builder
    {
        return $query->whereNull('resolved_at');
    }
}
