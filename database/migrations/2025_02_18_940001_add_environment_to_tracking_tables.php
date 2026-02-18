<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected array $tables = [
        'tracking_events',
        'tracking_sessions',
        'navigation_events',
        'screen_views',
        'user_actions',
        'error_events',
        'performance_metrics',
        'funnel_step_events',
        'journey_events',
        'business_events',
        'financial_impacts',
        'network_monitoring_events',
        'third_party_api_events',
        'feature_usage_events',
        'module_health_scores',
        'security_events',
        'data_integrity_events',
        'alerts',
    ];

    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }
            $after = $tableName === 'funnel_step_events' ? 'funnel_id' : 'app_id';
            Schema::table($tableName, function (Blueprint $table) use ($after) {
                $table->string('environment', 32)->nullable()->default('production')->index()->after($after);
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn('environment');
            });
        }
    }
};
