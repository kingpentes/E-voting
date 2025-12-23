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
        // If elections table does not exist, nothing to migrate (fresh install)
        if (!Schema::hasTable('elections')) {
            return;
        }

        // Ensure any previous backup is removed to avoid rename errors in dev
        if (Schema::hasTable('elections_backup')) {
            Schema::drop('elections_backup');
        }

        // Backup election_user data
        $electionUsers = DB::table('election_user')->get()->toArray();
        
        // Rename old table
        Schema::rename('elections', 'elections_backup');
        
        // Create new elections table without enum constraint
        Schema::create('elections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('status')->default('draft');
            $table->boolean('is_published')->default(false);
            $table->string('access_code')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->string('contract_address')->nullable();
            $table->string('contract_abi_path')->nullable();
        });
        
        // Copy data, converting 'completed' to 'closed'
        $elections = DB::table('elections_backup')->get();
        foreach ($elections as $election) {
            DB::table('elections')->insert([
                'id' => $election->id,
                'user_id' => $election->user_id,
                'title' => $election->title,
                'description' => $election->description,
                'start_date' => $election->start_date,
                'end_date' => $election->end_date,
                'start_time' => $election->start_time,
                'end_time' => $election->end_time,
                'status' => $election->status === 'completed' ? 'closed' : $election->status,
                'is_published' => $election->is_published,
                'access_code' => $election->access_code,
                'created_at' => $election->created_at,
                'updated_at' => $election->updated_at,
                'deleted_at' => $election->deleted_at,
                'contract_address' => $election->contract_address ?? null,
                'contract_abi_path' => $election->contract_abi_path ?? null,
            ]);
        }
        
        // Restore election_user data
        foreach ($electionUsers as $eu) {
            DB::table('election_user')->insert((array)$eu);
        }
        
        // Drop backup table if exists
        if (Schema::hasTable('elections_backup')) {
            Schema::drop('elections_backup');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //  Reverse not needed, keep data as is
    }
};
