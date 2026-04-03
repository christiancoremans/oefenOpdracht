<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — Adding a column to an existing table
|--------------------------------------------------------------------------
| We do NOT modify the original create_users_table migration. Instead we
| create a NEW "alter" migration. This keeps the original framework file
| clean and untouched, and the migrations run in date order.
|
| Why a separate devtalk_role and not reuse the existing 'role' column?
|   → The 'role' column belongs to TechBazaar (admin/seller/buyer).
|   → DevTalk has completely different roles (admin/moderator/user).
|   → Using one column for both would conflate two unrelated domains —
|     a TechBazaar seller would also be a forum user in 'role' terms.
|   → Separate column = separate concern = exam-safe isolation.
|
| nullable() → existing users get NULL; the default 'user' applies
|   only to new rows. Could also use ->default('user') and run
|   User::query()->update(['devtalk_role' => 'user']) in an artisan call.
|--------------------------------------------------------------------------
*/
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('devtalk_role', ['admin', 'moderator', 'user'])
                  ->default('user')
                  ->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('devtalk_role');
        });
    }
};
