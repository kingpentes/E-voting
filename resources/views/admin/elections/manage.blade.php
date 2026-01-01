<x-admin-layout title="Kelola Judul & Peraturan">
    <x-admin-sidebar active="rules" />

    <div class="flex-1 flex flex-col overflow-hidden w-full lg:w-auto">
        <!-- Header -->
        <header class="bg-white shadow-sm z-10">
            <div class="px-4 sm:px-6 lg:px-8 py-4 lg:py-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900">Kelola Judul & Peraturan</h1>
                    <p class="text-gray-600 mt-1 text-sm lg:text-base">{{ $elections->count() }} pemilu Anda</p>
                </div>
                <a href="{{ route('admin.elections.create') }}"
                    class="px-4 sm:px-6 py-2.5 sm:py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold rounded-xl hover:from-purple-700 hover:to-pink-700 transition-all text-sm sm:text-base w-fit">
                    + Buat Pemilu Baru
                </a>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 pb-20 lg:pb-8">
            <!-- Flash Messages -->
            @if (session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
                    <p class="text-green-800 font-medium">{{ session('success') }}</p>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                    <p class="text-red-800 font-medium">{{ session('error') }}</p>
                </div>
            @endif

            <!-- Empty State -->
            @if ($elections->isEmpty())
                <div class="bg-white rounded-2xl shadow-md p-8 sm:p-12 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <h3 class="text-xl sm:text-2xl font-bold text-gray-700 mb-2">Belum Ada Pemilu</h3>
                    <p class="text-gray-500 mb-6">Buat pemilu pertama Anda untuk memulai</p>
                    <a href="{{ route('admin.elections.create') }}"
                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold rounded-xl hover:from-purple-700 hover:to-pink-700 transition-all">
                        + Buat Pemilu Baru
                    </a>
                </div>
            @else
                <!-- Elections List -->
                <div class="space-y-6">
                    @foreach ($elections as $election)
                        <div class="bg-white rounded-2xl shadow-md overflow-hidden {{ $election->is_published ? 'border-2 border-green-500' : '' }}">
                            
                            <!-- Card Header -->
                            <div class="bg-gradient-to-r {{ $election->is_published ? 'from-green-50 to-emerald-50' : 'from-gray-50 to-gray-100' }} px-4 sm:px-6 py-4 border-b">
                                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                                    
                                    <!-- Title with Status Badge -->
                                    <div class="flex flex-wrap items-center gap-2">
                                        @if ($election->status === 'closed')
                                            <span class="px-3 py-1 bg-gray-500 text-white text-xs font-bold rounded-full">DITUTUP</span>
                                        @elseif($election->is_published)
                                            <span class="px-3 py-1 bg-green-500 text-white text-xs font-bold rounded-full">AKTIF</span>
                                        @elseif($election->status === 'pending_payment')
                                            <span class="px-3 py-1 bg-orange-500 text-white text-xs font-bold rounded-full">MENUNGGU PEMBAYARAN</span>
                                        @else
                                            <span class="px-3 py-1 bg-gray-400 text-white text-xs font-bold rounded-full">DRAFT</span>
                                        @endif
                                        <h2 class="text-lg sm:text-xl font-bold text-gray-900">{{ $election->title }}</h2>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="flex flex-wrap items-center gap-2">
                                        {{-- Publish Button --}}
                                        @if (!$election->is_published && $election->status !== 'pending_payment')
                                            @if ($election->candidates_count === 0)
                                                <button disabled class="px-4 py-2 bg-gray-300 text-gray-600 text-sm font-medium rounded-lg cursor-not-allowed" title="Tambahkan kandidat terlebih dahulu">
                                                    Publish (Perlu Kandidat)
                                                </button>
                                            @elseif(!$election->contract_address)
                                                <button disabled class="px-4 py-2 bg-gray-300 text-gray-600 text-sm font-medium rounded-lg cursor-not-allowed" title="Deploy smart contract terlebih dahulu">
                                                    Publish (Perlu Contract)
                                                </button>
                                            @else
                                                <form action="{{ route('admin.elections.toggle-publish', $election->id) }}" method="POST" class="inline"
                                                    onsubmit="return confirm('⚠️ PERINGATAN!\n\nSetelah dipublish, Anda TIDAK DAPAT:\n- Mengubah data pemilu\n- Mengedit atau menghapus kandidat\n- Unpublish pemilu\n\nApakah Anda yakin?')">
                                                    @csrf
                                                    <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                                                        Publish Pemilu
                                                    </button>
                                                </form>
                                            @endif
                                        @endif

                                        {{-- Close & Extend Buttons (Active Elections) --}}
                                        @if ($election->is_published && $election->status !== 'closed')
                                            <form action="{{ route('admin.elections.close', $election->id) }}" method="POST" class="inline"
                                                onsubmit="return confirm('Yakin ingin menutup pemilu ini?\n\nSetelah ditutup, hasil voting akan ditampilkan.')">
                                                @csrf
                                                <button type="submit" class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-sm font-medium rounded-lg transition-colors">
                                                    Tutup Pemilu
                                                </button>
                                            </form>
                                            <button type="button"
                                                onclick="openExtendModal({{ $election->id }}, '{{ $election->end_date ? $election->end_date->format('Y-m-d') : '' }}', '{{ $election->end_time ?? '' }}')"
                                                class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-medium rounded-lg transition-colors inline-flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Perpanjang
                                            </button>
                                        @endif

                                        {{-- Pay Button --}}
                                        @if ($election->status === 'pending_payment')
                                            <a href="{{ route('admin.elections.payment', $election->id) }}"
                                                class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-sm font-medium rounded-lg transition-colors animate-pulse">
                                                Bayar Sekarang
                                            </a>
                                        @endif

                                        {{-- Deploy Contract Button --}}
                                        @if ($election->status !== 'pending_payment')
                                            @if ($election->contract_address)
                                                <span class="px-4 py-2 bg-gray-400 text-white text-sm font-medium rounded-lg">
                                                    Kontrak Terdeploy
                                                </span>
                                            @else
                                                <form action="{{ route('admin.elections.deploy-contract', $election->id) }}" method="POST" class="inline"
                                                    onsubmit="return confirm('Deploy smart contract untuk pemilu ini?');">
                                                    @csrf
                                                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                                                        Deploy Contract
                                                    </button>
                                                </form>
                                            @endif
                                        @endif

                                        {{-- Edit Button --}}
                                        @if (!$election->is_published && $election->status !== 'closed' && $election->status !== 'pending_payment')
                                            <a href="{{ route('admin.elections.edit', $election->id) }}"
                                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                                                Edit
                                            </a>
                                        @else
                                            <button disabled class="px-4 py-2 bg-gray-300 text-gray-500 text-sm font-medium rounded-lg cursor-not-allowed">
                                                Edit
                                            </button>
                                        @endif

                                        {{-- Delete Button --}}
                                        @if (!$election->is_published && $election->status !== 'closed' && $election->status !== 'pending_payment')
                                            <form action="{{ route('admin.elections.delete', $election->id) }}" method="POST" class="inline"
                                                onsubmit="return confirm('Yakin hapus {{ $election->title }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors">
                                                    Hapus
                                                </button>
                                            </form>
                                        @else
                                            <button disabled class="px-4 py-2 bg-gray-300 text-gray-500 text-sm font-medium rounded-lg cursor-not-allowed">
                                                Hapus
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Card Body -->
                            <div class="p-4 sm:p-6">
                                {{-- Description --}}
                                @if ($election->description)
                                    <p class="text-gray-600 mb-4">{{ $election->description }}</p>
                                @endif

                                {{-- Period --}}
                                @if ($election->start_date)
                                    <div class="flex items-center gap-2 text-gray-700 mb-4">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <span><strong>Periode:</strong> {{ $election->start_date->format('d M Y') }} - {{ $election->end_date->format('d M Y') }}</span>
                                    </div>
                                @endif

                                {{-- Rules --}}
                                @if ($election->rules->count() > 0)
                                    <div class="mb-4">
                                        <h3 class="font-bold text-gray-900 mb-2">Peraturan:</h3>
                                        <ul class="space-y-1 text-gray-600">
                                            @foreach ($election->rules as $rule)
                                                <li class="flex items-start gap-2">
                                                    <span class="text-purple-600 mt-1">•</span>
                                                    <span>{{ $rule->rule }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                {{-- Access Code --}}
                                <div class="mt-4 p-4 bg-gradient-to-r from-purple-50 to-pink-50 border border-purple-200 rounded-xl">
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                        <div>
                                            <h3 class="text-sm font-bold text-gray-700">Kode Akses Pemilu</h3>
                                            <p class="text-xs text-gray-500">Bagikan kode ini kepada pemilih</p>
                                        </div>
                                        <code class="px-4 py-2 bg-white border-2 border-purple-300 rounded-lg font-mono text-lg font-bold text-purple-900">
                                            {{ $election->access_code }}
                                        </code>
                                    </div>
                                </div>

                                {{-- Smart Contract Address --}}
                                @if ($election->contract_address)
                                    <div class="mt-4 p-4 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl">
                                        <h3 class="text-sm font-bold text-gray-700 mb-2">Alamat Smart Contract</h3>
                                        <code class="block px-3 py-2 bg-white border border-green-300 rounded-lg font-mono text-xs text-green-900 break-all">
                                            {{ $election->contract_address }}
                                        </code>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </main>
    </div>

    <!-- Extend Time Modal -->
    <div id="extendTimeModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeExtendModal()"></div>

            <!-- Modal panel -->
            <div class="relative inline-block w-full max-w-md p-6 my-8 text-left bg-white rounded-2xl shadow-xl transform transition-all">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Perpanjang Waktu Pemilu
                    </h3>
                    <button type="button" onclick="closeExtendModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-sm text-blue-800">
                        <strong>Info:</strong> Perpanjang waktu pemilu untuk memberikan lebih banyak waktu kepada pemilih.
                    </p>
                </div>

                <div id="currentEndDateInfo" class="mb-4 p-3 bg-gray-50 border border-gray-200 rounded-lg">
                    <p class="text-sm text-gray-600">
                        <strong>Tanggal berakhir saat ini:</strong> <span id="currentEndDateDisplay">-</span>
                    </p>
                </div>

                <form id="extendTimeForm" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label for="new_end_date" class="block text-sm font-semibold text-gray-700 mb-2">
                                Tanggal Berakhir Baru <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="new_end_date" name="new_end_date" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                        </div>

                        <div>
                            <label for="new_end_time" class="block text-sm font-semibold text-gray-700 mb-2">
                                Waktu Berakhir Baru (Opsional)
                            </label>
                            <input type="time" id="new_end_time" name="new_end_time"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                        </div>
                    </div>

                    <div class="mt-6 flex flex-col-reverse sm:flex-row gap-3">
                        <button type="button" onclick="closeExtendModal()"
                            class="w-full sm:w-auto px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-300 transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                            class="w-full sm:w-auto px-6 py-3 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-xl transition-colors flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Perpanjang Waktu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openExtendModal(electionId, currentEndDate, currentEndTime) {
            const modal = document.getElementById('extendTimeModal');
            const form = document.getElementById('extendTimeForm');
            const dateInput = document.getElementById('new_end_date');
            const timeInput = document.getElementById('new_end_time');
            const currentDisplay = document.getElementById('currentEndDateDisplay');

            form.action = `/admin/elections/${electionId}/extend-time`;

            if (currentEndDate) {
                const date = new Date(currentEndDate);
                const options = { day: 'numeric', month: 'long', year: 'numeric' };
                let displayText = date.toLocaleDateString('id-ID', options);
                if (currentEndTime) {
                    displayText += ` pukul ${currentEndTime}`;
                }
                currentDisplay.textContent = displayText;

                const minDate = new Date(date);
                minDate.setDate(minDate.getDate() + 1);
                dateInput.min = minDate.toISOString().split('T')[0];
            } else {
                currentDisplay.textContent = 'Tidak ditentukan';
                const tomorrow = new Date();
                tomorrow.setDate(tomorrow.getDate() + 1);
                dateInput.min = tomorrow.toISOString().split('T')[0];
            }

            if (currentEndTime) {
                timeInput.value = currentEndTime;
            }

            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeExtendModal() {
            const modal = document.getElementById('extendTimeModal');
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            document.getElementById('extendTimeForm').reset();
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeExtendModal();
            }
        });
    </script>
</x-admin-layout>
