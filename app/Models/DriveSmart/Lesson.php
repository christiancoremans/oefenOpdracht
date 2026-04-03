<?php

namespace App\Models\DriveSmart;

use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — DriveSmart Lesson model
|--------------------------------------------------------------------------
| $table = 'ds_lessons' because the migration created 'ds_lessons', not
| 'lessons' (the default Laravel would infer from the class name).
|
| STATUS constants prevent magic strings. Use Lesson::STATUS_PLANNED
| instead of typing 'planned' by hand — safer to refactor later.
|
| Two separate relationships to User:
|   instructor() → the user with ds_role='instructor' who teaches
|   student()    → the user with ds_role='student' who learns
|   Both are BelongsTo with explicit FK names because there are two
|   FK columns pointing to the same 'users' table.
|
| scopeUpcoming() → planned lessons in the future.
|   Used by the student view to show "upcoming lessons".
|   Note: uses STATUS_PLANNED (not all future lessons — cancelled future
|   lessons are excluded).
|
| isModifiable() → true if the lesson is planned and in the future.
|   Students can only cancel/report-sick modifiable lessons.
|--------------------------------------------------------------------------
*/
#[Fillable(['instructor_id', 'student_id', 'scheduled_at', 'status', 'notes'])]
class Lesson extends Model
{
    protected $table = 'ds_lessons';

    // ── Status constants ─────────────────────────────────────────────────────
    public const STATUS_PLANNED   = 'planned';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_SICK      = 'sick';

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
        ];
    }

    // ── Query scopes ─────────────────────────────────────────────────────────
    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PLANNED)
                     ->where('scheduled_at', '>', now())
                     ->orderBy('scheduled_at');
    }

    // ── Business logic ───────────────────────────────────────────────────────
    public function isModifiable(): bool
    {
        return $this->status === self::STATUS_PLANNED
            && $this->scheduled_at->isFuture();
    }

    // ── Relationships ────────────────────────────────────────────────────────
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
