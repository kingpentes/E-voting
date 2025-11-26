<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Election;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoterApprovalController extends Controller
{
    /**
     * Display list of voters pending approval
     */
    public function index(Request $request)
    {
        // Get organizer's elections
        $elections = Election::forOrganizer(Auth::id())->get();
        $electionIds = $elections->pluck('id');

        // Get all election-user combinations
        $query = \Illuminate\Support\Facades\DB::table('election_user')
            ->join('users', 'election_user.user_id', '=', 'users.id')
            ->join('elections', 'election_user.election_id', '=', 'elections.id')
            ->whereIn('election_user.election_id', $electionIds)
            ->where('users.role', 'voter')
            ->select(
                'election_user.id as pivot_id',
                'users.id as user_id',
                'users.name',
                'users.email',
                'users.id_card',
                'users.face_photo',
                'users.verification_status',
                'election_user.approval_status',
                'election_user.rejection_reason as pivot_rejection_reason',
                'elections.id as election_id',
                'elections.title as election_title',
                'election_user.access_code_used',
                'election_user.joined_at'
            );

        // Filter by status if provided
        if ($request->filled('status')) {
            $query->where('election_user.approval_status', $request->status);
        }

        $voters = $query->latest('election_user.joined_at')->paginate(20);

        return view('admin.voters.approval', compact('voters', 'elections'));
    }

    /**
     * Approve voter verification for specific election
     */
    public function approve($pivotId)
    {
        // Update election_user pivot table
        $updated = \Illuminate\Support\Facades\DB::table('election_user')
            ->where('id', $pivotId)
            ->update([
                'approval_status' => 'approved',
                'approved_at' => now(),
                'approved_by' => Auth::id(),
                'rejection_reason' => null,
            ]);

        if (!$updated) {
            return back()->with('error', 'Data tidak ditemukan.');
        }

        // Get voter and election info
        $pivotData = \Illuminate\Support\Facades\DB::table('election_user')
            ->join('users', 'election_user.user_id', '=', 'users.id')
            ->join('elections', 'election_user.election_id', '=', 'elections.id')
            ->where('election_user.id', $pivotId)
            ->select('users.name', 'users.id as user_id', 'elections.title')
            ->first();

        // Update user verification_status to approved if not already
        if ($pivotData) {
            User::where('id', $pivotData->user_id)->update([
                'verification_status' => 'approved',
                'verified_at' => now(),
                'verified_by' => Auth::id(),
            ]);
        }

        return back()->with('success', "✓ Voter {$pivotData->name} untuk pemilu '{$pivotData->title}' berhasil disetujui!");
    }

    /**
     * Reject voter verification for specific election
     */
    public function reject(Request $request, $pivotId)
    {
        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ]);

        // Get voter and election info
        $pivotData = \Illuminate\Support\Facades\DB::table('election_user')
            ->join('users', 'election_user.user_id', '=', 'users.id')
            ->join('elections', 'election_user.election_id', '=', 'elections.id')
            ->where('election_user.id', $pivotId)
            ->select('users.name', 'elections.title')
            ->first();

        if (!$pivotData) {
            return back()->with('error', 'Data tidak ditemukan.');
        }

        // Update election_user pivot table
        \Illuminate\Support\Facades\DB::table('election_user')
            ->where('id', $pivotId)
            ->update([
                'approval_status' => 'rejected',
                'rejection_reason' => $request->rejection_reason,
                'approved_at' => null,
                'approved_by' => Auth::id(),
            ]);

        return back()->with('success', "Voter {$pivotData->name} untuk pemilu '{$pivotData->title}' ditolak. Alasan: {$request->rejection_reason}");
    }
}
