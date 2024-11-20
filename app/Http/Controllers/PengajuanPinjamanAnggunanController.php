<?php

namespace App\Http\Controllers;

use App\Models\PengajuanPinjamanAnggunan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PengajuanPinjamanAnggunanController extends Controller
{
    // Fungsi untuk membuat pengajuan pinjaman dengan anggunan baru
 public function store(Request $request)
{
    // Ambil pengguna yang sedang login dari token autentikasi
    $user = $request->user(); // Pastikan middleware auth sudah aktif

    // Validasi data yang diperlukan
    $validatedData = $request->validate([
        'nominal_pinjaman' => 'required|integer',
        'jangka_waktu' => 'required|integer',
        'nominal_angsuran' => 'required|integer',
        'jenis_anggunan' => 'required|string',
        'file_anggunan' => 'required|string', // Harus menggunakan URL atau path yang valid
    ]);

    try {
        Log::info('Data diterima untuk disimpan ke database', ['data' => $validatedData]);

        // Tambahkan user_id secara otomatis
        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan, harap login terlebih dahulu'], 401);
        }

        $validatedData['user_id'] = $user->id;

        // Pilih rekening_id secara otomatis (contoh: ambil rekening pertama milik user)
        $rekening = $user->rekenings()->first();
        if (!$rekening) {
            return response()->json(['message' => 'Tidak ada rekening yang terdaftar untuk pengguna ini'], 400);
        }
        $validatedData['rekening_id'] = $rekening->id;

        // Simpan ke database
        $pengajuan = PengajuanPinjamanAnggunan::create($validatedData);

        Log::info('Pengajuan pinjaman anggunan berhasil dibuat', ['pengajuan' => $pengajuan]);
        return response()->json($pengajuan, 201);
    } catch (\Exception $e) {
        Log::error('Gagal menyimpan pengajuan ke database', ['error' => $e->getMessage()]);
        return response()->json(['message' => 'Gagal membuat pengajuan pinjaman anggunan'], 500);
    }
}



    // Fungsi untuk melihat semua pengajuan pinjaman anggunan
    public function index()
    {
        Log::info('Menampilkan semua pengajuan pinjaman anggunan');
        $pengajuanPinjaman = PengajuanPinjamanAnggunan::with(['user', 'rekening'])->get();
        return response()->json($pengajuanPinjaman);
    }

    // Fungsi untuk melihat detail pengajuan pinjaman anggunan berdasarkan ID
    public function show($id)
    {
        try {
            Log::info("Menampilkan detail pengajuan pinjaman anggunan dengan ID: $id");
            $pengajuan = PengajuanPinjamanAnggunan::with(['user', 'rekening'])->findOrFail($id);
            return response()->json($pengajuan);
        } catch (\Exception $e) {
            Log::error("Pengajuan pinjaman anggunan dengan ID: $id tidak ditemukan", ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Pengajuan pinjaman anggunan tidak ditemukan'], 404);
        }
    }

    // Fungsi untuk memperbarui pengajuan pinjaman anggunan
    public function update(Request $request, $id)
    {
        Log::info("Memperbarui pengajuan pinjaman anggunan dengan ID: $id", ['request' => $request->all()]);
        
        $request->validate([
            'nominal_pinjaman' => 'sometimes|integer',
            'jangka_waktu' => 'sometimes|integer',
            'nominal_angsuran' => 'sometimes|integer',
            'status' => 'sometimes|in:Pending,Diterima,Ditolak',
            'jenis_anggunan' => 'sometimes|string',
            'file_anggunan' => 'sometimes|string',
        ]);

        try {
            $pengajuan = PengajuanPinjamanAnggunan::findOrFail($id);
            $pengajuan->update($request->only([
                'nominal_pinjaman',
                'jangka_waktu',
                'nominal_angsuran',
                'status',
                'jenis_anggunan',
                'file_anggunan'
            ]));
            Log::info("Pengajuan pinjaman anggunan dengan ID: $id berhasil diperbarui", ['pengajuan' => $pengajuan]);
            return response()->json($pengajuan);
        } catch (\Exception $e) {
            Log::error("Gagal memperbarui pengajuan pinjaman anggunan dengan ID: $id", ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Gagal memperbarui pengajuan pinjaman anggunan'], 500);
        }
    }

    // Fungsi untuk menghapus pengajuan pinjaman anggunan
    public function destroy($id)
    {
        try {
            Log::info("Menghapus pengajuan pinjaman anggunan dengan ID: $id");
            $pengajuan = PengajuanPinjamanAnggunan::findOrFail($id);
            $pengajuan->delete();
            Log::info("Pengajuan pinjaman anggunan dengan ID: $id berhasil dihapus");
            return response()->json(['message' => 'Pengajuan pinjaman anggunan berhasil dihapus']);
        } catch (\Exception $e) {
            Log::error("Gagal menghapus pengajuan pinjaman anggunan dengan ID: $id", ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Gagal menghapus pengajuan pinjaman anggunan'], 500);
        }
    }
}
