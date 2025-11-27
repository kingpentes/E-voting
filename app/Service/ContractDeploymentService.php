<?php

namespace App\Service;

use Symfony\Component\Process\Process;

/**
 * CONTRACT DEPLOYMENT SERVICE
 * ===========================
 * Service untuk mendeploy smart contract EvoteEncrypted ke blockchain Quorum
 * 
 * Fungsi utama:
 * - Menjalankan Node.js deployment script
 * - Mengcompile dan deploy smart contract
 * - Mengambil contract address hasil deployment
 * 
 * Output:
 * - Contract address (alamat smart contract yang berhasil dideploy)
 * - Deployment log untuk audit
 */
class ContractDeploymentService
{
    /**
     * DEPLOY SMART CONTRACT
     * =====================
     * Mendeploy smart contract baru ke blockchain
     * 
     * @return array{address: string, log: string}
     * @throws \RuntimeException - Jika deployment gagal atau konfigurasi tidak valid
     * 
     * Proses:
     * 1. Validasi environment variables (BLOCKCHAIN_RPC, BLOCKCHAIN_FROM)
     * 2. Jalankan Node.js deploy.js script
     * 3. Parse output untuk mendapatkan contract address
     * 4. Return address dan log
     */
    public function deploy(): array
    {
        // Ambil konfigurasi blockchain dari environment
        $rpc = \env('BLOCKCHAIN_RPC');          // URL RPC node Quorum
        $from = \env('BLOCKCHAIN_FROM');        // Alamat akun yang deploy
        
        // Validasi: RPC dan FROM address harus diset
        if (!$rpc || !$from) {
            throw new \RuntimeException('Missing BLOCKCHAIN_RPC or BLOCKCHAIN_FROM in environment');
        }

        // Path ke direktori deployment script
        // Menggunakan folder di luar Laravel project (quorum-network/evote/evote-deploy)
        $workdir = base_path('..\\quorum-network\\evote\\evote-deploy');
        
        // Validasi: Folder deploy harus exist
        if (!is_dir($workdir)) {
            throw new \RuntimeException('Deploy folder not found: ' . $workdir);
        }

        // Setup environment variables untuk Node.js process
        $env = [ 
            'RPC' => $rpc,          // Diteruskan ke deploy.js
            'DEPLOY_FROM' => $from  // Diteruskan ke deploy.js
        ];
        
        // Command untuk menjalankan deployment
        $cmd = ['node', 'deploy.js'];
        
        // Buat process dengan timeout 600 detik (10 menit)
        // Deployment bisa memakan waktu karena compile + deploy + verify
        $process = new Process(
            $cmd,       // Command
            $workdir,   // Working directory
            $env,       // Environment variables
            null,       // Input
            600         // Timeout (10 menit)
        );
        
        // Jalankan deployment process
        $process->run();

        // Gabungkan stdout dan stderr untuk analisis
        $output = $process->getOutput() . "\n" . $process->getErrorOutput();
        
        // Validasi: Process harus berhasil
        if (!$process->isSuccessful()) {
            throw new \RuntimeException('Deploy failed: ' . $output);
        }

        /**
         * PARSE CONTRACT ADDRESS
         * Parse output untuk mencari contract address
         * Format yang dicari: "contractAddress: 0x..."
         */
        $address = null;
        
        // Split output per line
        foreach (preg_split('/\r?\n/', $output) as $line) {
            // Cari line yang mengandung "contractAddress:"
            if (stripos($line, 'contractAddress:') !== false) {
                // Split line berdasarkan "contractAddress:"
                $parts = preg_split('/contractAddress:\s*/i', $line);
                $addr = trim(end($parts));
                
                // Validasi format address (0x + 40 hex chars)
                if (preg_match('/^0x[a-fA-F0-9]{40}$/', $addr)) {
                    $address = $addr;
                    break;
                }
            }
        }
        
        // Validasi: Address harus ditemukan
        if (!$address) {
            throw new \RuntimeException('Could not find contractAddress in deploy output: ' . $output);
        }

        // Return contract address dan deployment log
        // Caller akan menyimpan address ke database (per-election)
        return [
            'address' => $address,  // Contract address untuk disimpan
            'log' => $output        // Full deployment log untuk audit
        ];
    }
}
