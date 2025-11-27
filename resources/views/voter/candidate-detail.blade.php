<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $candidate->name }} - {{ $election->title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gradient-to-br from-blue-50 via-white to-indigo-50">
    <div class="min-h-screen pb-12">
        <!-- Header with Gradient -->
        <header class="bg-gradient-to-r from-blue-600 to-cyan-700 shadow-lg">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <a href="{{ route('voter.election', ['code' => $election->access_code]) }}" 
                   class="inline-flex items-center text-white hover:text-blue-100 font-semibold mb-4 transition-colors group">
                    <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>
                <h1 class="text-3xl font-bold text-white">Detail Kandidat</h1>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="bg-white rounded-2xl shadow-2xl overflow-hidden border border-gray-100">
                <!-- Candidate Profile Section -->
                <div class="p-8 text-center border-b border-gray-100">
                    <div class="relative inline-block mb-4">
                        <img src="{{ $candidate->photo_url }}" 
                             alt="{{ $candidate->name }}"
                             class="w-40 h-40 rounded-full object-cover border-4 border-blue-100 shadow-xl mx-auto">
                        <span class="absolute -bottom-3 left-1/2 transform -translate-x-1/2 bg-gradient-to-r from-blue-600 to-cyan-600 text-white text-lg font-bold px-6 py-2 rounded-full shadow-lg">
                            #{{ $candidate->number }}
                        </span>
                    </div>
                    <h2 class="text-4xl font-bold text-gray-900 mt-6 mb-2">{{ $candidate->name }}</h2>
                    <p class="text-gray-600 text-lg">Kandidat {{ $election->title }}</p>
                </div>

                <div class="p-8">
                    <!-- Visi Section -->
                    <div class="mb-10">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900">Visi</h3>
                        </div>
                        <div class="bg-blue-50 p-6 rounded-xl border-l-4 border-blue-500">
                            <p class="text-gray-800 text-lg leading-relaxed">{{ $candidate->vision }}</p>
                        </div>
                    </div>

                    <!-- Misi Section -->
                    @if($candidate->missions->count() > 0)
                    <div class="mb-10">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900">Misi</h3>
                        </div>
                        <div class="space-y-4">
                            @foreach($candidate->missions->sortBy('order') as $mission)
                            <div class="flex items-start bg-white border-2 border-gray-100 rounded-xl p-5 hover:border-blue-300 hover:shadow-md transition-all">
                                <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-blue-600 to-cyan-600 text-white rounded-lg flex items-center justify-center font-bold shadow-md mr-4">
                                    {{ $loop->iteration }}
                                </div>
                                <p class="flex-1 text-gray-800 text-base pt-2 leading-relaxed">{{ $mission->mission }}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Vote Button -->
                    @auth
                    @if(!Auth::user()->hasVotedIn($election->id))
                    <div class="mt-10 pt-8 border-t-2 border-gray-100">
                        <form action="{{ route('voter.vote', ['code' => $election->access_code]) }}" 
                              method="POST" 
                              onsubmit="return confirmVote('{{ $candidate->number }}', '{{ $candidate->name }}')">
                            @csrf
                            <input type="hidden" name="candidate_id" value="{{ $candidate->id }}">
                            <button type="submit" 
                                    class="w-full bg-gradient-to-r from-blue-600 to-cyan-600 text-white font-bold py-5 px-8 rounded-xl hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-4 focus:ring-blue-300 transition-all duration-200 shadow-xl hover:shadow-2xl">
                                <span class="flex items-center justify-center text-lg">
                                    <svg class="w-7 h-7 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Pilih Kandidat Ini
                                </span>
                            </button>
                        </form>
                    </div>
                    @else
                    <div class="mt-10 pt-8 border-t-2 border-gray-100">
                        <div class="bg-green-50 border-2 border-green-200 rounded-xl p-6 text-center">
                            <svg class="w-12 h-12 text-green-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-green-800 font-semibold text-lg">Anda sudah memberikan suara pada pemilu ini</p>
                        </div>
                    </div>
                    @endif
                    @endauth
                </div>
            </div>
        </main>
    </div>

    <script>
        function confirmVote(candidateNumber, candidateName) {
            return confirm(`Apakah Anda yakin memilih kandidat nomor ${candidateNumber} - ${candidateName}?\n\nPilihan Anda tidak dapat diubah setelah dikonfirmasi.`);
        }
    </script>
</body>
</html>
