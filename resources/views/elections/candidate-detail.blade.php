<x-voter-layout :title="$candidate['name'] ?? 'Detail Kandidat'">
    <!-- Header -->
    <header class="bg-gradient-to-r from-blue-600 to-indigo-700 shadow-lg">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex items-center">
            <a href="{{ route('voter.dashboard') }}" class="text-white hover:text-blue-200 mr-4 transition-colors">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h1 class="text-3xl font-bold text-white">
                Detail Kandidat
            </h1>
        </div>
    </header>

    <!-- Main Content -->
    <main class="py-12 px-4">
        <div class="max-w-4xl mx-auto">
            <!-- Candidate Card -->
            <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
                <!-- Header Section -->
                <div class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white p-8">
                    <div class="flex flex-col md:flex-row items-center md:items-start space-y-6 md:space-y-0 md:space-x-8">
                        <!-- Photo -->
                        <div class="relative flex-shrink-0">
                            <div class="w-48 h-48 rounded-full overflow-hidden border-4 border-white shadow-2xl">
                                <img src="{{ $candidate['photo'] }}" 
                                     alt="{{ $candidate['name'] }}" 
                                     class="w-full h-full object-cover">
                            </div>
                            <!-- Number Badge -->
                            <div class="absolute -bottom-3 -right-3 w-16 h-16 bg-white text-blue-600 rounded-full flex items-center justify-center font-bold text-3xl shadow-xl">
                                {{ $candidate['number'] }}
                            </div>
                        </div>
                        
                        <!-- Info -->
                        <div class="flex-1 text-center md:text-left">
                            <h2 class="text-4xl font-bold mb-3">{{ $candidate['name'] }}</h2>
                            <p class="text-blue-100 text-xl mb-4">
                                Kandidat {{ $candidate['number'] }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Candidate Details -->
                <div class="p-8">
                    <!-- Visi -->
                    <div class="mb-8">
                        <h3 class="text-3xl font-bold text-gray-800 mb-4 flex items-center">
                            <svg class="w-8 h-8 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Visi
                        </h3>
                        <div class="bg-blue-50 rounded-xl p-6 border-l-4 border-blue-600">
                            <p class="text-gray-700 text-lg leading-relaxed">{{ $candidate['visi'] }}</p>
                        </div>
                    </div>

                    <!-- Misi -->
                    <div class="mb-8">
                        <h3 class="text-3xl font-bold text-gray-800 mb-4 flex items-center">
                            <svg class="w-8 h-8 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                            Misi
                        </h3>
                        <ul class="space-y-4">
                            @foreach($candidate['misi'] as $index => $misi)
                            <li class="flex items-start bg-gray-50 rounded-xl p-4 hover:bg-blue-50 transition-colors">
                                <span class="flex-shrink-0 w-10 h-10 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-full flex items-center justify-center font-bold mr-4 shadow-md">
                                    {{ $index + 1 }}
                                </span>
                                <span class="text-gray-700 text-lg pt-1.5">{{ $misi }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Vote Button -->
                    <div class="flex justify-center pt-8 border-t border-gray-200">
                        <button 
                            onclick="confirmVote({{ $candidate['number'] }}, '{{ $candidate['name'] }}')"
                            class="inline-flex items-center justify-center px-10 py-4 bg-gradient-to-r from-green-500 to-emerald-600 text-white text-xl font-bold rounded-xl hover:from-green-600 hover:to-emerald-700 focus:outline-none focus:ring-4 focus:ring-green-300 transform transition duration-200 hover:scale-105 shadow-xl hover:shadow-2xl">
                            <svg class="w-7 h-7 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Pilih Kandidat Ini
                        </button>
                    </div>
                </div>
            </div>

            <!-- Back Button -->
            <div class="text-center mt-8">
                <a href="{{ route('voter.dashboard') }}" 
                   class="inline-flex items-center justify-center px-8 py-3 bg-gray-600 text-white font-semibold rounded-xl hover:bg-gray-700 transition-all duration-200 shadow-md hover:shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Daftar Kandidat
                </a>
            </div>
        </div>
    </main>

    <script>
        function confirmVote(candidateNumber, candidateName) {
            if (confirm(`Apakah Anda yakin ingin memilih ${candidateName}?\n\nPilihan Anda tidak dapat diubah setelah dikonfirmasi.`)) {
                // Here you would implement the actual voting logic
                alert('Terima kasih! Suara Anda telah tercatat untuk ' + candidateName);
                // Redirect to confirmation page or dashboard
                // window.location.href = '/voter/confirm';
            }
        }
    </script>
</x-voter-layout>
