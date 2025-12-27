<?php

namespace App\Service;

use Google\Client as GoogleClient;
use Google\Service\Gmail;
use Google\Service\Gmail\Message;
use Illuminate\Support\Facades\Log;

class GmailService
{
    private GoogleClient $client;
    private Gmail $gmail;

    public function __construct()
    {
        $this->client = new GoogleClient();
        $this->client->setApplicationName('E-Voting System');
        $this->client->setScopes([Gmail::GMAIL_SEND]);
        // Support for JSON string in ENV (for Railway/Cloud) or File Path
        $credentials = env('GOOGLE_APPLICATION_CREDENTIALS');
        if (is_string($credentials) && str_starts_with(trim($credentials), '{')) {
            $credentials = json_decode($credentials, true);
        }
        $this->client->setAuthConfig($credentials);
        $this->client->setAccessType('offline');
        
        // Load access token dari ENV (Bootstrap untuk Railway) atau File
        $tokenPath = storage_path('app/gmail_token.json');
        
        // Cek apakah ada token di ENV (GMAIL_TOKEN_JSON) dan file belum ada
        $envToken = env('GMAIL_TOKEN_JSON');
        if (!file_exists($tokenPath) && $envToken && is_string($envToken)) {
            file_put_contents($tokenPath, $envToken);
        }

        if (file_exists($tokenPath)) {
            $accessToken = json_decode(file_get_contents($tokenPath), true);
            $this->client->setAccessToken($accessToken);
            
            // Refresh token jika expired
            if ($this->client->isAccessTokenExpired()) {
                if ($this->client->getRefreshToken()) {
                    $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
                    file_put_contents($tokenPath, json_encode($this->client->getAccessToken()));
                }
            }
        }
        
        $this->gmail = new Gmail($this->client);
    }

    /**
     * Send OTP email via Gmail
     * 
     * @param string $to Recipient email
     * @param string $otp 6-digit OTP code
     * @return bool Success status
     */
    public function sendOTP(string $to, string $otp): bool
    {
        try {
            $subject = 'Reset Password - Kode OTP Anda';
            $body = $this->getOTPEmailBody($otp);
            
            $rawMessage = $this->createRawMessage($to, $subject, $body);
            $message = new Message();
            $message->setRaw($rawMessage);
            
            $this->gmail->users_messages->send('me', $message);
            
            Log::info('OTP email sent successfully', ['to' => $to]);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send OTP email', [
                'to' => $to,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Create raw email message
     */
    private function createRawMessage(string $to, string $subject, string $body): string
    {
        $from = env('MAIL_FROM_ADDRESS', 'noreply@evoting.local');
        $fromName = env('MAIL_FROM_NAME', 'E-Voting System');
        
        $message = "From: {$fromName} <{$from}>\r\n";
        $message .= "To: {$to}\r\n";
        $message .= "Subject: {$subject}\r\n";
        $message .= "MIME-Version: 1.0\r\n";
        $message .= "Content-Type: text/html; charset=utf-8\r\n";
        $message .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $message .= chunk_split(base64_encode($body));
        
        return base64_encode($message);
    }

    /**
     * Get HTML body for OTP email
     */
    private function getOTPEmailBody(string $otp): string
    {
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .container {
            background: #f9f9f9;
            border-radius: 10px;
            padding: 30px;
            border: 1px solid #ddd;
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 2px solid #667eea;
        }
        .otp-box {
            background: #667eea;
            color: white;
            font-size: 32px;
            font-weight: bold;
            letter-spacing: 8px;
            text-align: center;
            padding: 20px;
            margin: 30px 0;
            border-radius: 8px;
        }
        .footer {
            text-align: center;
            color: #666;
            font-size: 12px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
        .warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 10px 15px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="color: #667eea; margin: 0;">🔐 Reset Password</h1>
        </div>
        
        <p>Halo,</p>
        <p>Anda telah meminta untuk mereset password akun E-Voting Anda. Gunakan kode OTP berikut untuk melanjutkan:</p>
        
        <div class="otp-box">
            {$otp}
        </div>
        
        <div class="warning">
            <strong>⚠️ Penting:</strong>
            <ul style="margin: 5px 0;">
                <li>Kode OTP ini berlaku selama <strong>10 menit</strong></li>
                <li>Jangan bagikan kode ini kepada siapapun</li>
                <li>Jika Anda tidak meminta reset password, abaikan email ini</li>
            </ul>
        </div>
        
        <p>Masukkan kode OTP di halaman reset password untuk melanjutkan.</p>
        
        <div class="footer">
            <p>Email ini dikirim otomatis oleh sistem E-Voting.<br>
            Mohon tidak membalas email ini.</p>
        </div>
    </div>
</body>
</html>
HTML;
    }
}
