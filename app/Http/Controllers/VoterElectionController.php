<?php

namespace App\Http\Controllers;

use App\Models\Election;
use App\Models\Candidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoterElectionController extends Controller
{
    /**
     * Show election by access code
     */
    public function show(string $code)
    {
        // Find election by access code
        $election = Election::with(['candidates.missions', 'rules', 'settings'])
            ->where('access_code', strtoupper($code))
            ->where('is_published', true)
            ->firstOrFail();

        // Get all candidates for this election
        $candidates = $election->candidates()
            ->with('missions')
            ->orderBy('number')
            ->get();

        // Check if user already voted
        $hasVoted = false;
        if (Auth::check()) {
            $hasVoted = Auth::user()->hasVotedIn($election->id);
        }

        return view('voter.election', compact('election', 'candidates', 'hasVoted'));
    }

    /**
     * Show candidate detail
     */
    public function candidateDetail(string $code, int $candidateId)
    {
        // Find election by access code
        $election = Election::where('access_code', strtoupper($code))
            ->where('is_published', true)
            ->firstOrFail();

        // Get candidate with missions
        $candidate = Candidate::with('missions')
            ->where('election_id', $election->id)
            ->where('id', $candidateId)
            ->firstOrFail();

        return view('voter.candidate-detail', compact('election', 'candidate'));
    }

    /**
     * Submit vote
     */
    public function vote(Request $request, string $code)
    {
        $election = Election::where('access_code', strtoupper($code))
            ->where('is_published', true)
            ->firstOrFail();

        // Check if user already voted
        if (Auth::user()->hasVotedIn($election->id)) {
            return back()->with('error', 'Anda sudah memberikan suara di pemilu ini.');
        }

        $validated = $request->validate([
            'candidate_id' => ['required', 'exists:candidates,id'],
        ]);

        // Verify candidate belongs to this election
        $candidate = Candidate::where('id', $validated['candidate_id'])
            ->where('election_id', $election->id)
            ->firstOrFail();

        // Create vote
        Auth::user()->votes()->create([
            'election_id' => $election->id,
            'candidate_id' => $candidate->id,
        ]);

        return redirect()->route('voter.election', ['code' => $code])
            ->with('success', '✓ Suara Anda berhasil dicatat! Terima kasih telah berpartisipasi.');
    }
}
