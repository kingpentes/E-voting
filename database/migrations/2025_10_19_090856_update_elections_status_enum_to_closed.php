<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update any existing 'completed' status to 'closed'
        DB::table('elections')
            ->where('status', 'completed')
            ->update(['status' => 'draft']); // Temporarily set to draft to avoid constraint violation

        // Alter the enum to change 'completed' to 'closed' (MySQL only)
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE elections MODIFY COLUMN status ENUM('draft', 'active', 'closed') DEFAULT 'draft'");
        } else {
            // SQLite/PostgreSQL: skip enum alteration; the column should already be TEXT/VARCHAR.
            // Optionally, ensure values are within the expected set elsewhere via validation.
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert 'closed' back to 'completed'
        DB::table('elections')
            ->where('status', 'closed')
            ->update(['status' => 'draft']); // Temporarily set to draft

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE elections MODIFY COLUMN status ENUM('draft', 'active', 'completed') DEFAULT 'draft'");
        } else {
            // Non-MySQL: skip
        }
    }
};
