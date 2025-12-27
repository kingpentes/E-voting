<x-admin-layout title="Daftar Voter">
    <x-admin-sidebar active="voters" />

    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white shadow-sm z-10">
            <div class="px-8 py-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Daftar Voter</h1>
                        <p class="text-gray-600 mt-1">Kelola dan pantau pemilih yang terdaftar</p>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-4 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-100 text-sm">Total Voter</p>
                                <p class="text-3xl font-bold mt-1">{{ $stats['total_voters'] }}</p>
                            </div>
                            <div class="bg-white/20 rounded-lg p-3">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-4 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-green-100 text-sm">Sudah Voting</p>
                                <p class="text-3xl font-bold mt-1">{{ $stats['voted'] }}</p>
                            </div>
                            <div class="bg-white/20 rounded-lg p-3">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-4 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-orange-100 text-sm">Belum Voting</p>
                                <p class="text-3xl font-bold mt-1">{{ $stats['not_voted'] }}</p>
                            </div>
                            <div class="bg-white/20 rounded-lg p-3">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-8">
            @if (session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
                    <p class="text-green-800 font-medium">{{ session('success') }}</p>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                    <p class="text-red-800 font-medium">{{ session('error') }}</p>
                </div>
            @endif

            @if (!$election)
                <!-- No Election Warning -->
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 rounded-lg mb-6">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-yellow-600 mr-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                        <div>
                            <p class="text-yellow-800 font-semibold">Anda belum memiliki pemilu</p>
                            <p class="text-yellow-700 text-sm mt-1">Buat pemilu terlebih dahulu untuk melihat daftar
                                voter yang mendaftar menggunakan access code Anda.</p>
                        </div>
                    </div>
                </div>
            @else
                <!-- Info Banner -->
                <div class="bg-blue-50 border-l-4 border-blue-400 p-6 rounded-lg mb-6">
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-blue-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <p class="text-blue-800 font-semibold">Menampilkan voter pemilu: {{ $election->title }}</p>
                            <p class="text-blue-700 text-sm mt-1">Hanya voter yang mendaftar menggunakan access code
                                <span class="font-mono font-bold">{{ $election->access_code }}</span> yang ditampilkan
                                di sini.</p>
                        </div>
                    </div>
                </div>

                <!-- Search & Election filter -->
                <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                    <form method="GET" action="{{ route('admin.voters.index') }}"
                        class="flex flex-col md:flex-row gap-4 items-center">
                        <div class="w-full md:w-1/2">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari nama, email, ID number, atau organisasi..."
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        </div>

                        <div class="w-full md:w-1/4">
                            <select name="election_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                @if (isset($elections) && $elections->isNotEmpty())
                                    @foreach ($elections as $opt)
                                        <option value="{{ $opt->id }}"
                                            {{ request('election_id', optional($election)->id) == $opt->id ? 'selected' : '' }}>
                                            {{ $opt->title }}
                                        </option>
                                    @endforeach
                                @else
                                    <option value="">-- Tidak ada pemilu --</option>
                                @endif
                            </select>
                        </div>

                        <div class="flex items-center space-x-2">
                            <button type="submit"
                                class="px-6 py-2 bg-purple-600 text-white font-semibold rounded-lg hover:bg-purple-700 transition-colors">
                                Cari
                            </button>
                            @if (request()->query())
                                <a href="{{ route('admin.voters.index') }}"
                                    class="px-6 py-2 bg-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-400 transition-colors">
                                    Reset
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            @endif

            <!-- Voters Table -->
            @if ($voters->isEmpty())
                <div class="bg-white rounded-2xl shadow-md p-12 text-center">
                    <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                        </path>
                    </svg>
                    <h3 class="text-2xl font-bold text-gray-700 mb-2">Belum Ada Voter</h3>
                    <p class="text-gray-500 mb-6">Voter akan muncul setelah mereka mendaftar menggunakan access code
                        pemilu Anda</p>
                </div>
            @else
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Voter</th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Code Digunakan</th>
                                    <th
                                        class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Status Vote</th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Bergabung</th>
                                    <th
                                        class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($voters as $voter)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    @if ($voter->face_photo)
                                                        <img class="h-10 w-10 rounded-full object-cover"
                                                            src="{{ asset('storage/' . $voter->face_photo) }}"
                                                            alt="{{ $voter->name }}">
                                                    @else
                                                        <div
                                                            class="h-10 w-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white font-bold">
                                                            {{ strtoupper(substr($voter->name, 0, 1)) }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ $voter->name }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">{{ $voter->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-mono font-bold text-purple-600">
                                                {{ $voter->participatingElections->first()->pivot->access_code_used }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $voter->organization ?? 'Tidak ada organisasi' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @if ($voter->hasVotedIn($election->id))
                                                <span
                                                    class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Sudah Vote
                                                </span>
                                            @else
                                                <span
                                                    class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    ⏳ Belum Vote
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $joinedAt = \Carbon\Carbon::parse(
                                                    $voter->participatingElections->first()->pivot->joined_at,
                                                );
                                            @endphp
                                            <div class="text-sm text-gray-900">{{ $joinedAt->format('d M Y') }}</div>
                                            <div class="text-xs text-gray-500">{{ $joinedAt->format('H:i') }} WIB
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <a href="{{ route('admin.voters.show', $voter->id) }}"
                                                class="text-purple-600 hover:text-purple-900 font-medium text-sm">
                                                Detail
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        {{ $voters->links() }}
                    </div>
                </div>
            @endif
        </main>
    </div>
</x-admin-layout>
