<?php

namespace App\Http\Controllers;

use App\Models\PengajuanPinjaman;
use App\Models\Rekening;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebPengajuanPinjamanController extends Controller
{

     public function create()
    {
        // Mengambil daftar rekening dari tabel rekenings
        $rekenings = Rekening::all();
        $jangkaWaktuOptions = [3, 6, 9, 12, 15, 18]; // Pilihan jangka waktu dalam bulan

        return view('pengajuan.create', compact('rekenings', 'jangkaWaktuOptions'));
    }

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
            'user_id' => 'nullable|exists:users,id',  // user_id bisa null
            'rekening_id' => 'nullable|exists:rekenings,id', // rekening_id bisa null
        ]);

        // Hitung nominal angsuran otomatis berdasarkan nominal pinjaman dan jangka waktu
        $nominalPinjaman = $request->nominal_pinjaman;
        $jangkaWaktu = $request->jangka_waktu;
        $bungaPerTahun = 0.1;  // Misalnya bunga 10% per tahun
        $bungaPerBulan = $bungaPerTahun / 12;
        $totalBunga = $nominalPinjaman * $bungaPerBulan * $jangkaWaktu;
        $totalPinjamanDenganBunga = $nominalPinjaman + $totalBunga;
        $nominalAngsuran = $totalPinjamanDenganBunga / $jangkaWaktu;

        // Simpan data pengajuan pinjaman
        try {
            $pengajuan = PengajuanPinjaman::create([
                'nominal_pinjaman' => $nominalPinjaman,
                'jangka_waktu' => $jangkaWaktu,
                'nominal_angsuran' => $nominalAngsuran,  // Angsuran yang dihitung otomatis
                'status' => 'Pending', // Set default status
                'user_id' => $request->user_id,
                'rekening_id' => $request->rekening_id,
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

            // Hitung nominal angsuran jika diperlukan
            if ($request->has('nominal_pinjaman') && $request->has('jangka_waktu')) {
                $nominalPinjaman = $request->nominal_pinjaman;
                $jangkaWaktu = $request->jangka_waktu;
                $bungaPerTahun = 0.1;
                $bungaPerBulan = $bungaPerTahun / 12;
                $totalBunga = $nominalPinjaman * $bungaPerBulan * $jangkaWaktu;
                $totalPinjamanDenganBunga = $nominalPinjaman + $totalBunga;
                $nominalAngsuran = $totalPinjamanDenganBunga / $jangkaWaktu;

                $request->merge(['nominal_angsuran' => $nominalAngsuran]);
            }

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
}
