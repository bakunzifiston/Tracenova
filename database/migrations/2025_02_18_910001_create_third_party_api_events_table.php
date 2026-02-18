<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('third_party_api_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('app_id')->constrained('apps')->cascadeOnDelete();
            $table->string('session_id', 64)->nullable()->index();
            $table->string('user_id', 64)->nullable()->index();
            $table->string('provider_type', 32)->index(); // payment, sms, email
            $table->string('provider_name', 128)->nullable()->index();
            $table->string('operation', 64)->nullable()->index();
            $table->boolean('success')->index();
            $table->unsignedInteger('response_time_ms')->nullable();
            $table->unsignedSmallInteger('status_code')->nullable();
            $table->string('error_message', 512)->nullable();
            $table->string('request_id', 128)->nullable()->index();
            $table->json('payload')->nullable();
            $table->timestamp('occurred_at')->nullable()->index();
            $table->timestamps();

            $table->index(['app_id', 'provider_type', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('third_party_api_events');
    }
};
