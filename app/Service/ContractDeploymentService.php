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

        // Use blockchain folder in Laravel repo, or fallback to CONTRACT_DEPLOY_PATH
        $workdir = env('CONTRACT_DEPLOY_PATH') ?: base_path('blockchain');
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
        ];
        
        // Cross-platform command: detect OS
        if (PHP_OS_FAMILY === 'Windows') {
            $env['SystemRoot'] = getenv('SystemRoot') ?: 'C:\\Windows';
            $cmd = ['cmd', '/c', 'node', 'deploy.js'];
        } else {
            // Linux/Railway
            $cmd = ['node', 'deploy.js'];
        }
        
        $process = new Process($cmd, $workdir, $env, null, 600);
        
        // Log debug info
        $debugInfo = sprintf(
            "[DEBUG] Workdir: %s | Command: %s | Node check: %s",
            $workdir,
            implode(' ', $cmd),
            shell_exec('which node 2>&1') ?: 'node not found in PATH'
        );
        
        $process->run();

        $stdout = $process->getOutput();
        $stderr = $process->getErrorOutput();
        $exitCode = $process->getExitCode();
        
        $output = $stdout . "\n" . $stderr;
        
        if (!$process->isSuccessful()) {
            throw new \RuntimeException(sprintf(
                "Deploy failed (exit code: %d)\n[Debug]: %s\n[STDOUT]: %s\n[STDERR]: %s",
                $exitCode,
                $debugInfo,
                $stdout ?: '(empty)',
                $stderr ?: '(empty)'
            ));
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
