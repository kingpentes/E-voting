<x-admin-layout title="Kelola Kandidat">
    <x-admin-sidebar active="manage" />
    
    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white shadow-sm z-10">
            <div class="px-8 py-6 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Kelola Kandidat</h1>
                    <p class="text-gray-600 mt-1">{{ $candidates->count() }} kandidat dari pemilu Anda</p>
                </div>
                <a href="{{ route('admin.candidates.create') }}" 
                   class="px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold rounded-xl">
                    <span>+ Tambah Kandidat Baru</span>
                </a>
            </div>
        </header>
        
        <main class="flex-1 overflow-y-auto p-8">
            @if(session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
                    <p class="text-green-800 font-medium">{{ session('success') }}</p>
                </div>
            @endif

            @if($candidates->isEmpty())
                <div class="bg-white rounded-2xl shadow-md p-12 text-center">
                    <h3 class="text-2xl font-bold text-gray-700 mb-2">Belum Ada Kandidat</h3>
                    <p class="text-gray-500 mb-6">Mulai dengan menambahkan kandidat pertama Anda</p>
                </div>
            @else
                <div class="space-y-6">
                    @foreach($candidates as $candidate)
                    <div class="bg-white rounded-2xl shadow-md overflow-hidden {{ $candidate->election->is_published ? 'border-2 border-green-500' : '' }}">
                        <div class="bg-gray-50 px-8 py-4 border-b">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div class="w-16 h-16 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-full flex items-center justify-center font-bold text-2xl">
                                        {{ $candidate->number }}
                                    </div>
                                    <div>
                                        <h2 class="text-2xl font-bold text-gray-900">{{ $candidate->name }}</h2>
                                        <div class="flex items-center space-x-2">
                                            <p class="text-gray-500">{{ $candidate->election->title }}</p>
                                            @if($candidate->election->is_published)
                                                <span class="px-2 py-0.5 bg-green-500 text-white text-xs font-bold rounded-full">AKTIF</span>
                                            @endif
                                        </div>
                                        <div class="flex items-center space-x-4 mt-2">
                                            <div class="flex items-center space-x-2">
                                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span class="text-sm font-semibold text-purple-600">{{ $candidate->votes_count }} Suara</span>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                                </svg>
                                                <span class="text-sm font-semibold text-blue-600">{{ $candidate->missions->count() }} Misi</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    @if($candidate->election->is_published)
                                        <button disabled class="px-5 py-2.5 bg-gray-300 text-gray-500 font-semibold rounded-lg cursor-not-allowed" title="Tidak dapat edit kandidat dari pemilu yang sudah dipublish">
                                            Edit
                                        </button>
                                        <button disabled class="px-5 py-2.5 bg-gray-300 text-gray-500 font-semibold rounded-lg cursor-not-allowed" title="Tidak dapat hapus kandidat dari pemilu yang sudah dipublish">
                                            Hapus
                                        </button>
                                    @else
                                        <a href="{{ route('admin.candidates.edit', $candidate->id) }}" 
                                           class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors">
                                            Edit
                                        </a>
                                        <form action="{{ route('admin.candidates.destroy', $candidate->id) }}" method="POST" 
                                              onsubmit="return confirm('Yakin hapus {{ $candidate->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors">
                                                Hapus
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-8">
                            <!-- Vote Statistics -->
                            @php
                                $totalVotes = $candidate->election->votes()->count();
                                $percentage = $totalVotes > 0 ? round(($candidate->votes_count / $totalVotes) * 100, 1) : 0;
                            @endphp
                            
                            <div class="mb-6 p-4 bg-gradient-to-r from-purple-50 to-pink-50 border-2 border-purple-200 rounded-xl">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-semibold text-gray-700">Perolehan Suara</span>
                                    <span class="text-2xl font-bold text-purple-600">{{ $candidate->votes_count }} / {{ $totalVotes }}</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
                                    <div class="bg-gradient-to-r from-purple-600 to-pink-600 h-4 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                                </div>
                                <div class="mt-2 text-right">
                                    <span class="text-lg font-bold text-purple-700">{{ $percentage }}%</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-6">
                                <div>
                                    @if($candidate->photo)
                                        <img src="{{ asset('storage/' . $candidate->photo) }}" alt="{{ $candidate->name }}" class="w-full rounded-xl">
                                    @else
                                        <div class="w-full aspect-square bg-gray-200 rounded-xl"></div>
                                    @endif
                                </div>
                                
                                <div class="col-span-2">
                                    <div class="mb-6">
                                        <h3 class="font-bold text-gray-900 mb-2">Visi</h3>
                                        <p class="text-gray-700 bg-blue-50 p-4 rounded-lg">{{ $candidate->visi }}</p>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-gray-900 mb-2">Misi</h3>
                                        @if($candidate->missions->isEmpty())
                                            <p class="text-gray-500 italic">Belum ada misi</p>
                                        @else
                                            <ul class="space-y-2">
                                                @foreach($candidate->missions as $mission)
                                                    <li class="flex space-x-2 bg-purple-50 p-3 rounded-lg">
                                                        <span class="font-bold">{{ $loop->iteration }}.</span>
                                                        <p>{{ $mission->mission }}</p>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </main>
    </div>
</x-admin-layout>
