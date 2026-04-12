<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — music_reservations table (snapshot pattern)
|--------------------------------------------------------------------------
| WHY store full_name, email, phone, music_experience here?
|
|   This is called a DATA SNAPSHOT. At the moment a user reserves, we
|   copy their profile data into the reservation row.
|
|   Reason: if a user updates their profile AFTER reserving, the reservation
|   should still show what their data looked like AT THE TIME of booking.
|   This creates a reliable audit trail.
|
|   Same principle as OrderItem storing `price` on the TechBazaar project.
|
| unique(['user_id', 'workshop_id'])
|   → Composite unique constraint: one user can only reserve one spot
|     per workshop. Tries to insert a duplicate → DB-level rejection.
|   → The controller also checks this BEFORE inserting (defence in depth).
|
| capacity_check
|   → Not a DB constraint here — enforced in the controller by counting
|     existing reservations and comparing to workshop->capacity.
|--------------------------------------------------------------------------
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('music_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('workshop_id')
                ->constrained('music_workshops')
                ->cascadeOnDelete();

            // Snapshot of user profile at time of reservation
            $table->string('full_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->text('music_experience')->nullable();

            $table->timestamps();

            // Prevent duplicate reservations
            $table->unique(['user_id', 'workshop_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('music_reservations');
    }
};
