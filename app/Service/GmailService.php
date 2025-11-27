<?php

namespace App\Service;

use Google\Client as GoogleClient;
use Google\Service\Gmail;
use Google\Service\Gmail\Message;
use Illuminate\Support\Facades\Log;

/**
 * GMAIL SERVICE
 * =============
 * Service untuk mengirim email menggunakan Gmail API
 * 
 * Fungsi utama:
 * - Mengirim email OTP untuk reset password
 * - Autentikasi menggunakan Google OAuth2
 * - Automatic token refresh
 * 
 * Requirement:
 * - Google Cloud Project dengan Gmail API enabled
 * - OAuth2 credentials (client_secret.json)
 * - Refresh token (gmail_token.json)
 */
class GmailService
{
    private GoogleClient $client;
    private Gmail $gmail;

    /**
     * CONSTRUCTOR
     * ===========
     * Inisialisasi Google Client dan Gmail Service
     * 
     * Setup:
     * 1. Load OAuth2 credentials
     * 2. Load access token dari file
     * 3. Refresh token jika expired
     * 4. Inisialisasi Gmail service
     * 
     * @throws \Exception - Jika konfigurasi tidak valid
     */
    public function __construct()
    {
        // Inisialisasi Google Client
        $this->client = new GoogleClient();
        $this->client->setApplicationName('E-Voting System');
        
        // Set scope untuk mengirim email
        $this->client->setScopes([Gmail::GMAIL_SEND]);
        
        // Load OAuth2 credentials dari file JSON
        // File path diambil dari environment variable GOOGLE_APPLICATION_CREDENTIALS
        $this->client->setAuthConfig(env('GOOGLE_APPLICATION_CREDENTIALS'));
        
        // Set access type ke 'offline' untuk mendapatkan refresh token
        $this->client->setAccessType('offline');
        
        /**
         * LOAD ACCESS TOKEN
         * Token disimpan di base_path/gmail_token.json
         */
        $tokenPath = base_path('gmail_token.json');
        
        if (file_exists($tokenPath)) {
            // Load token dari file
            $accessToken = json_decode(file_get_contents($tokenPath), true);
            $this->client->setAccessToken($accessToken);
            
            /**
             * AUTO REFRESH TOKEN
             * Jika access token sudah expired, gunakan refresh token
             * untuk mendapatkan access token baru
             */
            if ($this->client->isAccessTokenExpired()) {
                if ($this->client->getRefreshToken()) {
                    // Fetch access token baru menggunakan refresh token
                    $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
                    
                    // Simpan access token baru ke file
                    file_put_contents($tokenPath, json_encode($this->client->getAccessToken()));
                }
            }
        }
        
        // Inisialisasi Gmail service dengan authenticated client
        $this->gmail = new Gmail($this->client);
    }

    /**
     * KIRIM EMAIL OTP
     * ===============
     * Mengirim kode OTP untuk reset password via Gmail
     * 
     * @param string $to - Email tujuan (recipient)
     * @param string $otp - Kode OTP 6 digit
     * @return bool - True jika berhasil, False jika gagal
     * 
     * Security:
     * - OTP berlaku 10 menit
     * - HTML email dengan styling professional
     * - Warning untuk tidak share OTP
     */
    public function sendOTP(string $to, string $otp): bool
    {
        try {
            // Subject email
            $subject = 'Reset Password - Kode OTP Anda';
            
            // Generate HTML body untuk email
            $body = $this->getOTPEmailBody($otp);
            
            // Buat raw email message (RFC 2822 format)
            $rawMessage = $this->createRawMessage($to, $subject, $body);
            
            // Buat Message object untuk Gmail API
            $message = new Message();
            $message->setRaw($rawMessage);
            
            // Kirim email menggunakan Gmail API
            // 'me' berarti authenticated user (from GOOGLE_APPLICATION_CREDENTIALS)
            $this->gmail->users_messages->send('me', $message);
            
            // Log success untuk monitoring
            Log::info('OTP email sent successfully', ['to' => $to]);
            
            return true;
            
        } catch (\Exception $e) {
            // Log error dengan detail untuk debugging
            Log::error('Failed to send OTP email', [
                'to' => $to,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return false;
        }
    }

    /**
     * BUAT RAW EMAIL MESSAGE
     * ======================
     * Membuat raw email dalam format RFC 2822 untuk Gmail API
     * 
     * @param string $to - Email tujuan
     * @param string $subject - Subject email
     * @param string $body - HTML body email
     * @return string - Base64 encoded raw message
     * 
     * Format:
     * - Headers: From, To, Subject, MIME-Version, Content-Type
     * - Body: Base64 encoded HTML
     */
    private function createRawMessage(string $to, string $subject, string $body): string
    {
        // Ambil sender info dari environment
        $from = env('MAIL_FROM_ADDRESS', 'noreply@evoting.local');
        $fromName = env('MAIL_FROM_NAME', 'E-Voting System');
        
        // Bangun email headers
        $message = "From: {$fromName} <{$from}>\r\n";
        $message .= "To: {$to}\r\n";
        $message .= "Subject: {$subject}\r\n";
        $message .= "MIME-Version: 1.0\r\n";
        $message .= "Content-Type: text/html; charset=utf-8\r\n";
        $message .= "Content-Transfer-Encoding: base64\r\n\r\n";
        
        // Encode body ke base64 dan split per 76 karakter (RFC 2045)
        $message .= chunk_split(base64_encode($body));
        
        // Encode seluruh message ke base64 untuk Gmail API
        return base64_encode($message);
    }

    /**
     * GENERATE HTML BODY OTP EMAIL
     * ============================
     * Membuat HTML template untuk email OTP
     * 
     * @param string $otp - Kode OTP 6 digit
     * @return string - HTML content
     * 
     * Template includes:
     * - Professional styling dengan CSS inline
     * - OTP box yang prominent
     * - Warning box dengan security tips
     * - Footer dengan informasi sistem
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
