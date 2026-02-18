<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('platforms', function (Blueprint $table) {
            $table->id();
            $table->string('name', 64)->unique(); // e.g. "JavaScript", "React Native", "PHP"
            $table->string('slug', 64)->unique(); // e.g. "javascript", "react-native"
            $table->string('category', 32)->index(); // Browser, Mobile, Server, Desktop, Serverless, Gaming
            $table->string('icon', 128)->nullable(); // icon name or URL
            $table->text('description')->nullable();
            $table->json('default_features')->nullable(); // array of enabled feature keys
            $table->json('sdk_config')->nullable(); // platform-specific SDK config template
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('platforms');
    }
};
