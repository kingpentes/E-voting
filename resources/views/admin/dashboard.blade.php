<x-admin-layout title="Dashboard Pemilu">
    <!-- Sidebar -->
    <x-admin-sidebar active="dashboard" />

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden w-full lg:w-auto">
        <!-- Header -->
        <header class="bg-white shadow-sm z-10">
            <div class="px-4 sm:px-6 lg:px-8 py-4 lg:py-6">
                <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900">Dashboard Pemilu</h1>
                <p class="text-gray-600 mt-1 text-sm lg:text-base">Kelola dan pantau pemilihan umum Anda</p>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 pb-20 lg:pb-8">
            <!-- Alert Banner -->
            <div
                class="bg-gradient-to-r from-pink-50 to-purple-50 border-l-4 border-pink-500 rounded-xl p-4 sm:p-6 mb-6 lg:mb-8">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 mb-2">Statistik per Kandidat</h2>
                            <p class="text-gray-600">Perolehan suara masing-masing kandidat</p>
                        </div>

                        <!-- Blockchain Indicator / Error -->
                        @if ($election)
                            @if (isset($election->blockchain_error) && $election->blockchain_error)
                                <div
                                    class="flex items-center space-x-2 bg-red-50 border border-red-200 px-4 py-2 rounded-lg">
                                    <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-sm font-semibold text-red-700">Error Blockchain</span>
                                </div>
                            @elseif(isset($election->no_contract) && $election->no_contract)
                                <div
                                    class="flex items-center space-x-2 bg-yellow-50 border border-yellow-200 px-4 py-2 rounded-lg">
                                    <svg class="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-sm font-semibold text-yellow-700">Smart Contract Belum
                                        Deploy</span>
                                </div>
                            @elseif($usingBlockchain ?? false)
                                <div
                                    class="flex items-center space-x-2 bg-green-50 border border-green-200 px-4 py-2 rounded-lg">
                                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-sm font-semibold text-green-700">Data dari Blockchain</span>
                                </div>
                            @endif
                        @endif
                    </div>
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
                    @if (isset($election->blockchain_error) && $election->blockchain_error)
                        <div class="col-span-full">
                            <div class="bg-red-50 border-2 border-red-200 rounded-xl p-8 text-center">
                                <svg class="w-16 h-16 text-red-600 mx-auto mb-4" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                        clip-rule="evenodd" />
                                </svg>
                                <h3 class="text-lg font-bold text-red-900 mb-2">Gagal Mengambil Data Blockchain</h3>
                                <p class="text-red-700 mb-4">Terjadi kesalahan saat mengambil data dari blockchain.
                                    Pastikan node blockchain berjalan dan smart contract sudah dideploy dengan benar.
                                </p>
                                <a href="{{ route('admin.elections.blockchain-status', $election) }}"
                                    class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                                        </path>
                                    </svg>
                                    Cek Status Blockchain
                                </a>
                            </div>
                        </div>
                    @elseif(isset($election->no_contract) && $election->no_contract)
                        <div class="col-span-full">
                            <div class="bg-yellow-50 border-2 border-yellow-200 rounded-xl p-8 text-center">
                                <svg class="w-16 h-16 text-yellow-600 mx-auto mb-4" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                <h3 class="text-lg font-bold text-yellow-900 mb-2">Smart Contract Belum Dideploy</h3>
                                <p class="text-yellow-700 mb-4">Pemilu ini belum memiliki smart contract yang dideploy.
                                    Deploy smart contract untuk menyimpan hasil suara di blockchain.</p>
                                <a href="{{ route('admin.elections.manage', $election) }}"
                                    class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                    Deploy Smart Contract
                                </a>
                            </div>
                        </div>
                    @elseif($candidateStats->isNotEmpty())
                        @php
                            $colors = [
                                'bg-pink-600',
                                'bg-blue-500',
                                'bg-green-500',
                                'bg-orange-500',
                                'bg-purple-500',
                                'bg-yellow-500',
                                'bg-red-500',
                                'bg-indigo-500',
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
                    'rgb(219, 39, 119)', // pink-600
                    'rgb(59, 130, 246)', // blue-500
                    'rgb(34, 197, 94)', // green-500
                    'rgb(249, 115, 22)', // orange-500
                    'rgb(168, 85, 247)', // purple-500
                    'rgb(234, 179, 8)', // yellow-500
                    'rgb(239, 68, 68)', // red-500
                    'rgb(99, 102, 241)', // indigo-500
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
