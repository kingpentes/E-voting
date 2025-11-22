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
                
                // Redirect berdasarkan role
                if ($user->role === 'organizer') {
                    return redirect()->route('admin.dashboard');
                } elseif ($user->role === 'voter') {
                    // Redirect voter ke election terakhir mereka
                    $lastElection = $user->participatingElections()
                        ->where('is_published', true)
                        ->latest('election_user.joined_at')
                        ->first();
                    
                    if ($lastElection) {
                        return redirect()->route('voter.election', ['code' => $lastElection->access_code]);
                    }
                    
                    // Jika tidak ada election, redirect ke home
                    return redirect('/');
                }
                
                // Default redirect
                return redirect('/');
            }
        }

        return $next($request);
    }
}
