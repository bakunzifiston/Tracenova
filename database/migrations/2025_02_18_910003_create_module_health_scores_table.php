<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('module_health_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('app_id')->constrained('apps')->cascadeOnDelete();
            $table->string('module_id', 64)->index();
            $table->string('module_name', 128)->nullable();
            $table->decimal('score', 5, 2)->index(); // 0–100
            $table->string('period_type', 32)->nullable()->index(); // daily, weekly
            $table->date('period_start')->nullable()->index();
            $table->date('period_end')->nullable()->index();
            $table->unsignedInteger('errors_count')->nullable();
            $table->decimal('errors_score', 5, 2)->nullable(); // 0–100 component
            $table->decimal('speed_score', 5, 2)->nullable();  // 0–100 component
            $table->decimal('drop_off_score', 5, 2)->nullable(); // 0–100 component
            $table->json('metadata')->nullable();
            $table->timestamp('recorded_at')->nullable()->index();
            $table->timestamps();

            $table->index(['app_id', 'module_id', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('module_health_scores');
    }
};
