<x-app-layout>
    <div class="container mx-auto">
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 my-4">
            {{ __('Buku Populer') }}
        </h2>

        <div class="overflow-x-auto">
            <table class="table-auto w-full bg-white border border-gray-200">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="px-4 py-2">ID</th>
                        <th class="px-4 py-2">Gambar</th>
                        <th class="px-4 py-2">Judul Buku</th>
                        <th class="px-4 py-2">Penulis</th>
                        <th class="px-4 py-2">Rating</th>
                        {{-- Tambahkan kolom lain sesuai kebutuhan --}}
                    </tr>
                </thead>

                <tbody>
                    @foreach($topBukuPopuler as $b)
                        <tr>
                            <td class="border px-4 py-2">{{ $b->id }}</td>
                            <td class="border px-4 py-2">
                                @if($b->filepath)
                                    {{-- Tampilkan gambar buku --}}
                                    <img src="{{ $b->filepath }}" alt="Gambar Buku" class="w-20 h-50 object-cover">
                                @endif
                            </td>
                            <td class="border px-4 py-2">{{ $b->judul }}</td>
                            <td class="border px-4 py-2">{{ $b->penulis }}</td>
                            <td class="border px-4 py-2">{{ $b->avg_rating }}</td>
                            {{-- Tambahkan kolom lain sesuai kebutuhan --}}
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
