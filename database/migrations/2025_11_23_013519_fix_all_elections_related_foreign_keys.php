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
        // Disable foreign key checks depending on driver
        $driver = DB::connection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
        } else {
            DB::statement('PRAGMA foreign_keys = OFF');
        }
        
        // Backup candidates data
        $candidates = DB::table('candidates')->get()->toArray();
        
        // Backup candidate_missions data if table exists
        $candidateMissions = [];
        try {
            $candidateMissions = DB::table('candidate_missions')->get()->toArray();
        } catch (\Exception $e) {
            // Table doesn't exist yet, that's okay
        }
        
        // Drop and recreate tables
        DB::statement('DROP TABLE IF EXISTS candidate_missions');
        DB::statement('DROP TABLE IF EXISTS candidates');
        
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('election_id')->constrained('elections')->onDelete('cascade');
            $table->integer('number');
            $table->string('name');
            $table->string('photo')->nullable();
            $table->text('visi');
            $table->integer('vote_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['election_id', 'number']);
        });
        
        foreach ($candidates as $candidate) {
            DB::table('candidates')->insert((array)$candidate);
        }
        
        // Recreate candidate_missions
        Schema::create('candidate_missions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained('candidates')->onDelete('cascade');
            $table->text('mission');
            $table->integer('order')->default(0);
            $table->timestamps();
        });
        
        foreach ($candidateMissions as $mission) {
            DB::table('candidate_missions')->insert((array)$mission);
        }
        
        // Backup votes data
        $votes = [];
        try {
            $votes = DB::table('votes')->get()->toArray();
        } catch (\Exception $e) {
            // Table doesn't exist yet, that's okay
        }
        
        DB::statement('DROP TABLE IF EXISTS votes');
        
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('election_id')->constrained('elections')->onDelete('cascade');
            $table->foreignId('voter_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('candidate_id')->nullable()->constrained('candidates')->onDelete('cascade');
            $table->string('vote_hash')->unique();
            $table->string('blockchain_tx_hash', 66)->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('voted_at');
            $table->timestamps();
            
            $table->unique(['election_id', 'voter_id']);
        });
        
        foreach ($votes as $vote) {
            DB::table('votes')->insert((array)$vote);
        }
        
        // Re-enable foreign key checks
        if ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        } else {
            DB::statement('PRAGMA foreign_keys = ON');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse
    }
};
