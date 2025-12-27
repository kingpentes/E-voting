<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class OrganizerRegisterController extends Controller
{
    /**
     * Display the organizer registration view.
     */
    public function create(): View
    {
        return view('auth.register-organizer');
    }

    /**
     * Handle an incoming organizer registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'organization' => ['nullable', 'string', 'max:255'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'organizer', // Set role as organizer
            'organization' => $request->organization,
        ]);

        event(new Registered($user));

        // Redirect ke halaman login dengan pesan sukses
        return redirect()->route('login')->with('status', 'Registrasi sebagai Penyelenggara berhasil! Silakan login untuk mulai membuat pemilu.');
    }
}
