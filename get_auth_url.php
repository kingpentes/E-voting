<?php
require 'vendor/autoload.php';

$client = new Google\Client();
$client->setAuthConfig('client_secret_236667411997-ahf7pqcdck56fep2q39aoq1cmr9n3h7e.apps.googleusercontent.com.json');
$client->setRedirectUri('http://localhost/auth/google/callback');
$client->setScopes(['https://www.googleapis.com/auth/gmail.send']);
$client->setAccessType('offline');
$client->setPrompt('consent');

echo "\n╔════════════════════════════════════════════════════════════════╗\n";
echo "║           GMAIL API - GET AUTHORIZATION CODE                   ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

$authUrl = $client->createAuthUrl();

echo "1. BUKA URL INI DI BROWSER:\n\n";
echo $authUrl . "\n\n";
echo "2. Login dengan akun Gmail Anda\n";
echo "3. Klik 'Allow' untuk memberikan akses\n";
echo "4. Setelah redirect, COPY semua text di URL bar\n";
echo "5. Cari bagian 'code=' dan copy kode setelahnya\n";
echo "6. Paste code tersebut ketika diminta\n\n";
echo "══════════════════════════════════════════════════════════════════\n";
echo "NOTE: Halaman 'Not Found' itu NORMAL!\n";
echo "Yang penting adalah CODE di URL-nya!\n";
echo "══════════════════════════════════════════════════════════════════\n";
