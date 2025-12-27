<?php

namespace App\Console\Commands;

use Google\Client as GoogleClient;
use Google\Service\Gmail;
use Illuminate\Console\Command;

class GmailAuthCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gmail:auth 
                            {--refresh : Refresh existing token}
                            {--status : Check token status}
                            {--revoke : Revoke existing token}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Authenticate Gmail API and manage OAuth tokens';

    private string $tokenPath;
    private GoogleClient $client;

    public function __construct()
    {
        parent::__construct();
        $this->tokenPath = base_path('gmail_token.json');
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->initializeClient();

        if ($this->option('status')) {
            return $this->showStatus();
        }

        if ($this->option('refresh')) {
            return $this->refreshToken();
        }

        if ($this->option('revoke')) {
            return $this->revokeToken();
        }

        // Default: Run full authentication flow
        return $this->authenticate();
    }

    /**
     * Initialize Google Client
     */
    private function initializeClient(): void
    {
        $credentials = env('GOOGLE_APPLICATION_CREDENTIALS');
        $credentialsPath = null;
        $isJsonEnv = false;

        // Cek apakah ENV berisi JSON string (dimulai dengan kurung kurawal)
        if (is_string($credentials) && str_starts_with(trim($credentials), '{')) {
            $isJsonEnv = true;
        } else {
            // Jika bukan JSON, asumsikan path file
            $credentialsPath = $credentials;
        }

        if (!$isJsonEnv && (!$credentialsPath || !file_exists($credentialsPath))) {
            $this->error('❌ GOOGLE_APPLICATION_CREDENTIALS not set or file not found!');
            $this->info('Please set GOOGLE_APPLICATION_CREDENTIALS in your .env file');
            exit(1);
        }

        $this->client = new GoogleClient();
        $this->client->setApplicationName('E-Voting System');
        $this->client->setScopes([Gmail::GMAIL_SEND]);
        if ($isJsonEnv) {
            $this->client->setAuthConfig(json_decode($credentials, true));
        } else {
            $this->client->setAuthConfig($credentialsPath);
        }
        $this->client->setAccessType('offline');
        $this->client->setPrompt('consent');
        $this->client->setRedirectUri('http://localhost:8000/admin/gmail/callback');
    }

    /**
     * Full authentication flow
     */
    private function authenticate(): int
    {
        $this->info('🔐 Gmail OAuth Authentication');
        $this->newLine();

        // Check if token exists
        if (file_exists($this->tokenPath)) {
            $token = json_decode(file_get_contents($this->tokenPath), true);
            
            if ($this->client->isAccessTokenExpired()) {
                $this->warn('⚠  Existing token is expired.');
                
                if (isset($token['refresh_token'])) {
                    $this->info('Attempting to refresh token...');
                    return $this->refreshToken();
                } else {
                    $this->error('No refresh token available. Need to re-authenticate.');
                }
            } else {
                $this->info('✅ Token is still valid!');
                $this->showStatus();
                return 0;
            }
        }

        // Generate auth URL
        $authUrl = $this->client->createAuthUrl();
        
        $this->newLine();
        $this->info('📋 Step 1: Open this URL in your browser:');
        $this->newLine();
        $this->line("<fg=cyan>{$authUrl}</>");
        $this->newLine();
        
        $this->info('📋 Step 2: Login with your Gmail account and authorize the app');
        $this->newLine();
        
        $this->info('📋 Step 3: Copy the authorization code from the callback URL');
        $this->newLine();
        
        // Get auth code from user
        $authCode = $this->ask('Enter the authorization code');
        
        if (!$authCode) {
            $this->error('❌ Authorization code is required!');
            return 1;
        }

        try {
            // Exchange auth code for access token
            $accessToken = $this->client->fetchAccessTokenWithAuthCode($authCode);
            
            if (isset($accessToken['error'])) {
                $this->error("❌ Error: {$accessToken['error']}");
                if (isset($accessToken['error_description'])) {
                    $this->error("   {$accessToken['error_description']}");
                }
                return 1;
            }

            // Save token to file
            file_put_contents($this->tokenPath, json_encode($accessToken, JSON_PRETTY_PRINT));
            
            $this->newLine();
            $this->info('✅ Token saved successfully to: ' . $this->tokenPath);
            $this->newLine();
            
            $this->showStatus();
            
            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Failed to fetch access token: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Refresh existing token
     */
    private function refreshToken(): int
    {
        $this->info('🔄 Refreshing Gmail OAuth Token...');
        $this->newLine();

        if (!file_exists($this->tokenPath)) {
            $this->error('❌ No token file found. Please run php artisan gmail:auth first.');
            return 1;
        }

        $token = json_decode(file_get_contents($this->tokenPath), true);
        
        if (!isset($token['refresh_token'])) {
            $this->error('❌ No refresh token available. Please re-authenticate with php artisan gmail:auth');
            return 1;
        }

        try {
            $this->client->setAccessToken($token);
            
            // Refresh the token
            $newToken = $this->client->fetchAccessTokenWithRefreshToken($token['refresh_token']);
            
            if (isset($newToken['error'])) {
                $this->error("❌ Error: {$newToken['error']}");
                return 1;
            }

            // Make sure refresh_token is preserved
            if (!isset($newToken['refresh_token'])) {
                $newToken['refresh_token'] = $token['refresh_token'];
            }

            // Save new token
            file_put_contents($this->tokenPath, json_encode($newToken, JSON_PRETTY_PRINT));
            
            $this->info('✅ Token refreshed successfully!');
            $this->newLine();
            
            $this->showStatus();
            
            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Failed to refresh token: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Show token status
     */
    private function showStatus(): int
    {
        $this->info('📊 Gmail Token Status');
        $this->newLine();

        if (!file_exists($this->tokenPath)) {
            $this->warn('⚠  No token file found.');
            $this->line('   Run php artisan gmail:auth to authenticate.');
            return 1;
        }

        $token = json_decode(file_get_contents($this->tokenPath), true);
        $this->client->setAccessToken($token);

        $headers = ['Property', 'Value'];
        $data = [];

        // Token expiry
        if (isset($token['expires_in'], $token['created'])) {
            $expiresAt = $token['created'] + $token['expires_in'];
            $expiresAtFormatted = date('Y-m-d H:i:s', $expiresAt);
            $isExpired = $this->client->isAccessTokenExpired();
            
            $data[] = ['Token Expires At', $expiresAtFormatted];
            $data[] = ['Status', $isExpired ? '❌ EXPIRED' : '✅ VALID'];
            $data[] = ['Time Remaining', $isExpired ? 'Expired' : $this->humanReadableTime($expiresAt - time())];
        }

        // Refresh token
        $data[] = ['Refresh Token', isset($token['refresh_token']) ? '✅ Available' : '❌ Not available'];

        // Scope
        if (isset($token['scope'])) {
            $data[] = ['Scope', $token['scope']];
        }

        $this->table($headers, $data);

        return 0;
    }

    /**
     * Revoke existing token
     */
    private function revokeToken(): int
    {
        $this->info('🗑  Revoking Gmail OAuth Token...');
        $this->newLine();

        if (!file_exists($this->tokenPath)) {
            $this->warn('⚠  No token file found.');
            return 0;
        }

        if (!$this->confirm('Are you sure you want to revoke the token?')) {
            $this->line('Cancelled.');
            return 0;
        }

        $token = json_decode(file_get_contents($this->tokenPath), true);

        try {
            $this->client->setAccessToken($token);
            $this->client->revokeToken();
            
            unlink($this->tokenPath);
            
            $this->info('✅ Token revoked and file deleted.');
            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Failed to revoke token: ' . $e->getMessage());
            
            // Still delete local file
            if (file_exists($this->tokenPath)) {
                unlink($this->tokenPath);
                $this->info('Local token file deleted.');
            }
            
            return 1;
        }
    }

    /**
     * Convert seconds to human readable time
     */
    private function humanReadableTime(int $seconds): string
    {
        if ($seconds < 60) {
            return "{$seconds} seconds";
        } elseif ($seconds < 3600) {
            $minutes = floor($seconds / 60);
            return "{$minutes} minutes";
        } else {
            $hours = floor($seconds / 3600);
            $minutes = floor(($seconds % 3600) / 60);
            return "{$hours}h {$minutes}m";
        }
    }
}