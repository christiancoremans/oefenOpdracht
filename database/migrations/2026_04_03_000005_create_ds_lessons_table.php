<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — ds_lessons table
|--------------------------------------------------------------------------
| Prefix 'ds_' avoids collisions with any future generic 'lessons' table.
|
| Two FK columns pointing to the same 'users' table:
|   instructor_id → the user who teaches this lesson (ds_role='instructor')
|   student_id   → the user who takes this lesson (ds_role='student')
| Laravel: foreignId('instructor_id')->constrained('users') explicitly
|   names the referenced table because the column name doesn't match
|   the default convention (Laravel would otherwise look for an
|   'instructors' table).
|
| status enum values:
|   planned    → future lesson, not yet started
|   completed  → lesson has taken place
|   cancelled  → lesson was cancelled (by student or instructor)
|   sick       → student reported sick; lesson did not take place
|
| scheduled_at is dateTime (date + time) because driving lessons have
|   a specific start time, not just a date.
|
| notes → optional remarks by the instructor (nullable).
|--------------------------------------------------------------------------
*/
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ds_lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->dateTime('scheduled_at');
            $table->enum('status', ['planned', 'completed', 'cancelled', 'sick'])->default('planned');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ds_lessons');
    }
};
