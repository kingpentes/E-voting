<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Service\ContractDeploymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class BlockchainController extends Controller
{
    public function index()
    {
        $rpc = \env('BLOCKCHAIN_RPC');
        $from = \env('BLOCKCHAIN_FROM');
        $address = \env('CONTRACT_ADDRESS');
        $abiPath = \env('CONTRACT_ABI_PATH');
        return view('admin.blockchain', compact('rpc', 'from', 'address', 'abiPath'));
    }

    public function deploy(Request $request, ContractDeploymentService $service): RedirectResponse
    {
        try {
            $result = $service->deploy();
            Session::flash('status', 'Kontrak berhasil dideploy: ' . ($result['address'] ?? ''));
            return Redirect::route('admin.blockchain.index');
        } catch (\Throwable $e) {
            Session::flash('error', 'Gagal deploy kontrak: ' . $e->getMessage());
            return Redirect::route('admin.blockchain.index');
        }
    }
}
