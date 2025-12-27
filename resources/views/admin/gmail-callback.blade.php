@extends('layouts.admin')

@section('title', 'Gmail Authorization Code')

@section('content')
    <div class="min-h-screen bg-gray-100 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full bg-white rounded-xl shadow-lg p-8">
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900">Authorization Successful!</h2>
                <p class="text-gray-600 mt-2">Copy the code below and paste it in your terminal</p>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Authorization Code:</label>
                <div class="relative">
                    <input type="text" id="authCode" value="{{ $code }}" readonly
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg font-mono text-sm focus:ring-2 focus:ring-blue-500"
                        onclick="this.select()">
                    <button onclick="copyCode()"
                        class="absolute right-2 top-1/2 -translate-y-1/2 px-3 py-1.5 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition-colors">
                        Copy
                    </button>
                </div>
            </div>

            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-sm font-medium text-gray-700 mb-2">Instructions:</h3>
                <ol class="text-sm text-gray-600 space-y-1">
                    <li>1. Go back to your terminal</li>
                    <li>2. Paste this code when prompted</li>
                    <li>3. Press Enter to complete authentication</li>
                </ol>
            </div>

            <div id="copySuccess" class="hidden mt-4 p-3 bg-green-100 text-green-800 rounded-lg text-center text-sm">
                ✅ Code copied to clipboard!
            </div>
        </div>
    </div>

    <script>
        function copyCode() {
            const codeInput = document.getElementById('authCode');
            codeInput.select();
            codeInput.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(codeInput.value);

            const successMsg = document.getElementById('copySuccess');
            successMsg.classList.remove('hidden');
            setTimeout(() => successMsg.classList.add('hidden'), 3000);
        }
    </script>
@endsection
