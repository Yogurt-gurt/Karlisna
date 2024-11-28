<?php

namespace App\Http\Controllers;

use App\Models\SimpananSukarela;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SimpananSukarelaController extends Controller
{
    // Fungsi untuk membuat simpanan sukarela baru
    public function store(Request $request)
    {
        // Log request data
        Log::info('Membuat simpanan sukarela baru', ['request' => $request->all()]);

        // Validasi data input
        $request->validate([
            'bank' => 'required|string',
            'nominal' => 'required|integer|min:1',
        ]);

        // Simpan data simpanan sukarela
        try {
            $simpanan = SimpananSukarela::create([
                'no_simpanan' => $this->generateNomorSimpanan(),
                'user_id' => auth()->id(), // Mengambil user_id dari pengguna yang login
                'bank' => $request->bank,
                'nominal' => $request->nominal,
                'status_manager' => 'pending', // Status awal untuk manager
                'status_ketua' => 'pending',  // Status awal untuk ketua
            ]);

            Log::info('Simpanan sukarela berhasil dibuat', ['simpanan' => $simpanan]);
            return response()->json($simpanan, 201);
        } catch (\Exception $e) {
            Log::error('Gagal membuat simpanan sukarela', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Gagal membuat simpanan sukarela'], 500);
        }
    }

    // Fungsi untuk melihat semua simpanan sukarela
    public function index()
    {
        Log::info('Menampilkan semua simpanan sukarela');
        $simpananSukarela = SimpananSukarela::with('user')->get();
        return response()->json($simpananSukarela);
    }

    // Fungsi untuk melihat detail simpanan sukarela berdasarkan ID
    public function show($id)
    {
        try {
            Log::info("Menampilkan detail simpanan sukarela dengan ID: $id");
            $simpanan = SimpananSukarela::with('user')->findOrFail($id);
            return response()->json($simpanan);
        } catch (\Exception $e) {
            Log::error("Simpanan sukarela dengan ID: $id tidak ditemukan", ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Simpanan sukarela tidak ditemukan'], 404);
        }
    }

    // Fungsi untuk memperbarui simpanan sukarela
    public function update(Request $request, $id)
    {
        Log::info("Memperbarui simpanan sukarela dengan ID: $id", ['request' => $request->all()]);

        $request->validate([
            'bank' => 'sometimes|string',
            'nominal' => 'sometimes|integer|min:1',
            'status_manager' => 'sometimes|in:pending,approved,rejected',
            'status_ketua' => 'sometimes|in:pending,approved,rejected',
        ]);

        try {
            $simpanan = SimpananSukarela::findOrFail($id);
            $simpanan->update($request->only([
                'bank',
                'nominal',
                'status_manager',
                'status_ketua'
            ]));

            Log::info("Simpanan sukarela dengan ID: $id berhasil diperbarui", ['simpanan' => $simpanan]);
            return response()->json($simpanan);
        } catch (\Exception $e) {
            Log::error("Gagal memperbarui simpanan sukarela dengan ID: $id", ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Gagal memperbarui simpanan sukarela'], 500);
        }
    }

    // Fungsi untuk menghapus simpanan sukarela
    public function destroy($id)
    {
        try {
            Log::info("Menghapus simpanan sukarela dengan ID: $id");
            $simpanan = SimpananSukarela::findOrFail($id);
            $simpanan->delete();
            Log::info("Simpanan sukarela dengan ID: $id berhasil dihapus");
            return response()->json(['message' => 'Simpanan sukarela berhasil dihapus']);
        } catch (\Exception $e) {
            Log::error("Gagal menghapus simpanan sukarela dengan ID: $id", ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Gagal menghapus simpanan sukarela'], 500);
        }
    }

    // Fungsi untuk menghasilkan nomor simpanan unik
    private function generateNomorSimpanan()
    {
        // Format nomor simpanan: SS-YYYYMMDD-RANDOM
        $date = now()->format('Ymd');
        $random = strtoupper(substr(md5(uniqid(rand(), true)), 0, 5));
        return "SS-{$date}-{$random}";
    }
}
