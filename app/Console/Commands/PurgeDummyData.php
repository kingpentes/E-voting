<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Election;
use App\Models\Candidate;
use App\Models\CandidateMission;
use Illuminate\Support\Facades\DB;

class PurgeDummyData extends Command
{
    protected $signature = 'app:purge-dummy {--force : Run without confirmation}';

    protected $description = 'Remove known dummy seed data (users/elections/candidates) before deployment';

    public function handle(): int
    {
        if (! $this->option('force')) {
            if (! $this->confirm('This will delete dummy data (users @example.com, test elections/candidates). Continue?')) {
                $this->info('Aborted');
                return self::SUCCESS;
            }
        }

        $deletedCandidates = 0;
        $deletedMissions = 0;
        $deletedUsers = 0;
        $deletedElections = 0;

        DB::transaction(function () use (&$deletedCandidates, &$deletedMissions, &$deletedUsers, &$deletedElections) {
            // Remove dummy users
            $deletedUsers = User::where('email', 'like', '%@example.com')->delete();

            // Remove dummy elections by common titles
            $deletedElections = Election::whereIn('title', [
                'Pemilihan Ketua OSIS 2025',
                'Pemilihan Presiden BEM 2025',
                'Pilkada Kabupaten',
                'Pemilihan Ketua RT',
            ])->forceDelete();

            // Remove dummy candidates by common names
            $deletedCandidates = Candidate::whereIn('name', [
                'reno','andi','Rizky Pratama','Dewi Sartika','Ahmad Fauzi','Siti Nurhaliza','Budi Santoso','Maya Kusuma'
            ])->withTrashed()->forceDelete();

            // Cleanup orphan missions
            $deletedMissions = CandidateMission::whereNotIn('candidate_id', function ($q) {
                $q->select('id')->from('candidates');
            })->delete();
        });

        $this->info("Users deleted: {$deletedUsers}");
        $this->info("Elections deleted: {$deletedElections}");
        $this->info("Candidates deleted: {$deletedCandidates}");
        $this->info("Orphan missions deleted: {$deletedMissions}");

        return self::SUCCESS;
    }
}
