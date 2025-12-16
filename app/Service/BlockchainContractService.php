<?php

namespace App\Service;

use Web3\Contract;
use Web3\Web3;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class BlockchainContractService
{
    private Web3 $web3;
    private Contract $contract;

    public function __construct(string $contractAddress, ?string $contractAbiPath = null)
    {
        $rpc = \env('BLOCKCHAIN_RPC');
        $abiPath = $contractAbiPath ?: \env('CONTRACT_ABI_PATH');
        $address = $contractAddress;
        if (!$rpc) {
            throw new \RuntimeException('BLOCKCHAIN_RPC is not set.');
        }
        if (!$abiPath || !is_file($abiPath)) {
            throw new \RuntimeException('CONTRACT_ABI_PATH is not set or file not found: ' . $abiPath);
        }
        if (!$address) {
            throw new \RuntimeException('Contract address is not set.');
        }
        $this->web3 = new Web3($rpc);
        $abiJson = file_get_contents($abiPath);
        $abi = json_decode($abiJson, true);
        if (!is_array($abi)) {
            throw new \RuntimeException('Invalid ABI JSON at ' . $abiPath);
        }

        // Web3 provider property is protected; pass the RPC endpoint directly
        $this->contract = new Contract($rpc, $abi);
        $this->contract->at($address);
    }

    public static function b64ToHex(string $b64): string
    {
        $raw = base64_decode($b64, true);
        if ($raw === false) {
            throw new \InvalidArgumentException('Invalid base64: ' . $b64);
        }
        return '0x' . bin2hex($raw);
    }

    public static function hex32(string $hex): string
    {
        $hex = strtolower($hex);
        $hex = ltrim($hex, '0x');
        return '0x' . str_pad($hex, 64, '0', STR_PAD_LEFT);
    }

    public function storeEncryptedVote(string $cipherB64, string $nonceB64, string $tagB64, string $hashHex32, string $electionId, string $voterId): string
    {
        // Use Node.js web3 to avoid PHP Web3 library issues with string parameter encoding
        // Get contract address from the contract instance
        $reflection = new \ReflectionClass($this->contract);
        $property = $reflection->getProperty('toAddress');
        $property->setAccessible(true);
        $contractAddress = $property->getValue($this->contract);
        
        $workdir = env('CONTRACT_DEPLOY_PATH', base_path('..\\blockchain\\evote-deploy'));
        $scriptPath = $workdir . '\\storeVote.js';
        
        // Ensure hash has 0x prefix and is 66 chars (0x + 64 hex chars)
        $hash32 = self::hex32($hashHex32);
        
        // Build environment variables
        $env = [
            'RPC' => env('BLOCKCHAIN_RPC', 'http://127.0.0.1:18545'),
            'BLOCKCHAIN_RPC' => env('BLOCKCHAIN_RPC', 'http://127.0.0.1:18545'),
            'DEPLOY_FROM' => env('BLOCKCHAIN_FROM'),
            'ABI_OUTPUT_PATH' => env('CONTRACT_ABI_PATH'),
            'CONTRACT_ABI_PATH' => env('CONTRACT_ABI_PATH'),
            'PATH' => getenv('PATH'),
            'SystemRoot' => getenv('SystemRoot') ?: 'C:\\Windows',
        ];
        
        $process = new \Symfony\Component\Process\Process(
            ['node', $scriptPath, $contractAddress, $cipherB64, $nonceB64, $tagB64, $hash32, $electionId, $voterId],
            null,
            $env,
            null,
            60
        );
        
        $process->run();
        
        if (!$process->isSuccessful()) {
            throw new \RuntimeException('storeVote.js failed: ' . $process->getErrorOutput());
        }
        
        $output = trim($process->getOutput());
        
        // Transaction hash format: 0x[64 hex chars]
        if (!preg_match('/^0x[0-9a-fA-F]{64}$/', $output)) {
            throw new \RuntimeException('storeVote.js failed: ' . $output);
        }
        
        return $output;
    }

    public function getVoteCount(string $electionId): int
    {
        // Use Node.js web3 to avoid PHP Web3 library issues
        
        // Get contract address from the contract instance
        $reflection = new \ReflectionClass($this->contract);
        $property = $reflection->getProperty('toAddress');
        $property->setAccessible(true);
        $contractAddress = $property->getValue($this->contract);
        
        if (!$contractAddress) {
            throw new \RuntimeException('Contract address not set');
        }
        
        // Call Node.js script to get vote count
        $deployPath = env('CONTRACT_DEPLOY_PATH', base_path('..\\blockchain\\evote-deploy'));
        $scriptPath = $deployPath . '\\getVoteCount.js';

        
        if (!file_exists($scriptPath)) {
            throw new \RuntimeException('getVoteCount.js script not found at: ' . $scriptPath);
        }
        
        // Build environment variables for Node.js
        $env = [
            'RPC' => env('BLOCKCHAIN_RPC', 'http://127.0.0.1:18545'),
            'BLOCKCHAIN_RPC' => env('BLOCKCHAIN_RPC', 'http://127.0.0.1:18545'),
            'ABI_OUTPUT_PATH' => env('CONTRACT_ABI_PATH'),
            'CONTRACT_ABI_PATH' => env('CONTRACT_ABI_PATH'),
            'PATH' => getenv('PATH'),
            'SystemRoot' => getenv('SystemRoot') ?: 'C:\\Windows',
        ];
        
        $process = new \Symfony\Component\Process\Process(
            ['node', $scriptPath, $contractAddress, $electionId],
            null,
            $env,
            null,
            30
        );
        
        $process->run();
        
        if (!$process->isSuccessful()) {
            throw new \RuntimeException('Node.js call failed: ' . $process->getErrorOutput());
        }
        
        $output = trim($process->getOutput());
        
        if (!is_numeric($output)) {
            throw new \RuntimeException('Invalid vote count returned: ' . $output);
        }
        
        return (int)$output;
    }

    public function getVote(string $electionId, int $index): array
    {
        $out = [];
        $this->contract->call('getVote', $electionId, $index, function ($err, $res) use (&$out) {
            if ($err !== null) {
                throw new \RuntimeException('Contract call failed: ' . $err->getMessage());
            }
            $cipher = isset($res[0]) ? (string)$res[0] : '';
            $nonce = isset($res[1]) ? (string)$res[1] : '';
            $tag = isset($res[2]) ? (string)$res[2] : '';
            $hash = isset($res[3]) ? (string)$res[3] : '';
            $voterId = isset($res[4]) ? (string)$res[4] : '';
            $timestamp = isset($res[5]) ? (method_exists($res[5], 'toString') ? (int)$res[5]->toString() : (int)$res[5]) : 0;

            $out = [
                'ciphertext' => $this->toBase64FromHex($cipher),
                'nonce' => $this->toBase64FromHex($nonce),
                'tag' => $this->toBase64FromHex($tag),
                'hash' => $hash,
                'voterId' => $voterId,
                'timestamp' => $timestamp,
            ];
        });
        return $out;
    }

    private function toBase64FromHex(string $hex): string
    {
        $hex = strtolower($hex);
        $hex = ltrim($hex, '0x');
        if ($hex === '') return base64_encode('');
        return base64_encode(hex2bin($hex));
    }
}
