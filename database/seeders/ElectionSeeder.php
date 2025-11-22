<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Election;
use App\Models\ElectionRule;
use App\Models\ElectionSetting;
use App\Models\Candidate;
use App\Models\CandidateMission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ElectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Organizer 1
        $organizer1 = User::create([
            'name' => 'Ahmad Wijaya',
            'email' => 'organizer1@example.com',
            'password' => Hash::make('password'),
            'role' => 'organizer',
            'organization' => 'OSIS SMA Negeri 1',
        ]);

        // Create Election 1 for Organizer 1
        $election1 = Election::create([
            'user_id' => $organizer1->id,
            'title' => 'Pemilihan Ketua OSIS 2025',
            'description' => 'Pemilihan Ketua OSIS periode 2025-2026 SMA Negeri 1',
            'start_date' => '2025-02-01',
            'end_date' => '2025-02-07',
            'start_time' => '08:00:00',
            'end_time' => '16:00:00',
            'status' => 'active',
            'is_published' => true,
        ]);

        // Create Rules for Election 1
        $rules1 = [
            'Setiap siswa hanya boleh memilih 1 kandidat',
            'Pemilihan dilakukan secara online melalui sistem E-Voting',
            'Siswa harus login dengan akun yang telah terdaftar',
            'Hasil pemilihan bersifat rahasia dan transparan',
            'Pemenang ditentukan berdasarkan suara terbanyak',
        ];

        foreach ($rules1 as $index => $rule) {
            ElectionRule::create([
                'election_id' => $election1->id,
                'rule' => $rule,
                'order' => $index + 1,
            ]);
        }

        // Create Settings for Election 1
        ElectionSetting::create([
            'election_id' => $election1->id,
            'allow_abstain' => true,
            'show_results_after_vote' => true,
            'require_confirmation' => true,
            'allow_vote_change' => false,
            'max_votes_per_voter' => 1,
        ]);

        // Create Candidates for Election 1
        $candidates1 = [
            [
                'number' => 1,
                'name' => 'Dr. Ahmad Santoso',
                'photo' => 'https://i.pravatar.cc/256?img=12',
                'visi' => 'Mewujudkan kepemimpinan yang humanis, efektif, dan berintegritas.',
                'missions' => [
                    'Meningkatkan program pengembangan karakter siswa',
                    'Memperkuat kolaborasi antar organisasi siswa',
                    'Transparansi dalam setiap pengambilan keputusan',
                ],
            ],
            [
                'number' => 2,
                'name' => 'Prof. Dr. Siti Nurhaliza, M.Pd.',
                'photo' => 'https://i.pravatar.cc/256?img=47',
                'visi' => 'Mewujudkan institusi pendidikan berkelas dunia.',
                'missions' => [
                    'Meningkatkan kualitas pembelajaran melalui digitalisasi',
                    'Mendorong budaya riset dan publikasi ilmiah',
                    'Memperkuat jaringan alumni dan stakeholder',
                    'Mengembangkan program pengabdian masyarakat berdampak',
                ],
            ],
            [
                'number' => 3,
                'name' => 'Ir. Budi Pratama',
                'photo' => 'https://i.pravatar.cc/256?img=15',
                'visi' => 'Membangun budaya kerja yang disiplin dan berprestasi.',
                'missions' => [
                    'Optimalisasi fasilitas dan sumber daya',
                    'Program efisiensi dan tata kelola modern',
                    'Kompetisi akademik dan non-akademik rutin',
                ],
            ],
        ];

        foreach ($candidates1 as $candidateData) {
            $missions = $candidateData['missions'];
            unset($candidateData['missions']);

            $candidate = Candidate::create([
                'election_id' => $election1->id,
                ...$candidateData
            ]);

            foreach ($missions as $index => $mission) {
                CandidateMission::create([
                    'candidate_id' => $candidate->id,
                    'mission' => $mission,
                    'order' => $index + 1,
                ]);
            }
        }

        // Create Organizer 2
        $organizer2 = User::create([
            'name' => 'Budi Santoso',
            'email' => 'organizer2@example.com',
            'password' => Hash::make('password'),
            'role' => 'organizer',
            'organization' => 'BEM Universitas Indonesia',
        ]);

        // Create Election 2 for Organizer 2
        $election2 = Election::create([
            'user_id' => $organizer2->id,
            'title' => 'Pemilihan Ketua BEM UI 2025',
            'description' => 'Pemilihan Ketua BEM Universitas Indonesia periode 2025-2026',
            'start_date' => '2025-03-01',
            'end_date' => '2025-03-15',
            'start_time' => '00:00:00',
            'end_time' => '23:59:59',
            'status' => 'draft',
            'is_published' => false,
        ]);

        // Create Rules for Election 2
        $rules2 = [
            'Mahasiswa aktif Universitas Indonesia dapat memilih',
            'Setiap mahasiswa memiliki 1 suara',
            'Pemilihan dilakukan online selama 2 minggu',
            'Hasil pemilihan diumumkan setelah periode voting berakhir',
        ];

        foreach ($rules2 as $index => $rule) {
            ElectionRule::create([
                'election_id' => $election2->id,
                'rule' => $rule,
                'order' => $index + 1,
            ]);
        }

        // Create Settings for Election 2
        ElectionSetting::create([
            'election_id' => $election2->id,
            'allow_abstain' => false,
            'show_results_after_vote' => false,
            'require_confirmation' => true,
            'allow_vote_change' => false,
            'max_votes_per_voter' => 1,
        ]);

        // Create Candidates for Election 2
        $candidates2 = [
            [
                'number' => 1,
                'name' => 'Andi Firmansyah',
                'photo' => 'https://i.pravatar.cc/256?img=33',
                'visi' => 'BEM UI yang inklusif, inovatif, dan berdampak untuk seluruh mahasiswa.',
                'missions' => [
                    'Memperkuat advokasi mahasiswa di tingkat universitas',
                    'Meningkatkan program kesejahteraan mahasiswa',
                    'Membangun ekosistem kewirausahaan mahasiswa',
                ],
            ],
            [
                'number' => 2,
                'name' => 'Dewi Kartika',
                'photo' => 'https://i.pravatar.cc/256?img=44',
                'visi' => 'Membangun BEM UI yang demokratis dan responsif terhadap isu mahasiswa.',
                'missions' => [
                    'Transparansi pengelolaan organisasi',
                    'Program literasi digital dan keuangan',
                    'Kolaborasi dengan organisasi mahasiswa lainnya',
                ],
            ],
        ];

        foreach ($candidates2 as $candidateData) {
            $missions = $candidateData['missions'];
            unset($candidateData['missions']);

            $candidate = Candidate::create([
                'election_id' => $election2->id,
                ...$candidateData
            ]);

            foreach ($missions as $index => $mission) {
                CandidateMission::create([
                    'candidate_id' => $candidate->id,
                    'mission' => $mission,
                    'order' => $index + 1,
                ]);
            }
        }

        // Create some voters
        for ($i = 1; $i <= 5; $i++) {
            User::create([
                'name' => "Voter $i",
                'email' => "voter$i@example.com",
                'password' => Hash::make('password'),
                'role' => 'voter',

                'organization' => 'SMA Negeri 1',
            ]);
        }

        $this->command->info('Election data seeded successfully!');
    }
}
