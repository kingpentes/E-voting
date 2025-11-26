@props(['active' => 'dashboard'])

<!-- Sidebar -->
<aside class="w-72 bg-gradient-to-b from-indigo-900 via-purple-900 to-indigo-900 text-white flex-shrink-0 overflow-y-auto">
    <div class="p-6">
        <!-- Logo/Brand -->
        <div class="flex items-center space-x-3 mb-8">
            <div class="w-12 h-12 bg-pink-600 rounded-xl flex items-center justify-center">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
            </div>
            <div>
                <h1 class="text-xl font-bold">E-Voter</h1>
                <p class="text-sm text-indigo-300">Admin Panel</p>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="space-y-2">
            <!-- Dashboard -->
            <a href="{{ route('admin.dashboard') }}" 
               class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 {{ $active === 'dashboard' ? 'bg-pink-600 text-white shadow-lg' : 'text-indigo-200 hover:bg-white/10' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span class="font-medium">Dashboard</span>
            </a>

            <!-- Kelola Kandidat -->
            <a href="{{ route('admin.candidates.manage') }}" 
               class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 {{ $active === 'manage' ? 'bg-pink-600 text-white shadow-lg' : 'text-indigo-200 hover:bg-white/10' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <span class="font-medium">Kelola Kandidat</span>
            </a>

            <!-- Daftar Voter -->
            <a href="{{ route('admin.voters.index') }}" 
               class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 {{ $active === 'voters' ? 'bg-pink-600 text-white shadow-lg' : 'text-indigo-200 hover:bg-white/10' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <span class="font-medium">Daftar Voter</span>
            </a>

            <!-- Persetujuan Voter -->
            <a href="{{ route('admin.voters.approval') }}" 
               class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 {{ $active === 'voters-approval' ? 'bg-pink-600 text-white shadow-lg' : 'text-indigo-200 hover:bg-white/10' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
                <span class="font-medium">Persetujuan Voter</span>
            </a>

            <!-- Judul & Peraturan -->
            <a href="{{ route('admin.elections.rules.manage') }}" 
               class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 {{ $active === 'rules' ? 'bg-pink-600 text-white shadow-lg' : 'text-indigo-200 hover:bg-white/10' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span class="font-medium">Judul & Peraturan</span>
            </a>

            <!-- Status Sinkronisasi -->
            <a href="{{ route('admin.elections.sync-status') }}" 
               class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 {{ $active === 'sync-status' ? 'bg-pink-600 text-white shadow-lg' : 'text-indigo-200 hover:bg-white/10' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9H4m0 0V4m16 16v-5h-.581m0 0A8.003 8.003 0 014.582 15H4m0 5v-5" />
                </svg>
                <span class="font-medium">Status Sinkronisasi</span>
            </a>
        </nav>
    </div>

   
    
</aside>

