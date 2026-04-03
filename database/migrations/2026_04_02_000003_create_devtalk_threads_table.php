<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — devtalk_threads table
|--------------------------------------------------------------------------
| is_locked (boolean, default false)
|   → When true, no new posts can be added (enforced in PostController).
|   → Moderators can lock toxic or resolved threads.
|
| views (unsignedBigInteger, default 0)
|   → Incremented every time someone opens the thread.
|   → Used to rank "popular threads" in the admin dashboard.
|   → increment() in the model helper avoids a full model save.
|
| body (text)
|   → Use text() not string() for long content. string() = varchar(255),
|     too short for a forum post body.
|
| Foreign key constraints:
|   → constrained() adds ON DELETE CASCADE by default in Laravel.
|   → If a user is deleted, their threads are deleted too.
|--------------------------------------------------------------------------
*/
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devtalk_threads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('devtalk_categories')->cascadeOnDelete();
            $table->string('title');
            $table->text('body');
            $table->boolean('is_locked')->default(false);
            $table->unsignedBigInteger('views')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devtalk_threads');
    }
};
