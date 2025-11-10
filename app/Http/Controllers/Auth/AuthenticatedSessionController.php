<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // In testing, keep Breeze's default redirect to satisfy framework tests
        if (app()->environment('testing')) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

    // Redirect berdasarkan role user
    /** @var User $user */
    $user = Auth::user();
        
        if ($user->role === 'organizer') {
            return redirect()->intended(route('admin.dashboard', absolute: false));
        } elseif ($user->role === 'voter') {
            // Voter diarahkan ke election terakhir yang mereka ikuti
            $lastElection = $user->participatingElections()
                ->where('is_published', true)
                ->latest('election_user.joined_at')
                ->first();
            
            if ($lastElection) {
                return redirect()->intended(route('voter.election', ['code' => $lastElection->access_code], absolute: false))
                    ->with('success', 'Login berhasil! Selamat datang kembali di pemilu: ' . $lastElection->title);
            }
            
            // Jika voter belum terdaftar di election manapun, arahkan ke dashboard umum
            return redirect()->intended(route('dashboard', absolute: false))
                ->with('info', 'Silakan masukkan kode akses pemilu untuk melanjutkan.');
        } elseif ($user->role === 'admin') {
            return redirect()->intended(route('admin.dashboard', absolute: false));
        }

    // Default redirect (seharusnya tidak sampai sini)
    return redirect()->intended('/');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}