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

                        <!-- Registration Steps Indicator -->
                        <div class="flex items-center justify-center mb-8">
                                <div class="flex items-center space-x-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-purple-600 rounded-full flex items-center justify-center text-white text-sm font-semibold" data-step="1">1</div>
                                    <span class="ml-2 text-sm font-medium text-gray-700">Basic Info</span>
                                </div>
                                <div class="w-8 h-0.5 bg-gray-300"></div>
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center text-gray-600 text-sm font-semibold" data-step="2">2</div>
                                    <span class="ml-2 text-sm font-medium text-gray-500">ID Verification</span>
                                </div>
                                <div class="w-8 h-0.5 bg-gray-300"></div>
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center text-gray-600 text-sm font-semibold" data-step="3">3</div>
                                    <span class="ml-2 text-sm font-medium text-gray-500">Face Scan</span>
                                </div>
                            </div>
                        </div>

                        <!-- Registration Form -->
                        <div class="bg-white/80 backdrop-blur-sm shadow-xl rounded-2xl p-8 border border-white/20">
                            <form method="POST" action="{{ route('register.voter.store') }}" enctype="multipart/form-data" class="space-y-6" id="voterRegistrationForm">
                                @csrf
                                <input type="hidden" name="user_type" value="voter">

                                <!-- Step 1: Basic Information -->
                                <div id="step1" class="step-content">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        Basic Information
                                    </h3>

                                    <div class="grid grid-cols-1 gap-6">
                                        <!-- Invite code removed from registration; voters can enter it during login -->
                                        <div class="mb-4">
                                            <p class="text-sm text-gray-600">If you already have an invitation code, enter it on the login page after creating your account. You can also provide the code during registration, but it's optional.</p>
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
                                                placeholder="Enter your full name as on ID"
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
                                                        Password
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
                                                        Confirm Password
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
                                            type="button"
                                            onclick="nextStep(1)"
                                            class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white font-semibold py-3 px-6 rounded-xl hover:from-purple-700 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transform transition duration-200 hover:scale-[1.02] active:scale-[0.98] shadow-lg hover:shadow-xl"
                                        >
                                            Continue to ID Verification
                                        </button>
                                    </div>
                                </div>

                                <!-- Step 2: ID Card Upload -->
                                <div id="step2" class="step-content hidden">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                                        </svg>
                                        ID Card Verification
                                    </h3>

                                    <div class="space-y-6">
                                        <!-- ID Card Upload -->
                                        <div class="group">
                                            <label for="id_card" class="block text-sm font-semibold text-gray-700 mb-3">
                                                <span class="flex items-center">
                                                    <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                                    </svg>
                                                    Upload ID Card <span class="text-red-500">*</span>
                                                </span>
                                            </label>
                                            <div class="relative">
                                                <input 
                                                    id="id_card" 
                                                    type="file" 
                                                    name="id_card" 
                                                    accept="image/*"
                                                    required
                                                    class="hidden"
                                                    onchange="handleFileUpload(this, 'id-preview')"
                                                />
                                                <label for="id_card" class="flex flex-col items-center justify-center w-full h-32 border-2 border-purple-300 border-dashed rounded-xl cursor-pointer bg-purple-50 hover:bg-purple-100 transition duration-200">
                                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                        <svg class="w-8 h-8 mb-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                                        </svg>
                                                        <p class="mb-2 text-sm text-purple-600"><span class="font-semibold">Click to upload</span> your ID card</p>
                                                        <p class="text-xs text-purple-500">PNG, JPG up to 10MB</p>
                                                    </div>
                                                </label>
                                                <div id="id-preview" class="mt-4 hidden">
                                                    <img class="w-full h-48 object-cover rounded-xl border" />
                                                </div>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-2">Please ensure your ID card is clearly visible and readable</p>
                                            <x-input-error :messages="$errors->get('id_card')" class="mt-2" />
                                        </div>
                                    </div>

                                    <div class="flex space-x-4 pt-6">
                                        <button 
                                            type="button"
                                            onclick="prevStep(2)"
                                            class="flex-1 bg-gray-200 text-gray-700 font-semibold py-3 px-6 rounded-xl hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition duration-200"
                                        >
                                            Back
                                        </button>
                                        <button 
                                            type="button"
                                            onclick="nextStep(2)"
                                            class="flex-1 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-semibold py-3 px-6 rounded-xl hover:from-purple-700 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transform transition duration-200 hover:scale-[1.02] active:scale-[0.98] shadow-lg hover:shadow-xl"
                                        >
                                            Continue to Face Scan
                                        </button>
                                    </div>
                                </div>

                                <!-- Step 3: Face Scan -->
                                <div id="step3" class="step-content hidden">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                        </svg>
                                        Face Verification
                                    </h3>

                                    <div class="space-y-6">
                                        <!-- Face Scan Section -->
                                        <div class="group">
                                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                                <span class="flex items-center">
                                                    <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                    </svg>
                                                    Face Scan <span class="text-red-500">*</span>
                                                </span>
                                            </label>
                                            
                                            <div class="bg-gray-900 rounded-xl p-6 text-center">
                                                <video id="cameraStream" class="w-full max-w-sm mx-auto rounded-lg hidden" autoplay></video>
                                                <canvas id="faceCanvas" class="w-full max-w-sm mx-auto rounded-lg hidden"></canvas>
                                                
                                                <div id="cameraPlaceholder" class="flex flex-col items-center justify-center h-64">
                                                    <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                    </svg>
                                                    <p class="text-gray-400 mb-4">Position your face in the camera frame</p>
                                                    <button 
                                                        type="button"
                                                        onclick="startCamera()"
                                                        class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition duration-200"
                                                    >
                                                        Start Camera
                                                    </button>
                                                </div>

                                                <div id="cameraControls" class="hidden mt-4 space-x-4">
                                                    <button 
                                                        type="button"
                                                        onclick="capturePhoto()"
                                                        class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition duration-200"
                                                    >
                                                        Capture Photo
                                                    </button>
                                                    <button 
                                                        type="button"
                                                        onclick="retakePhoto()"
                                                        class="bg-yellow-600 text-white px-6 py-2 rounded-lg hover:bg-yellow-700 transition duration-200"
                                                    >
                                                        Retake
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            <input type="hidden" name="face_image" id="faceImageData">
                                            <p class="text-xs text-gray-500 mt-2">Look directly at the camera and ensure good lighting</p>
                                        </div>
                                    </div>

                                    <div class="flex space-x-4 pt-6">
                                        <button 
                                            type="button"
                                            onclick="prevStep(3)"
                                            class="flex-1 bg-gray-200 text-gray-700 font-semibold py-3 px-6 rounded-xl hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition duration-200"
                                        >
                                            Back
                                        </button>
                                        <button 
                                            type="submit"
                                            class="flex-1 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-semibold py-3 px-6 rounded-xl hover:from-purple-700 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transform transition duration-200 hover:scale-[1.02] active:scale-[0.98] shadow-lg hover:shadow-xl"
                                        >
                                            <span class="flex items-center justify-center">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Complete Registration
                                            </span>
                                        </button>
                                    </div>
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

        <script>
            let currentStep = 1;
            let stream = null;

            // Step Navigation
            function nextStep(step) {
                if (validateStep(step)) {
                    hideStep(step);
                    showStep(step + 1);
                    updateProgressIndicator(step + 1);
                    currentStep = step + 1;
                }
            }

            function prevStep(step) {
                hideStep(step);
                showStep(step - 1);
                updateProgressIndicator(step - 1);
                currentStep = step - 1;
            }

            function showStep(step) {
                document.getElementById('step' + step).classList.remove('hidden');
            }

            function hideStep(step) {
                document.getElementById('step' + step).classList.add('hidden');
            }

            function updateProgressIndicator(step) {
                for (let i = 1; i <= 3; i++) {
                    const indicator = document.querySelector(`[data-step="${i}"]`);
                    if (i <= step) {
                        indicator?.classList.add('bg-purple-600', 'text-white');
                        indicator?.classList.remove('bg-gray-300', 'text-gray-600');
                    } else {
                        indicator?.classList.add('bg-gray-300', 'text-gray-600');
                        indicator?.classList.remove('bg-purple-600', 'text-white');
                    }
                }
            }

            function validateStep(step) {
                if (step === 1) {
                    // Basic info required fields. 'invite_code' was made optional and may not exist.
                    const required = ['name', 'email', 'password', 'password_confirmation'];
                    for (const field of required) {
                        const el = document.getElementById(field);
                        if (!el || el.value.trim() === '') {
                            return false;
                        }
                    }
                    return true;
                }
                if (step === 2) {
                    const input = document.getElementById('id_card');
                    return input && input.files && input.files.length > 0;
                }
                return true;
            }

            // File Upload Handler
            function handleFileUpload(input, previewId) {
                const file = input.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const preview = document.getElementById(previewId);
                        preview.querySelector('img').src = e.target.result;
                        preview.classList.remove('hidden');
                    };
                    reader.readAsDataURL(file);
                }
            }

            // Camera Functions
            async function startCamera() {
                try {
                    stream = await navigator.mediaDevices.getUserMedia({ 
                        video: { 
                            width: 480, 
                            height: 480,
                            facingMode: 'user'
                        } 
                    });
                    
                    const video = document.getElementById('cameraStream');
                    video.srcObject = stream;
                    
                    document.getElementById('cameraPlaceholder').classList.add('hidden');
                    video.classList.remove('hidden');
                    document.getElementById('cameraControls').classList.remove('hidden');
                } catch (err) {
                    alert('Error accessing camera: ' + err.message);
                }
            }

            function capturePhoto() {
                const video = document.getElementById('cameraStream');
                const canvas = document.getElementById('faceCanvas');
                const context = canvas.getContext('2d');
                
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                context.drawImage(video, 0, 0);
                
                const imageData = canvas.toDataURL('image/jpeg', 0.8);
                document.getElementById('faceImageData').value = imageData;
                
                video.classList.add('hidden');
                canvas.classList.remove('hidden');
                
                // Update controls
                const controls = document.getElementById('cameraControls');
                controls.innerHTML = `
                    <button type="button" onclick="retakePhoto()" class="bg-yellow-600 text-white px-6 py-2 rounded-lg hover:bg-yellow-700 transition duration-200">
                        Retake Photo
                    </button>
                    <span class="text-green-600 font-semibold">✓ Photo captured successfully</span>
                `;
            }

            function retakePhoto() {
                const video = document.getElementById('cameraStream');
                const canvas = document.getElementById('faceCanvas');
                
                canvas.classList.add('hidden');
                video.classList.remove('hidden');
                
                document.getElementById('faceImageData').value = '';
                
                // Reset controls
                const controls = document.getElementById('cameraControls');
                controls.innerHTML = `
                    <button type="button" onclick="capturePhoto()" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition duration-200">
                        Capture Photo
                    </button>
                    <button type="button" onclick="retakePhoto()" class="bg-yellow-600 text-white px-6 py-2 rounded-lg hover:bg-yellow-700 transition duration-200">
                        Retake
                    </button>
                `;
            }

            // Cleanup camera stream when page unloads
            window.addEventListener('beforeunload', function() {
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                }
            });
        </script>
    </body>
</html>