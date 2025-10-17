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
            <div class="bg-gradient-to-r from-pink-50 to-purple-50 border-l-4 border-pink-500 rounded-xl p-6 mb-8">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-800 font-medium">Tambahkan kandidat untuk publish pemilu dan mulai proses voting</p>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Pemilih -->
                <x-stat-card 
                    title="Total Pemilih" 
                    value="2,847"
                    change="+12.5%"
                    iconColor="text-purple-600">
                    <x-slot name="icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </x-slot>
                </x-stat-card>

                <!-- Sudah Memilih -->
                <x-stat-card 
                    title="Sudah Memilih" 
                    value="1,453"
                    change="51%"
                    iconColor="text-green-600">
                    <x-slot name="icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </x-slot>
                </x-stat-card>

                <!-- Belum Memilih -->
                <x-stat-card 
                    title="Belum Memilih" 
                    value="1,394"
                    change="49%"
                    iconColor="text-yellow-600">
                    <x-slot name="icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </x-slot>
                </x-stat-card>

                <!-- Tingkat Partisipasi -->
                <x-stat-card 
                    title="Tingkat Partisipasi" 
                    value="51%"
                    change="+5.2%"
                    iconColor="text-blue-600">
                    <x-slot name="icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
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
                    <button onclick="showChart('bar')" id="btnBar" class="px-6 py-2.5 bg-gray-900 text-white rounded-xl font-medium transition-all">
                        Grafik Batang
                    </button>
                    <button onclick="showChart('pie')" id="btnPie" class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-xl font-medium hover:bg-gray-300 transition-all">
                        Grafik Pie
                    </button>
                </div>

                <!-- Chart Canvas -->
                <div class="relative" style="height: 400px;">
                    <canvas id="votesChart"></canvas>
                </div>

                <!-- Legend -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-8">
                    <div class="flex items-center space-x-3">
                        <div class="w-4 h-4 bg-pink-600 rounded"></div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Kandidat A</p>
                            <p class="text-xs text-gray-600">542 suara</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="w-4 h-4 bg-blue-500 rounded"></div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Kandidat B</p>
                            <p class="text-xs text-gray-600">438 suara</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="w-4 h-4 bg-green-500 rounded"></div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Kandidat C</p>
                            <p class="text-xs text-gray-600">325 suara</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="w-4 h-4 bg-orange-500 rounded"></div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Kandidat D</p>
                            <p class="text-xs text-gray-600">148 suara</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        let chart = null;
        const chartData = {
            labels: ['Kandidat A', 'Kandidat B', 'Kandidat C', 'Kandidat D'],
            datasets: [{
                label: 'Jumlah Suara',
                data: [542, 438, 325, 148],
                backgroundColor: [
                    'rgb(219, 39, 119)', // pink-600
                    'rgb(59, 130, 246)',  // blue-500
                    'rgb(34, 197, 94)',   // green-500
                    'rgb(249, 115, 22)'   // orange-500
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
                                }
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
                btnPie.className = 'px-6 py-2.5 bg-gray-200 text-gray-700 rounded-xl font-medium hover:bg-gray-300 transition-all';
            } else {
                btnPie.className = 'px-6 py-2.5 bg-gray-900 text-white rounded-xl font-medium transition-all';
                btnBar.className = 'px-6 py-2.5 bg-gray-200 text-gray-700 rounded-xl font-medium hover:bg-gray-300 transition-all';
            }
        }

        // Initialize with bar chart
        window.addEventListener('load', function() {
            createChart('bar');
        });
    </script>
</x-admin-layout>
