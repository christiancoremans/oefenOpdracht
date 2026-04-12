<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — music_workshops table
|--------------------------------------------------------------------------
| start_time / end_time → stored as DATETIME so we can compare times for
|   conflict detection ("is room X already booked at this time?").
|   In the model these are cast to Carbon instances automatically.
|
| picture → stores the FILE PATH (like "workshops/filename.jpg"),
|   NOT the raw image data. We use Storage::url($path) in the view.
|
| room → string (e.g. "Room A", "Studio 3"). Used for conflict checks.
|
| capacity → how many people can attend. Used to prevent overbooking
|   (reservations count must stay below capacity).
|--------------------------------------------------------------------------
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('music_workshops', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->string('picture')->nullable();
            $table->string('room');
            $table->unsignedInteger('capacity');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('music_workshops');
    }
};
