<x-admin-layout title="Edit Pengaturan Pemilu">
    <x-admin-sidebar active="rules" />
    
    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white shadow-sm z-10">
            <div class="px-8 py-6 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Edit Pengaturan Pemilu</h1>
                    <p class="text-gray-600 mt-1">Ubah judul, deskripsi, dan peraturan pemilu</p>
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
                <form action="{{ route('admin.elections.update', $election->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
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
                            <input type="text" id="title" name="title" required value="{{ old('title', $election->title) }}"
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
                                      placeholder="Masukkan deskripsi singkat tentang pemilu...">{{ old('description', $election->description) }}</textarea>
                        </div>

                        <!-- Tanggal Pemilu -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="start_date" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Tanggal Mulai <span class="text-red-500">*</span>
                                </label>
                                <input type="datetime-local" id="start_date" name="start_date" 
                                       value="{{ old('start_date', $election->start_date ? $election->start_date->format('Y-m-d\TH:i') : '') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">
                            </div>
                            <div>
                                <label for="end_date" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Tanggal Berakhir <span class="text-red-500">*</span>
                                </label>
                                <input type="datetime-local" id="end_date" name="end_date" 
                                       value="{{ old('end_date', $election->end_date ? $election->end_date->format('Y-m-d\TH:i') : '') }}"
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
                            @foreach($election->rules as $index => $rule)
                            <div class="flex items-start space-x-3 rule-item bg-gray-50 p-4 rounded-xl">
                                <div class="flex-shrink-0 w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold mt-1">{{ $index + 1 }}</div>
                                <input type="text" name="rules[]" required value="{{ old('rules.' . $index, $rule->rule) }}"
                                       class="flex-1 px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all"
                                       placeholder="Masukkan peraturan...">
                                @if($index > 0)
                                <button type="button" onclick="removeRule(this)" 
                                        class="flex-shrink-0 w-10 h-10 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-all mt-1">
                                    <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                                @endif
                            </div>
                            @endforeach
                                    </svg>
                                </button>
                            </div>
                            
                            <div class="flex items-start space-x-3 rule-item bg-gray-50 p-4 rounded-xl">
                                <div class="flex-shrink-0 w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold mt-1">4</div>
                                <input type="text" name="rules[]" required value="Hasil pemilihan akan diumumkan setelah periode voting berakhir"
                                       class="flex-1 px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all"
                                       placeholder="Masukkan peraturan...">
                                <button type="button" onclick="removeRule(this)" 
                                        class="flex-shrink-0 w-10 h-10 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-all mt-1">
                                    <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        
                        <button type="button" onclick="addRule()" 
                                class="mt-6 inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-100 to-cyan-100 text-blue-700 font-semibold rounded-xl hover:from-purple-200 hover:to-pink-200 transition-all shadow-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Tambah Peraturan
                        </button>
                    </div>

                    <!-- Pengaturan Lanjutan removed (not used) -->

                    <!-- Action Buttons -->
                    <div class="flex items-center space-x-4">
                        <button type="submit"
                                class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold py-4 px-8 rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                            <span class="flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Update Pengaturan
                            </span>
                        </button>
                        <a href="{{ route('admin.elections.rules.manage') }}"
                           class="px-8 py-4 bg-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-300 transition-all">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        let ruleCount = 4;

        function addRule() {
            ruleCount++;
            const container = document.getElementById('rulesContainer');
            const newRule = document.createElement('div');
            newRule.className = 'flex items-start space-x-3 rule-item bg-gray-50 p-4 rounded-xl';
            newRule.innerHTML = `
                <div class="flex-shrink-0 w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold mt-1">${ruleCount}</div>
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
            const items = document.querySelectorAll('.rule-item');
            if (items.length > 1) {
                button.closest('.rule-item').remove();
                updateRuleNumbers();
            } else {
                alert('Minimal harus ada 1 peraturan!');
            }
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
