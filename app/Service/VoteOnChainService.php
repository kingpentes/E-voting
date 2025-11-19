<?php

namespace App\Service;

use App\Models\Election;

class VoteOnChainService
{
    public function __construct(
        private readonly VoteCryptoService $crypto,
        private readonly BlockchainContractService $contract
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
}
