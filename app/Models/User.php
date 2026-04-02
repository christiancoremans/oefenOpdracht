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

#[Fillable(['name', 'email', 'password', 'role'])]
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

    // ── Role helpers ────────────────────────────────────────────────────────
    public function isAdmin(): bool  { return $this->role === 'admin';  }
    public function isSeller(): bool { return $this->role === 'seller'; }
    public function isBuyer(): bool  { return $this->role === 'buyer';  }

    // ── Relationships ────────────────────────────────────────────────────────
    public function products(): HasMany { return $this->hasMany(Product::class); }
    public function orders(): HasMany   { return $this->hasMany(Order::class);   }
    public function reviews(): HasMany  { return $this->hasMany(Review::class);  }
}
