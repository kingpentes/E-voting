<?php

require __DIR__.'/vendor/autoload.php';

use Google\Client;
use Google\Service\Gmail;

$client = new Client();
$client->setApplicationName('E-Voting System');
$client->setScopes([Gmail::GMAIL_SEND]);
$client->setAuthConfig(__DIR__.'/client_secret_236667411997-ahf7pqcdck56fep2q39aoq1cmr9n3h7e.apps.googleusercontent.com.json');
$client->setAccessType('offline');
$client->setPrompt('consent');
$client->setRedirectUri('http://localhost/auth/google/callback');

// File untuk menyimpan token
$tokenPath = __DIR__.'/gmail_token.json';

// Cek apakah token sudah ada
if (file_exists($tokenPath)) {
    $accessToken = json_decode(file_get_contents($tokenPath), true);
    $client->setAccessToken($accessToken);
}

// Jika token expired atau belum ada, minta authorization
if ($client->isAccessTokenExpired()) {
    // Jika ada refresh token, gunakan untuk refresh
    if ($client->getRefreshToken()) {
        $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
    } else {
        // Request authorization dari user
        $authUrl = $client->createAuthUrl();
        
        echo "\n==============================================\n";
        echo "GMAIL API AUTHORIZATION\n";
        echo "==============================================\n\n";
        echo "1. Buka URL ini di browser:\n\n";
        echo $authUrl . "\n\n";
        echo "2. Login dengan akun Gmail yang akan digunakan\n";
        echo "3. Klik 'Allow' untuk memberikan izin\n";
        echo "4. Copy authorization code yang muncul\n";
        echo "5. Paste code di bawah ini:\n\n";
        
        // Input authorization code
        echo "Enter authorization code: ";
        $authCode = trim(fgets(STDIN));
        
        // Exchange authorization code untuk access token
        $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
        
        // Cek error
        if (array_key_exists('error', $accessToken)) {
            echo "\nError: " . $accessToken['error_description'] . "\n";
            exit(1);
        }
    }
    
    // Simpan token ke file
    if (!file_exists(dirname($tokenPath))) {
        mkdir(dirname($tokenPath), 0700, true);
    }
    file_put_contents($tokenPath, json_encode($client->getAccessToken()));
}

echo "\n==============================================\n";
echo "✓ Token berhasil dibuat/diperbarui!\n";
echo "==============================================\n\n";
echo "Token disimpan di: gmail_token.json\n";
echo "Sekarang Anda bisa menggunakan forgot password!\n\n";

// Test kirim email
echo "Apakah ingin test kirim email? (y/n): ";
$test = trim(fgets(STDIN));

if (strtolower($test) === 'y') {
    echo "Masukkan email tujuan: ";
    $toEmail = trim(fgets(STDIN));
    
    try {
        $service = new Gmail($client);
        
        // Buat email
        $subject = 'Test Email dari E-Voting System';
        $messageText = 'Ini adalah test email. Kode OTP Anda adalah: 123456';
        
        $message = new Google\Service\Gmail\Message();
        $rawMessage = "From: E-Voting System <me>\r\n";
        $rawMessage .= "To: {$toEmail}\r\n";
        $rawMessage .= "Subject: {$subject}\r\n";
        $rawMessage .= "Content-Type: text/html; charset=utf-8\r\n\r\n";
        $rawMessage .= $messageText;
        
        $message->setRaw(base64_encode($rawMessage));
        $result = $service->users_messages->send('me', $message);
        
        echo "\n✓ Email berhasil dikirim! Message ID: " . $result->getId() . "\n";
    } catch (Exception $e) {
        echo "\n✗ Gagal mengirim email: " . $e->getMessage() . "\n";
    }
}

echo "\nSelesai!\n";
