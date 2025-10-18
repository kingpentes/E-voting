<x-admin-layout title="Buat Pengaturan Pemilu">
    <x-admin-sidebar active="rules" />
    
    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white shadow-sm z-10">
            <div class="px-8 py-6 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Buat Pengaturan Pemilu</h1>
                    <p class="text-gray-600 mt-1">Atur judul, deskripsi, dan peraturan pemilu baru</p>
                </div>
                <a href="{{ route('admin.elections.rules.manage') }}" 
                   class="px-6 py-2.5 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition-all flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Kembali</span>
                </a>
            </div>
        </header>
        
        <main class="flex-1 overflow-y-auto p-8">
            <div class="max-w-5xl mx-auto">
                <form action="{{ route('admin.elections.store') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <!-- Informasi Pemilu -->
                    <div class="bg-white rounded-2xl shadow-md p-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                            <svg class="w-7 h-7 mr-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Informasi Pemilu
                        </h2>
                        
                        <!-- Judul Pemilu -->
                        <div class="mb-6">
                            <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">
                                Judul Pemilu <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="title" name="title" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all text-lg font-semibold"
                                   placeholder="Contoh: Pemilihan Ketua OSIS 2025">
                        </div>

                        <!-- Deskripsi Pemilu -->
                        <div class="mb-6">
                            <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                                Deskripsi Pemilu <span class="text-red-500">*</span>
                            </label>
                            <textarea id="description" name="description" rows="3" required
                                      class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all"
                                      placeholder="Masukkan deskripsi singkat tentang pemilu..."></textarea>
                        </div>

                        <!-- Tanggal Pemilu -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="start_date" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Tanggal Mulai <span class="text-red-500">*</span>
                                </label>
                                <input type="datetime-local" id="start_date" name="start_date" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">
                            </div>
                            <div>
                                <label for="end_date" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Tanggal Berakhir <span class="text-red-500">*</span>
                                </label>
                                <input type="datetime-local" id="end_date" name="end_date" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">
                            </div>
                        </div>
                    </div>

                    <!-- Peraturan Pemilihan -->
                    <div class="bg-white rounded-2xl shadow-md p-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                            <svg class="w-7 h-7 mr-3 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Peraturan Pemilihan
                        </h2>
                        
                        <div id="rulesContainer" class="space-y-4">
                            <div class="flex items-start space-x-3 rule-item bg-gray-50 p-4 rounded-xl">
                                <div class="flex-shrink-0 w-10 h-10 bg-purple-600 text-white rounded-full flex items-center justify-center font-bold mt-1">1</div>
                                <input type="text" name="rules[]" required
                                       class="flex-1 px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all"
                                       placeholder="Contoh: Setiap pemilih hanya dapat memberikan satu suara">
                            </div>
                        </div>
                        
                        <button type="button" onclick="addRule()" 
                                class="mt-6 inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-100 to-pink-100 text-purple-700 font-semibold rounded-xl hover:from-purple-200 hover:to-pink-200 transition-all shadow-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Tambah Peraturan
                        </button>
                    </div>

                    <!-- Pengaturan Lanjutan -->
                    <div class="bg-white rounded-2xl shadow-md p-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                            <svg class="w-7 h-7 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Pengaturan Lanjutan
                        </h2>
                        
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900">Izinkan Golput</h3>
                                    <p class="text-sm text-gray-600">Pemilih dapat memilih untuk tidak memilih (abstain)</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="allow_abstain" value="1" class="sr-only peer">
                                    <div class="w-14 h-7 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-purple-600"></div>
                                </label>
                            </div>
                            
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900">Tampilkan Hasil Setelah Vote</h3>
                                    <p class="text-sm text-gray-600">Pemilih dapat melihat hasil setelah mereka memilih</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="show_results_after_vote" value="1" class="sr-only peer">
                                    <div class="w-14 h-7 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-purple-600"></div>
                                </label>
                            </div>
                            
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900">Butuh Konfirmasi</h3>
                                    <p class="text-sm text-gray-600">Pemilih harus konfirmasi pilihan sebelum submit</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="require_confirmation" value="1" checked class="sr-only peer">
                                    <div class="w-14 h-7 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-purple-600"></div>
                                </label>
                            </div>
                            
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900">Izinkan Ubah Pilihan</h3>
                                    <p class="text-sm text-gray-600">Pemilih dapat mengubah pilihan mereka</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="allow_vote_change" value="1" class="sr-only peer">
                                    <div class="w-14 h-7 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-purple-600"></div>
                                </label>
                            </div>
                            
                            <div class="p-4 bg-gray-50 rounded-xl">
                                <label for="max_votes_per_voter" class="block font-semibold text-gray-900 mb-2">
                                    Maksimal Suara Per Pemilih
                                </label>
                                <input type="number" id="max_votes_per_voter" name="max_votes_per_voter" value="1" min="1" max="10"
                                       class="w-32 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center space-x-4">
                        <button type="submit"
                                class="flex-1 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold py-4 px-8 rounded-xl hover:from-purple-700 hover:to-pink-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                            <span class="flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Simpan Pengaturan
                            </span>
                        </button>
                        <a href="{{ route('admin.dashboard') }}"
                           class="px-8 py-4 bg-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-300 transition-all">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        let ruleCount = 1;

        function addRule() {
            ruleCount++;
            const container = document.getElementById('rulesContainer');
            const newRule = document.createElement('div');
            newRule.className = 'flex items-start space-x-3 rule-item bg-gray-50 p-4 rounded-xl';
            newRule.innerHTML = `
                <div class="flex-shrink-0 w-10 h-10 bg-purple-600 text-white rounded-full flex items-center justify-center font-bold mt-1">${ruleCount}</div>
                <input type="text" name="rules[]" required
                       class="flex-1 px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all"
                       placeholder="Masukkan peraturan...">
                <button type="button" onclick="removeRule(this)" 
                        class="flex-shrink-0 w-10 h-10 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-all mt-1">
                    <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            `;
            container.appendChild(newRule);
        }

        function removeRule(button) {
            button.closest('.rule-item').remove();
            updateRuleNumbers();
        }

        function updateRuleNumbers() {
            const items = document.querySelectorAll('.rule-item');
            ruleCount = items.length;
            items.forEach((item, index) => {
                const div = item.querySelector('div');
                div.textContent = index + 1;
            });
        }
    </script>
</x-admin-layout>
