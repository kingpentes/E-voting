@props(['title', 'description'])

<div class="bg-gradient-to-r from-blue-600 to-cyan-700 text-white py-16 px-4">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-bold mb-6">{{ $title }}</h1>
            <p class="text-lg md:text-xl text-blue-100 max-w-3xl mx-auto leading-relaxed">
                {{ $description }}
            </p>
        </div>
        
        <!-- Peraturan Pemilihan Box -->
        <div class="max-w-4xl mx-auto">
            <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-8 border border-white/20">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-2xl font-bold mb-4">Peraturan Pemilihan</h3>
                        <ul class="space-y-3 text-blue-50">
                            <li class="flex items-start">
                                <svg class="w-6 h-6 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-lg">Setiap pemilih hanya dapat memberikan satu suara</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-6 h-6 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-lg">Klik pada kartu kandidat untuk melihat detail visi dan misi</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-6 h-6 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-lg">Pastikan pilihan Anda sudah tepat sebelum mengkonfirmasi</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-6 h-6 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-lg">Hasil pemilihan akan diumumkan setelah periode voting berakhir</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
