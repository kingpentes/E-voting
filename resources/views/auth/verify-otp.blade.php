<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verifikasi OTP - E-Voting</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-700 py-12 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-lg">
            <!-- Card Container -->
            <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">
                <!-- Decorative Header -->
                <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-8 py-6">
                    <div class="flex items-center justify-center mb-4">
                        <div class="bg-white/20 backdrop-blur-sm rounded-full p-4">
                            <svg class="h-12 w-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <h2 class="text-2xl font-bold text-white text-center">
                        Verifikasi Kode OTP
                    </h2>
                    <p class="mt-2 text-sm text-blue-100 text-center">
                        Kode verifikasi telah dikirim ke
                    </p>
                    <div class="mt-2 bg-white/10 backdrop-blur-sm rounded-lg px-4 py-2 text-center">
                        <p class="text-sm font-semibold text-white">
                            {{ $email }}
                        </p>
                    </div>
                </div>

                <div class="px-8 py-8">

                <div class="px-8 py-8">
                    <!-- Alert Messages -->
                    @if (session('success'))
                        <div class="mb-6 bg-green-50 border-l-4 border-green-500 rounded-lg p-4 animate-slide-in">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <div class="bg-green-500 rounded-full p-1">
                                        <svg class="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-6 bg-red-50 border-l-4 border-red-500 rounded-lg p-4 animate-shake">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <div class="bg-red-500 rounded-full p-1">
                                        <svg class="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Form -->
                    <form method="POST" action="{{ route('password.verify-otp') }}" id="otpForm" class="space-y-6">
                        @csrf
                        <input type="hidden" name="email" value="{{ $email }}">

                        <!-- OTP Input Container -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3 text-center">
                                Masukkan 6 Digit Kode OTP
                            </label>
                            
                            <!-- Individual OTP Boxes -->
                            <div class="flex justify-center gap-2 mb-2">
                                <input type="text" maxlength="1" class="otp-input w-12 h-14 text-center text-2xl font-bold border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none transition-all" data-index="0" inputmode="numeric" pattern="[0-9]" autocomplete="off">
                                <input type="text" maxlength="1" class="otp-input w-12 h-14 text-center text-2xl font-bold border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none transition-all" data-index="1" inputmode="numeric" pattern="[0-9]" autocomplete="off">
                                <input type="text" maxlength="1" class="otp-input w-12 h-14 text-center text-2xl font-bold border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none transition-all" data-index="2" inputmode="numeric" pattern="[0-9]" autocomplete="off">
                                <input type="text" maxlength="1" class="otp-input w-12 h-14 text-center text-2xl font-bold border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none transition-all" data-index="3" inputmode="numeric" pattern="[0-9]" autocomplete="off">
                                <input type="text" maxlength="1" class="otp-input w-12 h-14 text-center text-2xl font-bold border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none transition-all" data-index="4" inputmode="numeric" pattern="[0-9]" autocomplete="off">
                                <input type="text" maxlength="1" class="otp-input w-12 h-14 text-center text-2xl font-bold border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none transition-all" data-index="5" inputmode="numeric" pattern="[0-9]" autocomplete="off">
                            </div>
                            
                            <!-- Hidden input untuk submit -->
                            <input type="hidden" name="otp" id="otp" value="{{ old('otp') }}">
                            
                            @error('otp')
                                <p class="mt-2 text-sm text-red-600 text-center animate-shake">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Timer Display -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                            <div class="flex items-center justify-center space-x-2">
                                <svg class="h-5 w-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-sm font-medium text-blue-700">
                                    Kode akan kadaluarsa dalam <span id="timer" class="font-bold">10:00</span>
                                </span>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div>
                            <button type="submit" id="verifyBtn"
                                    class="w-full flex justify-center items-center py-3.5 px-4 border border-transparent rounded-xl shadow-lg text-base font-semibold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 transform hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Verifikasi Kode
                            </button>
                        </div>
                    </form>

                    <!-- Resend OTP -->
                    <div class="mt-6 text-center">
                        <p class="text-sm text-gray-600 mb-2">
                            Tidak menerima kode?
                        </p>
                        <form method="POST" action="{{ route('password.resend-otp') }}" class="inline" id="resendForm">
                            @csrf
                            <input type="hidden" name="email" value="{{ $email }}">
                            <button type="submit" id="resendBtn"
                                    class="inline-flex items-center px-4 py-2 text-sm font-semibold text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-all duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Kirim Ulang OTP
                            </button>
                        </form>
                    </div>

                    <!-- Back Button -->
                    <div class="mt-4 text-center pt-4 border-t border-gray-200">
                        <a href="{{ route('password.request') }}" 
                           class="inline-flex items-center text-sm font-medium text-gray-600 hover:text-gray-800 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Gunakan Email Lain
                        </a>
                    </div>
                </div>
            </div>

            <!-- Security Notice -->
            <div class="mt-6 bg-yellow-50 border-l-4 border-yellow-400 rounded-lg p-4 shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-semibold text-yellow-800 mb-1">Keamanan Akun</h3>
                        <p class="text-xs text-yellow-700">
                            Jangan bagikan kode OTP kepada siapapun. Tim kami tidak akan pernah meminta kode verifikasi Anda.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* OTP Input Styling */
        .otp-input:focus {
            transform: scale(1.05);
        }
        
        .otp-input.error {
            border-color: #ef4444;
            animation: shake 0.5s;
        }
        
        /* Animations */
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }
        
        @keyframes slide-in {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-shake {
            animation: shake 0.5s;
        }
        
        .animate-slide-in {
            animation: slide-in 0.3s ease-out;
        }
        
        /* Prevent number input spinners */
        input[type="text"].otp-input::-webkit-outer-spin-button,
        input[type="text"].otp-input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    </style>

    <script>
        // OTP Input Handler
        document.addEventListener('DOMContentLoaded', function() {
            const otpInputs = document.querySelectorAll('.otp-input');
            const hiddenOtpInput = document.getElementById('otp');
            const verifyBtn = document.getElementById('verifyBtn');
            const otpForm = document.getElementById('otpForm');
            
            // Auto-focus first input
            otpInputs[0].focus();
            
            // Handle input
            otpInputs.forEach((input, index) => {
                input.addEventListener('input', function(e) {
                    const value = e.target.value;
                    
                    // Only allow numbers
                    if (!/^[0-9]$/.test(value) && value !== '') {
                        e.target.value = '';
                        return;
                    }
                    
                    // Remove error styling
                    input.classList.remove('error');
                    
                    // Move to next input
                    if (value && index < otpInputs.length - 1) {
                        otpInputs[index + 1].focus();
                    }
                    
                    // Update hidden input
                    updateHiddenInput();
                    
                    // Auto-submit when all filled
                    if (index === otpInputs.length - 1 && value) {
                        const otp = Array.from(otpInputs).map(input => input.value).join('');
                        if (otp.length === 6) {
                            setTimeout(() => otpForm.submit(), 300);
                        }
                    }
                });
                
                // Handle backspace
                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Backspace' && !e.target.value && index > 0) {
                        otpInputs[index - 1].focus();
                    }
                });
                
                // Handle paste
                input.addEventListener('paste', function(e) {
                    e.preventDefault();
                    const pastedData = e.clipboardData.getData('text');
                    const digits = pastedData.match(/\d/g);
                    
                    if (digits) {
                        digits.slice(0, 6).forEach((digit, i) => {
                            if (otpInputs[i]) {
                                otpInputs[i].value = digit;
                            }
                        });
                        updateHiddenInput();
                        
                        // Focus last filled input
                        const lastIndex = Math.min(digits.length - 1, 5);
                        otpInputs[lastIndex].focus();
                        
                        // Auto-submit if complete
                        if (digits.length >= 6) {
                            setTimeout(() => otpForm.submit(), 300);
                        }
                    }
                });
            });
            
            // Update hidden input with combined OTP
            function updateHiddenInput() {
                const otp = Array.from(otpInputs).map(input => input.value).join('');
                hiddenOtpInput.value = otp;
                verifyBtn.disabled = otp.length !== 6;
            }
            
            // Timer countdown (10 minutes)
            let timeLeft = 600; // 10 minutes in seconds
            const timerElement = document.getElementById('timer');
            const resendBtn = document.getElementById('resendBtn');
            
            function updateTimer() {
                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;
                timerElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
                
                if (timeLeft === 0) {
                    clearInterval(timerInterval);
                    timerElement.textContent = 'Kadaluarsa';
                    timerElement.classList.add('text-red-600', 'font-bold');
                    verifyBtn.disabled = true;
                    
                    // Show error on inputs
                    otpInputs.forEach(input => {
                        input.classList.add('error');
                        input.disabled = true;
                    });
                    
                    // Enable resend button
                    resendBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    resendBtn.classList.add('animate-pulse');
                }
                
                timeLeft--;
            }
            
            const timerInterval = setInterval(updateTimer, 1000);
            updateTimer(); // Initial call
            
            // Handle form submission
            otpForm.addEventListener('submit', function(e) {
                const otp = hiddenOtpInput.value;
                if (otp.length !== 6) {
                    e.preventDefault();
                    otpInputs.forEach(input => input.classList.add('error'));
                }
            });
        });
    </script>
</body>
</html>
