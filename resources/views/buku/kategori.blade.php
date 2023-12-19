<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <form action="{{ route('buku.kategori') }}" method="GET" class="mb-4">
        <label for="kategoriFilter" class="block text-gray-700 font-bold">Filter berdasarkan Kategori</label>
        <div class="flex items-center">
            <input type="text" name="kategoriFilter" id="kategoriFilter" class="border border-gray-300 rounded px-3 py-2 mr-2" placeholder="Masukkan kategori" value="{{ request('kategoriFilter') }}">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Filter</button>
        </div>
    </form>
    

    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Gambar</th>
                    <th>Judul Buku</th>
                    <th>Penulis</th>
                    <th>Rating</th>
                    <th>Harga</th>
                    <th>Tanggal Terbit</th>
                    <th>Kategori</th>
                    <th>Detail Buku</th>
                    @if(Auth::check() && Auth::user()->level == 'admin')
                    <th>Aksi</th>
                    @endif
                    <th>Isikan Rating</th>

                </tr>
            </thead>

            <tbody>
                @if(Session::has('pesan'))
                    <div class="alert alert-success">{{ Session::get('pesan') }}</div>
                @endif

                @foreach($data_buku as $b)
                <tr>
                    <td>{{ $b->id }}</td>
                    <td>
                        @if($b->filepath)
                            <div class="relative h-24 w-24">
                                <img
                                    class="h-full w-full object-cover object-center"
                                    src="{{ asset($b->filepath) }}"
                                    alt=""
                                    style="padding-right: 20px;"
                                />
                            </div>
                        @endif
                    </td>
                    

                    <td>
                        {{ $b->judul }}
                    </td>
                    <td>
                        {{ $b->penulis }}
                    </td>
                    <td>
                        <p class="text-blue-700">" Rating : {{$b->avg_rating}}"</p>  
                    </td>    
                    <td>
                        {{ 'Rp'.number_format($b->harga, 2, ',', '.') }}
                    </td>
                    <td>
                        {{ \Carbon\Carbon::parse($b->tgl_terbit)->format('D/m/Y') }}
                    </td>
                    <td>
                        {{ $b->kategori }}
                    </td>
                    <td>
                        <form action="{{ route('buku.detail_buku', $b->id) }}">
                            <button class="btn btn-primary" onclick="return confirm('Yakin mau lihat detail buku?')">Detail Buku</button>
                        </form>
                    </td>
                    
                    @if(Auth::check() && Auth::user()->level == 'admin')
                    <td>
                        <div class="flex item-center">
                            <form action="{{ route('buku.edit', $b->id) }}">
                                <button class="btn btn-primary mr-2 " onclick="return confirm('Yakin mau diedit?')">Edit</button>
                            </form>
                            <form action="{{ route('buku.destroy', $b->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger" onclick="return confirm('Yakin mau dihapus?')">Hapus</button>
                            </form>
                        </div>
                    </td>
                    @endif

                    <td>
                        <form action="{{ route('buku.rating', $b->id) }}">
                            <button class="btn btn-primary">Rating</button>
                        </form>
                    </td>    
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div>
        <a href="{{ route('buku.buku-populer') }}">Buku Populer</a>

    </div>

    <div class="mt-4">
        {{ $data_buku->links() }}
    </div>
    <div>
        @if(Auth::check() && Auth::user()->level == 'admin')
        <p><a href="{{ route('buku.create') }}">
            <button class="btn btn-success">Tambah Buku</button> 
        </a></p>
        @endif

    </div>   
    <div>
        <p class="text-lg">Jumlah data buku : {{ $jumlah_buku }}</p>
        <p class="text-lg">Jumlah harga semua buku adalah : Rp {{ number_format($total_harga, 2, ',', '.') }}</p>
    </div>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

</x-app-layout>
