<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — OrderItem model
|--------------------------------------------------------------------------
| This is the "junction table with extra data" model.
| It connects orders and products, and stores the quantity AND price.
|
| Why store price here instead of just product_id?
|   → Price snapshot! If the seller changes the product price later,
|     historical orders must still show the original price paid.
|
| ->belongsTo(Order::class)   and
| ->belongsTo(Product::class)
|   → An order_item belongs to both an order AND a product.
|   → Usage: $item->product->name, $item->order->status
|--------------------------------------------------------------------------
*/

class OrderItem extends Model
{
    protected $fillable = ['order_id', 'product_id', 'quantity', 'price'];

    protected $casts = ['price' => 'decimal:2'];

    public function order(): BelongsTo   { return $this->belongsTo(Order::class);   }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
}
