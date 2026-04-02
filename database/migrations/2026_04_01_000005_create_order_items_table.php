<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — Order items (the pivot between orders and products)
|--------------------------------------------------------------------------
| WHY a separate order_items table instead of just order_id on products?
|
|   One order can contain MULTIPLE products.
|   order_items is the "many-to-many with extra data" table.
|
|   ERD:
|     orders  1 ──── * order_items * ──── 1  products
|
| KEY EXAM CONCEPT — Price snapshot:
|   We store 'price' on order_items (NOT just reference products.price).
|   Reason: if a seller later changes the product's price, the old orders
|   must still show what was paid at the time of purchase.
|   This is called a "price snapshot" or "denormalization for historical data".
|--------------------------------------------------------------------------
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity');
            $table->decimal('price', 10, 2); // price snapshot at time of order
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
