<?php

namespace App\Http\Controllers;

use App\Models\Election;
use App\Models\Candidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class VoterElectionController extends Controller
{
    /**
     * Show election by access code
     */
    public function show(string $code): View
    {
        // Find election by access code
        $election = Election::with(['candidates.missions', 'rules', 'settings'])
            ->where('access_code', strtoupper($code))
            ->where('is_published', true)
            ->firstOrFail();

        // Get all candidates for this election
        $candidates = $election->candidates()
            ->with(['missions', 'votes'])
            ->orderBy('number')
            ->get();

        // Check if user already voted
        $hasVoted = false;
        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $hasVoted = $user->hasVotedIn($election->id);
        }

        return view('voter.election', compact('election', 'candidates', 'hasVoted'));
    }

    /**
     * Show candidate detail
     */
    public function candidateDetail(string $code, int $candidateId): View
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
    public function vote(Request $request, string $code): RedirectResponse
    {
        // Ensure user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login untuk memberikan suara.');
        }

        $election = Election::where('access_code', strtoupper($code))
            ->where('is_published', true)
            ->firstOrFail();

        // Check if user already voted
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user->hasVotedIn($election->id)) {
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
        $user->votes()->create([
            'election_id' => $election->id,
            'candidate_id' => $candidate->id,
        ]);

        return redirect()->route('voter.election', ['code' => $code])
            ->with('success', '✓ Suara Anda berhasil dicatat! Terima kasih telah berpartisipasi.');
    }
}
