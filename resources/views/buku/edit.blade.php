
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Buku
        </h2>
    </x-slot>
    @if(count($errors) > 0)
    <ul class="alert alert-danger">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    @endif 
    <div class="container mx-auto mt-10 max-w-md p-6 bg-white rounded-lg shadow-md">
        <h2 class="text-2xl font-semibold mb-4">Edit Buku</h2>
        <form action="{{ route('buku.update', $buku->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label for="id" class="block text-gray-700 font-bold">id</label>
                <input type="text" name="id" id="id" value="{{ $buku->id }}" class="border border-gray-300 rounded px-3 py-2 w-full">
            </div>

            <div class="mb-4">
                <label for="judul" class="block text-gray-700 font-bold">Judul</label>
                <input type="text" name="judul" id="judul" value="{{ $buku->judul }}" class="border border-gray-300 rounded px-3 py-2 w-full">
            </div>

            <div class="mb-4">
                <label for="penulis" class="block text-gray-700 font-bold">Penulis</label>
                <input type="text" name="penulis" id="penulis" value="{{ $buku->penulis }}" class="border border-gray-300 rounded px-3 py-2 w-full">
            </div>

            <div class="mb-4">
                <label for="harga" class="block text-gray-700 font-bold">Harga</label>
                <input type="text" name="harga" id="harga" value="{{ $buku->harga }}" class="border border-gray-300 rounded px-3 py-2 w-full">
            </div>
            <div class="mb-4">
                <label for="kategori" class="block text-gray-700 font-bold">Kategori</label>
                <input type="text" name="kategori" id="kategori" value="{{ $buku->kategori }}" class="border border-gray-300 rounded px-3 py-2 w-full">
            </div>

            <div class="mb-4">
                <label for="tgl_terbit" class="block text-gray-700 font-bold">Tanggal Terbit</label>
                <div class='input-group date' id='datetimepicker'>
                    <input type='text' name="tgl_terbit" id="tgl_terbit" value="{{ $buku->tgl_terbit }}" class="form-control" placeholder="yyyy/mm/dd" />
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
            </div>

            <div class="col-span-full mt-6">
                <label for="thumbnail" class="block text-sm font-medium leading-6 text-gray-900">Thumbnail</label>
                <div class="mt-2">
                    <input type="file" name="thumbnail" id="thumbnail">
                </div>
            </div>

            <div class="col-span-full mt-6">
                <label for="gallery" class="block text-sm font-medium leading-6 text-gray-900">Gallery</label>
                <div class="mt-2" id="fileinput_wrapper">
                </div>
                <a href="javascript:void(0);" id="tambah" onclick="addFileInput()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Tambah
                </a>
                <script type="text/javascript">
                    function addFileInput() {
                        var div = document.getElementById('fileinput_wrapper');
                        div.innerHTML += '<input type="file" name="gallery[]" id="gallery" class="block w-full mb-5" style="margin-bottom:5px;">';
                    };
                </script>
            </div>

            <div class="gallery_items mt-6 flex space-x-4">
                @foreach($buku->galleries()->get() as $gallery)
                    <div class="gallery_item border-2 border-gray-200 shadow-lg p-2 rounded flex flex-col items-center">
                        <img
                            class="rounded-full object-cover object-center mb-2"
                            src="{{ asset($gallery->path) }}"
                            alt=""
                            width="100"
                            height="100"
                        />
                    </div>
                @endforeach
            </div>

            <div class="flex justify-between"> <!-- Menggunakan flex untuk meletakkan tombol "Simpan" dan "Batal" sejajar -->
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-4">Simpan</button>
                <a href="/dashboard" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded mt-4">Batal</a>
            </div>
            
        </form>
        <div class="mt-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Hapus Galeri
            </h2>
            @foreach($buku->galleries as $gallery)
            <div class="relative group flex items-center justify-between p-2 border border-gray-200 rounded-lg shadow-md transition duration-300 transform hover:scale-105">
                <img src="{{ asset($gallery->path) }}" alt="Gallery Image" class="rounded-lg object-cover h-32 w-32">

                <form action="{{ route('buku.hapusGallery', ['buku' => $buku->id, 'gallery' => $gallery->id]) }}" method="POST">
                    @csrf
                    @method('DELETE')

                    <button type="submit" class="ml-2 px-4 py-2 bg-red-500 text-white rounded">
                        Hapus
                    </button>
                </form>
            </div>
        @endforeach
        </div>
       
    </div>
   
    

</x-app-layout>    

