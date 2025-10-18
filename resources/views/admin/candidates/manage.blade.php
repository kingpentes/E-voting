<x-admin-layout title="Kelola Kandidat">
    <x-admin-sidebar active="manage" />
    
    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white shadow-sm z-10">
            <div class="px-8 py-6 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Kelola Kandidat</h1>
                    <p class="text-gray-600 mt-1">{{ $candidates->count() }} kandidat dari pemilu Anda</p>
                </div>
                <a href="{{ route('admin.candidates.create') }}" 
                   class="px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold rounded-xl">
                    <span>+ Tambah Kandidat Baru</span>
                </a>
            </div>
        </header>
        
        <main class="flex-1 overflow-y-auto p-8">
            @if(session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
                    <p class="text-green-800 font-medium">{{ session('success') }}</p>
                </div>
            @endif

            @if($candidates->isEmpty())
                <div class="bg-white rounded-2xl shadow-md p-12 text-center">
                    <h3 class="text-2xl font-bold text-gray-700 mb-2">Belum Ada Kandidat</h3>
                    <p class="text-gray-500 mb-6">Mulai dengan menambahkan kandidat pertama Anda</p>
                </div>
            @else
                <div class="space-y-6">
                    @foreach($candidates as $candidate)
                    <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                        <div class="bg-gray-50 px-8 py-4 border-b">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div class="w-16 h-16 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-full flex items-center justify-center font-bold text-2xl">
                                        {{ $candidate->number }}
                                    </div>
                                    <div>
                                        <h2 class="text-2xl font-bold text-gray-900">{{ $candidate->name }}</h2>
                                        <p class="text-gray-500">{{ $candidate->election->title }}</p>
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.candidates.edit', $candidate->id) }}" 
                                       class="px-5 py-2.5 bg-blue-600 text-white font-semibold rounded-lg">Edit</a>
                                    <form action="{{ route('admin.candidates.destroy', $candidate->id) }}" method="POST" 
                                          onsubmit="return confirm('Yakin hapus {{ $candidate->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-5 py-2.5 bg-red-600 text-white font-semibold rounded-lg">Hapus</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-8">
                            <div class="grid grid-cols-3 gap-6">
                                <div>
                                    @if($candidate->photo)
                                        <img src="{{ asset('storage/' . $candidate->photo) }}" alt="{{ $candidate->name }}" class="w-full rounded-xl">
                                    @else
                                        <div class="w-full aspect-square bg-gray-200 rounded-xl"></div>
                                    @endif
                                </div>
                                
                                <div class="col-span-2">
                                    <div class="mb-6">
                                        <h3 class="font-bold text-gray-900 mb-2">Visi</h3>
                                        <p class="text-gray-700 bg-blue-50 p-4 rounded-lg">{{ $candidate->visi }}</p>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-gray-900 mb-2">Misi</h3>
                                        @if($candidate->missions->isEmpty())
                                            <p class="text-gray-500 italic">Belum ada misi</p>
                                        @else
                                            <ul class="space-y-2">
                                                @foreach($candidate->missions as $mission)
                                                    <li class="flex space-x-2 bg-purple-50 p-3 rounded-lg">
                                                        <span class="font-bold">{{ $loop->iteration }}.</span>
                                                        <p>{{ $mission->mission }}</p>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </main>
    </div>
</x-admin-layout>
