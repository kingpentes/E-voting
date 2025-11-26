<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} - Register as Voter</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen bg-gradient-to-br from-purple-50 via-white to-pink-50">
            <div class="min-h-screen flex">
                <!-- Left Side - Hero Section -->
                <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-purple-600 via-pink-600 to-purple-800 relative overflow-hidden">
                    <div class="absolute inset-0 bg-black/20"></div>
                    <!-- Decorative Elements -->
                    <div class="absolute top-20 left-20 w-32 h-32 bg-white/10 rounded-full blur-xl"></div>
                    <div class="absolute bottom-32 right-16 w-24 h-24 bg-white/5 rounded-full blur-lg"></div>
                    <div class="absolute top-1/2 right-1/4 w-16 h-16 bg-white/10 rounded-full blur-md"></div>
                    
                    <div class="relative z-10 flex flex-col justify-center items-center text-center text-white p-12 max-w-2xl mx-auto">
                        <div class="mb-8">
                            <div class="w-20 h-20 bg-white/20 rounded-3xl flex items-center justify-center mb-6 mx-auto">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                            </div>
                            <h1 class="text-4xl font-bold mb-4 leading-tight">
                                Secure Voter Registration
                            </h1>
                            <p class="text-xl text-purple-100 leading-relaxed max-w-md">
                                Join elections with advanced identity verification and anonymous voting protection.
                            </p>
                        </div>
                        
                        <!-- Security Features -->
                        <div class="space-y-4 text-left w-full max-w-sm">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <span class="text-purple-100">Biometric verification</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <span class="text-purple-100">ID card validation</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <span class="text-purple-100">Anonymous ballot casting</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <span class="text-purple-100">Invitation-based access</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Side - Registration Form -->
                <div class="w-full lg:w-1/2 flex items-center justify-center p-6 lg:p-12">
                    <div class="w-full max-w-lg">
                        <!-- Header -->
                        <div class="mb-8">
                            <div class="flex lg:hidden justify-center mb-6">
                                <div class="w-16 h-16 bg-gradient-to-r from-purple-600 to-pink-600 rounded-2xl flex items-center justify-center shadow-lg">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                </div>
                            </div>
                            <h2 class="text-4xl font-bold text-gray-900 mb-3">Register as Voter</h2>
                            <p class="text-gray-600 text-lg">Complete verification to participate in elections</p>
                        </div>



                        <!-- Registration Form -->
                        <div class="bg-white/80 backdrop-blur-sm shadow-xl rounded-2xl p-8 border border-white/20">
                            <form method="POST" action="{{ route('register.voter.store') }}" class="space-y-6">
                                @csrf
                                <input type="hidden" name="user_type" value="voter">

                                <div class="grid grid-cols-1 gap-6">
                                    <div class="mb-4 bg-purple-50 border border-purple-200 rounded-lg p-4">
                                        <p class="text-sm text-purple-800">
                                            <strong>Note:</strong> After registration, you'll be able to complete ID verification and face scan from your voter dashboard.
                                        </p>
                                    </div>

                                    <!-- Full Name -->
                                    <div class="group">
                                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-3">
                                            <span class="flex items-center">
                                                <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                                Full Name <span class="text-red-500">*</span>
                                            </span>
                                        </label>
                                        <input 
                                            id="name" 
                                            type="text" 
                                            name="name" 
                                            value="{{ old('name') }}" 
                                            required 
                                            autocomplete="name"
                                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-200 bg-gray-50 focus:bg-white group-hover:bg-white placeholder-gray-400"
                                            placeholder="Enter your full name"
                                        />
                                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                    </div>

                                    <!-- Email Address -->
                                    <div class="group">
                                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-3">
                                            <span class="flex items-center">
                                                <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                                </svg>
                                                Email Address <span class="text-red-500">*</span>
                                            </span>
                                        </label>
                                        <input 
                                            id="email" 
                                            type="email" 
                                            name="email" 
                                            value="{{ old('email') }}" 
                                            required 
                                            autocomplete="username"
                                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-200 bg-gray-50 focus:bg-white group-hover:bg-white placeholder-gray-400"
                                            placeholder="Enter your email address"
                                        />
                                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                    </div>

                                    <!-- Password Fields -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div class="group">
                                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-3">
                                                <span class="flex items-center">
                                                    <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                                    </svg>
                                                    Password <span class="text-red-500">*</span>
                                                </span>
                                            </label>
                                            <input 
                                                id="password" 
                                                type="password" 
                                                name="password" 
                                                required 
                                                autocomplete="new-password"
                                                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-200 bg-gray-50 focus:bg-white group-hover:bg-white placeholder-gray-400"
                                                placeholder="Create password"
                                            />
                                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                        </div>

                                        <div class="group">
                                            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-3">
                                                <span class="flex items-center">
                                                    <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    Confirm Password <span class="text-red-500">*</span>
                                                </span>
                                            </label>
                                            <input 
                                                id="password_confirmation" 
                                                type="password" 
                                                name="password_confirmation" 
                                                required 
                                                autocomplete="new-password"
                                                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-200 bg-gray-50 focus:bg-white group-hover:bg-white placeholder-gray-400"
                                                placeholder="Confirm password"
                                            />
                                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                                        </div>
                                    </div>
                                </div>

                                <div class="pt-6">
                                    <button 
                                        type="submit"
                                        class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white font-semibold py-3 px-6 rounded-xl hover:from-purple-700 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transform transition duration-200 hover:scale-[1.02] active:scale-[0.98] shadow-lg hover:shadow-xl"
                                    >
                                        <span class="flex items-center justify-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                            </svg>
                                            Create Account
                                        </span>
                                    </button>
                                </div>

                                <!-- Navigation Links -->
                                <div class="text-center pt-6 border-t border-gray-100 space-y-2">
                                    <p class="text-gray-600">
                                        Want to register as an organizer instead? 
                                        <a href="{{ route('register.organizer') }}" class="font-semibold text-purple-600 hover:text-purple-500 transition duration-200 hover:underline">
                                            Click here
                                        </a>
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        Already have an account? 
                                        <a href="{{ route('login') }}" class="font-semibold text-purple-600 hover:text-purple-500 transition duration-200 hover:underline">
                                            Sign in here
                                        </a>
                                    </p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </body>
</html>