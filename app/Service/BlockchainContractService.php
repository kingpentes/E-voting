<?php

namespace App\Service;

use Web3\Contract;

/**
 * BLOCKCHAIN CONTRACT SERVICE
 * ===========================
 * Service untuk berinteraksi dengan smart contract di blockchain Quorum
 * 
 * Fungsi utama:
 * - Menghubungkan ke blockchain melalui RPC
 * - Menyimpan vote terenkripsi ke blockchain
 * - Mengambil data vote dari blockchain
 * - Konversi format data (base64 <-> hex)
 */
class BlockchainContractService
{
    private Contract $contract;

    /**
     * CONSTRUCTOR
     * ===========
     * Inisialisasi koneksi ke blockchain dan smart contract
     * 
     * @param string $contractAddress - Alamat smart contract
     * @param string|null $contractAbiPath - Path ke file ABI (opsional)
     * @throws \RuntimeException - Jika konfigurasi tidak valid
     */
    public function __construct(string $contractAddress, ?string $contractAbiPath = null)
    {
        // Ambil konfigurasi dari environment variable
        $rpc = \env('BLOCKCHAIN_RPC');
        $abiPath = $contractAbiPath ?: \env('CONTRACT_ABI_PATH');
        
        // Validasi: RPC harus diset
        if (!$rpc) {
            throw new \RuntimeException('BLOCKCHAIN_RPC is not set.');
        }
        
        // Validasi: ABI path harus ada dan file harus exist
        if (!$abiPath || !is_file($abiPath)) {
            throw new \RuntimeException('CONTRACT_ABI_PATH is not set or file not found: ' . $abiPath);
        }
        
        // Validasi: Contract address harus diset
        if (!$contractAddress) {
            throw new \RuntimeException('Contract address is not set.');
        }
        
        // Load dan parse ABI dari file JSON
        $abiJson = file_get_contents($abiPath);
        $abi = json_decode($abiJson, true);
        
        // Validasi: ABI harus berupa array valid
        if (!is_array($abi)) {
            throw new \RuntimeException('Invalid ABI JSON at ' . $abiPath);
        }

        // Inisialisasi contract instance dengan RPC dan ABI
        // Web3 provider property adalah protected, jadi pass RPC endpoint langsung
        $this->contract = new Contract($rpc, $abi);
        $this->contract->at($contractAddress);
    }

    /**
     * KONVERSI BASE64 KE HEX
     * ======================
     * Mengkonversi string base64 menjadi hexadecimal dengan prefix 0x
     * 
     * @param string $b64 - String dalam format base64
     * @return string - String hexadecimal dengan prefix 0x
     * @throws \InvalidArgumentException - Jika base64 tidak valid
     */
    public static function b64ToHex(string $b64): string
    {
        // Decode base64 ke binary
        $raw = base64_decode($b64, true);
        
        // Validasi: base64 harus valid
        if ($raw === false) {
            throw new \InvalidArgumentException('Invalid base64: ' . $b64);
        }
        
        // Konversi binary ke hex dengan prefix 0x
        return '0x' . bin2hex($raw);
    }

    /**
     * NORMALISASI HEX KE 32 BYTES
     * ===========================
     * Memastikan hex string memiliki panjang 32 bytes (64 karakter hex)
     * Digunakan untuk hash yang harus tepat 32 bytes
     * 
     * @param string $hex - String hexadecimal
     * @return string - Hex string 32 bytes dengan prefix 0x (total 66 karakter)
     */
    public static function hex32(string $hex): string
    {
        // Lowercase dan hapus prefix 0x jika ada
        $hex = strtolower($hex);
        $hex = ltrim($hex, '0x');
        
        // Pad dengan 0 di kiri hingga 64 karakter (32 bytes)
        return '0x' . str_pad($hex, 64, '0', STR_PAD_LEFT);
    }

    /**
     * SIMPAN VOTE TERENKRIPSI KE BLOCKCHAIN
     * =====================================
     * Menyimpan vote yang sudah dienkripsi ke smart contract
     * Menggunakan Node.js untuk menghindari issue dengan PHP Web3 library
     * 
     * @param string $cipherB64 - Ciphertext dalam format base64
     * @param string $nonceB64 - Nonce dalam format base64
     * @param string $tagB64 - Authentication tag dalam format base64
     * @param string $hashHex32 - Hash dalam format hexadecimal
     * @param string $electionId - ID pemilihan
     * @param string $voterId - ID voter
     * @return string - Transaction hash dari blockchain
     * @throws \RuntimeException - Jika penyimpanan gagal
     */
    public function storeEncryptedVote(string $cipherB64, string $nonceB64, string $tagB64, string $hashHex32, string $electionId, string $voterId): string
    {
        // Ambil contract address dari instance menggunakan reflection
        // (karena property toAddress adalah protected)
        $reflection = new \ReflectionClass($this->contract);
        $property = $reflection->getProperty('toAddress');
        $property->setAccessible(true);
        $contractAddress = $property->getValue($this->contract);
        
        // Path ke direktori Node.js script
        $workdir = base_path('..\\quorum-network\\evote\\evote-deploy');
        
        // Normalisasi hash menjadi 32 bytes (0x + 64 hex chars)
        $hash32 = self::hex32($hashHex32);
        
        // Bangun command untuk menjalankan storeVote.js
        $cmd = "cd " . escapeshellarg($workdir) . " && node storeVote.js "
            . escapeshellarg($contractAddress) . " "
            . escapeshellarg($cipherB64) . " "
            . escapeshellarg($nonceB64) . " "
            . escapeshellarg($tagB64) . " "
            . escapeshellarg($hash32) . " "
            . escapeshellarg($electionId) . " "
            . escapeshellarg($voterId);
        
        // Jalankan command dan tangkap output (termasuk stderr)
        $output = shell_exec($cmd . " 2>&1");
        $output = trim($output ?? '');
        
        // Validasi: Output harus berupa transaction hash yang valid
        // Format: 0x diikuti 64 karakter hexadecimal
        if (!preg_match('/^0x[0-9a-fA-F]{64}$/', $output)) {
            throw new \RuntimeException('storeVote.js failed: ' . $output);
        }
        
        // Return transaction hash
        return $output;
    }

