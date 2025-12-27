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

        // If a previous backup already exists, assume this migration ran before.
        // If the new elections table also exists, skip.
        if (Schema::hasTable('elections_backup')) {
            if (Schema::hasTable('elections')) {
                return; // nothing to do
            }
            // else: continue to recreate elections from existing backup
        }

        // Backup election_user data
        $electionUsers = DB::table('election_user')->get()->toArray();

        // Drop foreign key from elections before renaming to avoid constraint name collision
        if (Schema::hasTable('elections')) {
            Schema::table('elections', function (Blueprint $table) {
                // Check if index exists before dropping to be safe, or just try catch
                // Standard laravel way:
                $table->dropForeign(['user_id']); 
            });
        }
        
        // Rename old table
        Schema::rename('elections', 'elections_backup');
        
        // Create new elections table without enum constraint
        Schema::create('elections', function (Blueprint $table) {
            $table->id();
            // Use a specific, non-conflicting name for the foreign key constraint
            $table->foreignId('user_id')->constrained('users', 'id', 'elections_user_id_foreign_v2')->onDelete('cascade');
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
            ]);
        }
        
        // Restore election_user data
        foreach ($electionUsers as $eu) {
            DB::table('election_user')->insert((array)$eu);
        }
        
        // NOTE: Do not drop the backup here to avoid FK conflicts.
        // Later migrations (fix_*_foreign_key) will recreate the referencing tables
        // to point back to the new `elections` table. Keeping the backup ensures
        // smooth migration in environments with existing foreign keys.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //  Reverse not needed, keep data as is
    }
};
