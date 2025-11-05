<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} - Register</title>

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
        <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50">
            <div class="min-h-screen flex items-center justify-center p-6">
                <div class="w-full max-w-6xl">
                    <!-- Header -->
                    <div class="text-center mb-12">
                        <div class="mx-auto w-20 h-20 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-3xl flex items-center justify-center shadow-lg mb-6">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                        </div>
                        <h1 class="text-5xl font-bold text-gray-900 mb-4">Join Smart Voting</h1>
                        <p class="text-xl text-gray-600 max-w-2xl mx-auto">Choose your role to get started with our secure, transparent voting platform</p>
                    </div>

                    <!-- Registration Options -->
                    <div class="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                        <!-- Organizer Registration -->
                        <div class="group">
                            <div class="bg-white/80 backdrop-blur-sm shadow-xl rounded-3xl p-8 border border-white/20 hover:shadow-2xl transition-all duration-300 hover:scale-[1.02] cursor-pointer">
                                <div class="text-center">
                                    <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H9m0 0H5m0 0h2M7 7h10M7 11h10M7 15h10"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Event Organizer</h3>
                                    <p class="text-gray-600 mb-6 leading-relaxed">Create and manage elections, invite participants, and monitor real-time results.</p>
                                    
                                    <ul class="text-left space-y-3 mb-8">
                                        <li class="flex items-center text-gray-700">
                                            <svg class="w-5 h-5 text-green-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            Create unlimited elections
                                        </li>
                                        <li class="flex items-center text-gray-700">
                                            <svg class="w-5 h-5 text-green-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            Manage voter invitations
                                        </li>
                                        <li class="flex items-center text-gray-700">
                                            <svg class="w-5 h-5 text-green-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            Real-time analytics dashboard
                                        </li>
                                    </ul>

                                    <a href="{{ route('register.organizer') }}" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold py-4 px-6 rounded-xl hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transform transition duration-200 hover:scale-[1.02] active:scale-[0.98] shadow-lg hover:shadow-xl inline-block text-center">
                                        Register as Organizer
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Voter Registration -->
                        <div class="group">
                            <div class="bg-white/80 backdrop-blur-sm shadow-xl rounded-3xl p-8 border border-white/20 hover:shadow-2xl transition-all duration-300 hover:scale-[1.02] cursor-pointer">
                                <div class="text-center">
                                    <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-pink-600 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Voter</h3>
                                    <p class="text-gray-600 mb-6 leading-relaxed">Participate in elections with secure identity verification and anonymous voting.</p>
                                    
                                    <ul class="text-left space-y-3 mb-8">
                                        <li class="flex items-center text-gray-700">
                                            <svg class="w-5 h-5 text-green-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            Secure identity verification
                                        </li>
                                        <li class="flex items-center text-gray-700">
                                            <svg class="w-5 h-5 text-green-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            Anonymous voting system
                                        </li>
                                        <li class="flex items-center text-gray-700">
                                            <svg class="w-5 h-5 text-green-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            Invitation-based access
                                        </li>
                                    </ul>

                                    <a href="{{ route('register.voter') }}" class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white font-semibold py-4 px-6 rounded-xl hover:from-purple-700 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transform transition duration-200 hover:scale-[1.02] active:scale-[0.98] shadow-lg hover:shadow-xl inline-block text-center">
                                        Register as Voter
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Login Link -->
                    <div class="text-center mt-12">
                        <p class="text-gray-600 text-lg">
                            Already have an account? 
                            <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:text-indigo-500 transition duration-200 hover:underline ml-1">
                                Sign in here
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>