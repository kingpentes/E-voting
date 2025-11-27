<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Forgot Password</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @if (!app()->environment('testing'))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <!-- Vite disabled in testing to avoid manifest lookup -->
    @endif
</head>

<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-cyan-50">
        <div class="min-h-screen flex">
            <!-- Left Side - Hero Section -->
            <div
                class="hidden lg:flex lg:w-2/5 bg-gradient-to-br from-blue-600 via-cyan-600 to-blue-800 relative overflow-hidden">
                <div class="absolute inset-0 bg-black/20"></div>
                <!-- Decorative Elements -->
                <div class="absolute top-20 left-20 w-32 h-32 bg-white/10 rounded-full blur-xl"></div>
                <div class="absolute bottom-32 right-16 w-24 h-24 bg-white/5 rounded-full blur-lg"></div>
                <div class="absolute top-1/2 right-1/4 w-16 h-16 bg-white/10 rounded-full blur-md"></div>

                <div class="relative z-10 flex flex-col justify-center items-center text-center text-white p-12">
                    <div class="mb-8">
                        <div class="w-20 h-20 bg-white/20 rounded-3xl flex items-center justify-center mb-6 mx-auto">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z">
                                </path>
                            </svg>
                        </div>
                        <h1 class="text-4xl font-bold mb-4 leading-tight">
                            Reset Password Anda
                        </h1>
                        <p class="text-xl text-blue-100 leading-relaxed max-w-md">
                            Kami akan mengirimkan kode OTP ke email Anda untuk mereset password dengan aman.
                        </p>
                    </div>

                    <!-- Security Features -->
                    <div class="grid grid-cols-2 gap-6 w-full max-w-sm">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-white mb-1">10</div>
                            <div class="text-sm text-blue-200">Menit</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-white mb-1">Email</div>
                            <div class="text-sm text-blue-200">Verified</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-white mb-1">OTP</div>
                            <div class="text-sm text-blue-200">Secure Code</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-white mb-1">SSL</div>
                            <div class="text-sm text-blue-200">Protected</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side - Form -->
            <div class="w-full lg:w-3/5 flex items-center justify-center p-6 lg:p-12">
                <div class="w-full max-w-md">
                    <!-- Header -->
                    <div class="mb-8 text-center lg:text-left">
                        <div class="flex lg:hidden justify-center mb-6">
                            <div
                                class="w-16 h-16 bg-gradient-to-r from-blue-600 to-cyan-600 rounded-2xl flex items-center justify-center shadow-lg">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <h2 class="text-3xl font-bold text-gray-900 mb-2">Lupa Password?</h2>
                        <p class="text-gray-600">Masukkan email Anda untuk menerima kode OTP</p>
                    </div>

                    <!-- Session Status -->
                    @if (session('success'))
                        <div
                            class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-xl shadow-lg animate-scale-in">
                            <div class="flex">
                                <svg class="h-5 w-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
                            </div>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-xl shadow-lg animate-scale-in">
                            <div class="flex">
                                <svg class="h-5 w-5 text-red-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <p class="text-sm text-red-700 font-medium">{{ session('error') }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Form -->
                    <div class="bg-white/80 backdrop-blur-sm shadow-xl rounded-2xl p-8 border border-white/20">
                        <form method="POST" action="{{ route('password.send-otp') }}" class="space-y-6">
                            @csrf

                            <!-- Email Input -->
                            <div class="group">
                                <label for="email" class="block text-sm font-semibold text-gray-700 mb-3">
                                    <span class="flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        Email Address
                                    </span>
                                </label>
                                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                    autofocus autocomplete="username"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 bg-gray-50 focus:bg-white group-hover:bg-white placeholder-gray-400 @error('email') border-red-500 @enderror"
                                    placeholder="nama@email.com" />
                                @error('email')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Submit Button -->
                            <div class="pt-4">
                                <button type="submit"
                                    class="w-full bg-gradient-to-r from-blue-600 to-cyan-600 text-white font-semibold py-3 px-4 rounded-xl hover:from-blue-700 hover:to-cyan-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transform transition duration-200 hover:scale-[1.02] active:scale-[0.98] shadow-lg hover:shadow-xl">
                                    <span class="flex items-center justify-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        Kirim Kode OTP
                                    </span>
                                </button>
                            </div>

                            <!-- Back Link -->
                            <div class="text-center pt-4 border-t border-gray-100">
                                <a href="{{ route('login') }}"
                                    class="text-sm font-semibold text-blue-600 hover:text-blue-500 transition duration-200 hover:underline">
                                    ← Kembali ke halaman login
                                </a>
                            </div>
                        </form>
                    </div>

                    <!-- Info Notice -->
                    <div class="mt-6 glass rounded-xl p-4 border border-blue-200">
                        <div class="flex items-start">
                            <svg class="h-5 w-5 text-blue-500 mr-3 flex-shrink-0 mt-0.5" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <p class="text-sm text-blue-700">
                                Kode OTP akan dikirim ke email Anda dan berlaku selama 10 menit.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
