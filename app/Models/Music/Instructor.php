<?php

namespace App\Models\Music;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — Instructor model (Music project)
|--------------------------------------------------------------------------
| Instructors are NOT users — they are a standalone entity that belongs
| to the MusicHub system only. They are created by admins, not by
| self-registration.
|
| workshops() — belongsToMany (inverse of Workshop::instructors):
|   → Allows querying: $instructor->workshops — all workshops they teach.
|   → The same pivot table 'music_instructor_workshop' is used.
|   → This is the INVERSE side of the same many-to-many relationship.
|--------------------------------------------------------------------------
*/

class Instructor extends Model
{
    protected $table = 'music_instructors';

    protected $fillable = [
        'name',
        'specialization',
    ];

    public function workshops(): BelongsToMany
    {
        return $this->belongsToMany(Workshop::class, 'music_instructor_workshop');
    }
}