    /**
     * AMBIL JUMLAH VOTE
     * =================
     * Mendapatkan total jumlah vote untuk suatu pemilihan dari blockchain
     * Menggunakan Node.js untuk menghindari issue dengan PHP Web3 library
     * 
     * @param string $electionId - ID pemilihan
     * @return int - Jumlah total vote
     * @throws \RuntimeException - Jika gagal mengambil data
     */
    public function getVoteCount(string $electionId): int
    {
        // Ambil contract address menggunakan reflection
        $reflection = new \ReflectionClass($this->contract);
        $property = $reflection->getProperty('toAddress');
        $property->setAccessible(true);
        $contractAddress = $property->getValue($this->contract);
        
        // Validasi: Contract address harus ada
        if (!$contractAddress) {
            throw new \RuntimeException('Contract address not set');
        }
        
        // Path ke Node.js script
        $scriptPath = base_path('..\\quorum-network\\evote\\evote-deploy\\getVoteCount.js');
        
        // Validasi: Script harus exist
        if (!file_exists($scriptPath)) {
            throw new \RuntimeException('getVoteCount.js script not found at: ' . $scriptPath);
        }
        
        // Buat process untuk menjalankan Node.js script
        // Timeout: 30 detik
        $process = new \Symfony\Component\Process\Process(
            ['node', $scriptPath, $contractAddress, $electionId],
            null,  // Working directory (null = current)
            null,  // Environment variables
            null,  // Input
            30     // Timeout dalam detik
        );
        
        // Jalankan process
        $process->run();
        
        // Validasi: Process harus berhasil
        if (!$process->isSuccessful()) {
            throw new \RuntimeException('Node.js call failed: ' . $process->getErrorOutput());
        }
        
        // Ambil output dan trim whitespace
        $output = trim($process->getOutput());
        
        // Validasi: Output harus berupa angka
        if (!is_numeric($output)) {
            throw new \RuntimeException('Invalid vote count returned: ' . $output);
        }
        
        // Return sebagai integer
        return (int)$output;
    }

    /**
     * AMBIL DATA VOTE SPESIFIK
     * ========================
     * Mengambil data vote berdasarkan election ID dan index
     * 
     * @param string $electionId - ID pemilihan
     * @param int $index - Index vote (0-based)
     * @return array - Array berisi ciphertext, nonce, tag, hash, voterId, timestamp
     * @throws \RuntimeException - Jika gagal mengambil data
     */
    public function getVote(string $electionId, int $index): array
    {
        $out = [];
        
        // Panggil fungsi getVote di smart contract
        $this->contract->call('getVote', $electionId, $index, function ($err, $res) use (&$out) {
            // Handle error
            if ($err !== null) {
                throw new \RuntimeException('Contract call failed: ' . $err->getMessage());
            }
            
            // Extract data dari response
            // res[0] = ciphertext (hex)
            // res[1] = nonce (hex)
            // res[2] = tag (hex)
            // res[3] = hash (hex)
            // res[4] = voterId (string)
            // res[5] = timestamp (uint256)
            $cipher = isset($res[0]) ? (string)$res[0] : '';
            $nonce = isset($res[1]) ? (string)$res[1] : '';
            $tag = isset($res[2]) ? (string)$res[2] : '';
            $hash = isset($res[3]) ? (string)$res[3] : '';
            $voterId = isset($res[4]) ? (string)$res[4] : '';
            
            // Konversi timestamp (BigNumber -> int)
            $timestamp = isset($res[5]) ? (method_exists($res[5], 'toString') ? (int)$res[5]->toString() : (int)$res[5]) : 0;

            // Konversi hex ke base64 untuk cipher, nonce, tag
            // Hash tetap dalam format hex
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

    /**
     * KONVERSI HEX KE BASE64
     * =====================
     * Helper function untuk mengkonversi hexadecimal ke base64
     * 
     * @param string $hex - String hexadecimal (dengan atau tanpa prefix 0x)
     * @return string - String dalam format base64
     */
    private function toBase64FromHex(string $hex): string
    {
        // Lowercase dan hapus prefix 0x
        $hex = strtolower($hex);
        $hex = ltrim($hex, '0x');
        
        // Handle empty string
        if ($hex === '') return base64_encode('');
        
        // Konversi hex ke binary, lalu encode ke base64
        return base64_encode(hex2bin($hex));
    }
}
