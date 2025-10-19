<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Election;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoterController extends Controller
{
    /**
     * Display a listing of voters
     */
    public function index(Request $request)
    {
        // Get organizer's election
        $election = Election::forOrganizer(Auth::id())->first();
        
        // If no election, show empty state
        if (!$election) {
            $voters = collect();
            $stats = [
                'total_voters' => 0,
                'voted' => 0,
                'not_voted' => 0,
            ];
            return view('admin.voters.index', compact('voters', 'election', 'stats'));
        }
        
        // Default: Only show voters who joined this election (using invite code)
        $query = User::where('role', 'voter')
            ->whereHas('participatingElections', function($q) use ($election) {
                $q->where('election_id', $election->id);
            })
            ->with(['participatingElections' => function($q) use ($election) {
                $q->where('election_id', $election->id);
            }])
            ->withCount('votes')
            ->orderBy('created_at', 'desc');
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('id_number', 'like', "%{$search}%")
                  ->orWhere('organization', 'like', "%{$search}%");
            });
        }
        
        $voters = $query->paginate(20);
        
        // Get statistics
        $stats = [
            'total_voters' => $election->participants()->count(),
            'voted' => $election->votes()->distinct('voter_id')->count(),
            'not_voted' => $election->participants()->count() - $election->votes()->distinct('voter_id')->count(),
        ];
        
        return view('admin.voters.index', compact('voters', 'election', 'stats'));
    }
    
    /**
     * Show voter detail
     */
    public function show(string $id)
    {
        $voter = User::where('role', 'voter')
            ->with(['participatingElections'])
            ->findOrFail($id);
            
        $election = Election::forOrganizer(Auth::id())->first();
        
        return view('admin.voters.show', compact('voter', 'election'));
    }
}
