<?php

namespace App\Models\Music;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — Reservation model (Music project)
|--------------------------------------------------------------------------
| This model stores a SNAPSHOT of user data at the time of reservation:
|   full_name, email, phone, music_experience
|
|   WHY? If a user changes their name or email after booking, the
|   reservation record must still reflect what was booked. This is the
|   same snapshot pattern as OrderItem::price in TechBazaar.
|
| Two FKs:
|   user_id    → which user made the reservation (for auth checks)
|   workshop_id → which workshop was reserved
|
| unique(['user_id', 'workshop_id']) is enforced at the DB level AND in
| the controller (defence in depth).
|--------------------------------------------------------------------------
*/

class Reservation extends Model
{
    protected $table = 'music_reservations';

    protected $fillable = [
        'user_id',
        'workshop_id',
        'full_name',
        'email',
        'phone',
        'music_experience',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function workshop(): BelongsTo
    {
        return $this->belongsTo(Workshop::class);
    }
}
