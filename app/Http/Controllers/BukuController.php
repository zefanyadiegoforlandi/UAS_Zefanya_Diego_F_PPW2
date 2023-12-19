<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Buku;
use Illuminate\Support\Facades\DB;
use App\Models\Gallery;
use Intervention\Image\Facades\Image;

class BukuController extends Controller
{
        public function index(Request $request)
        {
            $batas = 5;
            $jumlah_buku = Buku::count();
            $data_buku = Buku::orderBy('id', 'desc')->paginate($batas);
            $kategoriFilter = $request->input('kategoriFilter');

            $buku = $kategoriFilter
            ? Buku::where('kategori', $kategoriFilter)->get()
            : Buku::all();

            $no = $batas * ($data_buku->currentPage() - 1);
            foreach ($data_buku as $buku) {
                $jumlah_rating = $buku->rating_1 + $buku->rating_2 + $buku->rating_3 + $buku->rating_4 + $buku->rating_5;
                $avg_rating = ($jumlah_rating > 0) ? ($buku->rating_1 + $buku->rating_2 * 2 + $buku->rating_3 * 3 + $buku->rating_4 * 4 + $buku->rating_5 * 5) / $jumlah_rating : 0;
                $buku->avg_rating = number_format($avg_rating, 2);
            }
            $total_harga = Buku::sum('harga');
            return view('/dashboard', compact('data_buku', 'no', 'total_harga', 'jumlah_buku', 'avg_rating','buku','kategoriFilter'));
        }

        public function rating_update(Request $request, string $id)
        {
            $selectedRating = $request->input('rating', 0);
        
            $buku = Buku::find($id);
        
            $request->validate([
                'rating' => 'numeric|min:1|max:5',
            ]);
          
            if (!$buku) {
                return redirect('/buku')->with('pesan', 'Buku tidak ditemukan');
            }
        
            $buku->update([
                'rating_1' => ($selectedRating == 1) ? $buku->rating_1 + 1 : $buku->rating_1,
                'rating_2' => ($selectedRating == 2) ? $buku->rating_2 + 1 : $buku->rating_2,
                'rating_3' => ($selectedRating == 3) ? $buku->rating_3 + 1 : $buku->rating_3,
                'rating_4' => ($selectedRating == 4) ? $buku->rating_4 + 1 : $buku->rating_4,
                'rating_5' => ($selectedRating == 5) ? $buku->rating_5 + 1 : $buku->rating_5,
            ]);
        
            $bukuData = [
                'rating_1' => $buku->rating_1,
                'rating_2' => $buku->rating_2,
                'rating_3' => $buku->rating_3,
                'rating_4' => $buku->rating_4,
                'rating_5' => $buku->rating_5
            ];
        
            // Redirect ke halaman buku dengan pesan sukses
            return redirect('/buku')->with('pesan', 'Rating Berhasil di Simpan');
        }
        //DETAIL,LIST,RATING
        public function rating($id) {
            $buku = Buku::find($id);
            return view('buku.rating', compact('buku'));
        }
        public function list_buku(){
            $batas = 5;
            $data_buku = Buku::orderBy('id','desc')->paginate($batas);
            $no = $batas * ($data_buku->currentPage()-1);
            return view('/buku/list_buku', compact('data_buku'));
        }
        public function detail_buku(){
            $batas = 5;
            $data_buku = Buku::orderBy('id','desc')->paginate($batas);
            $no = $batas * ($data_buku->currentPage()-1);
            return view('/buku/detail_buku', compact('data_buku'));
        }


        //CREATE
        public function create(){
            $buku = new Buku; 
            return view('buku.create', compact('buku'));
        }

