<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('app_id')->constrained('apps')->cascadeOnDelete();
            $table->string('session_id', 64)->nullable()->index();
            $table->string('user_id', 64)->nullable()->index();
            $table->string('event_type', 80)->index();
            $table->string('reference_id', 255)->nullable()->index(); // e.g. order_id, payment_id
            $table->json('payload');
            $table->timestamp('occurred_at')->nullable()->index();
            $table->string('url', 2048)->nullable();
            $table->string('user_agent', 512)->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamps();

            $table->index(['app_id', 'event_type', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_events');
    }
};
