<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Promote the first user (lowest id) to super admin and approved if no super admin exists yet.
     * Ensures existing single-user installs get one administrator after running migrations.
     */
    public function up(): void
    {
        $hasSuperAdmin = DB::table('users')->where('is_super_admin', true)->exists();
        if ($hasSuperAdmin) {
            return;
        }

        $firstId = DB::table('users')->min('id');
        if ($firstId === null) {
            return;
        }

        DB::table('users')->where('id', $firstId)->update([
            'is_approved' => true,
            'is_super_admin' => true,
        ]);
    }

    public function down(): void
    {
        // Optionally revoke super admin; we leave as-is to avoid data loss
    }
};
