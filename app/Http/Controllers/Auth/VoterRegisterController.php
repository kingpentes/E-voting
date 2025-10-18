<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
            'invite_code' => ['nullable', 'string', 'max:8'], // Kode undangan dari penyelenggara
            'id_card' => ['nullable', 'image', 'max:2048'], // Upload KTP/ID Card (Max 2MB)
            'face_image' => ['nullable', 'string'], // Base64 image dari kamera
        ]);

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
            'id_number' => $request->invite_code ?? null, // Simpan invite code sebagai id_number sementara
            'organization' => $idCardPath, // Simpan path ID card di field organization sementara
            'face_photo' => $facePhotoPath,
        ]);

        event(new Registered($user));

        // Redirect ke halaman login dengan pesan sukses
        return redirect()->route('login')->with('status', '✓ Registrasi sebagai Pemilih berhasil! Silakan login untuk mulai memberikan suara.');
    }
}
