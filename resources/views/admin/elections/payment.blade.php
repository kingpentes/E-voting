<x-admin-layout title="Pembayaran Pemilu">
    <x-admin-sidebar active="elections" />

    <div class="flex-1 flex flex-col overflow-hidden bg-gray-50">
        <header class="bg-white shadow-sm z-10">
            <div class="px-8 py-6">
                <h1 class="text-3xl font-bold text-gray-900">Pembayaran Pemilu</h1>
                <p class="text-gray-600 mt-1">Selesaikan pembayaran untuk mengaktifkan pemilu Anda</p>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-8 flex items-center justify-center">
            <div
                class="max-w-md w-full bg-white rounded-2xl shadow-xl overflow-hidden transform transition-all hover:scale-105 duration-300">
                <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-8 py-6 text-center">
                    <h2 class="text-2xl font-bold text-white tracking-wide">Tagihan Pembayaran</h2>
                    <p class="text-purple-100 mt-1 font-medium">Biaya Pembuatan Pemilu</p>
                </div>

                <div class="p-8 space-y-6">
                    <div class="text-center">
                        <p class="text-gray-500 text-sm uppercase tracking-wider font-semibold mb-2">Judul Pemilu</p>
                        <h3 class="text-xl font-bold text-gray-900">{{ $election->title }}</h3>
                        <div
                            class="mt-4 inline-block px-4 py-1 bg-gray-100 rounded-full text-xs font-mono text-gray-600 border border-gray-200">
                            ID: #{{ $election->id }}
                        </div>
                    </div>

                    <div class="border-t border-dashed border-gray-300 my-6"></div>

                    <div class="flex justify-between items-center bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <span class="text-gray-600 font-medium">Total Tagihan</span>
                        <span
                            class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-pink-600">$5.00</span>
                    </div>

                    <form action="{{ route('admin.elections.process-payment', $election->id) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="w-full py-4 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold rounded-xl shadow-lg hover:shadow-xl hover:from-purple-700 hover:to-pink-700 transition-all duration-200 transform hover:-translate-y-1 flex items-center justify-center group">
                            <span class="mr-2 group-hover:animate-pulse">Bayar Sekarang</span>
                            <svg class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                            </svg>
                        </button>
                    </form>

                    <div class="text-center">
                        <a href="{{ route('admin.elections.manage') }}"
                            class="text-sm text-gray-500 hover:text-gray-700 font-medium transition-colors">
                            Batalkan & Kembali
                        </a>
                    </div>
                </div>
            </div>
        </main>
    </div>
</x-admin-layout>
