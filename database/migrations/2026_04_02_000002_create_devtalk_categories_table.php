<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — Table prefix: devtalk_categories
|--------------------------------------------------------------------------
| Why prefix with devtalk_?
|   → TechBazaar already has a 'categories' table in this same database.
|   → Two migrations creating a table with the same name would cause a
|     fatal error: "table already exists".
|   → Prefixing namespaces tables per project, just like you would in a
|     real multi-domain or multi-module application.
|
| slug
|   → A URL-safe, lowercase, hyphenated version of the name.
|   → Allows filtering by URL: /project/devtalk?category=php
|   → unique() ensures no two categories share the same slug.
|--------------------------------------------------------------------------
*/
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devtalk_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devtalk_categories');
    }
};
