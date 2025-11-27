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

<body class="font-sans antialiased bg-gradient-to-br from-blue-50 via-sky-50 to-cyan-50">
    <div class="min-h-screen flex flex-col">
        <!-- Header with glassmorphism -->
        <header class="sticky top-0 z-50 glass-strong shadow-lg border-b border-white/20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex justify-between items-center">
                    <div class="animate-fade-in">
                        <h1
                            class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent">
                            Pilih Pemilu</h1>
                        <p class="text-blue-700 text-sm mt-1">Selamat datang, {{ $user->name }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="text-sm text-gray-700 hover:text-blue-600 font-medium px-5 py-2.5 rounded-xl hover:bg-white/50 transition-all duration-200 backdrop-blur-sm">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 w-full animate-slide-up">
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-xl shadow-lg animate-scale-in">
                    <p class="text-green-800 font-medium">{{ session('success') }}</p>
                </div>
            @endif

            @if ($elections->isEmpty())
                <div class="glass rounded-3xl shadow-glass p-12 text-center animate-scale-in">
                    <div class="animate-float">
                        <svg class="w-24 h-24 text-blue-300 mx-auto mb-6" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                            </path>
                        </svg>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-3">Belum Ada Pemilu Tersedia</h2>
                    <p class="text-gray-600 mb-8 text-lg">Anda belum terdaftar di pemilu manapun. Silakan gunakan kode
                        undangan untuk bergabung.</p>
                    <a href="{{ route('voter.verification', ['add_new' => 1]) }}"
                        class="inline-block bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold py-4 px-8 rounded-xl transform transition-all duration-200 hover:scale-105 hover:shadow-glow shadow-lg">
                        Masukkan Kode Undangan
                    </a>
                </div>
            @else
                <div class="mb-10">
                    <h2 class="text-3xl font-bold text-gray-900 mb-3 animate-fade-in">Pemilu yang Tersedia</h2>
                    <p class="text-gray-600 text-lg animate-fade-in">Pilih pemilu yang ingin Anda ikuti untuk mulai
                        memberikan suara</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($elections as $election)
                        <div class="glass-strong rounded-2xl shadow-glass hover:shadow-glow-lg transition-all duration-300 overflow-hidden transform hover:scale-105 hover-lift {{ $election->pivot->approval_status !== 'approved' ? 'opacity-75' : '' }} animate-scale-in"
                            style="animation-delay: {{ $loop->index * 0.1 }}s;">
                            <div class="p-6">
                                <div class="flex items-center justify-between mb-4">
                                    @if ($election->status === 'closed')
                                        <span
                                            class="px-4 py-1.5 bg-gradient-to-r from-red-500 to-red-600 text-white text-xs font-semibold rounded-full shadow-md">
                                            Ditutup
                                        </span>
                                    @elseif($election->pivot->approval_status === 'approved')
                                        <span
                                            class="px-4 py-1.5 bg-gradient-to-r from-green-500 to-emerald-600 text-white text-xs font-semibold rounded-full shadow-md">
                                            Aktif
                                        </span>
                                    @elseif($election->pivot->approval_status === 'pending')
                                        <span
                                            class="px-4 py-1.5 bg-gradient-to-r from-yellow-500 to-orange-500 text-white text-xs font-semibold rounded-full shadow-md">
                                            Menunggu Persetujuan
                                        </span>
                                    @else
                                        <span
                                            class="px-4 py-1.5 bg-gradient-to-r from-red-500 to-red-600 text-white text-xs font-semibold rounded-full shadow-md">
                                            Ditolak
                                        </span>
                                    @endif
                                    @if ($election->pivot->access_code_used)
                                        <span
                                            class="text-xs text-gray-500 font-mono bg-gray-100 px-3 py-1 rounded-lg">{{ $election->pivot->access_code_used }}</span>
                                    @endif
                                </div>

                                <h3 class="text-2xl font-bold text-gray-900 mb-3">{{ $election->title }}</h3>

                                @if ($election->description)
                                    <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $election->description }}</p>
                                @endif

                                <div class="space-y-2 mb-6">
                                    <div class="flex items-center text-sm text-gray-600">
                                        <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Bergabung:
                                        {{ $election->pivot->joined_at ? \Carbon\Carbon::parse($election->pivot->joined_at)->format('d M Y') : '-' }}
                                    </div>
                                </div>

                                @if ($election->pivot->approval_status === 'approved')
                                    <a href="{{ route('voter.election', ['code' => $election->access_code]) }}"
                                        class="block w-full text-center bg-gradient-to-r from-blue-600 to-cyan-600 text-white font-semibold py-3.5 px-6 rounded-xl transform transition-all duration-200 hover:scale-105 shadow-lg hover:shadow-glow">
                                        Masuk ke Pemilu
                                    </a>
                                @elseif($election->pivot->approval_status === 'pending')
                                    <div
                                        class="w-full text-center bg-gradient-to-r from-gray-300 to-gray-400 text-gray-700 font-semibold py-3.5 px-6 rounded-xl cursor-not-allowed">
                                        <div class="flex items-center justify-center">
                                            <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                                    stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                </path>
                                            </svg>
                                            Menunggu Persetujuan Admin
                                        </div>
                                    </div>
                                @else
                                    <div
                                        class="w-full text-center bg-red-100 text-red-700 font-semibold py-3.5 px-6 rounded-xl border border-red-200">
                                        Ditolak
                                    </div>
                                    @if ($election->pivot->rejection_reason)
                                        <p class="text-xs text-red-600 mt-2">Alasan:
                                            {{ $election->pivot->rejection_reason }}</p>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Add More Elections -->
                <div class="mt-12 text-center animate-fade-in">
                    <p class="text-gray-600 mb-6 text-lg">Punya kode undangan pemilu lain?</p>
                    <a href="{{ route('voter.verification', ['add_new' => 1]) }}"
                        class="inline-block bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold py-4 px-8 rounded-xl transform transition-all duration-200 hover:scale-105 hover:shadow-glow shadow-lg">
                        Tambah Pemilu Baru
                    </a>
                </div>
            @endif
        </main>
    </div>
</body>

</html>
