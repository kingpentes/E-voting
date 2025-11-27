<?php

namespace App\Service;

use App\Models\Election;

/**
 * ELECTION SYNC STATUS SERVICE
 * ============================
 * Service untuk memeriksa status sinkronisasi antara database dan blockchain
 * 
 * Fungsi utama:
 * - Membandingkan jumlah vote di database vs blockchain
 * - Mendeteksi ketidaksesuaian data
 * - Memberikan status untuk monitoring dan audit
 * 
 * Use case:
 * - Validasi integritas data
 * - Monitoring real-time sync
 * - Deteksi anomali atau error
 */
class ElectionSyncStatusService
{
    /**
     * GET STATUS SINKRONISASI UNTUK ELECTION
     * ======================================
     * Mendapatkan status perbandingan vote antara database dan blockchain
     * 
     * @param Election $election - Model pemilihan yang akan dicek
     * @return array Status sinkronisasi dengan struktur:
     *               - has_contract: bool - Apakah election punya smart contract
     *               - db_votes: int - Jumlah vote di database
     *               - onchain_votes: int|null - Jumlah vote di blockchain
     *               - is_synced: bool|null - Apakah jumlah vote cocok
     *               - error: string|null - Error message jika ada
     * 
     * Skenario:
     * 1. Contract belum dideploy -> has_contract: false
     * 2. Contract ada, query berhasil -> perbandingan vote
     * 3. Contract ada, query error -> error message
     */
    public function getStatusForElection(Election $election): array
    {
        // Hitung jumlah vote di database
        $dbVotes = $election->votes()->count();

        /**
         * SKENARIO 1: CONTRACT BELUM DIDEPLOY
         * Jika election belum punya contract address, tidak bisa cek blockchain
         */
        if (!$election->contract_address) {
            return [
                'has_contract' => false,
                'db_votes' => $dbVotes,
                'onchain_votes' => null,
                'is_synced' => null,
                'error' => 'Smart contract belum dideploy untuk pemilu ini.',
            ];
        }

        /**
         * SKENARIO 2 & 3: CONTRACT ADA
         * Coba query blockchain untuk membandingkan
         */
        try {
            // Inisialisasi BlockchainContractService
            $contract = new BlockchainContractService(
                $election->contract_address,
                $election->contract_abi_path
            );
            
            // Ambil jumlah vote dari blockchain
            $onchainVotes = $contract->getVoteCount((string) $election->id);

            // Return status dengan perbandingan
            return [
                'has_contract' => true,
                'db_votes' => $dbVotes,
                'onchain_votes' => $onchainVotes,
                'is_synced' => $dbVotes === $onchainVotes,  // True jika sama
                'error' => null,
            ];
            
        } catch (\Throwable $e) {
            /**
             * Handle error saat query blockchain
             * Kemungkinan penyebab:
             * - Node blockchain offline
             * - Contract address tidak valid
             * - Network timeout
             * - ABI path salah
             */
            return [
                'has_contract' => true,
                'db_votes' => $dbVotes,
                'onchain_votes' => null,
                'is_synced' => null,
                'error' => $e->getMessage(),  // Error message untuk debugging
            ];
        }
    }
}
