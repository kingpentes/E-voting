@props(['candidate'])

<div class="glass-strong rounded-2xl shadow-glass hover:shadow-glow-lg transition-all duration-300 overflow-hidden cursor-pointer transform hover:scale-105 hover-lift w-full animate-scale-in border border-white/30"
    onclick="window.location.href='{{ route('voter.candidate', ['id' => $candidate['id']]) }}'">
    <div class="p-6">
        <!-- Candidate Photo -->
        <div class="flex justify-center mb-4">
            <div class="relative">
                <div class="w-32 h-32 rounded-full overflow-hidden ring-4 ring-blue-500 shadow-glow">
                    <img src="{{ $candidate['photo'] }}" alt="{{ $candidate['name'] }}" class="w-full h-full object-cover">
                </div>
                <!-- Candidate Number Badge -->
                <div
                    class="absolute -bottom-2 -right-2 w-12 h-12 bg-gradient-to-br from-blue-600 to-cyan-600 text-white rounded-full flex items-center justify-center font-bold text-xl shadow-glow ring-4 ring-white">
                    {{ $candidate['number'] }}
                </div>
            </div>
        </div>

        <!-- Candidate Info -->
        <div class="text-center">
            <h3 class="text-xl font-bold text-gray-900 mb-1">{{ $candidate['name'] }}</h3>
            <p class="text-blue-600 text-sm font-semibold mb-4">Kandidat {{ $candidate['number'] }}</p>

            <!-- View Details Button -->
            <button
                class="w-full inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-blue-600 to-cyan-600 text-white font-semibold rounded-xl hover:from-blue-700 hover:to-cyan-700 transition-all duration-200 shadow-lg hover:shadow-glow transform hover:scale-105">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                    </path>
                </svg>
                Lihat Detail
            </button>
        </div>
    </div>
</div>
