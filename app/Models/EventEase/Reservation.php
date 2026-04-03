<?php

namespace App\Models\EventEase;

use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — EventEase Reservation model
|--------------------------------------------------------------------------
| STATUS constants prevent magic strings scattered around your code.
|   → Use Reservation::STATUS_CONFIRMED instead of typing 'confirmed' by hand.
|   → If you ever rename a status, you change it in ONE place.
|
| scopeConfirmed() — only active (non-cancelled) reservations.
|   → Used when counting seats to prevent overbooking.
|
| Cancel = UPDATE status to 'cancelled' (not a DELETE).
|   → Keeps the row for audit history.
|   → Avoids unique-key issues if the user re-books later
|     (you just UPDATE back to 'confirmed').
|--------------------------------------------------------------------------
*/
#[Fillable(['user_id', 'event_id', 'seats', 'status'])]
class Reservation extends Model
{
    protected $table = 'ee_reservations';

    public const STATUS_CONFIRMED  = 'confirmed';
    public const STATUS_CANCELLED  = 'cancelled';

    // ── Query scopes ─────────────────────────────────────────────────────────
    public function scopeConfirmed(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    // ── Relationships ────────────────────────────────────────────────────────
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
