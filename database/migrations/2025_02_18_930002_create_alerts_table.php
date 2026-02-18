<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('app_id')->constrained('apps')->cascadeOnDelete();
            $table->string('alert_type', 48)->index(); // revenue_risk, error_spike, performance_drop
            $table->string('title', 255)->nullable();
            $table->text('message')->nullable();
            $table->string('severity', 32)->nullable()->index(); // low, medium, high, critical
            $table->json('payload')->nullable();
            $table->string('channel', 32)->nullable()->index(); // in_app, email, webhook
            $table->timestamp('occurred_at')->nullable()->index();
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamps();

            $table->index(['app_id', 'alert_type', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};
