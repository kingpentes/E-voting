<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Google\Client as GoogleClient;
use Google\Service\Oauth2;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    private GoogleClient $client;

    public function __construct()
    {
        $this->client = new GoogleClient();
        $this->client->setClientId(env('GOOGLE_CLIENT_ID'));
        $this->client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $this->client->setRedirectUri(env('GOOGLE_REDIRECT_URI', url('/auth/google/callback')));
        $this->client->addScope('email');
        $this->client->addScope('profile');
    }

    /**
     * Redirect to Google OAuth with role in state
     */
    public function redirect(Request $request)
    {
        $role = $request->query('role', 'voter');
        
        if (!in_array($role, ['organizer', 'voter'])) {
            $role = 'voter';
        }

        // Store role in state parameter
        $this->client->setState($role);
        
        $authUrl = $this->client->createAuthUrl();
        
        return redirect()->away($authUrl);
    }

    /**
     * Handle Google OAuth callback
     */
    public function callback(Request $request)
    {
        try {
            // Check for errors from Google
            if ($request->has('error')) {
                Log::error('Google OAuth error', ['error' => $request->get('error')]);
                return redirect('/login')->with('error', 'Google authentication was cancelled.');
            }

            $code = $request->get('code');
            $role = $request->get('state', 'voter');

            if (!$code) {
                return redirect('/login')->with('error', 'No authorization code received from Google.');
            }

            // Exchange code for access token
            $token = $this->client->fetchAccessTokenWithAuthCode($code);
            
            if (isset($token['error'])) {
                Log::error('Google token error', ['error' => $token]);
                return redirect('/login')->with('error', 'Failed to get access token from Google.');
            }

            $this->client->setAccessToken($token);

            // Get user info from Google
            $oauth2 = new Oauth2($this->client);
            $googleUser = $oauth2->userinfo->get();

            // Find or create user
            $user = $this->findOrCreateUser($googleUser, $role);

            // Login user
            Auth::login($user, true);

            Log::info('Google OAuth login successful', ['user_id' => $user->id, 'email' => $user->email]);

            return redirect()->intended('/dashboard');

        } catch (\Exception $e) {
            Log::error('Google OAuth exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect('/login')->with('error', 'Google login failed. Please try again.');
        }
    }

    /**
     * Find existing user or create new one
     */
    private function findOrCreateUser($googleUser, string $role): User
    {
        // Check if user exists by email
        $existingUser = User::where('email', $googleUser->email)->first();

        if ($existingUser) {
            // Update google_id if not set
            if (!$existingUser->google_id) {
                $existingUser->update(['google_id' => $googleUser->id]);
            }
            return $existingUser;
        }

        // Create new user with selected role
        return User::create([
            'name' => $googleUser->name,
            'email' => $googleUser->email,
            'google_id' => $googleUser->id,
            'role' => $role,
            'email_verified_at' => now(),
            'password' => bcrypt(Str::random(24)),
        ]);
    }
}
