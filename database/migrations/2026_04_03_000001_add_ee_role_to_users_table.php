<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — EventEase role column
|--------------------------------------------------------------------------
| Same pattern as devtalk_role: a separate column keeps EventEase roles
| completely independent from TechBazaar ('role') and DevTalk
| ('devtalk_role'). An organizer in EventEase has nothing to do with
| being a seller in TechBazaar or a moderator in DevTalk.
|
| Roles:
|   admin      → can manage all events and users
|   organizer  → can create/edit/delete their own events
|   visitor    → can browse events and make reservations (default)
|--------------------------------------------------------------------------
*/
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('ee_role', ['admin', 'organizer', 'visitor'])
                  ->default('visitor')
                  ->after('devtalk_role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('ee_role');
        });
    }
};
