<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\News;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    // Menampilkan semua berita
    public function index()
    {
        $news = News::all();
        return view('admin.news.index', compact('news'));
    }

    // Menampilkan form untuk membuat berita baru
    public function create()
    {
        return view('admin.news.create');
    }

    // Menyimpan berita baru ke database
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:102400', // Validasi gambar
        ]);

        // Jika ada file gambar, simpan ke penyimpanan lokal
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension(); // Buat nama file unik berdasarkan timestamp
            $request->image->move(public_path('uploads'), $imageName); // Simpan ke folder 'uploads'
            $imagePath = 'uploads/' . $imageName; // Simpan path ke database
        } else {
            $imagePath = null; // Jika tidak ada gambar, biarkan null
        }

        // Simpan berita ke database
        News::create([
            'title' => $request->title,
            'content' => $request->content,
            'image_url' => $imagePath, // Simpan path gambar ke database
        ]);

        return redirect()->route('admin.news.index')
            ->with('success', 'Berita berhasil ditambahkan.');
    }

    // Menampilkan form untuk mengedit berita
    public function edit($id)
    {
        $news = News::findOrFail($id);
        return view('admin.news.edit', compact('news'));
    }

    // Mengupdate berita yang ada
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10400', // Validasi gambar
        ]);

        $news = News::findOrFail($id);

        // Cek apakah ada file gambar baru yang di-upload
        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($news->image_url) {
                $oldImagePath = public_path($news->image_url);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            // Upload gambar baru
            $imageName = time() . '.' . $request->image->extension(); // Nama gambar unik
            $request->image->move(public_path('uploads'), $imageName); // Simpan ke folder 'uploads'
            $imagePath = 'uploads/' . $imageName; // Simpan path ke database
        } else {
            $imagePath = $news->image_url; // Jika tidak ada gambar baru, gunakan gambar lama
        }

        // Update berita di database
        $news->update([
            'title' => $request->title,
            'content' => $request->content,
            'image_url' => $imagePath, // Update path gambar di database
        ]);

        return redirect()->route('admin.news.index')
            ->with('success', 'Berita berhasil diperbarui.');
    }

    // Menghapus berita
    public function destroy($id)
    {
        $news = News::findOrFail($id);

        // Hapus gambar dari penyimpanan lokal
        if ($news->image_url) {
            $imagePath = public_path($news->image_url);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        // Hapus berita dari database
        $news->delete();

        return redirect()->route('admin.news.index')
            ->with('success', 'Berita berhasil dihapus.');
    }
}
