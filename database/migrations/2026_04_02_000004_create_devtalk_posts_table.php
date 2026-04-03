<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — devtalk_posts table (replies to threads)
|--------------------------------------------------------------------------
| "Post" here means a reply inside a thread, not a blog post.
| The thread itself has its own body — posts are add-on replies.
|
| is_flagged (boolean, default false)
|   → Set to true when a user reports the post.
|   → Moderators see flagged posts in the reports panel.
|   → Does NOT hide the post — moderators decide action after reviewing.
|
| thread_id → cascadeOnDelete
|   → If the thread is deleted, all replies go with it. Logical.
|--------------------------------------------------------------------------
*/
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devtalk_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('thread_id')->constrained('devtalk_threads')->cascadeOnDelete();
            $table->text('body');
            $table->boolean('is_flagged')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devtalk_posts');
    }
};
