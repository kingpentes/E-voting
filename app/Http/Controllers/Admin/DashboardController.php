<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Election;
use App\Service\VoteOnChainService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request, VoteOnChainService $onchain)
    {
        $user = Auth::user();

        // Get all elections for this organizer so dashboard can select which election to show
        $elections = Election::forOrganizer(Auth::id())
            ->with(['candidates', 'votes'])
            ->latest()
            ->get();

        // Determine selected election from query param or default to first
        $selectedId = (int) $request->get('election_id', 0);
        $election = $selectedId ? $elections->firstWhere('id', $selectedId) : $elections->first();
        
        // Initialize stats
        $stats = [
            'total_voters' => 0,
            'voted' => 0,
            'not_voted' => 0,
            'participation_rate' => 0,
        ];
        
        $candidateStats = collect([]);
        $usingBlockchain = false;
        
        if ($election) {
            // Get all voters who joined this election
            $totalVoters = $election->participants()->count();
            
            // Get voters who already voted (distinct voter_id)
            $votedCount = $election->votes()->distinct('voter_id')->count();
            
            // Calculate not voted
            $notVotedCount = $totalVoters - $votedCount;
            
            // Calculate participation rate
            $participationRate = $totalVoters > 0 ? round(($votedCount / $totalVoters) * 100, 1) : 0;
            
            $stats = [
                'total_voters' => $totalVoters,
                'voted' => $votedCount,
                'not_voted' => $notVotedCount,
                'participation_rate' => $participationRate,
            ];
            
            // Get candidate statistics
            // Try to get from blockchain if available and election is closed
            if ($election->contract_address && $election->status === 'closed') {
                try {
                    $blockchainResults = $onchain->getElectionResults($election);
                    $usingBlockchain = true;
                    
                    $candidateStats = $election->candidates->map(function ($candidate) use ($blockchainResults) {
                        return [
                            'name' => $candidate->name,
                            'votes' => $blockchainResults[$candidate->id] ?? 0,
                        ];
                    })->sortByDesc('votes')->values();
                    
                    Log::info('Dashboard results loaded from blockchain', [
                        'election_id' => $election->id,
                    ]);
                } catch (\Throwable $e) {
                    Log::error('Failed to load dashboard results from blockchain', [
                        'election_id' => $election->id,
                        'error' => $e->getMessage(),
                    ]);
                    $usingBlockchain = false;
                }
            }
            
            // Fallback to database if blockchain not available
            if (!$usingBlockchain) {
                $candidates = $election->candidates()
                    ->withCount('votes')
                    ->orderBy('votes_count', 'desc')
                    ->get();
                
                $candidateStats = $candidates->map(function ($candidate) {
                    return [
                        'name' => $candidate->name,
                        'votes' => $candidate->votes_count,
                    ];
                });
            }
        }

        return view('admin.dashboard', compact('user', 'election', 'stats', 'candidateStats', 'elections', 'usingBlockchain'));
    }
}
