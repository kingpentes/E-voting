<x-admin-layout title="Kelola Judul & Peraturan">
    <x-admin-sidebar active="rules" />
    
    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white shadow-sm z-10">
            <div class="px-8 py-6 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Kelola Judul & Peraturan</h1>
                    <p class="text-gray-600 mt-1">Lihat dan kelola pengaturan pemilu yang ada</p>
                </div>
                <a href="{{ route('admin.elections.rules.create') }}" 
                   class="px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold rounded-xl hover:from-purple-700 hover:to-pink-700 transition-all shadow-lg flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <span>Buat Pengaturan Baru</span>
                </a>
            </div>
        </header>
        
        <main class="flex-1 overflow-y-auto p-8">
            <div class="space-y-6">
                <!-- Election Settings Card 1 (Active) -->
                <div class="bg-white rounded-2xl shadow-md overflow-hidden border-2 border-green-500">
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-8 py-4 border-b border-green-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <span class="px-4 py-1.5 bg-green-500 text-white text-sm font-bold rounded-full">
                                    AKTIF
                                </span>
                                <h2 class="text-2xl font-bold text-gray-900">E-Voting 2025</h2>
                            </div>
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('admin.elections.rules.edit', 1) }}" 
                                   class="px-5 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all flex items-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    <span>Edit</span>
                                </a>
                                <button onclick="confirmDelete(1, 'E-Voting 2025')" 
                                        class="px-5 py-2.5 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-all flex items-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    <span>Hapus</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-8">
                        <!-- Description -->
                        <div class="mb-6">
                            <h3 class="text-sm font-semibold text-gray-500 uppercase mb-2">Deskripsi</h3>
                            <p class="text-gray-700 text-lg">
                                Sistem Pemilihan Elektronik untuk memilih pemimpin masa depan dengan transparan, aman, dan demokratis
                            </p>
                        </div>

                        <!-- Period -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div class="bg-purple-50 rounded-xl p-4">
                                <h3 class="text-sm font-semibold text-purple-700 mb-2 flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    Tanggal Mulai
                                </h3>
                                <p class="text-gray-900 font-bold text-lg">20 Oktober 2025, 08:00</p>
                            </div>
                            <div class="bg-pink-50 rounded-xl p-4">
                                <h3 class="text-sm font-semibold text-pink-700 mb-2 flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    Tanggal Berakhir
                                </h3>
                                <p class="text-gray-900 font-bold text-lg">25 Oktober 2025, 17:00</p>
                            </div>
                        </div>

                        <!-- Rules -->
                        <div class="mb-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Peraturan Pemilihan
                            </h3>
                            <div class="space-y-2">
                                <div class="flex items-start space-x-3 bg-gray-50 p-3 rounded-lg">
                                    <span class="flex-shrink-0 w-6 h-6 bg-purple-600 text-white rounded-full flex items-center justify-center text-xs font-bold">1</span>
                                    <p class="text-gray-700 pt-0.5">Setiap pemilih hanya dapat memberikan satu suara</p>
                                </div>
                                <div class="flex items-start space-x-3 bg-gray-50 p-3 rounded-lg">
                                    <span class="flex-shrink-0 w-6 h-6 bg-purple-600 text-white rounded-full flex items-center justify-center text-xs font-bold">2</span>
                                    <p class="text-gray-700 pt-0.5">Klik pada kartu kandidat untuk melihat detail visi dan misi</p>
                                </div>
                                <div class="flex items-start space-x-3 bg-gray-50 p-3 rounded-lg">
                                    <span class="flex-shrink-0 w-6 h-6 bg-purple-600 text-white rounded-full flex items-center justify-center text-xs font-bold">3</span>
                                    <p class="text-gray-700 pt-0.5">Pastikan pilihan Anda sudah tepat sebelum mengkonfirmasi</p>
                                </div>
                                <div class="flex items-start space-x-3 bg-gray-50 p-3 rounded-lg">
                                    <span class="flex-shrink-0 w-6 h-6 bg-purple-600 text-white rounded-full flex items-center justify-center text-xs font-bold">4</span>
                                    <p class="text-gray-700 pt-0.5">Hasil pemilihan akan diumumkan setelah periode voting berakhir</p>
                                </div>
                            </div>
                        </div>

                        <!-- Settings -->
                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Pengaturan Lanjutan
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="flex items-center justify-between bg-green-50 rounded-lg p-3">
                                    <span class="text-sm font-medium text-gray-700">Hasil Real-time</span>
                                    <span class="px-3 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-full">TIDAK</span>
                                </div>
                                <div class="flex items-center justify-between bg-green-50 rounded-lg p-3">
                                    <span class="text-sm font-medium text-gray-700">Perubahan Suara</span>
                                    <span class="px-3 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-full">TIDAK</span>
                                </div>
                                <div class="flex items-center justify-between bg-green-50 rounded-lg p-3">
                                    <span class="text-sm font-medium text-gray-700">Verifikasi Email</span>
                                    <span class="px-3 py-1 bg-green-500 text-white text-xs font-bold rounded-full">YA</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Election Settings Card 2 (Draft) -->
                <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                    <div class="bg-gray-50 px-8 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <span class="px-4 py-1.5 bg-gray-400 text-white text-sm font-bold rounded-full">
                                    DRAFT
                                </span>
                                <h2 class="text-2xl font-bold text-gray-900">Pemilu OSIS 2025</h2>
                            </div>
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('admin.elections.rules.edit', 2) }}" 
                                   class="px-5 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all flex items-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    <span>Edit</span>
                                </a>
                                <button onclick="confirmDelete(2, 'Pemilu OSIS 2025')" 
                                        class="px-5 py-2.5 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-all flex items-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    <span>Hapus</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-8">
                        <div class="mb-6">
                            <h3 class="text-sm font-semibold text-gray-500 uppercase mb-2">Deskripsi</h3>
                            <p class="text-gray-700 text-lg">
                                Pemilihan Ketua OSIS periode 2025-2026 untuk memimpin organisasi siswa
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div class="bg-purple-50 rounded-xl p-4">
                                <h3 class="text-sm font-semibold text-purple-700 mb-2">Tanggal Mulai</h3>
                                <p class="text-gray-900 font-bold text-lg">Belum ditentukan</p>
                            </div>
                            <div class="bg-pink-50 rounded-xl p-4">
                                <h3 class="text-sm font-semibold text-pink-700 mb-2">Tanggal Berakhir</h3>
                                <p class="text-gray-900 font-bold text-lg">Belum ditentukan</p>
                            </div>
                        </div>

                        <div class="text-center py-4 bg-gray-50 rounded-xl">
                            <p class="text-gray-500">Peraturan dan pengaturan lanjutan belum dikonfigurasi</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        function confirmDelete(id, title) {
            if (confirm(`Apakah Anda yakin ingin menghapus pengaturan pemilu "${title}"?\n\nTindakan ini tidak dapat dibatalkan.`)) {
                // Add delete logic here
                alert(`Pengaturan pemilu "${title}" berhasil dihapus!`);
                // In real app: form submit or fetch API call
                // window.location.href = '/admin/elections/rules/delete/' + id;
            }
        }
    </script>
</x-admin-layout>
