<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('funnel_step_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('funnel_id')->constrained('funnels')->cascadeOnDelete();
            $table->string('session_id', 64)->index();
            $table->string('user_id', 64)->nullable()->index();
            $table->string('step_key', 80)->index();
            $table->unsignedTinyInteger('step_index')->index();
            $table->timestamp('occurred_at')->nullable()->index();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['funnel_id', 'step_index', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('funnel_step_events');
    }
};
