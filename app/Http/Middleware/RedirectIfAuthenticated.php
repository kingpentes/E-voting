<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::user();
                
                // Redirect berdasarkan role dengan pesan
                if ($user->role === 'organizer') {
                    return redirect()->route('admin.dashboard')
                        ->with('error', 'Anda sudah login! Klik tombol KELUAR terlebih dahulu jika ingin registrasi akun baru.');
                } elseif ($user->role === 'voter') {
                    return redirect()->route('voter.dashboard')
                        ->with('error', 'Anda sudah login! Klik tombol KELUAR terlebih dahulu jika ingin registrasi akun baru.');
                }
                
                // Default redirect
                return redirect('/dashboard')
                    ->with('error', 'Anda sudah login! Logout terlebih dahulu untuk registrasi akun baru.');
            }
        }

        return $next($request);
    }
}
