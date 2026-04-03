<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — devtalk_reports table (moderation queue)
|--------------------------------------------------------------------------
| reporter_id (not user_id)
|   → We name the column reporter_id to be semantically clear.
|   → Because the FK doesn't follow the convention "user_id", we must
|     specify the foreign table explicitly:
|       $table->foreignId('reporter_id')->constrained('users')
|
| resolved_at (nullable timestamp)
|   → NULL = unresolved (still in the moderation queue)
|   → Has a value = resolved (moderator dismissed or acted on it)
|   → Using a timestamp not a boolean so you know WHEN it was resolved.
|   → Scope: scopeUnresolved() → where('resolved_at', null)
|
| WHY keep reports even after resolved?
|   → Audit trail. Admins can review moderation history.
|   → You can detect patterns (e.g., a user with 20 reports is probably
|     a repeat offender even if each report was individually resolved).
|--------------------------------------------------------------------------
*/
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devtalk_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reporter_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('post_id')->constrained('devtalk_posts')->cascadeOnDelete();
            $table->text('reason');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devtalk_reports');
    }
};
