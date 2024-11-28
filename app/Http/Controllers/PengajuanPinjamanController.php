<?php

namespace App\Http\Controllers;

use App\Models\PengajuanPinjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PengajuanPinjamanController extends Controller
{
    // Fungsi untuk membuat pengajuan pinjaman baru
    public function store(Request $request)
    {
        // Log request data
        Log::info('Membuat pengajuan pinjaman baru', ['request' => $request->all()]);

        // Validasi data input
        $request->validate([
            'nominal_pinjaman' => 'required|integer',
            'jangka_waktu' => 'required|integer',
            'nominal_angsuran' => 'required|integer',
            'rekening_id' => 'nullable|exists:rekenings,id', // rekening_id bisa null
        ]);

        // Simpan data pengajuan pinjaman
        try {

             // Ambil rekening_id dari tabel rekening berdasarkan user_id pengguna yang login
        $rekeningId = \App\Models\Rekening::where('user_id', auth()->id())->value('id');

        // Jika tidak ada rekening yang ditemukan, kembalikan respons error
        if (!$rekeningId) {
            Log::error('Gagal membuat pengajuan pinjaman: Rekening tidak ditemukan untuk user yang login');
            return response()->json(['message' => 'Rekening tidak ditemukan untuk pengguna yang login'], 404);
        }


            $pengajuan = PengajuanPinjaman::create([
                'nomor_pinjaman' => $this->generateNomorPinjaman(),
                'nominal_pinjaman' => $request->nominal_pinjaman,
                'jangka_waktu' => $request->jangka_waktu,
                'nominal_angsuran' => $request->nominal_angsuran,
                'status' => 'Pending', // Set default status
                'user_id' => auth()->id(), // Mengambil user_id dari pengguna yang login
                'rekening_id' => $rekeningId, // ID rekening dari tabel rekening
            ]);

            Log::info('Pengajuan pinjaman berhasil dibuat', ['pengajuan' => $pengajuan]);
            return response()->json($pengajuan, 201);
        } catch (\Exception $e) {
            Log::error('Gagal membuat pengajuan pinjaman', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Gagal membuat pengajuan pinjaman'], 500);
        }
    }

    // Fungsi untuk melihat semua pengajuan pinjaman
    public function index()
    {
        Log::info('Menampilkan semua pengajuan pinjaman');
        $pengajuanPinjaman = PengajuanPinjaman::with(['user', 'rekening'])->get();
        return response()->json($pengajuanPinjaman);
    }

    // Fungsi untuk melihat detail pengajuan pinjaman berdasarkan ID
    public function show($id)
    {
        try {
            Log::info("Menampilkan detail pengajuan pinjaman dengan ID: $id");
            $pengajuan = PengajuanPinjaman::with(['user', 'rekening'])->findOrFail($id);
            return response()->json($pengajuan);
        } catch (\Exception $e) {
            Log::error("Pengajuan pinjaman dengan ID: $id tidak ditemukan", ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Pengajuan pinjaman tidak ditemukan'], 404);
        }
    }

    // Fungsi untuk memperbarui pengajuan pinjaman
    public function update(Request $request, $id)
    {
        Log::info("Memperbarui pengajuan pinjaman dengan ID: $id", ['request' => $request->all()]);
        
        $request->validate([
            'nominal_pinjaman' => 'sometimes|integer',
            'jangka_waktu' => 'sometimes|integer',
            'nominal_angsuran' => 'sometimes|integer',
            'status' => 'sometimes|in:Pending,Diterima,Ditolak',
        ]);

        try {
            $pengajuan = PengajuanPinjaman::findOrFail($id);
            $pengajuan->update($request->only([
                'nominal_pinjaman',
                'jangka_waktu',
                'nominal_angsuran',
                'status'
            ]));
            Log::info("Pengajuan pinjaman dengan ID: $id berhasil diperbarui", ['pengajuan' => $pengajuan]);
            return response()->json($pengajuan);
        } catch (\Exception $e) {
            Log::error("Gagal memperbarui pengajuan pinjaman dengan ID: $id", ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Gagal memperbarui pengajuan pinjaman'], 500);
        }
    }
    

    // Fungsi untuk menghapus pengajuan pinjaman
    public function destroy($id)
    {
        try {
            Log::info("Menghapus pengajuan pinjaman dengan ID: $id");
            $pengajuan = PengajuanPinjaman::findOrFail($id);
            $pengajuan->delete();
            Log::info("Pengajuan pinjaman dengan ID: $id berhasil dihapus");
            return response()->json(['message' => 'Pengajuan pinjaman berhasil dihapus']);
        } catch (\Exception $e) {
            Log::error("Gagal menghapus pengajuan pinjaman dengan ID: $id", ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Gagal menghapus pengajuan pinjaman'], 500);
        }
    }

     private function generateNomorPinjaman()
    {
        // Format nomor pinjaman: PIN-YYYYMMDD-RANDOM
        $date = now()->format('Ymd');
        $random = strtoupper(substr(md5(uniqid(rand(), true)), 0, 5));
        return "PIN-{$date}-{$random}";
    }

}

