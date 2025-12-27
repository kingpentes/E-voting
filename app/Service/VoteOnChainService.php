<?php

namespace App\Service;

use App\Models\Election;
use Illuminate\Support\Facades\Log;

class VoteOnChainService
{
    public function __construct(
        private readonly VoteCryptoService $crypto,
    ) {}

    /**
     * Encrypts choice and submits to blockchain for a specific election.
     * Returns blockchain transaction hash.
     */
    public function submit(Election $election, string $voterId, string $choicePlain): string
    {
        if (!$election->contract_address) {
            throw new \RuntimeException('Smart contract belum dideploy untuk pemilu ini.');
        }

        $enc = $this->crypto->encrypt($choicePlain);
        $hashHex = '0x' . hash('sha3-256', $choicePlain);

        $contract = new BlockchainContractService(
            $election->contract_address,
            $election->contract_abi_path
        );

        return $contract->storeEncryptedVote(
            $enc['ciphertext'],
            $enc['nonce'],
            $enc['tag'],
            $hashHex,
            (string) $election->id,
            $voterId
        );
    }

    /**
     * Get election results from blockchain
     * Returns array of candidate_id => vote_count
     */
    public function getElectionResults(Election $election): array
    {
        if (!$election->contract_address) {
            throw new \RuntimeException('Smart contract belum dideploy untuk pemilu ini.');
        }

        $contract = new BlockchainContractService(
            $election->contract_address,
            $election->contract_abi_path
        );

        $results = [];
        
        // Get vote counts for each candidate from blockchain
        foreach ($election->candidates as $candidate) {
            try {
                $voteCount = $contract->getVotesForCandidate((string) $election->id, (string) $candidate->id);
                $results[$candidate->id] = $voteCount;
            } catch (\Throwable $e) {
                Log::error("Failed to get blockchain votes for candidate {$candidate->id}: " . $e->getMessage());
                $results[$candidate->id] = 0;
            }
        }

        return $results;
    }
}
