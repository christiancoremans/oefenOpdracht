<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — Adding multiple columns to an existing table
|--------------------------------------------------------------------------
| music_role  → enum('admin','user'), default 'user'
|   The role column is SEPARATE from TechBazaar's 'role', DevTalk's
|   'devtalk_role' etc. Each project manages its own access system.
|
| music_phone / music_experience  → prefixed with 'music_' so they don't
|   collide with any columns other projects might add later.
|   Nullable: a user can register without filling in music-specific info.
|--------------------------------------------------------------------------
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('music_role', ['admin', 'user'])->default('user')->after('ds_role');
            $table->string('music_phone')->nullable()->after('music_role');
            $table->text('music_experience')->nullable()->after('music_phone');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['music_role', 'music_phone', 'music_experience']);
        });
    }
};
