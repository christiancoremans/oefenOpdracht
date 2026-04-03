<?php

namespace App\Models\DevTalk;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — Custom table name + namespace
|--------------------------------------------------------------------------
| By default Laravel guesses the table name from the class name:
|   Category → categories
| But we need 'devtalk_categories' to avoid clashing with TechBazaar.
| Fix: override $table explicitly.
|
| Namespace App\Models\DevTalk\
|   → Groups all forum models together. In the User model we reference
|     them as DevTalk\Thread, DevTalk\Post, etc.
|   → In exam projects you usually have ONE app so no namespace needed.
|     This pattern is only here because two projects share a DB.
|--------------------------------------------------------------------------
*/
class Category extends Model
{
    protected $table = 'devtalk_categories';
    protected $fillable = ['name', 'slug', 'description'];

    public function threads(): HasMany
    {
        return $this->hasMany(Thread::class, 'category_id');
    }
}
