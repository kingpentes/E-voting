<?php

require __DIR__ . '/vendor/autoload.php';

use Google\Client as GoogleClient;
use Google\Service\Gmail;
use Google\Service\Gmail\Message;

echo "=== TEST KIRIM EMAIL GMAIL API ===\n\n";

// Load client
$client = new GoogleClient();
$client->setApplicationName('E-Voting System');
$client->setScopes([Gmail::GMAIL_SEND]);
$client->setAuthConfig(__DIR__ . '/client_secret_236667411997-ahf7pqcdck56fep2q39aoq1cmr9n3h7e.apps.googleusercontent.com.json');
$client->setAccessType('offline');

// Load access token
$tokenPath = __DIR__ . '/gmail_token.json';
if (!file_exists($tokenPath)) {
    die("Error: gmail_token.json tidak ditemukan!\n");
}

$accessToken = json_decode(file_get_contents($tokenPath), true);
echo "✓ Token loaded\n";

$client->setAccessToken($accessToken);

// Check if expired
if ($client->isAccessTokenExpired()) {
    echo "⚠ Token expired, refreshing...\n";
    if ($client->getRefreshToken()) {
        $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        file_put_contents($tokenPath, json_encode($client->getAccessToken()));
        echo "✓ Token refreshed\n";
    } else {
        die("Error: No refresh token available!\n");
    }
} else {
    echo "✓ Token valid\n";
}

// Create Gmail service
$gmail = new Gmail($client);
echo "✓ Gmail service created\n\n";

// Prepare email
$to = 'radityayusma@gmail.com';
$subject = 'Test Email - Kode OTP';
$otp = '123456';

$body = <<<HTML
<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif; padding: 20px;">
    <h2 style="color: #4F46E5;">Kode OTP Anda</h2>
    <p>Kode OTP untuk reset password:</p>
    <div style="background: #EEF2FF; padding: 20px; border-radius: 8px; text-align: center;">
        <h1 style="color: #4F46E5; font-size: 36px; margin: 0;">{$otp}</h1>
    </div>
    <p style="color: #666; margin-top: 20px;">Kode ini berlaku selama 10 menit.</p>
    <p style="color: #666;">Jika Anda tidak meminta reset password, abaikan email ini.</p>
</body>
</html>
HTML;

// Create raw message
$from = 'noreply@evoting.local';
$fromName = 'E-Voting System';

$rawMessage = "From: {$fromName} <{$from}>\r\n";
$rawMessage .= "To: {$to}\r\n";
$rawMessage .= "Subject: {$subject}\r\n";
$rawMessage .= "MIME-Version: 1.0\r\n";
$rawMessage .= "Content-Type: text/html; charset=utf-8\r\n";
$rawMessage .= "\r\n";
$rawMessage .= $body;

$encodedMessage = base64_encode($rawMessage);
$encodedMessage = str_replace(['+', '/', '='], ['-', '_', ''], $encodedMessage);

echo "Mengirim email ke: {$to}\n";
echo "Subject: {$subject}\n\n";

try {
    $message = new Message();
    $message->setRaw($encodedMessage);
    
    $result = $gmail->users_messages->send('me', $message);
    
    echo "✓ EMAIL BERHASIL DIKIRIM!\n";
    echo "Message ID: {$result->getId()}\n";
    echo "\nSilakan cek inbox/spam di Gmail Anda!\n";
    
} catch (\Exception $e) {
    echo "✗ GAGAL MENGIRIM EMAIL\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "\nDetail:\n";
    print_r($e);
}
