<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — Product model
|--------------------------------------------------------------------------
| BelongsTo  → "this model has a foreign key pointing to another model"
|   e.g. products.user_id → users.id  means Product->belongsTo(User)
|
| protected $casts = [...]
|   → Tells Eloquent how to convert DB values to PHP types.
|   → 'price' => 'decimal:2' ensures $product->price is always a string
|     with 2 decimal places (e.g. "29.99"), not a float.
|
| scopeSearch() — local scope
|   → A reusable WHERE clause you can chain like a regular query method.
|   → Usage: Product::search($request->search)->paginate(12)
|   → The "scope" prefix is required in the method name, but you call it
|     without "scope": Product::search('laptop')
|--------------------------------------------------------------------------
*/

class Product extends Model
{
    protected $fillable = ['user_id', 'category_id', 'name', 'description', 'price', 'stock', 'image'];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
    ];

    // ── Relationships ────────────────────────────────────────────────────────
    public function user(): BelongsTo     { return $this->belongsTo(User::class);     }
    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
    public function orderItems(): HasMany  { return $this->hasMany(OrderItem::class);  }
    public function reviews(): HasMany    { return $this->hasMany(Review::class);     }

    // ── Scopes ───────────────────────────────────────────────────────────────
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (! $term) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%");
        });
    }
}
