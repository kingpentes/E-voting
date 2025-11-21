<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Election;
use App\Service\VoteOnChainService;
use App\Service\BlockchainContractService;

class VoteController extends Controller
{
    public function store(Request $request, VoteOnChainService $onchain): JsonResponse
    {
        $data = $request->validate([
            'electionId' => 'required|integer',
            'voterId' => 'required|string|max:128',
            'choice' => 'required|string|max:4096',
        ]);

        $election = Election::findOrFail($data['electionId']);
        if (!$election->contract_address) {
            return response()->json([
                'status' => 'error',
                'message' => 'Smart contract belum dideploy untuk pemilu ini.',
            ], 422);
        }

        $tx = $onchain->submit($election, $data['voterId'], $data['choice']);

        return response()->json([
            'status' => 'ok',
            'tx' => $tx,
            'electionId' => $data['electionId'],
            'voterId' => $data['voterId'],
        ]);
    }

    public function count(Request $request): JsonResponse
    {
        $data = $request->validate(['electionId' => 'required|integer']);
        $election = Election::findOrFail($data['electionId']);
        if (!$election->contract_address) {
            return response()->json([
                'status' => 'error',
                'message' => 'Smart contract belum dideploy untuk pemilu ini.',
            ], 422);
        }

        $contract = new BlockchainContractService($election->contract_address, $election->contract_abi_path);
        $count = $contract->getVoteCount((string) $election->id);
        return response()->json(['electionId' => $election->id, 'count' => $count]);
    }

    public function show(int $electionId, int $index): JsonResponse
    {
        if ($electionId <= 0) {
            return response()->json(['message' => 'Invalid electionId'], 422);
        }
        if ($index < 0) {
            return response()->json(['message' => 'Index must be >= 0'], 422);
        }
        $election = Election::findOrFail($electionId);
        if (!$election->contract_address) {
            return response()->json([
                'status' => 'error',
                'message' => 'Smart contract belum dideploy untuk pemilu ini.',
            ], 422);
        }

        $contract = new BlockchainContractService($election->contract_address, $election->contract_abi_path);
        $vote = $contract->getVote((string) $election->id, $index);
        return response()->json(['electionId' => $election->id, 'index' => $index, 'vote' => $vote]);
    }
}
