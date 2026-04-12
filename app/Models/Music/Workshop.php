<?php

namespace App\Models\Music;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — Workshop model (Music project)
|--------------------------------------------------------------------------
| $table = 'music_workshops'
|   → Override required because the default would be 'workshops' (without prefix).
|
| Carbon casts on start_time / end_time:
|   → 'datetime' cast turns the DB string into a Carbon object in PHP.
|   → Allows: $workshop->start_time->format('d M Y, H:i')
|              $workshop->start_time->isFuture()
|              $workshop->start_time->diffForHumans()
|
| instructors() — belongsToMany:
|   → Explicit pivot table name 'music_instructor_workshop' required because
|     the prefix makes auto-detection fail.
|
| reservations() — hasMany:
|   → One workshop can have many reservation rows.
|   → Usage: $workshop->reservations()->count() to check how many spots taken.
|
| isFull() helper:
|   → Returns true when reservations >= capacity.
|   → Load reservations count first: $workshop->loadCount('reservations')
|     then: $workshop->reservations_count >= $workshop->capacity
|
| scopeUpcoming():
|   → Filters to workshops that haven't started yet.
|   → Usage: Workshop::upcoming()->get()
|--------------------------------------------------------------------------
*/

class Workshop extends Model
{
    protected $table = 'music_workshops';

    protected $fillable = [
        'title',
        'description',
        'start_time',
        'end_time',
        'picture',
        'room',
        'capacity',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time'   => 'datetime',
        'capacity'   => 'integer',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function instructors(): BelongsToMany
    {
        // Explicit pivot table name — required when using custom prefix
        return $this->belongsToMany(Instructor::class, 'music_instructor_workshop');
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function isFull(): bool
    {
        // Call loadCount('reservations') before using this in a loop
        return ($this->reservations_count ?? $this->reservations()->count()) >= $this->capacity;
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeUpcoming($query)
    {
        return $query->where('start_time', '>', now());
    }
}
