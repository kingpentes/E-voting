<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Election;
use App\Models\ElectionRule;
use App\Models\ElectionSetting;
use App\Service\ElectionSyncStatusService;
use App\Service\ContractDeploymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ElectionController extends Controller
{
    /**
     * Display a listing of the resource (manage elections).
     */
    public function index()
    {
        // Get only elections created by current organizer
        $elections = Election::forOrganizer(Auth::id())
            ->with(['rules', 'settings', 'candidates'])
            ->latest()
            ->get();

        return view('admin.elections.manage', compact('elections'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Show form to create a new election (allow multiple elections per organizer)
        return view('admin.elections.rules');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Allow creating multiple elections per organizer
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i'],
            'rules' => ['required', 'array', 'min:1'],
            'rules.*' => ['required', 'string'],
            'allow_abstain' => ['boolean'],
            'show_results_after_vote' => ['boolean'],
            'require_confirmation' => ['boolean'],
            'allow_vote_change' => ['boolean'],
            'max_votes_per_voter' => ['integer', 'min:1'],
        ]);

        DB::transaction(function () use ($validated, $request) {
            // Create Election
            $election = Election::create([
                'user_id' => Auth::id(),
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'start_date' => $validated['start_date'] ?? null,
                'end_date' => $validated['end_date'] ?? null,
                'start_time' => $validated['start_time'] ?? null,
                'end_time' => $validated['end_time'] ?? null,
                'status' => 'draft',
                'is_published' => false,
            ]);

            // Create Rules
            foreach ($validated['rules'] as $index => $rule) {
                ElectionRule::create([
                    'election_id' => $election->id,
                    'rule' => $rule,
                    'order' => $index + 1,
                ]);
            }

            // Create Settings
            ElectionSetting::create([
                'election_id' => $election->id,
                'allow_abstain' => $request->boolean('allow_abstain'),
                'show_results_after_vote' => $request->boolean('show_results_after_vote'),
                'require_confirmation' => $request->boolean('require_confirmation'),
                'allow_vote_change' => $request->boolean('allow_vote_change'),
                'max_votes_per_voter' => $validated['max_votes_per_voter'] ?? 1,
            ]);
        });

        return redirect()->route('admin.elections.manage')
            ->with('success', '✓ Pengaturan pemilu berhasil dibuat!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $election = Election::forOrganizer(Auth::id())
            ->with(['rules', 'settings'])
            ->findOrFail($id);

        // Prevent editing published election
        if ($election->is_published) {
            return redirect()->route('admin.elections.manage')
                ->with('error', '✗ Tidak dapat mengedit pemilu yang sudah dipublish. Unpublish terlebih dahulu jika perlu melakukan perubahan.');
        }

        return view('admin.elections.edit', compact('election'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $election = Election::forOrganizer(Auth::id())->findOrFail($id);

        // Prevent updating published election
        if ($election->is_published) {
            return redirect()->route('admin.elections.manage')
                ->with('error', '✗ Tidak dapat mengupdate pemilu yang sudah dipublish. Unpublish terlebih dahulu jika perlu melakukan perubahan.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i'],
            'rules' => ['required', 'array', 'min:1'],
            'rules.*' => ['required', 'string'],
            'allow_abstain' => ['boolean'],
            'show_results_after_vote' => ['boolean'],
            'require_confirmation' => ['boolean'],
            'allow_vote_change' => ['boolean'],
            'max_votes_per_voter' => ['integer', 'min:1'],
        ]);

        DB::transaction(function () use ($election, $validated, $request) {
            // Update Election
            $election->update([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'start_date' => $validated['start_date'] ?? null,
                'end_date' => $validated['end_date'] ?? null,
                'start_time' => $validated['start_time'] ?? null,
                'end_time' => $validated['end_time'] ?? null,
            ]);

            // Delete old rules and create new ones
            $election->rules()->delete();
            foreach ($validated['rules'] as $index => $rule) {
                ElectionRule::create([
                    'election_id' => $election->id,
                    'rule' => $rule,
                    'order' => $index + 1,
                ]);
            }

            // Update Settings
            $election->settings()->update([
                'allow_abstain' => $request->boolean('allow_abstain'),
                'show_results_after_vote' => $request->boolean('show_results_after_vote'),
                'require_confirmation' => $request->boolean('require_confirmation'),
                'allow_vote_change' => $request->boolean('allow_vote_change'),
                'max_votes_per_voter' => $validated['max_votes_per_voter'] ?? 1,
            ]);
        });

        return redirect()->route('admin.elections.manage')
            ->with('success', '✓ Pengaturan pemilu berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $election = Election::forOrganizer(Auth::id())->findOrFail($id);
        
        // Prevent deleting published election
        if ($election->is_published) {
            return redirect()->route('admin.elections.manage')
                ->with('error', '✗ Tidak dapat menghapus pemilu yang sudah dipublish. Unpublish terlebih dahulu jika ingin menghapus.');
        }
        
        $title = $election->title;
        $election->delete();

        return redirect()->route('admin.elections.manage')
            ->with('success', "✓ Pemilu '$title' berhasil dihapus!");
    }

    /**
     * Publish or unpublish an election.
     */
    public function togglePublish(string $id)
    {
        $election = Election::forOrganizer(Auth::id())->findOrFail($id);

        // Check if election has candidates before publishing
        if (!$election->is_published && $election->candidates()->count() === 0) {
            return redirect()->back()
                ->with('error', '✗ Tidak dapat mempublish pemilu tanpa kandidat. Tambahkan kandidat terlebih dahulu.');
        }

        // Once published, cannot unpublish - only can close
        if ($election->is_published) {
            return redirect()->back()
                ->with('error', '✗ Pemilu yang sudah dipublish tidak dapat di-unpublish. Gunakan tombol "Tutup Pemilu" untuk mengakhiri pemilu.');
        }

        // Publish the election
        $election->update([
            'is_published' => true,
            'status' => 'active',
        ]);

        return redirect()->back()
            ->with('success', "✓ Pemilu berhasil dipublish! Sekarang voter dapat mulai memberikan suara.");
    }

    /**
     * Close an election to show results.
     */
    public function closeElection(string $id)
    {
        $election = Election::forOrganizer(Auth::id())->findOrFail($id);

        // Can only close published elections
        if (!$election->is_published) {
            return redirect()->back()
                ->with('error', '✗ Hanya pemilu yang sudah dipublish yang dapat ditutup.');
        }

        // Close the election
        $election->update([
            'status' => 'closed',
        ]);

        return redirect()->back()
            ->with('success', "✓ Pemilu berhasil ditutup! Hasil voting sekarang dapat dilihat oleh voter.");
    }

    /**
     * Show on-chain vs DB sync status for the organizer's election.
     */
    public function syncStatus(ElectionSyncStatusService $syncService)
    {
        $election = Election::forOrganizer(Auth::id())
            ->with(['votes'])
            ->first();

        $status = null;
        if ($election) {
            $status = $syncService->getStatusForElection($election);
        }

        return view('admin.elections.sync-status', compact('election', 'status'));
    }

    /**
     * Deploy a dedicated smart contract for this election and store address.
     */
    public function deployContract(string $id, ContractDeploymentService $deploymentService)
    {
        $election = Election::forOrganizer(Auth::id())->findOrFail($id);

        // Optional: prevent deploying if election already has a contract
        if ($election->contract_address) {
            return redirect()->back()
                ->with('error', '✗ Kontrak untuk pemilu ini sudah tersedia.');
        }

        try {
            $result = $deploymentService->deploy();
            $election->contract_address = $result['address'] ?? null;
            // Reuse global ABI path setting if present
            $election->contract_abi_path = config('services.blockchain.contract_abi_path')
                ?? env('CONTRACT_ABI_PATH');
            $election->save();

            return redirect()->back()->with('success', '✓ Smart contract berhasil dideploy untuk pemilu ini. Alamat: ' . $election->contract_address);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', '✗ Gagal deploy smart contract: ' . $e->getMessage());
        }
    }
}
