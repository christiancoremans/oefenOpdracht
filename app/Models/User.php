<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Fortify\TwoFactorAuthenticatable;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — User model
|--------------------------------------------------------------------------
| #[Fillable([...])]  → PHP 8 attribute syntax, same as:  protected $fillable = [...]
|
| isAdmin() / isSeller() / isBuyer()
|   → Helper methods so you can write $user->isAdmin() instead of
|     $user->role === 'admin' everywhere in your code.
|   → Put business logic on the MODEL, not scattered in controllers/views.
|
| Relationships:
|   hasMany(Product::class)  → A user (seller) can own many products
|   hasMany(Order::class)    → A user (buyer) can place many orders
|   hasMany(Review::class)   → A user (buyer) can write many reviews
|--------------------------------------------------------------------------
*/

#[Fillable(['name', 'email', 'password', 'role', 'devtalk_role', 'ee_role', 'ds_role', 'music_role', 'music_phone', 'music_experience'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    // ── TechBazaar role helpers ──────────────────────────────────────────────
    public function isAdmin(): bool  { return $this->role === 'admin';  }
    public function isSeller(): bool { return $this->role === 'seller'; }
    public function isBuyer(): bool  { return $this->role === 'buyer';  }

    // ── DevTalk role helpers ─────────────────────────────────────────────────
    // EXAM NOTE: Each project has its own role column so they stay independent.
    // isDtAdmin() checks devtalk_role — not the same as isAdmin() (TechBazaar).
    public function isDtAdmin(): bool      { return $this->devtalk_role === 'admin';     }
    public function isDtModerator(): bool  { return $this->devtalk_role === 'moderator'; }
    public function isDtUser(): bool       { return $this->devtalk_role === 'user';      }
    // ── EventEase role helpers ───────────────────────────────────────────────
    // EXAM NOTE: ee_role is independent from 'role' (TechBazaar) and 'devtalk_role'.
    // An organizer here is only an organizer for EventEase — not for other projects.
    public function isEeAdmin(): bool      { return $this->ee_role === 'admin';     }
    public function isEeOrganizer(): bool  { return $this->ee_role === 'organizer'; }
    public function isEeVisitor(): bool    { return $this->ee_role === 'visitor';   }

    // ── DriveSmart role helpers ──────────────────────────────────────────────
    // EXAM NOTE: ds_role is independent from all other role columns.
    // A DriveSmart admin does NOT get EventEase or TechBazaar privileges.
    public function isDsAdmin(): bool       { return $this->ds_role === 'admin';      }
    public function isDsInstructor(): bool  { return $this->ds_role === 'instructor'; }
    public function isDsStudent(): bool     { return $this->ds_role === 'student';    }
    // ── TechBazaar relationships ─────────────────────────────────────────────
    public function products(): HasMany { return $this->hasMany(Product::class); }
    public function orders(): HasMany   { return $this->hasMany(Order::class);   }
    public function reviews(): HasMany  { return $this->hasMany(Review::class);  }

    // ── DevTalk relationships ────────────────────────────────────────────────
    // EXAM NOTE: when the FK on the related table is 'user_id' Laravel finds it
    // automatically. forumPosts uses a different method name to avoid clash with
    // a hypothetical 'posts' method on a blog project.
    public function threads(): HasMany    { return $this->hasMany(DevTalk\Thread::class); }
    public function forumPosts(): HasMany { return $this->hasMany(DevTalk\Post::class);   }
    public function votes(): HasMany      { return $this->hasMany(DevTalk\Vote::class);   }
    public function reports(): HasMany    { return $this->hasMany(DevTalk\Report::class, 'reporter_id'); }

    // ── EventEase relationships ──────────────────────────────────────────────
    public function organisedEvents(): HasMany { return $this->hasMany(EventEase\Event::class);       }
    public function reservations(): HasMany    { return $this->hasMany(EventEase\Reservation::class); }

    // ── DriveSmart relationships ──────────────────────────────────────────────
    // EXAM NOTE: Two relationships to Lesson — one as instructor, one as student.
    // Both point to the same DriveSmart\Lesson model but use different FK columns.
    // Method names are unique to avoid any collision.
    public function instructorLessons(): HasMany  { return $this->hasMany(DriveSmart\Lesson::class, 'instructor_id'); }
    public function studentLessons(): HasMany     { return $this->hasMany(DriveSmart\Lesson::class, 'student_id');    }
    public function progressReports(): HasMany   { return $this->hasMany(DriveSmart\ProgressReport::class, 'student_id');    }
    public function writtenReports(): HasMany    { return $this->hasMany(DriveSmart\ProgressReport::class, 'instructor_id'); }

    // ── MusicHub role helpers ────────────────────────────────────────────────
    // EXAM NOTE: music_role is independent from all other role columns.
    // A music admin has NO extra privileges in TechBazaar or DriveSmart.
    public function isMusicAdmin(): bool { return $this->music_role === 'admin'; }
    public function isMusicUser(): bool  { return $this->music_role === 'user';  }

    // ── MusicHub relationships ───────────────────────────────────────────────
    public function musicReservations(): HasMany { return $this->hasMany(Music\Reservation::class); }
}
