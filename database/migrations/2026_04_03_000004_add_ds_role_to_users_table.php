<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — DriveSmart role column
|--------------------------------------------------------------------------
| Same isolation pattern as devtalk_role and ee_role.
| A driving school admin is NOT the same as a TechBazaar admin.
|
| Roles:
|   admin      → full access: manage users, view all schedules, monitor
|   instructor → create/manage lessons, write progress reports
|   student    → view own lessons, cancel lesson, report sick
|--------------------------------------------------------------------------
*/
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('ds_role', ['admin', 'instructor', 'student'])
                  ->default('student')
                  ->after('ee_role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('ds_role');
        });
    }
};
