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

        // Alter the enum to change 'completed' to 'closed'
        DB::statement("ALTER TABLE elections MODIFY COLUMN status ENUM('draft', 'active', 'closed') DEFAULT 'draft'");
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

        DB::statement("ALTER TABLE elections MODIFY COLUMN status ENUM('draft', 'active', 'completed') DEFAULT 'draft'");
    }
};
