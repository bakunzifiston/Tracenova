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
        Schema::create('user_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('app_id')->constrained('apps')->cascadeOnDelete();
            $table->string('session_id', 64)->nullable()->index();
            $table->string('user_id', 64)->nullable()->index();
            $table->string('action_type', 80)->index(); // button_click, dashboard_access, payment, form_submission, custom
            $table->string('action_name', 255)->nullable()->index();
            $table->string('target', 255)->nullable(); // e.g. button id, form name, page
            $table->json('payload')->nullable();
            $table->timestamp('occurred_at')->nullable()->index();
            $table->string('url', 2048)->nullable();
            $table->string('user_agent', 512)->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamps();

            $table->index(['app_id', 'action_type', 'occurred_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_actions');
    }
};
