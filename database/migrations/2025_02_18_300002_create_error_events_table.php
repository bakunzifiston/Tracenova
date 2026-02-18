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
        Schema::create('error_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('app_id')->constrained('apps')->cascadeOnDelete();
            $table->string('session_id', 64)->nullable()->index();
            $table->string('user_id', 64)->nullable()->index();
            $table->text('message')->index();
            $table->longText('stack_trace')->nullable();
            $table->string('file', 1024)->nullable()->index();
            $table->unsignedInteger('line')->nullable();
            $table->string('severity', 20)->default('error')->index(); // debug, info, warning, error, critical
            $table->json('user_info')->nullable();
            $table->json('device_info')->nullable();
            $table->json('context')->nullable();
            $table->timestamp('occurred_at')->nullable()->index();
            $table->string('url', 2048)->nullable();
            $table->string('user_agent', 512)->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamps();

            $table->index(['app_id', 'severity', 'occurred_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('error_events');
    }
};
