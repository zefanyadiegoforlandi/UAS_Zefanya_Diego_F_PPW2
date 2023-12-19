<?php
// app/Http/Controllers/FavoriteController.php
// app/Http/Controllers/FavoriteController.php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Favorite;

class FavoriteController extends Controller
{
    public function store(Request $request, $bukuId)
    {
        $isFavorite = auth()->user()->favorites()->where('buku_id', $bukuId)->exists();

        if ($isFavorite) {
            return back()->with('pesan', 'Buku sudah ditambahkan ke daftar favorit.');
        }

        $favorite = new Favorite();
        $favorite->user_id = auth()->user()->id;
        $favorite->buku_id = $bukuId;
        $favorite->save();

        return back()->with('pesan', 'Buku telah ditambahkan ke daftar favorit.');
    }


    public function index()
    {
        $favouriteBooks = auth()->user()->favorites;

        return view('buku.myfavourite', compact('favouriteBooks'));
    }

    public function destroy($id){
        $favorite = Favorite::find($id);
        $favorite->delete();

        return redirect('/dashboard')->with('pesan','Favorite berhasil dihapus');
        
    }
}
