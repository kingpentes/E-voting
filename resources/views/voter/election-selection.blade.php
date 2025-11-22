<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pilih Pemilu - {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gradient-to-br from-purple-50 via-white to-pink-50">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="shadow-lg" style="background: linear-gradient(to right, #3b24cc, #6a4cff);">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-white">Pilih Pemilu</h1>
                        <p class="text-blue-100 text-sm mt-1">Selamat datang, {{ $user->name }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-white hover:text-blue-100 font-medium px-4 py-2 rounded-lg hover:bg-white/10 transition duration-200">Logout</button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 w-full">
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-green-800">{{ session('success') }}</p>
                </div>
            @endif

            @if($elections->isEmpty())
                <div class="bg-white rounded-2xl shadow-xl p-12 text-center">
                    <svg class="w-20 h-20 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Belum Ada Pemilu Tersedia</h2>
                    <p class="text-gray-600">Anda belum terdaftar di pemilu manapun. Silakan gunakan kode undangan untuk bergabung.</p>
                    <a href="{{ route('voter.verification', ['add_new' => 1]) }}" class="mt-6 inline-block text-white font-semibold py-3 px-6 rounded-xl transform transition duration-200 hover:scale-[1.02] shadow-lg" style="background: linear-gradient(to right, #3b24cc, #6a4cff);">
                        Masukkan Kode Undangan
                    </a>
                </div>
            @else
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Pemilu yang Tersedia</h2>
                    <p class="text-gray-600">Pilih pemilu yang ingin Anda ikuti untuk mulai memberikan suara</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($elections as $election)
                        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition duration-300 overflow-hidden {{ $election->pivot->approval_status !== 'approved' ? 'opacity-75' : '' }}">
                            <div class="p-6">
                                <div class="flex items-center justify-between mb-4">
                                    @if($election->pivot->approval_status === 'approved')
                                        <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                                            Aktif
                                        </span>
                                    @elseif($election->pivot->approval_status === 'pending')
                                        <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">
                                            Menunggu Persetujuan
                                        </span>
                                    @else
                                        <span class="px-3 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full">
                                            Ditolak
                                        </span>
                                    @endif
                                    @if($election->pivot->access_code_used)
                                        <span class="text-xs text-gray-500">Kode: {{ $election->pivot->access_code_used }}</span>
                                    @endif
                                </div>
                                
                                <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $election->title }}</h3>
                                
                                @if($election->description)
                                    <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $election->description }}</p>
                                @endif

                                <div class="space-y-2 mb-6">
                                    <div class="flex items-center text-sm text-gray-600">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                        {{ $election->candidates_count ?? 0 }} Kandidat
                                    </div>
                                    <div class="flex items-center text-sm text-gray-600">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Bergabung: {{ $election->pivot->joined_at ? \Carbon\Carbon::parse($election->pivot->joined_at)->format('d M Y') : '-' }}
                                    </div>
                                </div>

                                @if($election->pivot->approval_status === 'approved')
                                    <a href="{{ route('voter.election', ['code' => $election->access_code]) }}" 
                                       class="block w-full text-center text-white font-semibold py-3 px-6 rounded-xl transform transition duration-200 hover:scale-[1.02] shadow-md"
                                       style="background: linear-gradient(to right, #3b24cc, #6a4cff);">
                                        Masuk ke Pemilu
                                    </a>
                                @elseif($election->pivot->approval_status === 'pending')
                                    <div class="w-full text-center bg-gray-300 text-gray-600 font-semibold py-3 px-6 rounded-xl cursor-not-allowed">
                                        <div class="flex items-center justify-center">
                                            <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            Menunggu Persetujuan Admin
                                        </div>
                                    </div>
                                @else
                                    <div class="w-full text-center bg-red-100 text-red-700 font-semibold py-3 px-6 rounded-xl">
                                        Ditolak
                                    </div>
                                    @if($election->pivot->rejection_reason)
                                        <p class="text-xs text-red-600 mt-2">Alasan: {{ $election->pivot->rejection_reason }}</p>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Add More Elections -->
                <div class="mt-8 text-center">
                    <p class="text-gray-600 mb-4">Punya kode undangan pemilu lain?</p>
                    <a href="{{ route('voter.verification', ['add_new' => 1]) }}" class="inline-block text-white font-semibold py-3 px-6 rounded-xl transform transition duration-200 hover:scale-[1.02] shadow-lg" style="background: linear-gradient(to right, #3b24cc, #6a4cff);">
                        Tambah Pemilu Baru
                    </a>
                </div>
            @endif
        </main>
    </div>
</body>
</html>
