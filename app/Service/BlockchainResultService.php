<?php

namespace App\Service;

use App\Models\Election;
use App\Models\Candidate;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

/**
 * Service untuk mengambil hasil pemilu dari blockchain
 * 
 * Fungsi utama:
 * - Query semua vote dari smart contract
 * - Decrypt vote menggunakan VoteCryptoService
 * - Hitung suara per kandidat
 */
class BlockchainResultService
{
    public function __construct(
        private readonly VoteCryptoService $crypto
    ) {}

    /**
     * Mengambil hasil pemilu dari blockchain
     * 
     * @param Election $election
     * @return array{candidates: array, total: int, error: ?string}
     */
    public function getElectionResults(Election $election): array
    {
        if (!$election->contract_address) {
            return [
                'candidates' => [],
                'total' => 0,
                'error' => 'Smart contract belum dideploy untuk pemilu ini.'
            ];
        }

        try {
            // 1. Ambil semua vote dari blockchain
            $votes = $this->fetchAllVotesFromBlockchain($election);
            
            if (empty($votes)) {
                return [
                    'candidates' => [],
                    'total' => 0,
                    'error' => null
                ];
            }

            // 2. Decrypt dan hitung suara per kandidat
            $candidateVotes = [];
            $decryptErrors = 0;
            
            foreach ($votes as $vote) {
                try {
                    // Decrypt vote untuk mendapatkan candidate_id
                    $candidateId = $this->crypto->decrypt(
                        $vote['ciphertext'],
                        $vote['nonce'],
                        $vote['tag']
                    );
                    
                    // Increment vote count untuk kandidat
                    if (!isset($candidateVotes[$candidateId])) {
                        $candidateVotes[$candidateId] = 0;
                    }
                    $candidateVotes[$candidateId]++;
                    
                } catch (\Throwable $e) {
                    $decryptErrors++;
                    Log::warning('Failed to decrypt vote from blockchain', [
                        'election_id' => $election->id,
                        'voter_id' => $vote['voterId'] ?? 'unknown',
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // 3. Map hasil ke data kandidat
            $candidates = $election->candidates()->get();
            $results = [];
            $total = 0;
            
            foreach ($candidates as $candidate) {
                $voteCount = $candidateVotes[(string)$candidate->id] ?? 0;
                $total += $voteCount;
                
                $results[] = [
                    'id' => $candidate->id,
                    'number' => $candidate->number,
                    'name' => $candidate->name,
                    'photo_url' => $candidate->photo_url,
                    'vote_count' => $voteCount,
                ];
            }

            // Sort by vote count descending
            usort($results, fn($a, $b) => $b['vote_count'] <=> $a['vote_count']);

            // Calculate percentage
            foreach ($results as &$result) {
                $result['percentage'] = $total > 0 
                    ? round(($result['vote_count'] / $total) * 100, 1) 
                    : 0;
            }

            return [
                'candidates' => $results,
                'total' => $total,
                'decrypt_errors' => $decryptErrors,
                'error' => null
            ];

        } catch (\Throwable $e) {
            Log::error('Failed to get election results from blockchain', [
                'election_id' => $election->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'candidates' => [],
                'total' => 0,
                'error' => 'Gagal mengambil hasil dari blockchain: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Fetch semua vote dari blockchain via Node.js script
     */
    private function fetchAllVotesFromBlockchain(Election $election): array
    {
        $deployPath = env('CONTRACT_DEPLOY_PATH', base_path('..\\quorum-network\\evote\\evote-deploy'));
        $scriptPath = $deployPath . '\\getAllVotes.js';

        if (!file_exists($scriptPath)) {
            throw new \RuntimeException('getAllVotes.js script not found at: ' . $scriptPath);
        }

        // Environment variables untuk Node.js
        $env = [
            'PATH' => getenv('PATH'),
            'SystemRoot' => getenv('SystemRoot') ?: 'C:\\Windows',
        ];

        $process = new Process(
            ['node', $scriptPath, $election->contract_address, (string)$election->id],
            null,
            $env,
            null,
            60 // timeout 60 detik
        );

        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException('getAllVotes.js failed: ' . $process->getErrorOutput());
        }

        $output = trim($process->getOutput());
        
        $votes = json_decode($output, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Invalid JSON from getAllVotes.js: ' . $output);
        }

        return $votes;
    }
}
