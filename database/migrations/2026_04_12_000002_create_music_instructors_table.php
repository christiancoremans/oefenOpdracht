<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — music_instructors table
|--------------------------------------------------------------------------
| Instructors are NOT users — they are a separate entity.
| They exist only in the MusicHub system.
|
| specialization → free text describing what they teach:
|   "Classical Piano", "DJ Techniques", "Vocal Training", etc.
|
| This table is the "left side" of the many-to-many with workshops.
|--------------------------------------------------------------------------
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('music_instructors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('specialization');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('music_instructors');
    }
};
