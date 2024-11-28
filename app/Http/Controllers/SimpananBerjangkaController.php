<?php

namespace App\Http\Controllers;

use App\Models\SimpananBerjangka; // Gunakan model SimpananBerjangka
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SimpananBerjangkaController extends Controller
{
    // Fungsi untuk membuat simpanan berjangka baru
    public function store(Request $request)
    {
        // Log request data
        Log::info('Membuat simpanan berjangka baru', ['request' => $request->all()]);

        // Validasi data input
        $request->validate([
            'bank' => 'required|string',
            'nominal' => 'required|integer|min:1',
            'jangka_waktu_simpanan' => 'required|integer|min:1', // Tambahkan validasi untuk jangka waktu
            'jumlah_jasa_perbulan' => 'required|integer|min:0',  // Tambahkan validasi untuk jumlah jasa
        ]);

        // Simpan data simpanan berjangka
        try {
            $simpanan = SimpananBerjangka::create([
                'no_simpanan' => $this->generateNomorSimpanan(),
                'user_id' => auth()->id(), // Mengambil user_id dari pengguna yang login
                'bank' => $request->bank,
                'nominal' => $request->nominal,
                'jangka_waktu_simpanan' => $request->jangka_waktu_simpanan, // Data tambahan
                'jumlah_jasa_perbulan' => $request->jumlah_jasa_perbulan,   // Data tambahan
                'status_manager' => 'pending', // Status awal untuk manager
                'status_ketua' => 'pending',  // Status awal untuk ketua
            ]);

            Log::info('Simpanan berjangka berhasil dibuat', ['simpanan' => $simpanan]);
            return response()->json($simpanan, 201);
        } catch (\Exception $e) {
            Log::error('Gagal membuat simpanan berjangka', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Gagal membuat simpanan berjangka'], 500);
        }
    }

    // Fungsi untuk memperbarui simpanan berjangka
    public function update(Request $request, $id)
    {
        Log::info("Memperbarui simpanan berjangka dengan ID: $id", ['request' => $request->all()]);

        $request->validate([
            'bank' => 'sometimes|string',
            'nominal' => 'sometimes|integer|min:1',
            'jangka_waktu_simpanan' => 'sometimes|integer|min:1',
            'jumlah_jasa_perbulan' => 'sometimes|integer|min:0',
            'status_manager' => 'sometimes|in:pending,approved,rejected',
            'status_ketua' => 'sometimes|in:pending,approved,rejected',
        ]);

        try {
            $simpanan = SimpananBerjangka::findOrFail($id);
            $simpanan->update($request->only([
                'bank',
                'nominal',
                'jangka_waktu_simpanan',
                'jumlah_jasa_perbulan',
                'status_manager',
                'status_ketua',
            ]));

            Log::info("Simpanan berjangka dengan ID: $id berhasil diperbarui", ['simpanan' => $simpanan]);
            return response()->json($simpanan);
        } catch (\Exception $e) {
            Log::error("Gagal memperbarui simpanan berjangka dengan ID: $id", ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Gagal memperbarui simpanan berjangka'], 500);
        }
    }

    // Fungsi untuk menghasilkan nomor simpanan unik
    private function generateNomorSimpanan()
    {
        // Format nomor simpanan: SB-YYYYMMDD-RANDOM
        $date = now()->format('Ymd');
        $random = strtoupper(substr(md5(uniqid(rand(), true)), 0, 5));
        return "SB-{$date}-{$random}";
    }
}
