<?php

namespace App\Service;

use App\Models\Election;

class ElectionSyncStatusService
{
    public function getStatusForElection(Election $election): array
    {
        $dbVotes = $election->votes()->count();

        if (!$election->contract_address) {
            return [
                'has_contract' => false,
                'db_votes' => $dbVotes,
                'onchain_votes' => null,
                'is_synced' => null,
                'error' => 'Smart contract belum dideploy untuk pemilu ini.',
            ];
        }

        try {
            $contract = new BlockchainContractService(
                $election->contract_address,
                $election->contract_abi_path
            );
            $onchainVotes = $contract->getVoteCount((string) $election->id);

            return [
                'has_contract' => true,
                'db_votes' => $dbVotes,
                'onchain_votes' => $onchainVotes,
                'is_synced' => $dbVotes === $onchainVotes,
                'error' => null,
            ];
        } catch (\Throwable $e) {
            return [
                'has_contract' => true,
                'db_votes' => $dbVotes,
                'onchain_votes' => null,
                'is_synced' => null,
                'error' => $e->getMessage(),
            ];
        }
    }
}
