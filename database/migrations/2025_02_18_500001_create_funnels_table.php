<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('funnels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('app_id')->constrained('apps')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->index();
            $table->json('steps'); // ordered array of step keys, e.g. ["inventory", "requests", "payment", "success"]
            $table->timestamps();

            $table->unique(['app_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('funnels');
    }
};
