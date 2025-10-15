<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} - Register as Organizer</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50">
            <div class="min-h-screen flex">
                <!-- Left Side - Hero Section -->
                <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-blue-600 via-indigo-600 to-blue-800 relative overflow-hidden">
                    <div class="absolute inset-0 bg-black/20"></div>
                    <!-- Decorative Elements -->
                    <div class="absolute top-20 left-20 w-32 h-32 bg-white/10 rounded-full blur-xl"></div>
                    <div class="absolute bottom-32 right-16 w-24 h-24 bg-white/5 rounded-full blur-lg"></div>
                    <div class="absolute top-1/2 right-1/4 w-16 h-16 bg-white/10 rounded-full blur-md"></div>
                    
                    <div class="relative z-10 flex flex-col justify-center items-center text-center text-white p-12 max-w-2xl mx-auto">
                        <div class="mb-8">
                            <div class="w-20 h-20 bg-white/20 rounded-3xl flex items-center justify-center mb-6 mx-auto">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H9m0 0H5m0 0h2M7 7h10M7 11h10M7 15h10"></path>
                                </svg>
                            </div>
                            <h1 class="text-4xl font-bold mb-4 leading-tight">
                                Create & Manage Elections
                            </h1>
                            <p class="text-xl text-blue-100 leading-relaxed max-w-md">
                                Join as an organizer to create secure, transparent elections and engage your community.
                            </p>
                        </div>
                        
                        <!-- Organizer Features -->
                        <div class="space-y-4 text-left w-full max-w-sm">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <span class="text-blue-100">Unlimited election creation</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <span class="text-blue-100">Voter invitation management</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <span class="text-blue-100">Real-time analytics dashboard</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <span class="text-blue-100">Advanced security controls</span>
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
                                <div class="w-16 h-16 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H9m0 0H5m0 0h2M7 7h10M7 11h10M7 15h10"></path>
                                    </svg>
                                </div>
                            </div>
                            <h2 class="text-4xl font-bold text-gray-900 mb-3">Register as Organizer</h2>
                            <p class="text-gray-600 text-lg">Create your organizer account to start managing elections</p>
                        </div>

                        <!-- Registration Form -->
                        <div class="bg-white/80 backdrop-blur-sm shadow-xl rounded-2xl p-8 border border-white/20">
                            <form method="POST" action="{{ route('register.organizer.store') }}" class="space-y-6">
                                @csrf
                                <input type="hidden" name="user_type" value="organizer">

                                <div class="grid grid-cols-1 gap-6">
                                    <!-- Full Name -->
                                    <div class="group">
                                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-3">
                                            <span class="flex items-center">
                                                <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                                Full Name
                                            </span>
                                        </label>
                                        <input 
                                            id="name" 
                                            type="text" 
                                            name="name" 
                                            value="{{ old('name') }}" 
                                            required 
                                            autofocus 
                                            autocomplete="name"
                                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 bg-gray-50 focus:bg-white group-hover:bg-white placeholder-gray-400"
                                            placeholder="Enter your full name"
                                        />
                                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                    </div>

                                    <!-- Email Address -->
                                    <div class="group">
                                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-3">
                                            <span class="flex items-center">
                                                <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                                </svg>
                                                Email Address
                                            </span>
                                        </label>
                                        <input 
                                            id="email" 
                                            type="email" 
                                            name="email" 
                                            value="{{ old('email') }}" 
                                            required 
                                            autocomplete="username"
                                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 bg-gray-50 focus:bg-white group-hover:bg-white placeholder-gray-400"
                                            placeholder="Enter your email address"
                                        />
                                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                    </div>

                                    <!-- Organization Name -->
                                    <div class="group">
                                        <label for="organization" class="block text-sm font-semibold text-gray-700 mb-3">
                                            <span class="flex items-center">
                                                <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H9m0 0H5m0 0h2M7 7h10M7 11h10M7 15h10"></path>
                                                </svg>
                                                Organization Name
                                            </span>
                                        </label>
                                        <input 
                                            id="organization" 
                                            type="text" 
                                            name="organization" 
                                            value="{{ old('organization') }}" 
                                            required
                                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 bg-gray-50 focus:bg-white group-hover:bg-white placeholder-gray-400"
                                            placeholder="Enter your organization name"
                                        />
                                        <x-input-error :messages="$errors->get('organization')" class="mt-2" />
                                    </div>

                                    <!-- Phone Number -->
                                    <div class="group">
                                        <label for="phone" class="block text-sm font-semibold text-gray-700 mb-3">
                                            <span class="flex items-center">
                                                <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                                </svg>
                                                Phone Number
                                            </span>
                                        </label>
                                        <input 
                                            id="phone" 
                                            type="tel" 
                                            name="phone" 
                                            value="{{ old('phone') }}" 
                                            required
                                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 bg-gray-50 focus:bg-white group-hover:bg-white placeholder-gray-400"
                                            placeholder="Enter your phone number"
                                        />
                                        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                                    </div>

                                    <!-- Password Fields -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <!-- Password -->
                                        <div class="group">
                                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-3">
                                                <span class="flex items-center">
                                                    <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                                    </svg>
                                                    Password
                                                </span>
                                            </label>
                                            <input 
                                                id="password" 
                                                type="password" 
                                                name="password" 
                                                required 
                                                autocomplete="new-password"
                                                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 bg-gray-50 focus:bg-white group-hover:bg-white placeholder-gray-400"
                                                placeholder="Create password"
                                            />
                                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                        </div>

                                        <!-- Confirm Password -->
                                        <div class="group">
                                            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-3">
                                                <span class="flex items-center">
                                                    <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    Confirm Password
                                                </span>
                                            </label>
                                            <input 
                                                id="password_confirmation" 
                                                type="password" 
                                                name="password_confirmation" 
                                                required 
                                                autocomplete="new-password"
                                                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 bg-gray-50 focus:bg-white group-hover:bg-white placeholder-gray-400"
                                                placeholder="Confirm password"
                                            />
                                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="pt-6">
                                    <button 
                                        type="submit"
                                        class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold py-4 px-6 rounded-xl hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transform transition duration-200 hover:scale-[1.02] active:scale-[0.98] shadow-lg hover:shadow-xl text-lg"
                                    >
                                        <span class="flex items-center justify-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H9m0 0H5m0 0h2M7 7h10M7 11h10M7 15h10"></path>
                                            </svg>
                                            Create Organizer Account
                                        </span>
                                    </button>
                                </div>

                                <!-- Navigation Links -->
                                <div class="text-center pt-6 border-t border-gray-100 space-y-2">
                                    <p class="text-gray-600">
                                        Want to register as a voter instead? 
                                        <a href="{{ route('register.voter') }}" class="font-semibold text-blue-600 hover:text-blue-500 transition duration-200 hover:underline">
                                            Click here
                                        </a>
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        Already have an account? 
                                        <a href="{{ route('login') }}" class="font-semibold text-blue-600 hover:text-blue-500 transition duration-200 hover:underline">
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