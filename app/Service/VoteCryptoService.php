<?php

namespace App\Service;

/**
 * VOTE CRYPTO SERVICE
 * ===================
 * Service untuk enkripsi dan dekripsi data vote menggunakan AES-256-GCM
 * 
 * Fungsi utama:
 * - Enkripsi vote menggunakan AES-256-GCM
 * - Dekripsi vote yang tersimpan
 * - Manajemen encryption key dari environment
 * 
 * Security:
 * - Menggunakan AES-256-GCM (Galois/Counter Mode)
 * - Random nonce untuk setiap enkripsi
 * - Authentication tag untuk validasi integritas
 */
class VoteCryptoService
{
    private string $key;

    /**
     * CONSTRUCTOR
     * ===========
     * Inisialisasi service dengan memuat encryption key dari environment
     * 
     * @throws \RuntimeException - Jika VOTE_ENC_KEY tidak valid
     */
    public function __construct()
    {
        // Ambil encryption key dari environment variable
        $keyB64 = \env('VOTE_ENC_KEY');
        
        // Validasi: Key harus diset
        if (!$keyB64) {
            throw new \RuntimeException('VOTE_ENC_KEY is not set.');
        }
        
        // Decode base64 key
        $key = $this->fromBase64Key($keyB64);
        
        // Validasi: Key harus tepat 32 bytes untuk AES-256
        if (strlen($key) !== 32) {
            throw new \RuntimeException('VOTE_ENC_KEY must decode to 32 bytes (AES-256).');
        }
        
        $this->key = $key;
    }

    /**
     * DECODE BASE64 KEY
     * =================
     * Helper function untuk decode encryption key dari format base64
     * Menangani format Laravel "base64:..." dan plain base64
     * 
     * @param string $keyB64 - Key dalam format base64
     * @return string - Key dalam binary
     */
    private function fromBase64Key(string $keyB64): string
    {
        // Hapus prefix "base64:" jika ada (format Laravel)
        if (str_starts_with($keyB64, 'base64:')) {
            $keyB64 = substr($keyB64, 7);
        }
        
        // Decode base64
        $decoded = base64_decode($keyB64, true);
        
        // Return decoded jika valid, otherwise return original
        return $decoded !== false && strlen($decoded) > 0 ? $decoded : $keyB64;
    }

    /**
     * ENKRIPSI PLAINTEXT
     * ==================
     * Mengenkripsi data vote menggunakan AES-256-GCM
     * 
     * @param string $plaintext - Data yang akan dienkripsi (biasanya candidate ID)
     * @param string $aad - Additional Authenticated Data (opsional)
     * @return array{ciphertext:string, nonce:string, tag:string, algo:string}
     * @throws \RuntimeException - Jika enkripsi gagal
     * 
     * Output:
     * - ciphertext: Data terenkripsi dalam base64
     * - nonce: Random nonce 12-byte dalam base64
     * - tag: Authentication tag 16-byte dalam base64
     * - algo: Nama algoritma ("aes-256-gcm")
     */
    public function encrypt(string $plaintext, string $aad = ''): array
    {
        // Generate random nonce 12 bytes (96 bits) untuk GCM mode
        $nonce = random_bytes(12);
        
        // Variable untuk menyimpan authentication tag
        $tag = '';
        
        // Enkripsi menggunakan OpenSSL
        $cipher = openssl_encrypt(
            $plaintext,           // Data yang akan dienkripsi
            'aes-256-gcm',        // Algoritma: AES-256 dengan GCM mode
            $this->key,           // Encryption key (32 bytes)
            OPENSSL_RAW_DATA,     // Output dalam binary (bukan base64)
            $nonce,               // Nonce untuk setiap enkripsi
            $tag,                 // Output: authentication tag (passed by reference)
            $aad                  // Additional authenticated data (opsional)
        );
        
        // Validasi: Enkripsi harus berhasil
        if ($cipher === false) {
            throw new \RuntimeException('Encryption failed.');
        }
        
        // Return semua komponen dalam base64
        return [
            'ciphertext' => base64_encode($cipher),
            'nonce' => base64_encode($nonce),
            'tag' => base64_encode($tag),
            'algo' => 'aes-256-gcm',
        ];
    }

    /**
     * DEKRIPSI CIPHERTEXT
     * ===================
     * Mendekripsi data vote yang tersimpan di blockchain
     * 
     * @param string $ciphertextB64 - Ciphertext dalam base64
     * @param string $nonceB64 - Nonce dalam base64
     * @param string $tagB64 - Authentication tag dalam base64
     * @param string $aad - Additional Authenticated Data (opsional, harus sama dengan saat enkripsi)
     * @return string - Plaintext hasil dekripsi
     * @throws \InvalidArgumentException - Jika input base64 tidak valid
     * @throws \RuntimeException - Jika dekripsi gagal (auth tag mismatch)
     */
    public function decrypt(string $ciphertextB64, string $nonceB64, string $tagB64, string $aad = ''): string
    {
        // Decode semua input dari base64 ke binary
        $ciphertext = base64_decode($ciphertextB64, true);
        $nonce = base64_decode($nonceB64, true);
        $tag = base64_decode($tagB64, true);
        
        // Validasi: Semua input base64 harus valid
        if ($ciphertext === false || $nonce === false || $tag === false) {
            throw new \InvalidArgumentException('Invalid base64 inputs.');
        }
        
        // Dekripsi menggunakan OpenSSL
        $plain = openssl_decrypt(
            $ciphertext,          // Data terenkripsi
            'aes-256-gcm',        // Algoritma: AES-256 dengan GCM mode
            $this->key,           // Decryption key (harus sama dengan encryption key)
            OPENSSL_RAW_DATA,     // Input dalam binary
            $nonce,               // Nonce yang sama dengan saat enkripsi
            $tag,                 // Authentication tag untuk validasi
            $aad                  // Additional authenticated data (harus sama)
        );
        
        // Validasi: Dekripsi harus berhasil
        // Akan gagal jika tag tidak cocok atau data telah dimodifikasi
        if ($plain === false) {
            throw new \RuntimeException('Decryption failed (auth tag mismatch).');
        }
        
        // Return plaintext
        return $plain;
    }
}
