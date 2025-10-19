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
                    <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-8 py-6">
                        <div class="flex items-center space-x-6">
                            <div class="flex-shrink-0">
                                @if($voter->face_photo)
                                    <img class="h-24 w-24 rounded-full object-cover border-4 border-white shadow-lg" 
                                         src="{{ asset('storage/' . $voter->face_photo) }}" 
                                         alt="{{ $voter->name }}">
                                @else
                                    <div class="h-24 w-24 rounded-full bg-white flex items-center justify-center text-purple-600 font-bold text-3xl shadow-lg">
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
                                <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wider mb-2">ID Number</h3>
                                <p class="text-lg font-mono text-gray-900">{{ $voter->id_number ?? '-' }}</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wider mb-2">Organisasi</h3>
                                <p class="text-lg text-gray-900">{{ $voter->organization ?? '-' }}</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wider mb-2">Terdaftar Sejak</h3>
                                <p class="text-lg text-gray-900">{{ $voter->created_at->format('d F Y, H:i') }}</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wider mb-2">Status di Pemilu Saya</h3>
                                @if($election && $voter->hasVotedIn($election->id))
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Sudah Voting
                                    </span>
                                @elseif($election && $voter->participatingElections->contains($election->id))
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Belum Voting
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-gray-100 text-gray-800">
                                        Bukan Peserta
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Participating Elections -->
                <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                    <div class="px-8 py-4 border-b bg-gray-50">
                        <h3 class="text-xl font-bold text-gray-900">Pemilu yang Diikuti</h3>
                    </div>
                    <div class="p-8">
                        @if($voter->participatingElections->isEmpty())
                            <div class="text-center py-8">
                                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="text-gray-500">Belum mengikuti pemilu apapun</p>
                            </div>
                        @else
                            <div class="space-y-4">
                                @foreach($voter->participatingElections as $election)
                                <div class="border border-gray-200 rounded-xl p-4 hover:shadow-md transition-shadow">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h4 class="font-bold text-gray-900">{{ $election->title }}</h4>
                                            <p class="text-sm text-gray-600 mt-1">
                                                Bergabung: {{ \Carbon\Carbon::parse($election->pivot->joined_at)->format('d M Y, H:i') }}
                                            </p>
                                            <p class="text-xs text-gray-500 mt-1 font-mono">
                                                Access Code: {{ $election->pivot->access_code_used }}
                                            </p>
                                        </div>
                                        <div>
                                            @if($voter->hasVotedIn($election->id))
                                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                                                    ✓ Sudah Vote
                                                </span>
                                            @else
                                                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-semibold">
                                                    ⏳ Belum Vote
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </main>
    </div>
</x-admin-layout>
