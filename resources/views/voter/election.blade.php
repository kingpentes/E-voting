<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $election->title }} - E-Voting</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gradient-to-br from-blue-50 via-white to-indigo-50">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-gradient-to-r from-blue-600 to-indigo-700 shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        @auth
                            <a href="{{ route('voter.verification') }}" class="text-white hover:text-blue-100 font-medium px-4 py-2 rounded-lg hover:bg-white/10 transition duration-200 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                                Kembali
                            </a>
                        @endauth
                        <div>
                            <h1 class="text-3xl font-bold text-white">{{ $election->title }}</h1>
                            <p class="text-blue-100 mt-1">{{ $election->description }}</p>
                        </div>
                    </div>
                    @auth
                    <div class="flex items-center space-x-4">
                        <span class="text-white text-sm">{{ Auth::user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-blue-100 hover:text-white font-semibold text-sm transition-colors">
                                Logout
                            </button>
                        </form>
                    </div>
                    @endauth
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            @if(session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-400 text-green-700 px-4 py-3 rounded shadow-md">
                    <p class="font-semibold">{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-50 border-l-4 border-red-400 text-red-700 px-4 py-3 rounded shadow-md">
                    <p class="font-semibold">{{ session('error') }}</p>
                </div>
            @endif

            @if(Auth::check() && $hasVoted)
                <div class="mb-6 bg-blue-50 border-l-4 border-blue-400 text-blue-700 px-4 py-3 rounded shadow-md">
                    <p class="font-semibold">✓ Anda sudah memberikan suara pada pemilu ini</p>
                </div>
            @endif

            <!-- Election Schedule Info -->
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl shadow-lg p-6 mb-8 text-white">
                <div class="flex items-center mb-4">
                    <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <h3 class="text-2xl font-bold">Waktu Pelaksanaan Pemilu</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Start Date & Time -->
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                        <div class="flex items-center mb-2">
                            <svg class="w-5 h-5 mr-2 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-sm font-semibold text-green-200">Mulai</p>
                        </div>
                        <p class="text-2xl font-bold">{{ \Carbon\Carbon::parse($election->start_date)->format('d M Y') }}</p>
                        <p class="text-lg mt-1">Pukul {{ $election->start_time ?? '00:00' }} WIB</p>
                    </div>
                    
                    <!-- End Date & Time -->
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                        <div class="flex items-center mb-2">
                            <svg class="w-5 h-5 mr-2 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-sm font-semibold text-red-200">Berakhir</p>
                        </div>
                        <p class="text-2xl font-bold">{{ \Carbon\Carbon::parse($election->end_date)->format('d M Y') }}</p>
                        <p class="text-lg mt-1">Pukul {{ $election->end_time ?? '23:59' }} WIB</p>
                    </div>
                </div>
                
                <!-- Status Badge -->
                @php
                    $now = now();
                    
                    // Create start datetime from date and time
                    $startDate = $election->start_date instanceof \Carbon\Carbon 
                        ? $election->start_date->format('Y-m-d') 
                        : $election->start_date;
                    $start = \Carbon\Carbon::parse($startDate . ' ' . ($election->start_time ?? '00:00:00'));
                    
                    // Create end datetime from date and time
                    $endDate = $election->end_date instanceof \Carbon\Carbon 
                        ? $election->end_date->format('Y-m-d') 
                        : $election->end_date;
                    $end = \Carbon\Carbon::parse($endDate . ' ' . ($election->end_time ?? '23:59:59'));
                    
                    if ($now->lt($start)) {
                        $statusText = 'Belum Dimulai';
                        $statusColor = 'bg-yellow-500';
                        $statusIcon = 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z';
                    } elseif ($now->between($start, $end)) {
                        $statusText = 'Sedang Berlangsung';
                        $statusColor = 'bg-green-500';
                        $statusIcon = 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z';
                    } else {
                        $statusText = 'Telah Berakhir';
                        $statusColor = 'bg-red-500';
                        $statusIcon = 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z';
                    }
                @endphp
                
                <div class="mt-4 flex items-center justify-center">
                    <div class="{{ $statusColor }} px-6 py-2 rounded-full flex items-center shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $statusIcon }}"></path>
                        </svg>
                        <span class="font-bold text-lg">{{ $statusText }}</span>
                    </div>
                </div>
            </div>

           

            <!-- Election Rules -->
            @if($election->rules && $election->rules->count() > 0)
            <div class="bg-white rounded-2xl shadow-lg p-8 mb-8 border border-gray-100">
                <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                    <svg class="w-7 h-7 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Peraturan Pemilihan
                </h3>
                <ol class="space-y-3">
                    @foreach($election->rules as $rule)
                    <li class="flex items-start">
                        <span class="flex-shrink-0 w-8 h-8 bg-blue-100 text-blue-700 rounded-full flex items-center justify-center font-bold text-sm mr-3">
                            {{ $loop->iteration }}
                        </span>
                        <span class="text-gray-700 pt-1">{{ $rule->rule }}</span>
                    </li>
                    @endforeach
                </ol>
            </div>
            @endif

            <!-- Voting Results (shown when election is closed) -->
            @if($election->status === 'closed')
            <div class="bg-gradient-to-r from-purple-600 to-pink-600 rounded-2xl shadow-xl p-8 mb-8 text-white">
                <div class="flex items-center justify-center mb-6">
                    <svg class="w-10 h-10 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <h3 class="text-3xl font-bold">Hasil Voting</h3>
                </div>
                
                @php
                    $totalVotes = $candidates->sum(function($candidate) {
                        return $candidate->votes->count();
                    });
                @endphp

                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 mb-6">
                    <div class="flex justify-between items-center">
                        <span class="text-xl font-semibold">Total Suara Masuk:</span>
                        <span class="text-4xl font-bold">{{ $totalVotes }}</span>
                    </div>
                </div>

                <div class="space-y-4">
                    @foreach($candidates->sortByDesc(function($candidate) { return $candidate->votes->count(); }) as $candidate)
                        @php
                            $voteCount = $candidate->votes->count();
                            $percentage = $totalVotes > 0 ? ($voteCount / $totalVotes) * 100 : 0;
                            $isWinner = $loop->first && $voteCount > 0;
                        @endphp
                        
                        <div class="bg-white rounded-xl p-5 {{ $isWinner ? 'ring-4 ring-yellow-400 shadow-2xl' : 'shadow-lg' }}">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center space-x-4">
                                    @if($isWinner)
                                        <div class="flex-shrink-0">
                                            <svg class="w-8 h-8 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                    <img src="{{ $candidate->photo_url }}" alt="{{ $candidate->name }}" class="w-12 h-12 rounded-full object-cover border-2 border-purple-200">
                                    <div>
                                        <div class="flex items-center space-x-2">
                                            <span class="font-bold text-gray-900 text-lg">{{ $candidate->name }}</span>
                                            @if($isWinner)
                                                <span class="bg-yellow-400 text-yellow-900 text-xs font-bold px-2 py-1 rounded-full">PEMENANG</span>
                                            @endif
                                        </div>
                                        <span class="text-sm text-gray-600">Kandidat #{{ $candidate->number }}</span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-2xl font-bold text-purple-600">{{ $voteCount }}</div>
                                    <div class="text-sm text-gray-600">suara</div>
                                </div>
                            </div>
                            
                            <!-- Progress Bar -->
                            <div class="relative w-full bg-gray-200 rounded-full h-6 overflow-hidden">
                                <div class="absolute top-0 left-0 h-full bg-gradient-to-r from-purple-500 to-pink-500 rounded-full transition-all duration-500 flex items-center justify-end px-3" 
                                     style="width: {{ $percentage }}%">
                                    <span class="text-xs font-bold text-white">{{ number_format($percentage, 1) }}%</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Candidates Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @forelse($candidates as $candidate)
                    <a href="{{ route('voter.candidate.detail', ['code' => $election->access_code, 'candidateId' => $candidate->id]) }}" 
                       class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden border border-gray-100 hover:border-blue-300">
                        <div class="p-6">
                            <div class="flex flex-col items-center text-center">
                                <div class="relative mb-4">
                                    <img src="{{ $candidate->photo_url }}" 
                                         alt="{{ $candidate->name }}"
                                         class="w-32 h-32 rounded-full object-cover border-4 border-blue-100 group-hover:border-blue-300 transition-all shadow-lg">
                                    <span class="absolute -bottom-2 left-1/2 transform -translate-x-1/2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-sm font-bold px-4 py-1 rounded-full shadow-lg">
                                        #{{ $candidate->number }}
                                    </span>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors">
                                    {{ $candidate->name }}
                                </h3>
                            </div>
                            
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <div class="mb-3">
                                    <p class="text-xs font-semibold text-blue-600 mb-1">VISI</p>
                                    <p class="text-sm text-gray-600 line-clamp-3">
                                        {{ Str::limit($candidate->vision, 100) }}
                                    </p>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-500">
                                        {{ $candidate->missions->count() }} Misi
                                    </span>
                                    <span class="text-blue-600 font-semibold group-hover:text-blue-700 flex items-center">
                                        Lihat Detail
                                        <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full text-center py-16">
                        <div class="text-gray-300 mb-4">
                            <svg class="w-24 h-24 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <p class="text-gray-500 text-xl font-semibold">Belum ada kandidat terdaftar</p>
                    </div>
                @endforelse
            </div>
        </main>


        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 py-6 mt-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-gray-600">
                <p>&copy; {{ date('Y') }} E-Voting System. All rights reserved.</p>
            </div>
        </footer>
    </div>
</body>
</html>