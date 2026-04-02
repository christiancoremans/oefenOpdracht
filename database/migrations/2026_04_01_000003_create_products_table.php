<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — Foreign keys
|--------------------------------------------------------------------------
| foreignId('user_id')->constrained()
|   → Shorthand for: $table->unsignedBigInteger('user_id') +
|                    FOREIGN KEY referencing users.id
|   → constrained() auto-detects the table from the column name (user_id → users)
|
| ->cascadeOnDelete()
|   → When the parent (user) is deleted, this row is also deleted automatically.
|   → Alternative: ->nullOnDelete() sets the FK to NULL instead of deleting.
|
| decimal('price', 10, 2)
|   → total 10 digits, 2 after decimal point  e.g. 99999999.99
|   → ALWAYS use decimal for money, NEVER float (float is imprecise)
|
| ->nullable()
|   → The column can be NULL (no value). Use for optional fields like image.
|--------------------------------------------------------------------------
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->unsignedInteger('stock')->default(0);
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
