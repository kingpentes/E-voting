<?php

namespace App\Service;

use Symfony\Component\Process\Process;

class ContractDeploymentService
{
    public function deploy(): array
    {
        $rpc = env('BLOCKCHAIN_RPC');
        $from = env('BLOCKCHAIN_FROM');
        if (!$rpc || !$from) {
            throw new \RuntimeException('Missing BLOCKCHAIN_RPC or BLOCKCHAIN_FROM in environment');
        }

        // Use the evote-deploy folder from environment variable
        $workdir = env('CONTRACT_DEPLOY_PATH');
        if (!$workdir || !is_dir($workdir)) {
            throw new \RuntimeException('Deploy folder not found: ' . ($workdir ?: 'CONTRACT_DEPLOY_PATH not set'));
        }

        $abiPath = env('CONTRACT_ABI_PATH') ?: storage_path('contract/evote_abi.json');
        
        // Build environment array to pass to Process
        $env = [
            'RPC' => $rpc,
            'DEPLOY_FROM' => $from,
            'ABI_OUTPUT_PATH' => $abiPath,
            'PATH' => getenv('PATH'),
            'SystemRoot' => getenv('SystemRoot') ?: 'C:\\Windows',
        ];
        
        $cmd = ['cmd', '/c', 'node', 'deploy.js'];
        $process = new Process($cmd, $workdir, $env, null, 600);
        $process->run();

        $output = $process->getOutput() . "\n" . $process->getErrorOutput();
        if (!$process->isSuccessful()) {
            throw new \RuntimeException('Deploy failed: ' . $output);
        }

        $address = null;
        foreach (preg_split('/\r?\n/', $output) as $line) {
            if (stripos($line, 'contractAddress:') !== false) {
                $parts = preg_split('/contractAddress:\s*/i', $line);
                $addr = trim(end($parts));
                if (preg_match('/^0x[a-fA-F0-9]{40}$/', $addr)) {
                    $address = $addr;
                    break;
                }
            }
        }
        if (!$address) {
            throw new \RuntimeException('Could not find contractAddress in deploy output: ' . $output);
        }

        // Let callers decide where to persist the address (per-election, not global)
        return ['address' => $address, 'log' => $output];
    }
}
