<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — Creating a simple lookup/pivot table
|--------------------------------------------------------------------------
| ->string('slug')->unique()  → slug is a URL-friendly identifier, must be unique.
|   e.g. "Laptops" → slug "laptops"
|   In an exam always add ->unique() to columns like email, slug, username.
|--------------------------------------------------------------------------
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
