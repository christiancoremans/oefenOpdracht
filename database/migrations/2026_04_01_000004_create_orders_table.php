<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — Status enum
|--------------------------------------------------------------------------
| Orders move through a lifecycle:
|   pending → shipped → completed
|                     → cancelled
|
| Storing status as an ENUM is cleaner than integers (1,2,3,4)
| because the DB enforces valid values and the code is readable.
|
| Exam pattern:
|   When checking/changing status in PHP use constants defined on the Model,
|   e.g. Order::STATUS_PENDING, Order::STATUS_SHIPPED — not raw strings.
|--------------------------------------------------------------------------
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['pending', 'shipped', 'completed', 'cancelled'])->default('pending');
            $table->decimal('total', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
