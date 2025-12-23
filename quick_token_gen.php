<?php

require __DIR__.'/vendor/autoload.php';

use Google\Client;
use Google\Service\Gmail;

echo "=== GENERATE GMAIL TOKEN ===\n\n";

// Input authorization code from command line argument or prompt
if (isset($argv[1]) && !empty($argv[1])) {
    $authCode = $argv[1];
    echo "Using authorization code from argument...\n";
} else {
    echo "Enter authorization code (paste dari URL setelah redirect):\n";
    $authCode = trim(fgets(STDIN));
}

if (empty($authCode)) {
    echo "✗ Authorization code tidak boleh kosong!\n";
    exit(1);
}

$client = new Client();
$client->setApplicationName('E-Voting System');
$client->setScopes([Gmail::GMAIL_SEND]);
$client->setAuthConfig(__DIR__.'/client_secret_236667411997-ahf7pqcdck56fep2q39aoq1cmr9n3h7e.apps.googleusercontent.com.json');
$client->setAccessType('offline');
$client->setPrompt('consent');
$client->setRedirectUri('http://localhost/auth/google/callback');

try {
    // Exchange authorization code for access token
    $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
    
    // Check for errors
    if (array_key_exists('error', $accessToken)) {
        echo "\n✗ ERROR: " . $accessToken['error_description'] . "\n";
        exit(1);
    }
    
    // Save token to file
    $tokenPath = __DIR__.'/gmail_token.json';
    file_put_contents($tokenPath, json_encode($client->getAccessToken()));
    
    echo "\n✓ Token berhasil dibuat!\n";
    echo "Token disimpan di: gmail_token.json\n\n";
    
    // Show token info
    $token = $client->getAccessToken();
    echo "Token Info:\n";
    echo "- Access Token: " . substr($token['access_token'], 0, 30) . "...\n";
    echo "- Token Type: " . $token['token_type'] . "\n";
    echo "- Expires In: " . $token['expires_in'] . " seconds\n";
    
    if (isset($token['refresh_token'])) {
        echo "- Refresh Token: " . substr($token['refresh_token'], 0, 30) . "...\n";
    }
    
    echo "\n✓ Sekarang bisa kirim OTP via email!\n";
    
} catch (Exception $e) {
    echo "\n✗ ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
