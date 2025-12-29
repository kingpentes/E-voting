@props(['transparent' => false])

<nav class="{{ $transparent ? 'bg-transparent' : 'bg-gradient-to-r from-indigo-900 via-purple-900 to-indigo-900' }} text-white shadow-lg sticky top-0 z-50 backdrop-blur-sm"
    x-data="{ mobileMenuOpen: false }">
    <div class="container mx-auto px-4 sm:px-6 py-3 sm:py-4">
        <div class="flex items-center justify-between">
            <!-- Logo & Brand -->
            <div class="flex items-center space-x-2 sm:space-x-3">
                <div
                    class="w-8 h-8 sm:w-10 sm:h-10 {{ $transparent ? 'bg-indigo-600/80' : 'bg-white/20' }} rounded-lg flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <a href="/" class="font-bold text-lg sm:text-2xl hover:text-indigo-100 transition">
                    E-Voting System
                </a>
            </div>

            <!-- Mobile Menu Button -->
            <button @click="mobileMenuOpen = !mobileMenuOpen"
                class="lg:hidden p-2 rounded-lg hover:bg-white/10 transition">
                <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16">
                    </path>
                </svg>
                <svg x-show="mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    style="display: none;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>

            <!-- Desktop Navigation Menu -->
            <div class="hidden lg:flex items-center space-x-6">
                @auth
                    <!-- User Info -->
                    <div class="flex items-center space-x-4 bg-white/10 backdrop-blur-md rounded-xl px-4 py-2">
                        <div class="flex items-center space-x-3">
                            <div
                                class="w-10 h-10 bg-gradient-to-br from-pink-500 to-purple-600 rounded-full flex items-center justify-center font-bold text-white shadow-lg">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <div class="text-left">
                                <p class="text-sm font-semibold text-white">
                                    {{ Auth::user()->name }}
                                </p>
                                <p class="text-xs text-indigo-200">
                                    @if (Auth::user()->role === 'organizer')
                                        Penyelenggara
                                    @elseif(Auth::user()->role === 'voter')
                                        🗳️ Pemilih
                                    @else
                                        👑 Admin
                                    @endif
                                </p>
                            </div>
                        </div>

                        <!-- Dashboard Link (Voter Only) -->
                        @if (Auth::user()->role === 'voter')
                            <div class="h-8 w-px bg-white/20"></div>

                            <a href="{{ route('voter.dashboard') }}"
                                class="flex items-center space-x-2 px-4 py-2 bg-white/20 hover:bg-white/30 rounded-lg transition duration-200 font-medium backdrop-blur-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                                    </path>
                                </svg>
                                <span>Mulai Memilih</span>
                            </a>
                        @endif

                        <div class="h-8 w-px bg-white/20"></div>

                        <!-- Logout Button -->
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit"
                                class="flex items-center space-x-2 px-4 py-2 bg-red-500 hover:bg-red-600 rounded-lg transition duration-200 font-semibold shadow-lg transform hover:scale-105">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1">
                                    </path>
                                </svg>
                                <span>Keluar</span>
                            </button>
                        </form>
                    </div>
                @else
                    <!-- Guest Menu -->
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('login') }}"
                            class="flex items-center space-x-2 px-5 py-2.5 hover:bg-white/10 rounded-lg transition duration-200 font-medium backdrop-blur-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1">
                                </path>
                            </svg>
                            <span>Masuk</span>
                        </a>
                        <a href="{{ route('register') }}"
                            class="flex items-center space-x-2 px-6 py-2.5 bg-white text-indigo-600 hover:bg-indigo-50 rounded-lg transition duration-200 font-semibold shadow-lg transform hover:scale-105">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z">
                                </path>
                            </svg>
                            <span>Daftar</span>
                        </a>
                    </div>
                @endguest
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-show="mobileMenuOpen" x-collapse class="lg:hidden mt-4 pb-4 border-t border-white/20 pt-4">
            @auth
                <!-- User Info Mobile -->
                <div class="flex items-center space-x-3 mb-4 p-3 bg-white/10 rounded-xl">
                    <div
                        class="w-10 h-10 bg-gradient-to-br from-pink-500 to-purple-600 rounded-full flex items-center justify-center font-bold text-white shadow-lg">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-white">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-indigo-200">
                            @if (Auth::user()->role === 'organizer')
                                Penyelenggara
                            @elseif(Auth::user()->role === 'voter')
                                🗳️ Pemilih
                            @else
                                👑 Admin
                            @endif
                        </p>
                    </div>
                </div>

                <div class="space-y-2">
                    @if (Auth::user()->role === 'voter')
                        <a href="{{ route('voter.dashboard') }}"
                            class="flex items-center space-x-3 px-4 py-3 bg-white/10 hover:bg-white/20 rounded-lg transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                                </path>
                            </svg>
                            <span>Mulai Memilih</span>
                        </a>
                    @endif

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center space-x-3 px-4 py-3 bg-red-500 hover:bg-red-600 rounded-lg transition text-left">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1">
                                </path>
                            </svg>
                            <span>Keluar</span>
                        </button>
                    </form>
                </div>
            @else
                <div class="space-y-2">
                    <a href="{{ route('login') }}"
                        class="flex items-center space-x-3 px-4 py-3 bg-white/10 hover:bg-white/20 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1">
                            </path>
                        </svg>
                        <span>Masuk</span>
                    </a>
                    <a href="{{ route('register') }}"
                        class="flex items-center space-x-3 px-4 py-3 bg-white text-indigo-600 rounded-lg transition font-semibold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z">
                            </path>
                        </svg>
                        <span>Daftar</span>
                    </a>
                </div>
            @endguest
        </div>
    </div>
</nav>

@if (session('status'))
    <div class="bg-green-500 text-white px-4 sm:px-6 py-3 text-center animate-pulse">
        <p class="font-medium text-sm sm:text-base">{{ session('status') }}</p>
    </div>
@endif

@if (session('error'))
    <div class="bg-red-500 text-white px-4 sm:px-6 py-3 text-center animate-pulse">
        <p class="font-medium text-sm sm:text-base">{{ session('error') }}</p>
    </div>
@endif