        public function store(Request $request) {
            $request->validate([
                'thumbnail' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'gallery.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
        
            $bukuData = [
                'judul' => $request->judul,
                'penulis' => $request->penulis,
                'harga' => $request->harga,
                'tgl_terbit' => $request->tgl_terbit,
                'kategori' => $request->kategori,
            ];
        
            if ($request->hasFile('thumbnail')) {
                $thumbnailFile = $request->file('thumbnail');
                $thumbnailFileName = time() . '_' . $thumbnailFile->getClientOriginalName();
                $thumbnailFilePath = $thumbnailFile->storeAs('uploads', $thumbnailFileName, 'public');
        
                Image::make(storage_path() . '/app/public/uploads/' . $thumbnailFileName)
                    ->fit(240, 320)
                    ->save();
        
                $bukuData['filename'] = $thumbnailFileName;
                $bukuData['filepath'] = '/storage/' . $thumbnailFilePath;
            }
        
            $buku = Buku::create($bukuData);
        
            // Mendapatkan id buku yang baru saja dibuat
            $id = $buku->id;
        
            if ($request->hasFile('gallery')) {
                foreach ($request->file('gallery') as $file) {
                    $galleryFileName = time() . '_' . $file->getClientOriginalName();
                    $galleryFilePath = $file->storeAs('uploads', $galleryFileName, 'public');
        
                    $gallery = Gallery::create([
                        'nama_galeri'   => $galleryFileName,
                        'path'          => '/storage/' . $galleryFilePath,
                        'foto'          => $galleryFileName,
                        'buku_id'       => $id,
                        'galeri_seo'    => 'nilai_default_yang_diinginkan'
                    ]);
                }
            }
        
            return redirect('/dashboard')->with('pesan', 'Buku baru berhasil ditambahkan');
        }
        
        //DESTROY BUKU
        public function destroy($id){
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            $buku = Buku::find($id);
            $buku->delete();
            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            return redirect('/dashboard')->with('pesan','Buku berhasil dihapus');
        }
        public function hapusGallery($bukuId, $galleryId)
        {
            $buku = Buku::find($bukuId);
            $gallery = $buku->galleries()->find($galleryId);
            $gallery->delete();
        
            return redirect()->back()->with('success', 'Gambar berhasil dihapus');
        }

        //EDIT BUKU
        public function edit($id) {
            $buku = Buku::find($id);
            return view('buku.edit', compact('buku'));
        }
        //UPDATE
        public function update(Request $request, string $id)
        {
            $selectedRating = $request->input('rating', 0);

            $buku = Buku::find($id);
            $request->validate([
                'thumbnail' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'rating' => 'numeric|min:1|max:5',
                'kategori' => 'required|string|max:255', // Aturan validasi untuk kategori
            ]);

            $buku->update([
                'judul' => $request->judul,
                'penulis' => $request->penulis,
                'harga' => $request->harga,
                'tgl_terbit' => $request->tgl_terbit,
                'kategori' => $request->kategori, // Tambahkan kategori ke dalam update
            ]);

            if (!$buku) {
                return redirect()->back()->with('error', 'Buku tidak ditemukan');
            }

        if ($request->hasFile('thumbnail')) {
            $fileName = time() . '_' . $request->thumbnail->getClientOriginalName();
            $filePath = $request->file('thumbnail')->storeAs('uploads', $fileName, 'public');

            Image::make(storage_path() . '/app/public/uploads/' . $fileName)
                ->fit(240, 320)
                ->save();

            $buku->update([
                'judul' => $request->judul,
                'penulis' => $request->penulis,
                'harga' => $request->harga,
                'tgl_terbit' => $request->tgl_terbit,
                'kategori' => $request->kategori, // Tambahkan kategori ke dalam update
                'filename' => $fileName,
                'filepath' => '/storage/' . $filePath,
                'rating_1' => ($selectedRating == 1) ? $buku->rating_1 + 1 : $buku->rating_1,
                'rating_2' => ($selectedRating == 2) ? $buku->rating_2 + 1 : $buku->rating_2,
                'rating_3' => ($selectedRating == 3) ? $buku->rating_3 + 1 : $buku->rating_3,
                'rating_4' => ($selectedRating == 4) ? $buku->rating_4 + 1 : $buku->rating_4,
                'rating_5' => ($selectedRating == 5) ? $buku->rating_5 + 1 : $buku->rating_5,
            ]);
        }

        if ($request->file('gallery')) {
            foreach ($request->file('gallery') as $key => $file) {
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('uploads', $fileName, 'public');

                $gallery = Gallery::create([
                    'nama_galeri' => $fileName,
                    'path' => '/storage/' . $filePath,
                    'foto' => $fileName,
                    'buku_id' => $id
                ]);
            }
        } 

        return redirect('/dashboard')->with('pesan', 'Perubahan Berhasil di Simpan');
        }



        public function _construct(){
            $this->middleware('admin');
        }
        
        //SEARCH
        public function search(Request $request) {
            $batas = 5;
            $cari = $request->kata; 
            $data_buku = Buku::where('judul', 'like', '%' . $cari . '%')
                ->orWhere('penulis', 'like', '%' . $cari . '%')
                ->paginate($batas);
            $no = $batas * ($data_buku->currentPage() - 1);
            $total_harga = DB::table('buku')->sum('harga');
            $jumlah_buku = $data_buku->count();
        
            return view('buku.search', compact('data_buku', 'total_harga', 'no', 'jumlah_buku', 'cari'));
        }
        
        public function photos(){
            return $this->hasMany('App\Buku', 'id_buku', 'id');
        }

        public function galbuku($title)
        {
            $bukus = Buku::where('buku_seo', $title)->first();
            $galeries = $bukus->galleries()->orderBy('id', 'desc')->paginate(5);
            return view ('buku.detail_buku', compact('bukus', 'galeries'));
        }


        public function add_favourite(Buku $buku)
        {
            $user = Auth::user();

            if (!$user->favourites) {
                $user->favourites = [];
            }

            if (!in_array($buku->id, $user->favourites)) {
                $user->favourites[] = $buku->id;
                $user->save();
            }

            return redirect()->back()->with('success', 'Buku ditambahkan ke daftar favorit.');
        }

        public function my_favourite()
        {
            $user = Auth::user();
            $favouriteBooks = Buku::whereIn('id', $user->favourites ?? [])->get(['judul', 'pengarang']);

            return view('buku.myfavourite', compact('favouriteBooks'));
        }

        public function bukuPopuler()
        {
            $data_buku = Buku::orderBy('id', 'desc')->get();

            $topBukuPopuler = $data_buku->map(function ($buku) {
                $jumlah_rating = $buku->rating_1 + $buku->rating_2 + $buku->rating_3 + $buku->rating_4 + $buku->rating_5;
                $avg_rating = ($jumlah_rating > 0) ? ($buku->rating_1 * 1 + $buku->rating_2 * 2 + $buku->rating_3 * 3 + $buku->rating_4 * 4 + $buku->rating_5 * 5) / $jumlah_rating : 0;

                $buku->avg_rating = number_format($avg_rating, 2);

                return $buku;
            })
            ->sortByDesc('avg_rating')
            ->take(10);

            return view('buku.buku-populer', compact('topBukuPopuler'));
        }

        public function kategori(Request $request)
        {
            $batas = 5;
            $jumlah_buku = Buku::count();
            $kategoriFilter = $request->input('kategoriFilter');

            // Jika ada kategori yang dipilih, filter buku berdasarkan kategori
            if ($kategoriFilter) {
                $data_buku = Buku::where('kategori', $kategoriFilter)->orderBy('id', 'desc')->paginate($batas);
            } else {
                // Jika tidak ada kategori yang dipilih, tampilkan semua buku
                $data_buku = Buku::orderBy('id', 'desc')->paginate($batas);
            }

            $no = $batas * ($data_buku->currentPage() - 1);
            
            foreach ($data_buku as $buku) {
                $jumlah_rating = $buku->rating_1 + $buku->rating_2 + $buku->rating_3 + $buku->rating_4 + $buku->rating_5;
                $avg_rating = ($jumlah_rating > 0) ? ($buku->rating_1 + $buku->rating_2 * 2 + $buku->rating_3 * 3 + $buku->rating_4 * 4 + $buku->rating_5 * 5) / $jumlah_rating : 0;
                $buku->avg_rating = number_format($avg_rating, 2);
            }

            $total_harga = Buku::sum('harga');

            return view('/buku.kategori', compact('data_buku', 'no', 'total_harga', 'jumlah_buku', 'avg_rating', 'kategoriFilter'));
        }



    }