<?php

namespace App\Service;

class VoteCryptoService
{
    private string $key;

    public function __construct()
    {
    $keyB64 = \env('VOTE_ENC_KEY');
        if (!$keyB64) {
            throw new \RuntimeException('VOTE_ENC_KEY is not set.');
        }
        $key = $this->fromBase64Key($keyB64);
        if (strlen($key) !== 32) {
            throw new \RuntimeException('VOTE_ENC_KEY must decode to 32 bytes (AES-256).');
        }
        $this->key = $key;
    }

    private function fromBase64Key(string $keyB64): string
    {
        if (str_starts_with($keyB64, 'base64:')) {
            $keyB64 = substr($keyB64, 7);
        }
        $decoded = base64_decode($keyB64, true);
        return $decoded !== false && strlen($decoded) > 0 ? $decoded : $keyB64;
    }

    /**
     * Encrypt plaintext using AES-256-GCM
     * @return array{ciphertext:string, nonce:string, tag:string, algo:string}
     */
    public function encrypt(string $plaintext, string $aad = ''): array
    {
        $nonce = random_bytes(12);
        $tag = '';
        $cipher = openssl_encrypt(
            $plaintext,
            'aes-256-gcm',
            $this->key,
            OPENSSL_RAW_DATA,
            $nonce,
            $tag,
            $aad
        );
        if ($cipher === false) {
            throw new \RuntimeException('Encryption failed.');
        }
        return [
            'ciphertext' => base64_encode($cipher),
            'nonce' => base64_encode($nonce),
            'tag' => base64_encode($tag),
            'algo' => 'aes-256-gcm',
        ];
    }

    public function decrypt(string $ciphertextB64, string $nonceB64, string $tagB64, string $aad = ''): string
    {
        $ciphertext = base64_decode($ciphertextB64, true);
        $nonce = base64_decode($nonceB64, true);
        $tag = base64_decode($tagB64, true);
        if ($ciphertext === false || $nonce === false || $tag === false) {
            throw new \InvalidArgumentException('Invalid base64 inputs.');
        }
        $plain = openssl_decrypt(
            $ciphertext,
            'aes-256-gcm',
            $this->key,
            OPENSSL_RAW_DATA,
            $nonce,
            $tag,
            $aad
        );
        if ($plain === false) {
            throw new \RuntimeException('Decryption failed (auth tag mismatch).');
        }
        return $plain;
    }
}
