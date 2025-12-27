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
        DB::statement("ALTER TABLE elections MODIFY COLUMN status ENUM('draft', 'active', 'closed', 'pending_payment') DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum list. 
        // WARNING: This might fail if there are records with 'pending_payment'. 
        // Ideally we should handle data migration, but for dev rollback it's okay.
        DB::statement("ALTER TABLE elections MODIFY COLUMN status ENUM('draft', 'active', 'closed') DEFAULT 'draft'");
    }
};
