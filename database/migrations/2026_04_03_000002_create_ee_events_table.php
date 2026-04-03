<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — ee_events table
|--------------------------------------------------------------------------
| Prefix 'ee_' avoids collisions with any future generic 'events' table.
|
| price uses decimal(8,2) — NOT float/double — because:
|   → Floating-point types (float, double) cannot represent some decimal
|     fractions exactly (e.g. 0.1 + 0.2 ≠ 0.3 in binary floating point).
|   → decimal(8,2) stores the number exactly as a fixed-point value.
|   → This matters for money: €12.99 must stay €12.99, not €12.990000001.
|
| user_id = the organizer who created the event (FK → users).
| capacity = total seats available (unsigned so it can't go negative).
| date = dateTime because events have both a date and a time.
|--------------------------------------------------------------------------
*/
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ee_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('location');
            $table->dateTime('date');
            $table->unsignedInteger('capacity');
            $table->decimal('price', 8, 2)->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ee_events');
    }
};
