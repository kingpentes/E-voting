<x-admin-layout title="Detail Voter">
    <x-admin-sidebar active="voters" />
    
    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white shadow-sm z-10">
            <div class="px-8 py-6 flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.voters.index') }}" 
                       class="flex items-center space-x-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        <span>Kembali</span>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Detail Voter</h1>
                        <p class="text-gray-600 mt-1">Informasi lengkap pemilih</p>
                    </div>
                </div>
            </div>
        </header>
        
        <main class="flex-1 overflow-y-auto p-8">
            <div class="max-w-5xl mx-auto space-y-6">
                <!-- Voter Profile Card -->
                <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-600 to-cyan-600 px-8 py-6">
                        <div class="flex items-center space-x-6">
                            <div class="flex-shrink-0">
                                @if($voter->face_photo)
                                    <img class="h-24 w-24 rounded-full object-cover border-4 border-white shadow-lg" 
                                         src="{{ asset('storage/' . $voter->face_photo) }}" 
                                         alt="{{ $voter->name }}">
                                @else
                                    <div class="h-24 w-24 rounded-full bg-white flex items-center justify-center text-blue-600 font-bold text-3xl shadow-lg">
                                        {{ strtoupper(substr($voter->name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <div class="text-white">
                                <h2 class="text-3xl font-bold">{{ $voter->name }}</h2>
                                <p class="text-purple-100 mt-1">{{ $voter->email }}</p>
                                <div class="flex items-center space-x-4 mt-3">
                                    <span class="px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-sm font-medium">
                                        🗳️ Voter
                                    </span>
                                    <span class="px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-sm font-medium">
                                        Total {{ $voter->votes->count() }} Vote
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wider mb-2">Foto KTP</h3>
                                @if($voter->id_card)
                                    <a href="{{ asset('storage/' . $voter->id_card) }}" target="_blank" class="block">
                                        <img src="{{ asset('storage/' . $voter->id_card) }}" 
                                             alt="ID Card" 
                                             class="w-full max-w-md rounded-lg border-2 border-gray-200 hover:border-purple-400 transition-colors shadow-sm hover:shadow-md cursor-pointer">
                                    </a>
                                @else
                                    <p class="text-gray-500 italic">Tidak ada foto KTP</p>
                                @endif
                            </div>
                            <div>
                                <div class="space-y-6">
                                    <div>
                                        <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wider mb-2">Organisasi</h3>
                                        <p class="text-lg text-gray-900">{{ $voter->organization ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wider mb-2">Terdaftar Sejak</h3>
                                        <p class="text-lg text-gray-900">{{ $voter->created_at->format('d F Y, H:i') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</x-admin-layout>
