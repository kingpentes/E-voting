<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\CandidateMission;
use App\Models\Election;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CandidateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get all candidates from organizer's elections
        $elections = Election::forOrganizer(Auth::id())->pluck('id');
        $candidates = Candidate::whereIn('election_id', $elections)
            ->with(['election', 'missions'])
            ->withCount('votes') // Add vote count
            ->orderBy('election_id')
            ->orderBy('number')
            ->get();

        return view('admin.candidates.manage', compact('candidates'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get organizer's elections
        $elections = Election::forOrganizer(Auth::id())->get();
        
        if ($elections->isEmpty()) {
            return redirect()->route('admin.elections.create')
                ->with('error', '✗ Buat pemilu terlebih dahulu sebelum menambahkan kandidat.');
        }

        return view('admin.candidates.create', compact('elections'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'election_id' => ['required', 'exists:elections,id'],
            'number' => ['required', 'integer', 'min:1'],
            'name' => ['required', 'string', 'max:255'],
            'photo' => ['nullable', 'image', 'max:2048'], // Max 2MB
            'visi' => ['required', 'string'],
            'misi' => ['required', 'array', 'min:1'],
            'misi.*' => ['required', 'string'],
        ]);

        // Verify election belongs to current organizer
        $election = Election::forOrganizer(Auth::id())->findOrFail($validated['election_id']);

        // Prevent adding candidate to published election
        if ($election->is_published) {
            return redirect()->back()
                ->with('error', '✗ Tidak dapat menambah kandidat ke pemilu yang sudah dipublish.');
        }

        // Check if candidate number already exists in this election
        $exists = Candidate::where('election_id', $election->id)
            ->where('number', $validated['number'])
            ->exists();
        
        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['number' => "Nomor urut {$validated['number']} sudah digunakan di pemilu ini."]);
        }

        DB::transaction(function () use ($validated, $request) {
            // Handle photo upload
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('candidate-photos', 'public');
            }

            // Create Candidate
            $candidate = Candidate::create([
                'election_id' => $validated['election_id'],
                'number' => $validated['number'],
                'name' => $validated['name'],
                'photo' => $photoPath,
                'visi' => $validated['visi'],
            ]);

            // Create Missions
            foreach ($validated['misi'] as $index => $misi) {
                CandidateMission::create([
                    'candidate_id' => $candidate->id,
                    'mission' => $misi,
                    'order' => $index + 1,
                ]);
            }
        });

        return redirect()->route('admin.candidates.manage')
            ->with('success', '✓ Kandidat berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $candidate = Candidate::with(['missions', 'election'])->findOrFail($id);
        
        // Verify candidate's election belongs to current organizer
        if ($candidate->election->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke kandidat ini.');
        }

        // Prevent editing candidate from published election
        if ($candidate->election->is_published) {
            return redirect()->route('admin.candidates.manage')
                ->with('error', '✗ Tidak dapat mengedit kandidat dari pemilu yang sudah dipublish.');
        }

        $elections = Election::forOrganizer(Auth::id())->get();

        return view('admin.candidates.edit', compact('candidate', 'elections'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $candidate = Candidate::with('election')->findOrFail($id);
        
        // Verify candidate's election belongs to current organizer
        if ($candidate->election->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke kandidat ini.');
        }

        // Prevent updating candidate from published election
        if ($candidate->election->is_published) {
            return redirect()->route('admin.candidates.manage')
                ->with('error', '✗ Tidak dapat mengupdate kandidat dari pemilu yang sudah dipublish.');
        }

        $validated = $request->validate([
            'election_id' => ['required', 'exists:elections,id'],
            'number' => ['required', 'integer', 'min:1'],
            'name' => ['required', 'string', 'max:255'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'visi' => ['required', 'string'],
            'misi' => ['required', 'array', 'min:1'],
            'misi.*' => ['required', 'string'],
        ]);

        // Verify new election belongs to current organizer
        $election = Election::forOrganizer(Auth::id())->findOrFail($validated['election_id']);

        // Check if candidate number already exists (except current candidate)
        $exists = Candidate::where('election_id', $election->id)
            ->where('number', $validated['number'])
            ->where('id', '!=', $candidate->id)
            ->exists();
        
        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['number' => "Nomor urut {$validated['number']} sudah digunakan di pemilu ini."]);
        }

        DB::transaction(function () use ($candidate, $validated, $request) {
            // Handle photo upload
            if ($request->hasFile('photo')) {
                // Delete old photo
                if ($candidate->photo) {
                    Storage::disk('public')->delete($candidate->photo);
                }
                $photoPath = $request->file('photo')->store('candidate-photos', 'public');
                $candidate->photo = $photoPath;
            }

            // Update Candidate
            $candidate->update([
                'election_id' => $validated['election_id'],
                'number' => $validated['number'],
                'name' => $validated['name'],
                'visi' => $validated['visi'],
            ]);

            // Delete old missions and create new ones
            $candidate->missions()->delete();
            foreach ($validated['misi'] as $index => $misi) {
                CandidateMission::create([
                    'candidate_id' => $candidate->id,
                    'mission' => $misi,
                    'order' => $index + 1,
                ]);
            }
        });

        return redirect()->route('admin.candidates.manage')
            ->with('success', '✓ Kandidat berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $candidate = Candidate::with('election')->findOrFail($id);
        
        // Verify candidate's election belongs to current organizer
        if ($candidate->election->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke kandidat ini.');
        }

        // Prevent deleting candidate from published election
        if ($candidate->election->is_published) {
            return redirect()->route('admin.candidates.manage')
                ->with('error', '✗ Tidak dapat menghapus kandidat dari pemilu yang sudah dipublish.');
        }

        $name = $candidate->name;
        
        // Delete photo if exists
        if ($candidate->photo) {
            Storage::disk('public')->delete($candidate->photo);
        }

        $candidate->delete();

        return redirect()->route('admin.candidates.manage')
            ->with('success', "✓ Kandidat '$name' berhasil dihapus!");
    }
}
