<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — Altering an existing table
|--------------------------------------------------------------------------
| Use Schema::table() (NOT Schema::create()) to ADD a column to a table
| that already exists.
|
| enum('column', ['val1','val2']) → Only those exact values are allowed.
| ->default('buyer')              → Every new user is a buyer by default.
|
| down() is the ROLLBACK. Laravel runs it on: php artisan migrate:rollback
| Always write a proper down() so you can undo a migration cleanly.
|--------------------------------------------------------------------------
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'seller', 'buyer'])->default('buyer')->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
