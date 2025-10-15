<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} - Login</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50">
            <div class="min-h-screen flex">
                <!-- Left Side - Hero/Branding Section -->
                <div class="hidden lg:flex lg:w-2/5 bg-gradient-to-br from-indigo-600 via-purple-600 to-indigo-800 relative overflow-hidden">
                    <div class="absolute inset-0 bg-black/20"></div>
                    <!-- Decorative Elements -->
                    <div class="absolute top-16 left-16 w-32 h-32 bg-white/10 rounded-full blur-xl"></div>
                    <div class="absolute bottom-24 right-12 w-24 h-24 bg-white/5 rounded-full blur-lg"></div>
                    <div class="absolute top-1/2 right-1/3 w-16 h-16 bg-white/10 rounded-full blur-md"></div>
                    
                    <div class="relative z-10 flex flex-col justify-center items-center text-center text-white p-12">
                        <div class="mb-8">
                            <div class="w-20 h-20 bg-white/20 rounded-3xl flex items-center justify-center mb-6 mx-auto">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                            <h1 class="text-4xl font-bold mb-4 leading-tight">
                                Welcome Back to<br>
                                <span class="bg-gradient-to-r from-yellow-400 to-orange-400 bg-clip-text text-transparent">
                                    Smart Voting
                                </span>
                            </h1>
                            <p class="text-xl text-indigo-100 leading-relaxed max-w-md">
                                Sign in to access your voting dashboard and participate in secure elections.
                            </p>
                        </div>
                        
                        <!-- Stats/Features -->
                        <div class="grid grid-cols-2 gap-6 w-full max-w-sm">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-white mb-1">99.9%</div>
                                <div class="text-sm text-indigo-200">Uptime</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-white mb-1">256-bit</div>
                                <div class="text-sm text-indigo-200">Encryption</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-white mb-1">50K+</div>
                                <div class="text-sm text-indigo-200">Voters</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-white mb-1">100%</div>
                                <div class="text-sm text-indigo-200">Secure</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Side - Login Form -->
                <div class="w-full lg:w-3/5 flex items-center justify-center p-6 lg:p-12">
                    <div class="w-full max-w-md">
                        <!-- Header -->
                        <div class="mb-8 text-center lg:text-left">
                            <div class="flex lg:hidden justify-center mb-6">
                                <div class="w-16 h-16 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                </div>
                            </div>
                            <h2 class="text-3xl font-bold text-gray-900 mb-2">Welcome Back</h2>
                            <p class="text-gray-600">Please sign in to your account</p>
                        </div>

                        <!-- Session Status -->
                        <x-auth-session-status class="mb-4" :status="session('status')" />

                        <!-- Login Form -->
                        <div class="bg-white/80 backdrop-blur-sm shadow-xl rounded-2xl p-8 border border-white/20">
                            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                                @csrf

                                <!-- Email Address -->
                                <div class="group">
                                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-3">
                                        <span class="flex items-center">
                                            <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                            </svg>
                                            Email Address
                                        </span>
                                    </label>
                                    <div class="relative">
                                        <input 
                                            id="email" 
                                            type="email" 
                                            name="email" 
                                            value="{{ old('email') }}" 
                                            required 
                                            autofocus 
                                            autocomplete="username"
                                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition duration-200 bg-gray-50 focus:bg-white group-hover:bg-white placeholder-gray-400"
                                            placeholder="Enter your email"
                                        />
                                    </div>
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>

                                <!-- Password -->
                                <div class="group">
                                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-3">
                                        <span class="flex items-center">
                                            <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                            </svg>
                                            Password
                                        </span>
                                    </label>
                                    <div class="relative">
                                        <input 
                                            id="password" 
                                            type="password" 
                                            name="password" 
                                            required 
                                            autocomplete="current-password"
                                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition duration-200 bg-gray-50 focus:bg-white group-hover:bg-white placeholder-gray-400"
                                            placeholder="Enter your password"
                                        />
                                    </div>
                                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                </div>

                                <!-- Remember Me & Forgot Password -->
                                <div class="flex items-center justify-between">
                                    <label for="remember_me" class="inline-flex items-center">
                                        <input 
                                            id="remember_me" 
                                            type="checkbox" 
                                            name="remember"
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 focus:ring-offset-0"
                                        >
                                        <span class="ml-2 text-sm text-gray-600">Remember me</span>
                                    </label>

                                    @if (Route::has('password.request'))
                                        <a href="{{ route('password.request') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500 transition duration-200 hover:underline">
                                            Forgot password?
                                        </a>
                                    @endif
                                </div>

                                <!-- Submit Button -->
                                <div class="pt-4">
                                    <button 
                                        type="submit"
                                        class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold py-3 px-4 rounded-xl hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transform transition duration-200 hover:scale-[1.02] active:scale-[0.98] shadow-lg hover:shadow-xl"
                                    >
                                        <span class="flex items-center justify-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                            </svg>
                                            Sign In
                                        </span>
                                    </button>
                                </div>

                                <!-- Register Link -->
                                <div class="text-center pt-4 border-t border-gray-100">
                                    <p class="text-sm text-gray-600">
                                        Don't have an account? 
                                        <a href="{{ route('register') }}" class="font-semibold text-indigo-600 hover:text-indigo-500 transition duration-200 hover:underline">
                                            Create one here
                                        </a>
                                    </p>
                                </div>
                            </form>
                        </div>

                        <!-- Security Notice -->
                        <div class="mt-6 text-center">
                            <div class="flex items-center justify-center space-x-2 text-xs text-gray-500">
                                <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span>Secured with 256-bit SSL encryption</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
