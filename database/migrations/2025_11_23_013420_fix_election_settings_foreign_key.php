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
        // Backup election_settings data
        $settings = DB::table('election_settings')->get()->toArray();
        
        // Drop and recreate election_settings table with correct foreign key
        Schema::dropIfExists('election_settings');
        
        Schema::create('election_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('election_id')->constrained('elections')->onDelete('cascade');
            $table->boolean('allow_abstain')->default(false);
            $table->boolean('show_results_after_vote')->default(false);
            $table->boolean('require_confirmation')->default(false);
            $table->boolean('allow_vote_change')->default(false);
            $table->integer('max_votes_per_voter')->default(1);
            $table->timestamps();
        });
        
        // Restore data
        foreach ($settings as $setting) {
            DB::table('election_settings')->insert((array)$setting);
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
