<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — ee_reservations table
|--------------------------------------------------------------------------
| unique(['user_id', 'event_id'])
|   → A user can only have ONE reservation row per event.
|   → If they cancel, we update status to 'cancelled' (NOT delete the row).
|   → If they rebook, we update status back to 'confirmed'.
|   → Why not DELETE on cancel? Deleting would allow re-inserting, which
|     is fine, but UPDATE keeps the history and is safer against race conditions.
|   → Why unique? Prevents double-booking by the same user (e.g. two tabs).
|
| status enum:
|   confirmed  → active booking
|   cancelled  → user cancelled; row is kept for audit purposes
|
| seats = how many tickets in this reservation (default 1).
| FK cascadeOnDelete on event_id: if the event is deleted, remove all its reservations.
|--------------------------------------------------------------------------
*/
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ee_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('event_id')->constrained('ee_events')->cascadeOnDelete();
            $table->unsignedSmallInteger('seats')->default(1);
            $table->enum('status', ['confirmed', 'cancelled'])->default('confirmed');
            $table->timestamps();

            // One row per user per event — cancel/rebook UPDATES this row
            $table->unique(['user_id', 'event_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ee_reservations');
    }
};
