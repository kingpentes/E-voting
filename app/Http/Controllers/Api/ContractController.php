<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Service\ContractDeploymentService;
use Illuminate\Http\JsonResponse;

class ContractController extends Controller
{
    public function deploy(ContractDeploymentService $service): JsonResponse
    {
        $result = $service->deploy();
        return response()->json([
            'status' => 'ok',
            'address' => $result['address'] ?? null,
            'log' => $result['log'] ?? '',
        ]);
    }
}
