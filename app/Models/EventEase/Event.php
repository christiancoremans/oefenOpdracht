<?php

namespace App\Models\EventEase;

use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — EventEase Event model
|--------------------------------------------------------------------------
| Namespace App\Models\EventEase keeps this model isolated from TechBazaar
| and DevTalk models. No risk of class name collisions.
|
| $table = 'ee_events' because the migration created 'ee_events', not
| 'events'. Laravel by default would look for 'events' (snake_case plural
| of 'Event'), so we must declare the table explicitly.
|
| scopeUpcoming() — query scope: returns only events in the future.
|   → Called as: Event::upcoming()->get()  (no 'scope' prefix when using)
|
| remainingCapacity() — business logic on the model, not in the controller.
|   → Sums all confirmed reservation seats for this event, subtracts from capacity.
|   → Controller uses this to prevent overbooking.
|
| isFull() — convenience wrapper around remainingCapacity().
|--------------------------------------------------------------------------
*/
#[Fillable(['user_id', 'title', 'location', 'date', 'capacity', 'price', 'description'])]
class Event extends Model
{
    protected $table = 'ee_events';

    protected function casts(): array
    {
        return [
            'date'  => 'datetime',
            'price' => 'decimal:2',
        ];
    }

    // ── Query scopes ─────────────────────────────────────────────────────────
    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('date', '>', now())->orderBy('date');
    }

    // ── Business logic ───────────────────────────────────────────────────────
    public function remainingCapacity(): int
    {
        $booked = $this->reservations()
                       ->where('status', 'confirmed')
                       ->sum('seats');

        return max(0, $this->capacity - $booked);
    }

    public function isFull(): bool
    {
        return $this->remainingCapacity() === 0;
    }

    // ── Relationships ────────────────────────────────────────────────────────
    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }
}
