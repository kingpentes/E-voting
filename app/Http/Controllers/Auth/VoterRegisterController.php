<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Election;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class VoterRegisterController extends Controller
{
    /**
     * Display the voter registration view.
     */
    public function create(): View
    {
        return view('auth.register-voter');
    }

    /**
     * Handle an incoming voter registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'invite_code' => ['nullable', 'string', 'max:8'], // Optional now (can be provided at login instead)
            'id_card' => ['nullable', 'image', 'max:2048'], // Upload KTP/ID Card (Max 2MB)
            'face_image' => ['nullable', 'string'], // Base64 image dari kamera
        ]);
        // If invite code provided during registration, validate election and attach later.
        $election = null;
        if ($request->filled('invite_code')) {
            $election = Election::where('access_code', strtoupper($request->invite_code))
                ->where('is_published', true)
                ->first();

            if (! $election) {
                return back()->withErrors([
                    'invite_code' => 'Kode undangan tidak valid atau pemilu belum dipublikasikan. Silakan hubungi penyelenggara.'
                ])->withInput();
            }
        }

        // Handle ID card upload
        $idCardPath = null;
        if ($request->hasFile('id_card')) {
            $idCardPath = $request->file('id_card')->store('id-cards', 'public');
        }

        // Handle face image (base64 from camera)
        $facePhotoPath = null;
        if ($request->filled('face_image')) {
            // Convert base64 to image file
            $imageData = $request->face_image;
            
            // Remove data:image/jpeg;base64, prefix if exists
            if (strpos($imageData, 'data:image') === 0) {
                $imageData = substr($imageData, strpos($imageData, ',') + 1);
            }
            
            $imageData = base64_decode($imageData);
            $fileName = 'face_' . time() . '_' . uniqid() . '.jpg';
            $path = 'face-photos/' . $fileName;
            
            Storage::disk('public')->put($path, $imageData);
            $facePhotoPath = $path;
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'voter', // Set role as voter
            'id_number' => null, // Tidak digunakan untuk voter (nullable)
            'organization' => $election ? $election->title : null, // Simpan nama pemilu yang diikuti jika ada
            'face_photo' => $facePhotoPath,
        ]);

        event(new Registered($user));

        // Do NOT attach voter or auto-login after registration.
        // Redirect to the login page so the user can login and enter the invite code there.
        if ($request->filled('invite_code')) {
            // Prefill invite_code on the login page (uppercased) and show message
            return redirect()->route('login')
                ->withInput(['invite_code' => strtoupper($request->invite_code)])
                ->with('success', '✓ Registrasi berhasil! Silakan login dan masukkan kode undangan untuk bergabung ke pemilu.');
        }

        return redirect()->route('login')->with('success', '✓ Registrasi berhasil! Silakan login dan masukkan kode undangan saat diminta untuk bergabung ke pemilu.');
    }
}
