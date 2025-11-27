<x-admin-layout title="Dashboard Pemilu">
    <!-- Sidebar -->
    <x-admin-sidebar active="dashboard" />

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Header -->
        <header class="bg-white shadow-sm z-10">
            <div class="px-8 py-6">
                <h1 class="text-3xl font-bold text-gray-900">Dashboard Pemilu</h1>
                <p class="text-gray-600 mt-1">Kelola dan pantau pemilihan umum Anda</p>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="flex-1 overflow-y-auto p-8">
            <!-- Alert Banner -->
            <div class="bg-gradient-to-r from-blue-50 to-cyan-50 border-l-4 border-blue-500 rounded-xl p-6 mb-8">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-800 font-medium">Tambahkan kandidat untuk publish pemilu dan mulai proses
                            voting</p>
                    </div>
                </div>
            </div>

            <!-- Flash Messages -->
            @if (session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">{{ session('error') }}
                </div>
            @endif
            @if (session('info'))
                <div class="mb-6 p-4 bg-indigo-50 border border-indigo-200 text-indigo-700 rounded-lg">
                    {{ session('info') }}</div>
            @endif
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
                    {{ session('success') }}</div>
            @endif

            <!-- Election selector + Stats Grid -->
            <div class="mb-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <form method="GET" action="{{ route('admin.dashboard') }}" id="electionFilterForm"
                        class="flex flex-col md:flex-row items-stretch md:items-center gap-2 md:gap-4">
                        <label for="election_id"
                            class="text-sm font-medium text-gray-700 self-start md:self-center">Pilih Pemilu:</label>
                        <select id="election_id" name="election_id"
                            class="px-4 py-2 border border-gray-300 rounded-lg w-full md:w-80"
                            onchange="document.getElementById('electionFilterForm').submit()">
                            @if (isset($elections) && $elections->isNotEmpty())
                                @foreach ($elections as $opt)
                                    <option value="{{ $opt->id }}"
                                        {{ optional($election)->id == $opt->id ? 'selected' : '' }}>{{ $opt->title }}
                                    </option>
                                @endforeach
                            @else
                                <option value="">-- Tidak ada pemilu --</option>
                            @endif
                        </select>
                    </form>

                    <div class="text-sm text-gray-500">Menampilkan statistik untuk pemilu terpilih</div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Pemilih -->
                <x-stat-card title="Total Pemilih" value="{{ number_format($stats['total_voters']) }}" change=""
                    iconColor="text-purple-600">
                    <x-slot name="icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </x-slot>
                </x-stat-card>

                <!-- Sudah Memilih -->
                <x-stat-card title="Sudah Memilih" value="{{ number_format($stats['voted']) }}"
                    change="{{ $stats['total_voters'] > 0 ? round(($stats['voted'] / $stats['total_voters']) * 100, 1) . '%' : '0%' }}"
                    iconColor="text-green-600">
                    <x-slot name="icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </x-slot>
                </x-stat-card>

                <!-- Belum Memilih -->
                <x-stat-card title="Belum Memilih" value="{{ number_format($stats['not_voted']) }}"
                    change="{{ $stats['total_voters'] > 0 ? round(($stats['not_voted'] / $stats['total_voters']) * 100, 1) . '%' : '0%' }}"
                    iconColor="text-yellow-600">
                    <x-slot name="icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </x-slot>
                </x-stat-card>

                <!-- Tidak Memilih -->
                <x-stat-card title="Tidak Memilih" value="{{ number_format($stats['not_voted']) }}"
                    change="{{ $stats['participation_rate'] }}% partisipasi" iconColor="text-red-600">
                    <x-slot name="icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </x-slot>
                </x-stat-card>
            </div>

            <!-- Chart Section -->
            <div class="bg-white rounded-2xl shadow-md p-8">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Statistik per Kandidat</h2>
                    <p class="text-gray-600">Perolehan suara masing-masing kandidat</p>
                </div>

                <!-- Chart Type Tabs -->
                <div class="flex space-x-2 mb-6">
                    <button onclick="showChart('bar')" id="btnBar"
                        class="px-6 py-2.5 bg-gray-900 text-white rounded-xl font-medium transition-all">
                        Grafik Batang
                    </button>
                    <button onclick="showChart('pie')" id="btnPie"
                        class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-xl font-medium hover:bg-gray-300 transition-all">
                        Grafik Pie
                    </button>
                </div>

                <!-- Chart Canvas -->
                <div class="relative" style="height: 400px;">
                    <canvas id="votesChart"></canvas>
                </div>

                <!-- Legend -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-8">
                    @if ($candidateStats->isNotEmpty())
                        @php
                            $colors = [
                                'bg-blue-400',
                                'bg-cyan-400',
                                'bg-teal-400',
                                'bg-emerald-400',
                                'bg-sky-400',
                                'bg-indigo-400',
                                'bg-violet-400',
                                'bg-blue-500',
                            ];
                        @endphp
                        @foreach ($candidateStats as $index => $candidate)
                            <div class="flex items-center space-x-3">
                                <div class="w-4 h-4 {{ $colors[$index % count($colors)] }} rounded"></div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $candidate['name'] }}</p>
                                    <p class="text-xs text-gray-600">{{ number_format($candidate['votes']) }} suara
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="col-span-full text-center text-gray-500 py-4">
                            <p>Belum ada data kandidat</p>
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>

    <script>
        let chart = null;

        // Get candidate data from server
        const candidateData = @json($candidateStats);

        const chartData = {
            labels: candidateData.length > 0 ? candidateData.map(c => c.name) : ['Belum ada data'],
            datasets: [{
                label: 'Jumlah Suara',
                data: candidateData.length > 0 ? candidateData.map(c => c.votes) : [0],
                backgroundColor: [
                    'rgba(96, 165, 250, 0.8)', // blue-400
                    'rgba(34, 211, 238, 0.8)', // cyan-400
                    'rgba(45, 212, 191, 0.8)', // teal-400
                    'rgba(52, 211, 153, 0.8)', // emerald-400
                    'rgba(56, 189, 248, 0.8)', // sky-400
                    'rgba(129, 140, 248, 0.8)', // indigo-400
                    'rgba(167, 139, 250, 0.8)', // violet-400
                    'rgba(59, 130, 246, 0.8)', // blue-500
                ],
                borderColor: [
                    'rgba(96, 165, 250, 1)',
                    'rgba(34, 211, 238, 1)',
                    'rgba(45, 212, 191, 1)',
                    'rgba(52, 211, 153, 1)',
                    'rgba(56, 189, 248, 1)',
                    'rgba(129, 140, 248, 1)',
                    'rgba(167, 139, 250, 1)',
                    'rgba(59, 130, 246, 1)',
                ],
                borderWidth: 0,
                borderRadius: 8
            }]
        };

        function createChart(type) {
            const ctx = document.getElementById('votesChart');

            if (chart) {
                chart.destroy();
            }

            chart = new Chart(ctx, {
                type: type,
                data: chartData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            cornerRadius: 8,
                            titleFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 13
                            }
                        }
                    },
                    scales: type === 'bar' ? {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                font: {
                                    size: 12
                                },
                                stepSize: 1
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 12
                                }
                            }
                        }
                    } : {}
                }
            });
        }

        function showChart(type) {
            createChart(type);

            // Update button styles
            const btnBar = document.getElementById('btnBar');
            const btnPie = document.getElementById('btnPie');

            if (type === 'bar') {
                btnBar.className = 'px-6 py-2.5 bg-gray-900 text-white rounded-xl font-medium transition-all';
                btnPie.className =
                    'px-6 py-2.5 bg-gray-200 text-gray-700 rounded-xl font-medium hover:bg-gray-300 transition-all';
            } else {
                btnPie.className = 'px-6 py-2.5 bg-gray-900 text-white rounded-xl font-medium transition-all';
                btnBar.className =
                    'px-6 py-2.5 bg-gray-200 text-gray-700 rounded-xl font-medium hover:bg-gray-300 transition-all';
            }
        }

        // Initialize with bar chart
        window.addEventListener('load', function() {
            createChart('bar');
        });
    </script>
</x-admin-layout>
