<x-voter-layout :title="$election['title'] ?? 'E-Voting'">
    <!-- Hero Section -->
    <x-hero-section 
        :title="$election['title'] ?? 'E-Voting'"
        :description="$election['description'] ?? 'Sistem Pemilihan Elektronik untuk memilih pemimpin masa depan dengan transparan, aman, dan demokratis'"
    />

    <!-- Main Content -->
    <main class="py-12 px-4">
        <div class="max-w-7xl mx-auto">
            <!-- Section Title -->
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-blue-600 mb-4">Daftar Kandidat</h2>
                <p class="text-gray-600 text-lg">Pilih kandidat dengan mengklik kartu untuk melihat detail</p>
            </div>

            <!-- Candidates Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($candidates as $candidate)
                    <x-candidate-card :candidate="$candidate" />
                @endforeach
            </div>
        </div>
    </main>
</x-voter-layout>
