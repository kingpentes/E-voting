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
        // For SQLite, just update existing 'completed' values to 'closed'
        // SQLite doesn't strictly enforce enum constraints like MySQL
        DB::table('elections')
            ->where('status', 'completed')
            ->update(['status' => 'closed']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert 'closed' back to 'completed'
        DB::table('elections')
            ->where('status', 'closed')
            ->update(['status' => 'completed']);
    }
};
