<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('network_monitoring_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('app_id')->constrained('apps')->cascadeOnDelete();
            $table->string('session_id', 64)->nullable()->index();
            $table->string('user_id', 64)->nullable()->index();
            $table->string('event_type', 40)->index(); // offline_start, offline_end, sync_retry, network_strength
            $table->timestamp('occurred_at')->nullable()->index();
            $table->unsignedInteger('duration_seconds')->nullable(); // for offline_end: how long was offline
            $table->unsignedSmallInteger('retry_count')->nullable(); // for sync_retry
            $table->boolean('success')->nullable(); // for sync_retry: whether sync succeeded
            $table->string('network_strength', 20)->nullable()->index(); // weak, moderate, strong, or numeric
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->index(['app_id', 'event_type', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('network_monitoring_events');
    }
};
