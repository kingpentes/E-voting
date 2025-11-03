<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Service\VoteOnChainService;
use App\Service\BlockchainContractService;

class VoteController extends Controller
{
    public function store(Request $request, VoteOnChainService $onchain): JsonResponse
    {
        $data = $request->validate([
            'electionId' => 'required|string|max:128',
            'voterId' => 'required|string|max:128',
            'choice' => 'required|string|max:4096',
        ]);

        $tx = $onchain->submit($data['electionId'], $data['voterId'], $data['choice']);

        return response()->json([
            'status' => 'ok',
            'tx' => $tx,
            'electionId' => $data['electionId'],
            'voterId' => $data['voterId'],
        ]);
    }

    public function count(Request $request, BlockchainContractService $contract): JsonResponse
    {
        $request->validate(['electionId' => 'required|string|max:128']);
        $count = $contract->getVoteCount($request->string('electionId'));
        return response()->json(['electionId' => $request->string('electionId'), 'count' => $count]);
    }

    public function show(string $electionId, int $index, BlockchainContractService $contract): JsonResponse
    {
        if ($electionId === '' || strlen($electionId) > 128) {
            return response()->json(['message' => 'Invalid electionId'], 422);
        }
        if ($index < 0) {
            return response()->json(['message' => 'Index must be >= 0'], 422);
        }
        $vote = $contract->getVote($electionId, $index);
        return response()->json(['electionId' => $electionId, 'index' => $index, 'vote' => $vote]);
    }
}
