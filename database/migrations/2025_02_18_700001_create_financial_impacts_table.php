<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_impacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('app_id')->constrained('apps')->cascadeOnDelete();
            $table->string('impact_type', 40)->index(); // failed_payment, system_error, downtime, custom
            $table->decimal('amount', 15, 2);
            $table->string('currency', 10)->default('USD');
            $table->string('reference_id', 255)->nullable()->index(); // e.g. order_id, error_id, incident_id
            $table->string('description', 500)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('occurred_at')->nullable()->index();
            $table->string('session_id', 64)->nullable()->index();
            $table->string('user_id', 64)->nullable()->index();
            $table->timestamps();

            $table->index(['app_id', 'impact_type', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_impacts');
    }
};
