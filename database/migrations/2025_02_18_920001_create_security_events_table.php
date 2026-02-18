<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('security_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('app_id')->constrained('apps')->cascadeOnDelete();
            $table->string('session_id', 64)->nullable()->index();
            $table->string('user_id', 64)->nullable()->index();
            $table->string('event_type', 48)->index(); // failed_login, suspicious_activity, token_abuse
            $table->string('ip_address', 45)->nullable()->index();
            $table->string('user_identifier', 255)->nullable()->index(); // e.g. email/username attempted
            $table->string('reason', 512)->nullable();
            $table->json('payload')->nullable();
            $table->timestamp('occurred_at')->nullable()->index();
            $table->timestamps();

            $table->index(['app_id', 'event_type', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('security_events');
    }
};
