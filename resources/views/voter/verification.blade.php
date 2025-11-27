<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verifikasi Identitas - {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gradient-to-br from-blue-50 via-white to-cyan-50">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="shadow-lg" style="background: linear-gradient(to right, #3b24cc, #6a4cff);">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-4">
                        @if($user->participatingElections->count() > 0)
                            <a href="{{ route('voter.verification') }}" class="text-white hover:text-blue-100 font-medium px-4 py-2 rounded-lg hover:bg-white/10 transition duration-200 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                                Kembali ke Daftar Pemilu
                            </a>
                        @endif
                        <h1 class="text-2xl font-bold text-white">Verifikasi Identitas</h1>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-white hover:text-blue-100 font-medium px-4 py-2 rounded-lg hover:bg-white/10 transition duration-200">Logout</button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 w-full">
            <!-- Status Messages -->
            @if($user->verification_status === 'pending' && $user->id_card)
                <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-yellow-800">⏳ Verifikasi Anda sedang ditinjau oleh admin. Mohon tunggu persetujuan.</p>
                </div>
            @endif

            @if($user->verification_status === 'rejected')
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-red-800">❌ Verifikasi ditolak: {{ $user->rejection_reason ?? 'Tidak ada alasan diberikan' }}</p>
                    <p class="text-sm text-red-600 mt-2">Silakan kirim ulang data verifikasi Anda dengan informasi yang benar.</p>
                </div>
            @endif

            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-green-800">{{ session('success') }}</p>
                </div>
            @endif

            <!-- Verification Form -->
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Verifikasi Identitas untuk Pemilu Baru</h2>
                <p class="text-gray-600 mb-8">Upload ID card, ambil foto wajah, dan masukkan kode undangan pemilu</p>

                <form action="{{ route('voter.verification.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Invite Code -->
                    <div class="mb-6">
                        <label for="invite_code" class="block text-sm font-semibold text-gray-700 mb-2">
                            Kode Undangan Pemilu <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="invite_code" name="invite_code" required maxlength="8"
                               value="{{ old('invite_code') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent uppercase"
                               placeholder="Masukkan kode undangan dari penyelenggara">
                        @error('invite_code')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- ID Card Upload -->
                    <div class="mb-6">
                        <label for="id_card" class="block text-sm font-semibold text-gray-700 mb-2">
                            Upload KTP/ID Card <span class="text-red-500">*</span>
                        </label>
                        <div class="border-2 border-dashed border-blue-300 rounded-xl p-6 text-center bg-purple-50">
                            <input type="file" id="id_card" name="id_card" accept="image/*" required
                                   onchange="previewIdCard(event)" class="hidden">
                            <label for="id_card" class="cursor-pointer">
                                <svg class="w-12 h-12 mx-auto mb-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                <p class="text-sm text-blue-600 font-semibold">Klik untuk upload KTP/ID Card</p>
                                <p class="text-xs text-gray-500 mt-1">PNG, JPG - Max 2MB</p>
                            </label>
                            <div id="idCardPreview" class="mt-4 hidden">
                                <img src="" alt="Preview" class="max-w-md mx-auto rounded-lg border">
                            </div>
                        </div>
                        @error('id_card')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Face Scan -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Foto Wajah (Face Scan) <span class="text-red-500">*</span>
                        </label>
                        <div class="bg-gray-900 rounded-xl p-6">
                            <video id="cameraStream" class="w-full max-w-md mx-auto rounded-lg hidden" autoplay playsinline></video>
                            <canvas id="faceCanvas" class="w-full max-w-md mx-auto rounded-lg hidden"></canvas>
                            
                            <div id="cameraPlaceholder" class="flex flex-col items-center justify-center h-64">
                                <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                                <p class="text-gray-400 mb-4">Posisikan wajah Anda di kamera</p>
                                <button type="button" onclick="startCamera()" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                                    Aktifkan Kamera
                                </button>
                            </div>

                            <div id="cameraControls" class="hidden mt-4 text-center space-x-4">
                                <button type="button" onclick="capturePhoto()" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700">
                                    Ambil Foto
                                </button>
                                <button type="button" onclick="retakePhoto()" class="bg-yellow-600 text-white px-6 py-2 rounded-lg hover:bg-yellow-700">
                                    Foto Ulang
                                </button>
                            </div>
                        </div>
                        <input type="hidden" name="face_image" id="faceImageData" required>
                        @error('face_image')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-8">
                        <button type="submit" class="w-full text-white font-semibold py-4 px-6 rounded-xl transform transition duration-200 hover:scale-[1.02] shadow-lg hover:shadow-xl" style="background: linear-gradient(to right, #3b24cc, #6a4cff);">
                            Kirim Data Verifikasi
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        let stream = null;

        function previewIdCard(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('idCardPreview');
                    preview.querySelector('img').src = e.target.result;
                    preview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        }

        async function startCamera() {
            try {
                stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { width: 480, height: 480, facingMode: 'user' } 
                });
                
                const video = document.getElementById('cameraStream');
                video.srcObject = stream;
                
                document.getElementById('cameraPlaceholder').classList.add('hidden');
                video.classList.remove('hidden');
                document.getElementById('cameraControls').classList.remove('hidden');
            } catch (err) {
                alert('Error mengakses kamera: ' + err.message);
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
            
            const controls = document.getElementById('cameraControls');
            controls.innerHTML = `
                <button type="button" onclick="retakePhoto()" class="bg-yellow-600 text-white px-6 py-2 rounded-lg hover:bg-yellow-700">
                    Foto Ulang
                </button>
                <span class="text-green-600 font-semibold">✓ Foto berhasil diambil</span>
            `;
        }

        function retakePhoto() {
            const video = document.getElementById('cameraStream');
            const canvas = document.getElementById('faceCanvas');
            
            canvas.classList.add('hidden');
            video.classList.remove('hidden');
            
            document.getElementById('faceImageData').value = '';
            
            const controls = document.getElementById('cameraControls');
            controls.innerHTML = `
                <button type="button" onclick="capturePhoto()" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700">
                    Ambil Foto
                </button>
                <button type="button" onclick="retakePhoto()" class="bg-yellow-600 text-white px-6 py-2 rounded-lg hover:bg-yellow-700">
                    Foto Ulang
                </button>
            `;
        }

        window.addEventListener('beforeunload', function() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
            }
        });
    </script>
</body>
</html>
