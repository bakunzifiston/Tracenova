<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('performance_metrics', function (Blueprint $table) {
            $table->string('country_code', 10)->nullable()->after('ip')->index();
            $table->string('country', 100)->nullable()->after('country_code')->index();
            $table->string('region', 100)->nullable()->after('country')->index();
            $table->string('city', 100)->nullable()->after('region')->index();
        });
    }

    public function down(): void
    {
        Schema::table('performance_metrics', function (Blueprint $table) {
            $table->dropColumn(['country_code', 'country', 'region', 'city']);
        });
    }
};
