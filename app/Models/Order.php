<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — Order model
|--------------------------------------------------------------------------
| Status constants (STATUS_*)
|   → Define allowed statuses as class constants.
|   → Usage:  Order::create(['status' => Order::STATUS_PENDING])
|   → Advantage: if you rename a status string, you only change ONE place.
|
| items() relationship
|   → hasMany(OrderItem::class) — an order has many line items.
|   → Usage: $order->items → Collection of OrderItem models
|   → Eager loading: Order::with('items.product')->find($id)
|      This avoids N+1 queries when looping over items.
|
| N+1 query problem (exam favourite!):
|   BAD:   foreach ($order->items as $item) { $item->product->name; }
|          → hits the DB once per item
|   GOOD:  $order->load('items.product') first
|          → one query for items, one for products — done
|--------------------------------------------------------------------------
*/

class Order extends Model
{
    public const STATUS_PENDING   = 'pending';
    public const STATUS_SHIPPED   = 'shipped';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = ['user_id', 'status', 'total'];

    public function user(): BelongsTo { return $this->belongsTo(User::class);  }
    public function items(): HasMany  { return $this->hasMany(OrderItem::class); }
}
