<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — ds_progress_reports table
|--------------------------------------------------------------------------
| A progress report is written BY an instructor FOR a student.
| A student can have multiple reports over time (e.g. monthly check-ins).
| No unique constraint — multiple reports per student is intentional.
|
| lessons_completed → instructor's count of how many lessons this student
|   has successfully completed (assessed by the instructor). Unsigned
|   because it can't be negative.
|
| skill_level enum:
|   beginner      → just started, basic manoeuvres
|   intermediate  → handles traffic, needs refinement
|   advanced      → ready for or passed driving test
|
| PRIVACY NOTE (exam tip):
|   This table contains personal data about students (skill assessment,
|   written notes). In a real system you would:
|   1. Restrict access strictly to the student themselves and their instructor.
|   2. Log all accesses for audit purposes.
|   3. Allow students to request deletion (GDPR right to erasure).
|   4. Encrypt the notes column at rest.
|   In this exam project, access is restricted by middleware and ownership
|   checks in the controllers (student can only see their own reports).
|--------------------------------------------------------------------------
*/
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ds_progress_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('instructor_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedInteger('lessons_completed')->default(0);
            $table->enum('skill_level', ['beginner', 'intermediate', 'advanced'])->default('beginner');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ds_progress_reports');
    }
};
