<x-admin-layout title="Kelola Judul & Peraturan">
    <x-admin-sidebar active="rules" />
    
    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white shadow-sm z-10">
            <div class="px-8 py-6 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Kelola Judul & Peraturan</h1>
                    <p class="text-gray-600 mt-1">{{ $elections->count() }} pemilu Anda</p>
                </div>
                <!-- Selalu tampilkan tombol buat pemilu baru (izinkan multiple pemilu per organizer) -->
                <a href="{{ route('admin.elections.create') }}" 
                   class="px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold rounded-xl hover:from-purple-700 hover:to-pink-700 transition-all">
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
                                    @if($election->status === 'closed')
                                        <span class="px-3 py-1 bg-gray-500 text-white text-sm font-bold rounded-full">DITUTUP</span>
                                    @elseif($election->is_published)
                                        <span class="px-4 py-1.5 bg-green-500 text-white text-sm font-bold rounded-full">AKTIF</span>
                                    @else
                                        <span class="px-4 py-1.5 bg-gray-400 text-white text-sm font-bold rounded-full">DRAFT</span>
                                    @endif
                                    <h2 class="text-2xl font-bold text-gray-900">{{ $election->title }}</h2>
                                </div>
                                <div class="flex space-x-2">
                                    <!-- Publish Button (Only for Draft) -->
                                    @if(!$election->is_published && $election->candidates_count === 0)
                                        <button disabled class="px-5 py-2.5 bg-gray-300 text-gray-600 font-semibold rounded-lg cursor-not-allowed" title="Tambahkan kandidat terlebih dahulu untuk publish">
                                            Publish (Perlu Kandidat)
                                        </button>
                                    @elseif(!$election->is_published && !$election->contract_address)
                                        <button disabled class="px-5 py-2.5 bg-gray-300 text-gray-600 font-semibold rounded-lg cursor-not-allowed" title="Deploy smart contract terlebih dahulu untuk publish. Ini WAJIB agar semua vote tersimpan di blockchain!">
                                            Publish (Perlu Deploy Contract)
                                        </button>
                                    @elseif(!$election->is_published)
                                        <form action="{{ route('admin.elections.toggle-publish', $election->id) }}" method="POST" 
                                              onsubmit="return confirm('⚠️ PERINGATAN!\n\nSetelah dipublish, Anda TIDAK DAPAT:\n- Mengubah data pemilu\n- Mengedit atau menghapus kandidat\n- Unpublish pemilu\n\nAnda hanya dapat menutup pemilu untuk menampilkan hasil.\n\nApakah Anda yakin ingin mempublish pemilu ini?')">
                                            @csrf
                                            <button type="submit" class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors">
                                                Publish Pemilu
                                            </button>
                                        </form>
                                    @endif
                                    
                                    <!-- Close Election Button (Only for Active/Published) -->
                                    @if($election->is_published && $election->status !== 'closed')
                                        <form action="{{ route('admin.elections.close', $election->id) }}" method="POST"
                                              onsubmit="return confirm('Yakin ingin menutup pemilu ini?\n\nSetelah ditutup, hasil voting akan ditampilkan kepada voter.')">
                                            @csrf
                                            <button type="submit" class="px-5 py-2.5 bg-orange-600 hover:bg-orange-700 text-white font-semibold rounded-lg transition-colors">
                                                Tutup Pemilu
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Deploy Smart Contract Button -->
                                    <form action="{{ route('admin.elections.deploy-contract', $election->id) }}" method="POST"
                                          onsubmit="return confirm('Deploy smart contract khusus untuk pemilu ini?');">
                                        @csrf
                                        <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition-colors">
                                            {{ $election->contract_address ? 'Kontrak Terdeploy' : 'Deploy Smart Contract' }}
                                        </button>
                                    </form>
                                    
                                    <!-- Edit/Delete Buttons (Only for Draft or Closed cannot edit) -->
                                    @if($election->is_published || $election->status === 'closed')
                                        <button disabled class="px-5 py-2.5 bg-gray-300 text-gray-500 font-semibold rounded-lg cursor-not-allowed" title="Tidak dapat edit pemilu yang sudah dipublish atau ditutup">
                                            Edit
                                        </button>
                                        <button disabled class="px-5 py-2.5 bg-gray-300 text-gray-500 font-semibold rounded-lg cursor-not-allowed" title="Tidak dapat hapus pemilu yang sudah dipublish atau ditutup">
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
                                <span class="text-gray-700 {{ $election->candidates_count === 0 ? 'text-red-600 font-bold' : '' }}">
                                    {{ $election->candidates_count }} 
                                    @if($election->candidates_count === 0 && !$election->is_published)
                                        <span class="text-xs">(⚠️ Diperlukan untuk publish)</span>
                                    @endif
                                </span>
                                
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
                            
                            <!-- Pengaturan Lanjutan display removed -->
                            
                            <div class="mt-4 p-4 bg-gradient-to-r from-purple-50 to-pink-50 border-2 border-purple-200 rounded-xl">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="text-sm font-bold text-gray-700 mb-1">Kode Akses Pemilu</h3>
                                        <p class="text-xs text-gray-500">Bagikan kode ini kepada pemilih</p>
                                    </div>
                                    <code class="px-4 py-2 bg-white border-2 border-purple-300 rounded-lg font-mono text-xl font-bold text-purple-900">{{ $election->access_code }}</code>
                                </div>
                            </div>

                            @if($election->contract_address)
                                <div class="mt-4 p-4 bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-xl">
                                    <h3 class="text-sm font-bold text-gray-700 mb-1">Alamat Smart Contract</h3>
                                    <code class="block mt-1 px-3 py-2 bg-white border border-green-300 rounded-lg font-mono text-xs text-green-900 break-all">{{ $election->contract_address }}</code>
                                </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </main>
    </div>
</x-admin-layout>
