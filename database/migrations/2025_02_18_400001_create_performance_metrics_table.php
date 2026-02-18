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
        Schema::create('performance_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('app_id')->constrained('apps')->cascadeOnDelete();
            $table->string('session_id', 64)->nullable()->index();
            $table->string('user_id', 64)->nullable()->index();
            $table->string('metric_type', 40)->index(); // screen_load, api_response, session_duration, custom
            $table->string('name', 255)->nullable()->index(); // e.g. "Login API", "Home screen"
            $table->unsignedBigInteger('value'); // duration in value_unit (e.g. ms or seconds)
            $table->string('value_unit', 10)->default('ms'); // ms, s
            $table->boolean('is_slow')->default(false)->index();
            $table->unsignedInteger('threshold')->nullable(); // threshold used for is_slow (same unit)
            $table->json('metadata')->nullable(); // url, method, status_code, screen_name, etc.
            $table->timestamp('occurred_at')->nullable()->index();
            $table->string('url', 2048)->nullable();
            $table->string('user_agent', 512)->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamps();

            $table->index(['app_id', 'metric_type', 'occurred_at']);
            $table->index(['app_id', 'is_slow', 'occurred_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_metrics');
    }
};
