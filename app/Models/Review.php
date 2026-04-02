<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — Review model
|--------------------------------------------------------------------------
| A review links a USER to a PRODUCT to an ORDER.
| The order_id FK enforces that the buyer actually purchased the product.
| The composite unique key (user_id, product_id) on the DB level prevents
| duplicate reviews even if the application-level check is bypassed.
|
| This is "defence in depth" — validate in PHP AND enforce in the DB.
|--------------------------------------------------------------------------
*/

class Review extends Model
{
    protected $fillable = ['user_id', 'product_id', 'order_id', 'rating', 'comment'];

    public function user(): BelongsTo    { return $this->belongsTo(User::class);    }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function order(): BelongsTo   { return $this->belongsTo(Order::class);   }
}
