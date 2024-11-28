<?php

namespace App\Http\Controllers;

use App\Models\PinjamanEmergency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PinjamanEmergencyController extends Controller
{
    // Fungsi untuk membuat pengajuan pinjaman emergency baru
    public function store(Request $request)
    {
        // Log request data
        Log::info('Membuat pengajuan pinjaman emergency baru', ['request' => $request->all()]);

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
                Log::error('Gagal membuat pengajuan pinjaman emergency: Rekening tidak ditemukan untuk user yang login');
                return response()->json(['message' => 'Rekening tidak ditemukan untuk pengguna yang login'], 404);
            }

            $pengajuan = PinjamanEmergency::create([
                'nomor_pinjaman' => $this->generateNomorPinjaman(),
                'nominal_pinjaman' => $request->nominal_pinjaman,
                'jangka_waktu' => $request->jangka_waktu,
                'nominal_angsuran' => $request->nominal_angsuran,
                'status' => 'Pending', // Set default status
                'user_id' => auth()->id(), // Mengambil user_id dari pengguna yang login
                'rekening_id' => $rekeningId, // ID rekening dari tabel rekening
                'checkbox_syarat_3' => $request->input('checkbox_syarat_3', false),
                'checkbox_syarat_4' => $request->input('checkbox_syarat_4', false),
                'checkbox_syarat_5' => $request->input('checkbox_syarat_5', false),
                'keterangan' => $request->input('keterangan'),
            ]);

            Log::info('Pengajuan pinjaman emergency berhasil dibuat', ['pengajuan' => $pengajuan]);
            return response()->json($pengajuan, 201);
        } catch (\Exception $e) {
            Log::error('Gagal membuat pengajuan pinjaman emergency', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Gagal membuat pengajuan pinjaman emergency'], 500);
        }
    }

    // Fungsi untuk melihat semua pengajuan pinjaman emergency
    public function index()
    {
        Log::info('Menampilkan semua pengajuan pinjaman emergency');
        $pengajuanPinjaman = PinjamanEmergency::with(['user', 'rekening'])->get();
        return response()->json($pengajuanPinjaman);
    }

    // Fungsi untuk melihat detail pengajuan pinjaman emergency berdasarkan ID
    public function show($id)
    {
        try {
            Log::info("Menampilkan detail pengajuan pinjaman emergency dengan ID: $id");
            $pengajuan = PinjamanEmergency::with(['user', 'rekening'])->findOrFail($id);
            return response()->json($pengajuan);
        } catch (\Exception $e) {
            Log::error("Pengajuan pinjaman emergency dengan ID: $id tidak ditemukan", ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Pengajuan pinjaman emergency tidak ditemukan'], 404);
        }
    }

    // Fungsi untuk memperbarui pengajuan pinjaman emergency
    public function update(Request $request, $id)
    {
        Log::info("Memperbarui pengajuan pinjaman emergency dengan ID: $id", ['request' => $request->all()]);
        
        $request->validate([
            'nominal_pinjaman' => 'sometimes|integer',
            'jangka_waktu' => 'sometimes|integer',
            'nominal_angsuran' => 'sometimes|integer',
            'status' => 'sometimes|in:Pending,Diterima,Ditolak',
        ]);

        try {
            $pengajuan = PinjamanEmergency::findOrFail($id);
            $pengajuan->update($request->only([
                'nominal_pinjaman',
                'jangka_waktu',
                'nominal_angsuran',
                'status',
                'checkbox_syarat_3',
                'checkbox_syarat_4',
                'checkbox_syarat_5',
                'keterangan'
            ]));
            Log::info("Pengajuan pinjaman emergency dengan ID: $id berhasil diperbarui", ['pengajuan' => $pengajuan]);
            return response()->json($pengajuan);
        } catch (\Exception $e) {
            Log::error("Gagal memperbarui pengajuan pinjaman emergency dengan ID: $id", ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Gagal memperbarui pengajuan pinjaman emergency'], 500);
        }
    }

    // Fungsi untuk menghapus pengajuan pinjaman emergency
    public function destroy($id)
    {
        try {
            Log::info("Menghapus pengajuan pinjaman emergency dengan ID: $id");
            $pengajuan = PinjamanEmergency::findOrFail($id);
            $pengajuan->delete();
            Log::info("Pengajuan pinjaman emergency dengan ID: $id berhasil dihapus");
            return response()->json(['message' => 'Pengajuan pinjaman emergency berhasil dihapus']);
        } catch (\Exception $e) {
            Log::error("Gagal menghapus pengajuan pinjaman emergency dengan ID: $id", ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Gagal menghapus pengajuan pinjaman emergency'], 500);
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
