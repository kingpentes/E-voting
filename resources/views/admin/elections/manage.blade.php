<x-admin-layout title="Kelola Judul & Peraturan">
    <x-admin-sidebar active="rules" />
    
    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white shadow-sm z-10">
            <div class="px-8 py-6 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Kelola Judul & Peraturan</h1>
                    <p class="text-gray-600 mt-1">{{ $elections->count() }} pemilu Anda</p>
                </div>
                <a href="{{ route('admin.elections.create') }}" 
                   class="px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold rounded-xl">
                    <span>+ Buat Pemilu Baru</span>
                </a>
            </div>
        </header>
        
        <main class="flex-1 overflow-y-auto p-8">
            @if(session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
                    <p class="text-green-800 font-medium">{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                    <p class="text-red-800 font-medium">{{ session('error') }}</p>
                </div>
            @endif

            @if($elections->isEmpty())
                <div class="bg-white rounded-2xl shadow-md p-12 text-center">
                    <h3 class="text-2xl font-bold text-gray-700 mb-2">Belum Ada Pemilu</h3>
                    <p class="text-gray-500 mb-6">Buat pemilu pertama Anda untuk memulai</p>
                </div>
            @else
                <div class="space-y-6">
                    @foreach($elections as $election)
                    <div class="bg-white rounded-2xl shadow-md overflow-hidden {{ $election->is_published ? 'border-2 border-green-500' : '' }}">
                        <div class="bg-gradient-to-r {{ $election->is_published ? 'from-green-50 to-emerald-50' : 'from-gray-50 to-gray-100' }} px-8 py-4 border-b">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    @if($election->is_published)
                                        <span class="px-4 py-1.5 bg-green-500 text-white text-sm font-bold rounded-full">AKTIF</span>
                                    @else
                                        <span class="px-4 py-1.5 bg-gray-400 text-white text-sm font-bold rounded-full">DRAFT</span>
                                    @endif
                                    <h2 class="text-2xl font-bold text-gray-900">{{ $election->title }}</h2>
                                </div>
                                <div class="flex space-x-2">
                                    <form action="{{ route('admin.elections.toggle-publish', $election->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-5 py-2.5 {{ $election->is_published ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-green-600 hover:bg-green-700' }} text-white font-semibold rounded-lg transition-colors">
                                            {{ $election->is_published ? 'Unpublish' : 'Publish' }}
                                        </button>
                                    </form>
                                    
                                    @if($election->is_published)
                                        <button disabled class="px-5 py-2.5 bg-gray-300 text-gray-500 font-semibold rounded-lg cursor-not-allowed" title="Tidak dapat edit pemilu yang sudah dipublish">
                                            Edit
                                        </button>
                                        <button disabled class="px-5 py-2.5 bg-gray-300 text-gray-500 font-semibold rounded-lg cursor-not-allowed" title="Tidak dapat hapus pemilu yang sudah dipublish">
                                            Hapus
                                        </button>
                                    @else
                                        <a href="{{ route('admin.elections.edit', $election->id) }}" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors">
                                            Edit
                                        </a>
                                        <form action="{{ route('admin.elections.delete', $election->id) }}" method="POST" 
                                              onsubmit="return confirm('Yakin hapus {{ $election->title }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors">
                                                Hapus
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="flex space-x-6 mt-3 text-sm">
                                <span class="text-gray-700">{{ $election->candidates_count }} Kandidat</span>
                                <span class="text-gray-700">{{ $election->votes_count }} Suara</span>
                            </div>
                        </div>
                        
                        <div class="p-8">
                            @if($election->description)
                                <p class="text-gray-700 mb-4">{{ $election->description }}</p>
                            @endif
                            
                            @if($election->start_date)
                                <div class="mb-4">
                                    <strong>Periode:</strong> 
                                    {{ $election->start_date->format('d M Y') }} - {{ $election->end_date->format('d M Y') }}
                                </div>
                            @endif
                            
                            <div class="mb-4">
                                <h3 class="font-bold text-gray-900 mb-2">Peraturan:</h3>
                                <ul class="space-y-1">
                                    @foreach($election->rules as $rule)
                                        <li class="flex space-x-2">
                                            <span class="text-blue-600"></span>
                                            <span>{{ $rule->rule }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            
                            @if($election->settings)
                            <div class="mb-4">
                                <h3 class="font-bold text-gray-900 mb-2">Pengaturan:</h3>
                                <div class="flex flex-wrap gap-2">
                                    @if($election->settings->allow_abstain)
                                        <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">Izinkan Golput</span>
                                    @endif
                                    @if($election->settings->show_results_after_vote)
                                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">Tampilkan Hasil</span>
                                    @endif
                                    @if($election->settings->require_confirmation)
                                        <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm">Butuh Konfirmasi</span>
                                    @endif
                                    <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm">Max {{ $election->settings->max_votes_per_voter }} suara</span>
                                </div>
                            </div>
                            @endif
                            
                            <div class="mt-4 p-4 bg-gradient-to-r from-purple-50 to-pink-50 border-2 border-purple-200 rounded-xl">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="text-sm font-bold text-gray-700 mb-1">Kode Akses Pemilu</h3>
                                        <p class="text-xs text-gray-500">Bagikan kode ini kepada pemilih</p>
                                    </div>
                                    <code class="px-4 py-2 bg-white border-2 border-purple-300 rounded-lg font-mono text-xl font-bold text-purple-900">{{ $election->access_code }}</code>
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
