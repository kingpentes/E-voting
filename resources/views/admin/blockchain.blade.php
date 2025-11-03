<x-admin-layout title="Pengaturan Blockchain">
    <x-admin-sidebar active="blockchain" />

    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white shadow-sm z-10">
            <div class="px-8 py-6">
                <h1 class="text-3xl font-bold text-gray-900">Integrasi Blockchain</h1>
                <p class="text-gray-600 mt-1">Konfigurasi koneksi Quorum dan manajemen smart contract</p>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-8">
            @if(session('status'))
                <div class="mb-4 rounded-xl bg-green-50 border border-green-200 text-green-800 px-4 py-3">
                    {{ session('status') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 rounded-xl bg-red-50 border border-red-200 text-red-800 px-4 py-3">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-2xl shadow p-6">
                    <h2 class="text-xl font-semibold mb-4">Koneksi RPC</h2>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm text-gray-500">RPC URL</dt>
                            <dd class="text-gray-900 font-mono break-all">{{ $rpc ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">From Address</dt>
                            <dd class="text-gray-900 font-mono break-all">{{ $from ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Contract Address</dt>
                            <dd class="text-gray-900 font-mono break-all">{{ $address ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">ABI Path</dt>
                            <dd class="text-gray-900 font-mono break-all">{{ $abiPath ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="bg-white rounded-2xl shadow p-6">
                    <h2 class="text-xl font-semibold mb-4">Aksi</h2>
                    <p class="text-gray-600 mb-4">Deploy ulang smart contract jika diperlukan. Address baru akan tersimpan di .env.</p>
                    <form method="POST" action="{{ route('admin.blockchain.deploy') }}">
                        @csrf
                        <button type="submit" class="px-6 py-3 rounded-xl bg-gray-900 text-white font-medium hover:bg-gray-800 transition">
                            Deploy Smart Contract
                        </button>
                    </form>
                </div>
            </div>
        </main>
    </div>
</x-admin-layout>
