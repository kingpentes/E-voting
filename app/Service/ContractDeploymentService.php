<?php

namespace App\Service;

use Symfony\Component\Process\Process;

class ContractDeploymentService
{
    public function deploy(): array
    {
        $rpc = \env('BLOCKCHAIN_RPC');
        $from = \env('BLOCKCHAIN_FROM');
        if (!$rpc || !$from) {
            throw new \RuntimeException('Missing BLOCKCHAIN_RPC or BLOCKCHAIN_FROM in environment');
        }

        $workdir = base_path('..\\quorum-network\\quorum-examples\\evote-deploy');
        if (!is_dir($workdir)) {
            throw new \RuntimeException('Deploy folder not found: ' . $workdir);
        }

        $env = [ 'RPC' => $rpc, 'DEPLOY_FROM' => $from ];
        $cmd = ['node', 'deploy.js'];
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

        $this->updateEnv('CONTRACT_ADDRESS', $address);
        return ['address' => $address, 'log' => $output];
    }

    private function updateEnv(string $key, string $value): void
    {
        $envPath = base_path('.env');
        if (!is_file($envPath) || !is_writable($envPath)) {
            return;
        }
        $contents = file_get_contents($envPath);
        $pattern = "/^" . preg_quote($key, '/') . "=.*/m";
        $line = $key . '=' . $value;
        if (preg_match($pattern, $contents)) {
            $contents = preg_replace($pattern, $line, $contents);
        } else {
            $contents = rtrim($contents) . "\n" . $line . "\n";
        }
        @file_put_contents($envPath, $contents);
    }
}
