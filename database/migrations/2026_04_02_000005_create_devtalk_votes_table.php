<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — devtalk_votes table (upvote / downvote)
|--------------------------------------------------------------------------
| value (tinyInteger)
|   → Stores 1 (upvote) or -1 (downvote).
|   → Using a numeric value lets you SUM all votes to get a net score.
|   → tinyInteger saves space — we only ever need -128 to 127.
|
| unique(['user_id', 'post_id']) — COMPOSITE unique constraint
|   → A user can only cast ONE vote per post.
|   → The same constraint exists on reviews in TechBazaar.
|   → Toggle logic in VoteController: same value = delete,
|     different value = update, new = create.
|   → This is the Reddit/Stack Overflow voting pattern.
|
| WHY a votes table and not a +1/-1 counter on the post?
|   → A counter can go out of sync. A votes table is the source of truth.
|   → You can show "you already voted" by querying this table.
|   → You can count upvotes and downvotes separately if needed.
|--------------------------------------------------------------------------
*/
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devtalk_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('post_id')->constrained('devtalk_posts')->cascadeOnDelete();
            $table->tinyInteger('value'); // 1 = upvote, -1 = downvote
            $table->unique(['user_id', 'post_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devtalk_votes');
    }
};
