@props(['active' => 'dashboard'])

<!-- Mobile Sidebar Toggle Button (shown in header) -->
<button id="sidebarToggle"
    class="lg:hidden fixed bottom-4 right-4 z-50 w-14 h-14 bg-pink-600 text-white rounded-full shadow-lg flex items-center justify-center hover:bg-pink-700 transition">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
    </svg>
</button>

<!-- Overlay -->
<div id="sidebarOverlay" class="lg:hidden fixed inset-0 bg-black/50 z-40 hidden" onclick="closeSidebar()"></div>

<!-- Sidebar -->
<aside id="adminSidebar"
    class="fixed lg:static inset-y-0 left-0 z-50 w-64 lg:w-72 bg-gradient-to-b from-indigo-900 via-purple-900 to-indigo-900 text-white flex-shrink-0 overflow-y-auto transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">

    <!-- Close button for mobile -->
    <button id="closeSidebar" class="lg:hidden absolute top-4 right-4 p-2 text-white/70 hover:text-white"
        onclick="closeSidebar()">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
    </button>

    <div class="p-4 lg:p-6">
        <!-- Logo/Brand -->
        <div class="flex items-center space-x-3 mb-6 lg:mb-8">
            <div class="w-10 h-10 lg:w-12 lg:h-12 bg-pink-600 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 lg:w-7 lg:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                    </path>
                </svg>
            </div>
            <div>
                <h1 class="text-lg lg:text-xl font-bold">E-Voter</h1>
                <p class="text-xs lg:text-sm text-indigo-300">Admin Panel</p>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="space-y-1 lg:space-y-2">
            <!-- Dashboard -->
            <a href="{{ route('admin.dashboard') }}"
                class="flex items-center space-x-3 px-3 lg:px-4 py-2.5 lg:py-3 rounded-xl transition-all duration-200 {{ $active === 'dashboard' ? 'bg-pink-600 text-white shadow-lg' : 'text-indigo-200 hover:bg-white/10' }}"
                onclick="closeSidebar()">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                    </path>
                </svg>
                <span class="font-medium text-sm lg:text-base">Dashboard</span>
            </a>

            <!-- Kelola Kandidat -->
            <a href="{{ route('admin.candidates.manage') }}"
                class="flex items-center space-x-3 px-3 lg:px-4 py-2.5 lg:py-3 rounded-xl transition-all duration-200 {{ $active === 'manage' ? 'bg-pink-600 text-white shadow-lg' : 'text-indigo-200 hover:bg-white/10' }}"
                onclick="closeSidebar()">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                    </path>
                </svg>
                <span class="font-medium text-sm lg:text-base">Kelola Kandidat</span>
            </a>

            <!-- Daftar Voter -->
            <a href="{{ route('admin.voters.index') }}"
                class="flex items-center space-x-3 px-3 lg:px-4 py-2.5 lg:py-3 rounded-xl transition-all duration-200 {{ $active === 'voters' ? 'bg-pink-600 text-white shadow-lg' : 'text-indigo-200 hover:bg-white/10' }}"
                onclick="closeSidebar()">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                    </path>
                </svg>
                <span class="font-medium text-sm lg:text-base">Daftar Voter</span>
            </a>

            <!-- Persetujuan Voter -->
            <a href="{{ route('admin.voters.approval') }}"
                class="flex items-center space-x-3 px-3 lg:px-4 py-2.5 lg:py-3 rounded-xl transition-all duration-200 {{ $active === 'voters-approval' ? 'bg-pink-600 text-white shadow-lg' : 'text-indigo-200 hover:bg-white/10' }}"
                onclick="closeSidebar()">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                    </path>
                </svg>
                <span class="font-medium text-sm lg:text-base">Persetujuan Voter</span>
            </a>

            <!-- Judul & Peraturan -->
            <a href="{{ route('admin.elections.rules.manage') }}"
                class="flex items-center space-x-3 px-3 lg:px-4 py-2.5 lg:py-3 rounded-xl transition-all duration-200 {{ $active === 'rules' ? 'bg-pink-600 text-white shadow-lg' : 'text-indigo-200 hover:bg-white/10' }}"
                onclick="closeSidebar()">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
                <span class="font-medium text-sm lg:text-base">Judul & Peraturan</span>
            </a>

            <!-- Status Sinkronisasi -->
            <a href="{{ route('admin.elections.sync-status') }}"
                class="flex items-center space-x-3 px-3 lg:px-4 py-2.5 lg:py-3 rounded-xl transition-all duration-200 {{ $active === 'sync-status' ? 'bg-pink-600 text-white shadow-lg' : 'text-indigo-200 hover:bg-white/10' }}"
                onclick="closeSidebar()">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9H4m0 0V4m16 16v-5h-.581m0 0A8.003 8.003 0 014.582 15H4m0 5v-5" />
                </svg>
                <span class="font-medium text-sm lg:text-base">Status Sinkronisasi</span>
            </a>
        </nav>
    </div>
</aside>

<script>
    // Sidebar toggle functions
    function openSidebar() {
        document.getElementById('adminSidebar').classList.remove('-translate-x-full');
        document.getElementById('sidebarOverlay').classList.remove('hidden');
        document.body.classList.add('overflow-hidden', 'lg:overflow-auto');
    }

    function closeSidebar() {
        document.getElementById('adminSidebar').classList.add('-translate-x-full');
        document.getElementById('sidebarOverlay').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    // Toggle button event
    document.getElementById('sidebarToggle').addEventListener('click', function() {
        const sidebar = document.getElementById('adminSidebar');
        if (sidebar.classList.contains('-translate-x-full')) {
            openSidebar();
        } else {
            closeSidebar();
        }
    });
</script>
