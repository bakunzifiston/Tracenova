<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journey_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('app_id')->constrained('apps')->cascadeOnDelete();
            $table->string('session_id', 64)->index();
            $table->string('user_id', 64)->nullable()->index();
            $table->string('step_name', 255)->index();
            $table->string('step_type', 40)->default('custom')->index(); // screen, action, custom
            $table->json('payload')->nullable();
            $table->timestamp('occurred_at')->nullable()->index();
            $table->timestamps();

            $table->index(['app_id', 'session_id', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journey_events');
    }
};
