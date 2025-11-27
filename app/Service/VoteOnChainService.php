<?php

namespace App\Service;

use App\Models\Election;

/**
 * VOTE ON-CHAIN SERVICE
 * =====================
 * Service untuk mengirim vote terenkripsi ke blockchain
 * 
 * Fungsi utama:
 * - Mengenkripsi vote menggunakan VoteCryptoService
 * - Membuat hash untuk verifikasi integritas
 * - Mengirim vote ke blockchain melalui smart contract
 * 
 * Flow:
 * 1. Terima plaintext vote (candidate ID)
 * 2. Enkripsi menggunakan AES-256-GCM
 * 3. Generate hash SHA3-256
 * 4. Kirim ke blockchain via BlockchainContractService
 */
class VoteOnChainService
{
    /**
     * CONSTRUCTOR
     * ===========
     * Dependency injection untuk VoteCryptoService
     * 
     * @param VoteCryptoService $crypto - Service untuk enkripsi/dekripsi
     */
    public function __construct(
        private readonly VoteCryptoService $crypto,
    ) {}

    /**
     * SUBMIT VOTE KE BLOCKCHAIN
     * =========================
     * Enkripsi vote dan kirim ke blockchain untuk pemilihan tertentu
     * 
     * @param Election $election - Model pemilihan yang aktif
     * @param string $voterId - ID voter yang memberikan suara
     * @param string $choicePlain - Pilihan dalam plaintext (biasanya candidate ID)
     * @return string - Transaction hash dari blockchain
     * @throws \RuntimeException - Jika smart contract belum dideploy atau terjadi error
     * 
     * Proses:
     * 1. Validasi bahwa election sudah memiliki smart contract
     * 2. Enkripsi pilihan menggunakan AES-256-GCM
     * 3. Generate hash SHA3-256 dari plaintext untuk verifikasi
     * 4. Kirim data terenkripsi ke blockchain
     * 5. Return transaction hash sebagai bukti
     */
    public function submit(Election $election, string $voterId, string $choicePlain): string
    {
        // Validasi: Smart contract harus sudah dideploy
        if (!$election->contract_address) {
            throw new \RuntimeException('Smart contract belum dideploy untuk pemilu ini.');
        }

        // Enkripsi vote menggunakan AES-256-GCM
        // Menghasilkan: ciphertext, nonce, tag (semua dalam base64)
        $enc = $this->crypto->encrypt($choicePlain);
        
        // Generate hash SHA3-256 dari plaintext
        // Hash digunakan untuk validasi tanpa perlu dekripsi
        // Format: 0x + 64 karakter hex (32 bytes)
        $hashHex = '0x' . hash('sha3-256', $choicePlain);

        // Inisialisasi BlockchainContractService
        // Menggunakan contract address dan ABI path dari election
        $contract = new BlockchainContractService(
            $election->contract_address,
            $election->contract_abi_path
        );

        // Kirim vote terenkripsi ke blockchain
        // Return: transaction hash (0x + 64 hex chars)
        return $contract->storeEncryptedVote(
            $enc['ciphertext'],           // Vote terenkripsi (base64)
            $enc['nonce'],                // Nonce untuk dekripsi (base64)
            $enc['tag'],                  // Authentication tag (base64)
            $hashHex,                     // Hash untuk verifikasi (hex)
            (string) $election->id,       // Election ID
            $voterId                      // Voter ID
        );
    }
}
