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
        // Backup election_rules data
        $rules = DB::table('election_rules')->get()->toArray();
        
        // Drop and recreate election_rules table with correct foreign key
        Schema::dropIfExists('election_rules');
        
        Schema::create('election_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('election_id')->constrained('elections')->onDelete('cascade');
            $table->text('rule');
            $table->integer('order')->default(0);
            $table->timestamps();
        });
        
        // Restore data
        foreach ($rules as $rule) {
            DB::table('election_rules')->insert((array)$rule);
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
