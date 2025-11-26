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
        // Disable foreign key checks
        DB::statement('PRAGMA foreign_keys = OFF');
        
        // Backup election_user data
        $electionUsers = [];
        try {
            $electionUsers = DB::table('election_user')->get()->toArray();
        } catch (\Exception $e) {
            // Table might not exist or be corrupted
        }
        
        // Drop and recreate table
        DB::statement('DROP TABLE IF EXISTS election_user');
        
        Schema::create('election_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('election_id')->constrained('elections')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('access_code_used', 8);
            $table->timestamp('joined_at')->nullable();
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamps();
            
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            $table->unique(['election_id', 'user_id']);
        });
        
        // Restore data
        foreach ($electionUsers as $eu) {
            DB::table('election_user')->insert((array)$eu);
        }
        
        // Re-enable foreign key checks
        DB::statement('PRAGMA foreign_keys = ON');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse
    }
};
