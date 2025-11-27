<x-admin-layout title="Edit Kandidat">
    <x-admin-sidebar active="manage" />
    
    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white shadow-sm z-10">
            <div class="px-8 py-6 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Edit Kandidat</h1>
                    <p class="text-gray-600 mt-1">Ubah data kandidat yang sudah ada</p>
                </div>
                <a href="{{ route('admin.candidates.manage') }}" 
                   class="px-6 py-2.5 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition-all flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Kembali</span>
                </a>
            </div>
        </header>
        
        <main class="flex-1 overflow-y-auto p-8">
            <div class="max-w-4xl mx-auto">
                <form action="{{ route('admin.candidates.update', $candidate->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <!-- Card Form -->
                    <div class="bg-white rounded-2xl shadow-md p-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Data Kandidat</h2>
                        
                        <!-- Pilih Pemilu -->
                        <div class="mb-6">
                            <label for="election_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                Pilih Pemilu <span class="text-red-500">*</span>
                            </label>
                            <select id="election_id" name="election_id" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">
                                <option value="">-- Pilih Pemilu --</option>
                                @foreach($elections as $election)
                                    <option value="{{ $election->id }}" {{ old('election_id', $candidate->election_id) == $election->id ? 'selected' : '' }}>
                                        {{ $election->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Nomor Urut -->
                        <div class="mb-6">
                            <label for="number" class="block text-sm font-semibold text-gray-700 mb-2">
                                Nomor Urut <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="number" name="number" required min="1" 
                                   value="{{ old('number', $candidate->candidate_number) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all"
                                   placeholder="Masukkan nomor urut kandidat">
                            <p class="text-sm text-gray-500 mt-1">Nomor urut kandidat dalam pemilu</p>
                        </div>

                        <!-- Nama Kandidat -->
                        <div class="mb-6">
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                                Nama Kandidat <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="name" name="name" required 
                                   value="{{ old('name', $candidate->name) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all"
                                   placeholder="Masukkan nama lengkap kandidat">
                        </div>

                        <!-- Foto Kandidat -->
                        <div class="mb-6">
                            <label for="photo" class="block text-sm font-semibold text-gray-700 mb-2">
                                Foto Kandidat
                            </label>
                            <div class="flex items-center space-x-6">
                                <div class="flex-shrink-0">
                                     <img id="photoPreview" 
                                         src="{{ $candidate->photo_url ?? ('https://ui-avatars.com/api/?name=' . urlencode($candidate->name) . '&size=256&background=random') }}" 
                                         alt="{{ $candidate->name }}" 
                                         class="w-32 h-32 rounded-full object-cover border-4 border-gray-200"> 
                                </div>
                                <div class="flex-1">
                                    <input type="file" id="photo" name="photo" accept="image/*"
                                           onchange="previewPhoto(event)"
                                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-all cursor-pointer">
                                    <p class="text-sm text-gray-500 mt-2">Kosongkan jika tidak ingin mengubah foto. Format: JPG, PNG, atau GIF. Maksimal 2MB</p>
                                </div>
                            </div>
                        </div>

                        <!-- Visi -->
                        <div class="mb-6">
                            <label for="visi" class="block text-sm font-semibold text-gray-700 mb-2">
                                Visi <span class="text-red-500">*</span>
                            </label>
                            <textarea id="visi" name="visi" rows="4" required
                                      class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all"
                                      placeholder="Masukkan visi kandidat...">{{ old('visi', $candidate->vision) }}</textarea>
                            <p class="text-sm text-gray-500 mt-1">Visi jangka panjang kandidat</p>
                        </div>

                        <!-- Misi -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Misi <span class="text-red-500">*</span>
                            </label>
                            <div id="misiContainer" class="space-y-3">
                                @foreach($candidate->missions->sortBy('order') as $index => $mission)
                                <div class="flex items-start space-x-2 misi-item">
                                    <span class="flex-shrink-0 w-8 h-8 bg-purple-100 text-blue-700 rounded-full flex items-center justify-center font-bold text-sm mt-2">{{ $index + 1 }}</span>
                                    <input type="text" name="misi[]" required 
                                           value="{{ old('misi.' . $index, $mission->mission) }}"
                                           class="flex-1 px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all"
                                           placeholder="Masukkan misi ke-{{ $index + 1 }}">
                                    @if($index > 0)
                                    <button type="button" onclick="removeMisi(this)" 
                                            class="flex-shrink-0 w-10 h-10 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-all mt-2">
                                        <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                            <button type="button" onclick="addMisi()" 
                                    class="mt-4 inline-flex items-center px-4 py-2 bg-purple-100 text-blue-700 font-semibold rounded-lg hover:bg-purple-200 transition-all">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Tambah Misi
                            </button>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center space-x-4 pt-6 border-t border-gray-200">
                            <button type="submit"
                                    class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold py-4 px-8 rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                                <span class="flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Update Kandidat
                                </span>
                            </button>
                            <a href="{{ route('admin.candidates.manage') }}"
                               class="px-8 py-4 bg-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-300 transition-all">
                                Batal
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        let misiCount = {{ $candidate->missions->count() }};

        function previewPhoto(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('photoPreview').src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        }

        function addMisi() {
            misiCount++;
            const container = document.getElementById('misiContainer');
            const newMisi = document.createElement('div');
            newMisi.className = 'flex items-start space-x-2 misi-item';
            newMisi.innerHTML = `
                <span class="flex-shrink-0 w-8 h-8 bg-purple-100 text-blue-700 rounded-full flex items-center justify-center font-bold text-sm mt-2">${misiCount}</span>
                <input type="text" name="misi[]" required
                       class="flex-1 px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all"
                       placeholder="Masukkan misi ke-${misiCount}">
                <button type="button" onclick="removeMisi(this)" 
                        class="flex-shrink-0 w-10 h-10 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-all mt-2">
                    <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            `;
            container.appendChild(newMisi);
        }

        function removeMisi(button) {
            const items = document.querySelectorAll('.misi-item');
            if (items.length > 1) {
                button.closest('.misi-item').remove();
                updateMisiNumbers();
            } else {
                alert('Minimal harus ada 1 misi!');
            }
        }

        function updateMisiNumbers() {
            const items = document.querySelectorAll('.misi-item');
            misiCount = items.length;
            items.forEach((item, index) => {
                const span = item.querySelector('span');
                const input = item.querySelector('input');
                span.textContent = index + 1;
                input.placeholder = `Masukkan misi ke-${index + 1}`;
            });
        }
    </script>
</x-admin-layout>
