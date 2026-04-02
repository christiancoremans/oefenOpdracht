<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — Composite unique constraint
|--------------------------------------------------------------------------
| ->unique(['user_id', 'product_id'])
|   → The COMBINATION of user_id + product_id must be unique.
|   → This prevents a user from leaving 2 reviews on the same product.
|   → A single ->unique() on each column separately would be wrong:
|      that would allow only 1 review per user (total) and 1 per product (total).
|     The COMPOSITE unique means: each (user, product) pair is unique.
|
| ->foreignId('order_id')
|   → Ties the review to a specific order, so we can verify the user
|     actually purchased the product before allowing a review.
|
| tinyInteger('rating')->unsigned()
|   → Only positive numbers 0-255; we validate 1-5 in PHP.
|--------------------------------------------------------------------------
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('rating')->unsigned();
            $table->text('comment')->nullable();
            $table->unique(['user_id', 'product_id']); // one review per user per product
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
