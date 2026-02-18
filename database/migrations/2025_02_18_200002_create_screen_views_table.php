<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('screen_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('app_id')->constrained('apps')->cascadeOnDelete();
            $table->string('session_id', 64)->nullable()->index();
            $table->string('user_id', 64)->nullable()->index();
            $table->string('screen_name', 255)->index();
            $table->string('previous_screen', 255)->nullable()->index();
            $table->timestamp('occurred_at')->nullable()->index();
            $table->unsignedInteger('load_time_ms')->nullable();
            $table->json('metadata')->nullable();
            $table->string('user_agent', 512)->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamps();

            $table->index(['app_id', 'occurred_at']);
            $table->index(['app_id', 'screen_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('screen_views');
    }
};
