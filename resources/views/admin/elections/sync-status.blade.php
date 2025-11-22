<x-admin-layout title="Status Sinkronisasi Pemilu">
    <x-admin-sidebar active="sync-status" />

    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white shadow-sm z-10">
            <div class="px-8 py-6 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Status Sinkronisasi Pemilu</h1>
                    <p class="text-gray-600 mt-1">Perbandingan jumlah suara di database vs blockchain</p>
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-8">
            @if($elections->isEmpty())
                <div class="bg-white rounded-2xl shadow-md p-8 text-center">
                    <h3 class="text-2xl font-bold text-gray-700 mb-2">Belum Ada Pemilu</h3>
                    <p class="text-gray-500">Buat pemilu terlebih dahulu untuk melihat status sinkronisasi.</p>
                </div>
            @else
                <div class="space-y-6">
                    @foreach($elections as $election)
                    @php
                        $status = $statuses[$election->id] ?? null;
                    @endphp
                    <div class="bg-white rounded-2xl shadow-md p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-2">{{ $election->title }}</h2>
                        <p class="text-gray-600 mb-4">ID Pemilu: {{ $election->id }}</p>

                        @if(!$status || !$status['has_contract'])
                            <div class="p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded">
                                <p class="text-yellow-800 font-medium">Smart contract belum dideploy untuk pemilu ini.</p>
                            </div>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div class="p-4 bg-gray-50 rounded-xl border">
                                    <p class="text-sm text-gray-500">Suara di Database</p>
                                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ $status['db_votes'] }}</p>
                                </div>
                                <div class="p-4 bg-gray-50 rounded-xl border">
                                    <p class="text-sm text-gray-500">Suara di Blockchain</p>
                                    <p class="mt-2 text-3xl font-bold text-gray-900">
                                        {{ $status['onchain_votes'] === null ? '-' : $status['onchain_votes'] }}
                                    </p>
                                </div>
                                <div class="p-4 rounded-xl border {{ $status['is_synced'] ? 'bg-green-50 border-green-400' : 'bg-red-50 border-red-400' }}">
                                    <p class="text-sm {{ $status['is_synced'] ? 'text-green-600' : 'text-red-600' }}">Status Sinkronisasi</p>
                                    <p class="mt-2 text-xl font-bold {{ $status['is_synced'] ? 'text-green-700' : 'text-red-700' }}">
                                        @if($status['is_synced'] === null)
                                            Tidak Diketahui
                                        @elseif($status['is_synced'])
                                            Sinkron
                                        @else
                                            Tidak Sinkron
                                        @endif
                                    </p>
                                </div>
                            </div>

                            @if($status['error'])
                                <div class="p-4 bg-red-50 border-l-4 border-red-400 rounded">
                                    <p class="text-red-800 font-mono text-xs break-all">{{ $status['error'] }}</p>
                                </div>
                            @endif
                        @endif
                    </div>
                    @endforeach
                </div>
            @endif
        </main>
    </div>
</x-admin-layout>
