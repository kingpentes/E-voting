<?php

namespace App\Http\Controllers;

use App\Models\Election;
use App\Models\Candidate;
use App\Service\VoteOnChainService;
use App\Service\BlockchainResultService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class VoterElectionController extends Controller
{
    /**
     * Show election by access code
     */
    public function show(string $code, VoteOnChainService $onchain): View
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

        // Get results from blockchain if contract is deployed and election is closed
        $blockchainResults = [];
        $usingBlockchain = false;
        
        if ($election->status === 'closed') {
            // WAJIB: Smart contract harus ada untuk menampilkan hasil
            if (!$election->contract_address) {
                // Jika tidak ada contract, jangan tampilkan hasil
                Log::warning('Election closed without smart contract deployed', [
                    'election_id' => $election->id,
                ]);
                // Set flag bahwa tidak bisa tampilkan hasil
                $election->no_results_available = true;
            } else {
                try {
                    $blockchainResults = $onchain->getElectionResults($election);
                    $usingBlockchain = true;
                    
                    // Attach blockchain vote counts to candidates
                    foreach ($candidates as $candidate) {
                        $candidate->blockchain_vote_count = $blockchainResults[$candidate->id] ?? 0;
                    }
                    
                    Log::info('Election results loaded from blockchain', [
                        'election_id' => $election->id,
                        'results' => $blockchainResults,
                    ]);
                } catch (\Throwable $e) {
                    Log::error('Failed to load results from blockchain', [
                        'election_id' => $election->id,
                        'error' => $e->getMessage(),
                    ]);
                    // Jika error, tetap tidak tampilkan hasil
                    $election->no_results_available = true;
                }
            }
        }

        // Check if user already voted
        $hasVoted = false;
        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $hasVoted = $user->hasVotedIn($election->id);
        }

        return view('voter.election', compact('election', 'candidates', 'hasVoted', 'usingBlockchain'));
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
    public function vote(Request $request, string $code, VoteOnChainService $onchain): RedirectResponse
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

        // Check if election has started
        $now = now();
        if ($election->start_date && $now->lt($election->start_date)) {
            return back()->with('error', 'Pemilu belum dimulai. Voting akan dibuka pada ' . $election->start_date->format('d M Y H:i') . ' WIB.');
        }

        // Check if election has ended
        if ($election->end_date && $now->gt($election->end_date)) {
            return back()->with('error', 'Pemilu sudah berakhir pada ' . $election->end_date->format('d M Y H:i') . ' WIB.');
        }

        // Check if election is in active status
        if ($election->status !== 'active') {
            return back()->with('error', 'Pemilu tidak dalam status aktif. Status saat ini: ' . $election->status);
        }

        $validated = $request->validate([
            'candidate_id' => ['required', 'exists:candidates,id'],
        ]);

        // Verify candidate belongs to this election
        $candidate = Candidate::where('id', $validated['candidate_id'])
            ->where('election_id', $election->id)
            ->firstOrFail();

        // Check payment logic: First election free, subsequent $5
        // $previousVotes = $user->votes()->count();
        
        // Create vote in database first
        $vote = $user->votes()->create([
            'election_id' => $election->id,
            'candidate_id' => $candidate->id,
        ]);

        // Optionally mirror vote to blockchain if contract is deployed
        $blockchainSuccess = false;
        $blockchainError = null;
        
        if ($election->contract_address) {
            try {
                $txHash = $onchain->submit($election, (string) $user->id, (string) $candidate->id);
                $blockchainSuccess = true;
                
                // Optional: save tx hash to vote record
                $vote->update(['blockchain_tx_hash' => $txHash]);
                
                Log::info('Vote mirrored to blockchain', [
                    'vote_id' => $vote->id,
                    'election_id' => $election->id,
                    'tx_hash' => $txHash,
                ]);
            } catch (\Throwable $e) {
                // Log error tapi jangan gagalkan vote
                $blockchainError = $e->getMessage();
                Log::error('Failed to mirror vote to blockchain', [
                    'vote_id' => $vote->id,
                    'election_id' => $election->id,
                    'error' => $blockchainError,
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }

        $message = 'Suara Anda berhasil dicatat! Terima kasih telah berpartisipasi.';
        if ($election->contract_address && !$blockchainSuccess) {
            $message .= ' (Catatan: Vote tersimpan di database, namun belum tersinkronisasi ke blockchain)';
        }

        return redirect()->route('voter.election', ['code' => $code])
            ->with('success', $message);
    }
}
