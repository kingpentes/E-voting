<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-500 via-cyan-500 to-blue-600 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full">
            <!-- Card Container -->
            <div class="bg-white rounded-2xl shadow-2xl p-8">
                <!-- Header -->
                <div class="text-center mb-8">
                    <div class="mx-auto h-16 w-16 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-full flex items-center justify-center mb-4">
                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h2 class="text-3xl font-extrabold text-gray-900">
                        Verifikasi Kode OTP
                    </h2>
                    <p class="mt-2 text-sm text-gray-600">
                        Masukkan 6 digit kode yang telah dikirim ke
                    </p>
                    <p class="mt-1 text-sm font-semibold text-blue-600">
                        {{ $email }}
                    </p>
                </div>

                <!-- Alert Messages -->
                @if (session('success'))
                    <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-green-700">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700">{{ session('error') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Form -->
                <form method="POST" action="{{ route('password.verify-otp') }}" class="space-y-6">
                    @csrf
                    <input type="hidden" name="email" value="{{ $email }}">

                    <!-- OTP Input -->
                    <div>
                        <label for="otp" class="block text-sm font-medium text-gray-700 mb-2">
                            Kode OTP (6 Digit)
                        </label>
                        <input id="otp" 
                               name="otp" 
                               type="text" 
                               maxlength="6"
                               pattern="[0-9]{6}"
                               required
                               value="{{ old('otp') }}"
                               class="appearance-none block w-full px-4 py-4 border border-gray-300 rounded-lg placeholder-gray-400 text-center text-2xl font-bold letter-spacing-wide focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-150 @error('otp') border-red-500 @enderror"
                               placeholder="000000"
                               autocomplete="off">
                        @error('otp')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit" 
                                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-purple-700 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150">
                            Verifikasi Kode
                        </button>
                    </div>
                </form>

                <!-- Resend OTP -->
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">
                        Tidak menerima kode?
                    </p>
                    <form method="POST" action="{{ route('password.resend-otp') }}" class="inline">
                        @csrf
                        <input type="hidden" name="email" value="{{ $email }}">
                        <button type="submit" 
                                class="mt-2 text-sm font-medium text-blue-600 hover:text-indigo-500 transition duration-150 underline">
                            Kirim ulang OTP
                        </button>
                    </form>
                </div>

                <!-- Back Button -->
                <div class="mt-4 text-center">
                    <a href="{{ route('password.request') }}" 
                       class="text-sm font-medium text-gray-500 hover:text-gray-700 transition duration-150">
                        ← Gunakan email lain
                    </a>
                </div>
            </div>

            <!-- Timer Info -->
            <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            Kode OTP akan kadaluarsa dalam 10 menit setelah dikirim.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        input[type="text"]#otp {
            letter-spacing: 0.5em;
        }
    </style>
</x-guest-layout>
