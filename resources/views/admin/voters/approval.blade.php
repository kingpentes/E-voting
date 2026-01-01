<x-admin-layout title="Persetujuan Voter">
    <x-admin-sidebar active="voters-approval" />

    <div class="flex-1 flex flex-col overflow-hidden w-full lg:w-auto">
        <header class="bg-white shadow-sm z-10">
            <div class="px-4 sm:px-6 lg:px-8 py-4 lg:py-6">
                <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900">Persetujuan Verifikasi Voter</h1>
                <p class="text-gray-600 mt-1 text-sm lg:text-base">Review dan setujui verifikasi identitas voter</p>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 pb-20 lg:pb-8">
            <!-- Filter -->
            <div class="mb-4 sm:mb-6">
                <form method="GET" class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4">
                    <label for="status" class="text-sm font-medium text-gray-700">Filter Status:</label>
                    <select id="status" name="status" onchange="this.form.submit()" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 w-full sm:w-48">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </form>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Voters List -->
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pemilu</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID Card</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Face Photo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($voters as $voter)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-medium text-gray-900">{{ $voter->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $voter->email }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div class="font-medium text-gray-900">{{ $voter->election_title }}</div>
                                    @if($voter->access_code_used)
                                        <div class="text-xs text-gray-500 mt-1">Kode: {{ $voter->access_code_used }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($voter->approval_status === 'pending')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Pending
                                        </span>
                                    @elseif($voter->approval_status === 'approved')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Approved
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Rejected
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($voter->id_card)
                                        <a href="{{ asset('storage/' . $voter->id_card) }}" target="_blank" class="text-purple-600 hover:text-purple-900">
                                            Lihat ID
                                        </a>
                                    @else
                                        <span class="text-gray-400">Belum upload</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($voter->face_photo)
                                        <a href="{{ asset('storage/' . $voter->face_photo) }}" target="_blank" class="text-purple-600 hover:text-purple-900">
                                            Lihat Foto
                                        </a>
                                    @else
                                        <span class="text-gray-400">Belum upload</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    @if($voter->approval_status === 'pending' && $voter->id_card && $voter->face_photo)
                                        <form action="{{ route('admin.voters.approve', $voter->pivot_id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-900">
                                                Approve
                                            </button>
                                        </form>
                                        <button onclick="showRejectModal({{ $voter->pivot_id }}, '{{ $voter->name }}', '{{ $voter->election_title }}')" class="text-red-600 hover:text-red-900">
                                            Reject
                                        </button>
                                    @elseif($voter->approval_status === 'approved')
                                        <span class="text-gray-400">Sudah disetujui</span>
                                    @else
                                        <span class="text-gray-400">Data belum lengkap</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                    Tidak ada voter yang perlu diverifikasi
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                </div>

                <div class="px-4 sm:px-6 py-4">
                    {{ $voters->links() }}
                </div>
            </div>
        </main>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 px-4">
        <div class="relative top-10 sm:top-20 mx-auto p-4 sm:p-5 border w-full max-w-sm sm:max-w-md shadow-lg rounded-xl bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Tolak Verifikasi</h3>
                <form id="rejectForm" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan</label>
                        <textarea name="rejection_reason" rows="4" required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                                  placeholder="Jelaskan alasan penolakan..."></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeRejectModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                            Tolak
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showRejectModal(pivotId, voterName, electionTitle) {
            document.getElementById('rejectForm').action = `/admin/voters/${pivotId}/reject`;
            document.getElementById('rejectModal').classList.remove('hidden');
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
        }
    </script>
</x-admin-layout>
