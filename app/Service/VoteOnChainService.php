<?php

namespace App\Service;

class VoteOnChainService
{
    public function __construct(
        private readonly VoteCryptoService $crypto,
        private readonly BlockchainContractService $contract
    ) {}

    /**
     * Encrypts choice and submits to blockchain. Returns tx hash.
     */
    public function submit(string $electionId, string $voterId, string $choicePlain): string
    {
        $enc = $this->crypto->encrypt($choicePlain);
        $hashHex = '0x' . hash('sha3-256', $choicePlain);
        return $this->contract->storeEncryptedVote(
            $enc['ciphertext'],
            $enc['nonce'],
            $enc['tag'],
            $hashHex,
            $electionId,
            $voterId
        );
    }
}
