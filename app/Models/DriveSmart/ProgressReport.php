<?php

namespace App\Models\DriveSmart;

use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — DriveSmart ProgressReport model
|--------------------------------------------------------------------------
| A progress report is an instructor's written assessment of a student.
| One student can have MANY progress reports over time (no unique constraint
| on student_id — monthly reports are common in driving schools).
|
| SKILL_* constants prevent magic strings for the skill_level field.
|
| PRIVACY NOTE:
|   This model holds sensitive personal data (skill assessment + notes).
|   Access is protected at two levels:
|   1. Middleware: only authenticated instructors/admins can CREATE/EDIT.
|   2. Controller ownership check: only the writing instructor or an admin
|      can edit a specific report.
|   Students can VIEW their own reports (through student dashboard).
|--------------------------------------------------------------------------
*/
#[Fillable(['student_id', 'instructor_id', 'lessons_completed', 'skill_level', 'notes'])]
class ProgressReport extends Model
{
    protected $table = 'ds_progress_reports';

    // ── Skill level constants ─────────────────────────────────────────────────
    public const SKILL_BEGINNER     = 'beginner';
    public const SKILL_INTERMEDIATE = 'intermediate';
    public const SKILL_ADVANCED     = 'advanced';

    // ── Relationships ────────────────────────────────────────────────────────
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }
}
