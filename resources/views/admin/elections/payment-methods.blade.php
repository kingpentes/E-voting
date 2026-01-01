<x-admin-layout title="Pilih Metode Pembayaran">
    <x-admin-sidebar active="elections" />

    <div class="flex-1 flex flex-col overflow-hidden bg-gray-50 w-full lg:w-auto">
        <header class="bg-white shadow-sm z-10">
            <div class="px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
                <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900">Pilih Metode Pembayaran</h1>
                <p class="text-gray-600 mt-1 text-sm sm:text-base">Pilih metode pembayaran yang Anda inginkan</p>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 pb-20 lg:pb-8 flex items-center justify-center">
            <div class="max-w-2xl w-full bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="bg-gray-50 px-4 sm:px-6 lg:px-8 py-4 sm:py-6 border-b border-gray-100 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4">
                    <div>
                        <h2 class="text-lg sm:text-xl font-bold text-gray-800">Total Tagihan</h2>
                        <p class="text-sm text-gray-500">{{ $election->title }}</p>
                    </div>
                    <span
                        class="text-xl sm:text-2xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-pink-600">$5.00</span>
                </div>

                <form action="{{ route('admin.elections.process-payment', $election->id) }}" method="POST"
                    class="p-4 sm:p-6 lg:p-8">
                    @csrf

                    <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-4">Metode Tersedia</h3>

                    <div class="space-y-4">
                        <!-- Bank Transfer -->
                        <label
                            class="relative flex items-center p-4 border rounded-xl cursor-pointer hover:bg-purple-50 hover:border-purple-200 transition-all group">
                            <input type="radio" name="payment_method" value="bank_transfer"
                                class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300" checked>
                            <div class="ml-4 flex-1">
                                <div class="flex items-center justify-between">
                                    <span
                                        class="block text-sm font-medium text-gray-900 group-hover:text-purple-700">Transfer
                                        Bank (Virtual Account)</span>
                                    <div class="flex space-x-2">
                                        <span
                                            class="text-xs font-mono bg-gray-100 px-2 py-0.5 rounded text-gray-600">BCA</span>
                                        <span
                                            class="text-xs font-mono bg-gray-100 px-2 py-0.5 rounded text-gray-600">Mandiri</span>
                                    </div>
                                </div>
                                <span class="block text-sm text-gray-500 mt-1">Verifikasi otomatis</span>
                            </div>
                        </label>

                        <!-- E-Wallet -->
                        <label
                            class="relative flex items-center p-4 border rounded-xl cursor-pointer hover:bg-purple-50 hover:border-purple-200 transition-all group">
                            <input type="radio" name="payment_method" value="ewallet"
                                class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300">
                            <div class="ml-4 flex-1">
                                <div class="flex items-center justify-between">
                                    <span
                                        class="block text-sm font-medium text-gray-900 group-hover:text-purple-700">E-Wallet</span>
                                    <div class="flex space-x-2">
                                        <span
                                            class="text-xs font-mono bg-gray-100 px-2 py-0.5 rounded text-gray-600">GoPay</span>
                                        <span
                                            class="text-xs font-mono bg-gray-100 px-2 py-0.5 rounded text-gray-600">OVO</span>
                                    </div>
                                </div>
                                <span class="block text-sm text-gray-500 mt-1">Scan QR Code</span>
                            </div>
                        </label>

                        <!-- Credit Card -->
                        <label
                            class="relative flex items-center p-4 border rounded-xl cursor-pointer hover:bg-purple-50 hover:border-purple-200 transition-all group">
                            <input type="radio" name="payment_method" value="credit_card"
                                class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300">
                            <div class="ml-4 flex-1">
                                <span class="block text-sm font-medium text-gray-900 group-hover:text-purple-700">Kartu
                                    Kredit / Debit</span>
                                <span class="block text-sm text-gray-500 mt-1">Visa, Mastercard</span>
                            </div>
                        </label>
                    </div>

                    <div class="mt-6 sm:mt-8 pt-4 sm:pt-6 border-t border-gray-100 flex flex-col-reverse sm:flex-row items-center justify-between gap-3 sm:gap-4">
                        <a href="{{ route('admin.elections.payment', $election->id) }}"
                            class="text-gray-600 hover:text-gray-900 font-medium text-sm">Kembali</a>
                        <button type="submit"
                            class="w-full sm:w-auto px-6 sm:px-8 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold rounded-xl shadow-md hover:shadow-lg hover:from-purple-700 hover:to-pink-700 transition-all transform hover:-translate-y-0.5 text-sm sm:text-base">
                            Konfirmasi Pembayaran
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</x-admin-layout>
