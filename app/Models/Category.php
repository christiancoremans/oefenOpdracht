<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — Category model
|--------------------------------------------------------------------------
| protected $fillable = [...]
|   → Laravel's "mass assignment protection".
|   → Only columns listed here can be set via create() / fill() / update().
|   → Without $fillable, calling Category::create($request->all()) would
|     throw a MassAssignmentException.
|
| hasMany(Product::class)
|   → Category has MANY products.
|   → Laravel auto-resolves the foreign key as "category_id" on products.
|   → Usage: $category->products → returns a Collection of Products
|--------------------------------------------------------------------------
*/

class Category extends Model
{
    protected $fillable = ['name', 'slug'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
