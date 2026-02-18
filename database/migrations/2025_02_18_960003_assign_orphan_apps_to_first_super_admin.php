<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Assign apps with no owner to the first super admin (e.g. pre-multi-user installs).
     */
    public function up(): void
    {
        $superAdminId = DB::table('users')->where('is_super_admin', true)->value('id');
        if ($superAdminId === null) {
            return;
        }

        DB::table('apps')->whereNull('user_id')->update(['user_id' => $superAdminId]);
    }

    public function down(): void
    {
        // Leave ownership as-is
    }
};
