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
        Schema::create('tracking_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('app_id')->constrained('apps')->cascadeOnDelete();
            $table->string('session_id', 64)->index();
            $table->string('user_id', 64)->nullable()->index();
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable()->index();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->unsignedInteger('foreground_seconds')->default(0);
            $table->unsignedInteger('background_seconds')->default(0);
            $table->timestamp('last_activity_at')->nullable()->index();
            $table->json('metadata')->nullable();
            $table->string('user_agent', 512)->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamps();

            $table->unique(['app_id', 'session_id']);
            $table->index(['app_id', 'ended_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracking_sessions');
    }
};
