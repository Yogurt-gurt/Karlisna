<?php

namespace App\Http\Controllers\API;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class NewsController extends Controller
{
    // Mengambil semua berita, termasuk URL gambar lokal
    public function index(Request $request)
    {
        // Tangani preflight request untuk CORS
        if ($request->isMethod('OPTIONS')) {
            return response()->json([], 200)
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        }

        // Ambil semua berita
        $news = News::all();

        // Tambahkan URL lengkap gambar untuk setiap berita
        $news->each(function ($item) {
            if ($item->image_url) {
                $item->image_url = url($item->image_url); // Buat URL lengkap dari path yang disimpan di database
            }
        });

        // Tambahkan header CORS ke response
        return response()->json($news, 200)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    }

    // Menyimpan berita baru ke database, termasuk gambar lokal
    public function store(Request $request)
    {
        // Tangani preflight request untuk CORS
        if ($request->isMethod('OPTIONS')) {
            return response()->json([], 200)
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:102400', // Validasi gambar
        ]);

        $imageName = null;

        // Jika ada file gambar, simpan ke penyimpanan lokal
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = 'uploads/' . time() . '.' . $image->getClientOriginalExtension(); // Buat path file unik berdasarkan timestamp
            $image->move(public_path('uploads'), $imageName); // Simpan gambar di folder 'uploads'
        }

        // Simpan data berita ke database
        $news = News::create([
            'title' => $request->title,
            'content' => $request->content,
            'image_url' => $imageName, // Simpan path file gambar ke database
        ]);

        // Tambahkan URL lengkap gambar untuk respons
        if ($imageName) {
            $news->image_url = url($imageName); // URL lengkap gambar
        }

        // Tambahkan header CORS ke response
        return response()->json($news, 201)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    }
}
