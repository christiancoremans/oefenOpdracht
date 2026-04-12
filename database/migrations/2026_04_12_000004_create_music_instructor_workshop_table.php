<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — Pivot table for many-to-many (Workshop ↔ Instructor)
|--------------------------------------------------------------------------
| A workshop can have MULTIPLE instructors (e.g. two teachers co-lead).
| An instructor can teach MULTIPLE workshops (different days/topics).
| This is a many-to-many relationship → needs a pivot table.
|
| Naming convention:
|   Laravel expects: singular model names, alphabetical order, underscored.
|   Models: Instructor + Workshop → music_instructor_workshop
|   (We prefix with music_ to stay consistent with the project prefix.)
|
| The pivot has NO timestamps by default. Only FKs needed.
|
| In the model you declare this with:
|   $this->belongsToMany(Instructor::class, 'music_instructor_workshop')
|   The second argument (table name) is needed because the prefix makes it
|   non-standard — Laravel can't auto-detect it from the model names alone.
|--------------------------------------------------------------------------
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('music_instructor_workshop', function (Blueprint $table) {
            $table->foreignId('workshop_id')
                ->constrained('music_workshops')
                ->cascadeOnDelete();
            $table->foreignId('instructor_id')
                ->constrained('music_instructors')
                ->cascadeOnDelete();
            $table->primary(['workshop_id', 'instructor_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('music_instructor_workshop');
    }
};
