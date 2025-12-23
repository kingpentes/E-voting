<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Google\Client as GoogleClient;

class GoogleAuthController extends Controller
{
    /**
     * Redirect user to Google OAuth page
     */
    public function redirectToGoogle(Request $request)
    {
        $client = $this->getGoogleClient();
        
        $authUrl = $client->createAuthUrl();
        
        // Store intended role in session if registering
        if ($request->has('role')) {
            session(['google_register_role' => $request->role]);
        }
        
        return redirect()->away($authUrl);
    }

    /**
     * Redirect to Google for registration with specific role
     */
    public function redirectToGoogleRegister($role)
    {
        // Validate role
        if (!in_array($role, ['organizer', 'voter'])) {
            return redirect()->route('register')
                ->with('error', 'Invalid role specified.');
        }

        // Store role in session
        session(['google_register_role' => $role]);

        $client = $this->getGoogleClient();
        $authUrl = $client->createAuthUrl();
        
        return redirect()->away($authUrl);
    }

    /**
     * Handle callback from Google OAuth
     */
    public function handleGoogleCallback(Request $request)
    {
        if (!$request->has('code')) {
            return redirect()->route('login')
                ->with('error', 'Google authentication was cancelled.');
        }

        try {
            $client = $this->getGoogleClient();
            
            // Exchange authorization code for access token
            $token = $client->fetchAccessTokenWithAuthCode($request->code);
            
            if (isset($token['error'])) {
                return redirect()->route('login')
                    ->with('error', 'Failed to authenticate with Google: ' . $token['error']);
            }
            
            $client->setAccessToken($token);
            
            // Get user info from Google
            $oauth = new \Google\Service\Oauth2($client);
            $googleUser = $oauth->userinfo->get();
            
            // Get intended role from session (for registration)
            $intendedRole = session('google_register_role');
            
            // Find or create user
            $user = $this->findOrCreateUser($googleUser, $token, $intendedRole);
            
            // Clear the role from session
            session()->forget('google_register_role');
            
            // Log the user in
            Auth::login($user, true);
            
            $request->session()->regenerate();
            
            // Redirect based on role and verification status
            if ($user->isAdmin() || $user->isOrganizer()) {
                // Admin/Organizer go to admin dashboard
                return redirect()->route('admin.dashboard');
            } else {
                // Voter: cek verification status menggunakan helper method
                if ($user->needsVerification()) {
                    return redirect()->route('voter.verification')
                        ->with('info', 'Silakan lengkapi verifikasi identitas Anda terlebih dahulu.');
                }
                
                // Sudah verified, redirect ke voter.verification (akan show election-selection jika punya elections)
                return redirect()->route('voter.verification')
                    ->with('success', 'Login berhasil! Selamat datang, ' . $user->name);
            }
            
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->with('error', 'Failed to authenticate with Google: ' . $e->getMessage());
        }
    }

    /**
     * Get configured Google Client
     */
    private function getGoogleClient()
    {
        $client = new GoogleClient();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setRedirectUri(config('services.google.redirect'));
        $client->addScope('email');
        $client->addScope('profile');
        
        return $client;
    }

    /**
     * Find existing user or create new one
     */
    private function findOrCreateUser($googleUser, $token, $intendedRole = null)
    {
        // Check if user exists with this Google ID
        $user = User::where('google_id', $googleUser->id)->first();
        
        if ($user) {
            // Update existing user's token
            $user->update([
                'google_token' => $token['access_token'],
                'google_refresh_token' => $token['refresh_token'] ?? null,
                'avatar' => $googleUser->picture ?? null,
            ]);
            
            return $user;
        }
        
        // Check if user exists with this email
        $user = User::where('email', $googleUser->email)->first();
        
        if ($user) {
            // Link Google account to existing user
            $user->update([
                'google_id' => $googleUser->id,
                'google_token' => $token['access_token'],
                'google_refresh_token' => $token['refresh_token'] ?? null,
                'avatar' => $googleUser->picture ?? null,
            ]);
            
            return $user;
        }
        
        // Determine role: use intended role or default to voter
        $role = $intendedRole && in_array($intendedRole, ['organizer', 'voter']) 
            ? $intendedRole 
            : 'voter';
        
        // Create new user with specified role
        $user = User::create([
            'name' => $googleUser->name,
            'email' => $googleUser->email,
            'google_id' => $googleUser->id,
            'google_token' => $token['access_token'],
            'google_refresh_token' => $token['refresh_token'] ?? null,
            'avatar' => $googleUser->picture ?? null,
            'password' => Hash::make(Str::random(32)), // Random password since using OAuth
            'role' => $role,
            'email_verified_at' => now(), // Google emails are verified
        ]);
        
        return $user;
    }
}

