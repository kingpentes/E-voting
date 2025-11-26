<?php

namespace App\Http\Controllers;

use App\Models\Election;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class VoterVerificationController extends Controller
{
    /**
     * Show verification form for voter
     */
    public function show(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Load participating elections
        $user->load('participatingElections');
        
        // Show election selection if user has any elections (approved or pending)
        if ($user->participatingElections()->exists() && !$request->has('add_new')) {
            // Get all elections (approved and pending)
            $elections = $user->participatingElections()
                ->where('is_published', true)
                ->withPivot('approval_status', 'rejection_reason')
                ->get();
            
            return view('voter.election-selection', compact('user', 'elections'));
        }
        
        // Show verification form (for first time or adding new elections)
        return view('voter.verification', compact('user'));
    }

    /**
     * Submit verification data
     */
    public function store(Request $request)
    {
        $request->validate([
            'invite_code' => ['required', 'string', 'max:8'],
            'id_card' => ['required', 'image', 'max:2048'],
            'face_image' => ['required', 'string'], // Base64
        ], [
            'id_card.required' => 'Foto KTP wajib diunggah.',
            'id_card.image' => 'File KTP harus berupa gambar.',
            'id_card.max' => 'Ukuran file KTP maksimal 2MB.',
            'face_image.required' => 'Foto wajah wajib diambil.',
            'invite_code.required' => 'Kode undangan wajib diisi.',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Validate invite code
        $election = Election::where('access_code', strtoupper($request->invite_code))
            ->where('is_published', true)
            ->first();

        if (!$election) {
            return back()->withErrors([
                'invite_code' => 'Kode undangan tidak valid atau pemilu belum dipublikasikan.'
            ])->withInput();
        }

        // Handle ID card upload
        $idCardPath = null;
        if ($request->hasFile('id_card')) {
            // Delete old ID card if exists
            if ($user->id_card) {
                Storage::disk('public')->delete($user->id_card);
            }
            $idCardPath = $request->file('id_card')->store('id-cards', 'public');
        }

        // Handle face image (base64 from camera)
        $facePhotoPath = null;
        if ($request->filled('face_image')) {
            $imageData = $request->face_image;
            
            if (strpos($imageData, 'data:image') === 0) {
                $imageData = substr($imageData, strpos($imageData, ',') + 1);
            }
            
            $imageData = base64_decode($imageData);
            $fileName = 'face_' . time() . '_' . uniqid() . '.jpg';
            $path = 'face-photos/' . $fileName;
            
            // Delete old face photo if exists
            if ($user->face_photo) {
                Storage::disk('public')->delete($user->face_photo);
            }
            
            Storage::disk('public')->put($path, $imageData);
            $facePhotoPath = $path;
        }

        // Update user with verification data
        $user->update([
            'id_card' => $idCardPath,
            'face_photo' => $facePhotoPath,
            'organization' => $election->title,
            'verification_status' => 'pending', // Set to pending, waiting admin approval
        ]);

        // Attach to election (not yet active until approved)
        $user->participatingElections()->syncWithoutDetaching([
            $election->id => [
                'access_code_used' => strtoupper($request->invite_code),
                'joined_at' => now(),
                'approval_status' => 'pending',
            ],
        ]);

        return redirect()->route('voter.verification')
            ->with('success', '✓ Data verifikasi berhasil dikirim! Mohon tunggu persetujuan dari admin.');
    }
}
