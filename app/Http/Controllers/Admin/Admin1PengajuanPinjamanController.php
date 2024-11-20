<?php

namespace App\Http\Controllers\Admin;

use App\Models\PengajuanPinjaman;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class Admin1PengajuanPinjamanController extends Controller
{
    public function index()
{
    // Menampilkan hanya pengajuan yang sudah disetujui oleh Admin
            $pengajuanPinjaman = PengajuanPinjaman::whereIn('status', ['Diterima', 'Ditolak'])->get();
    return view('admin1.pengajuan.index', compact('pengajuanPinjaman'));
}

    
    public function edit($id)
    {
        $pengajuan = PengajuanPinjaman::findOrFail($id);
        
        // Pastikan hanya pengajuan yang menunggu persetujuan Admin1 yang bisa diedit
        if ($pengajuan->status_admin1 !== 'Menunggu Persetujuan Admin2') {
            return redirect()->route('admin1.pengajuan.index')->with('error', 'Pengajuan ini tidak menunggu persetujuan Admin1');
        }
        
        return view('admin1.pengajuan.edit', compact('pengajuan'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nominal_pinjaman' => 'required|integer',
            'jangka_waktu' => 'required|integer',
        ]);

        $pengajuan = PengajuanPinjaman::findOrFail($id);

        // Hitung nominal angsuran
        $nominalPinjaman = $request->nominal_pinjaman;
        $jangkaWaktu = $request->jangka_waktu;
        $bungaPerTahun = 0.1;
        $bungaPerBulan = $bungaPerTahun / 12;
        $totalBunga = $nominalPinjaman * $bungaPerBulan * $jangkaWaktu;
        $totalPinjamanDenganBunga = $nominalPinjaman + $totalBunga;
        $nominalAngsuran = $totalPinjamanDenganBunga / $jangkaWaktu;

        // Update pengajuan
        $pengajuan->update([
            'nominal_pinjaman' => $nominalPinjaman,
            'jangka_waktu' => $jangkaWaktu,
            'nominal_angsuran' => $nominalAngsuran,
        ]);

        return redirect()->route('admin1.pengajuan.index')->with('success', 'Pengajuan Pinjaman berhasil diperbarui oleh Admin1!');
    }

    // Fungsi untuk menerima pengajuan pinjaman
    public function approve($id)
{
    $pengajuan = PengajuanPinjaman::findOrFail($id);

    // Log pengajuan yang disetujui
    Log::info('Pengajuan Pinjaman diterima oleh Admin1', ['id' => $pengajuan->id, 'status_admin1' => $pengajuan->status_admin1]);

    $pengajuan->status_admin1 = 'Diterima';
    $pengajuan->status_admin2 = 'Menunggu Persetujuan Admin2';
    $pengajuan->save();

    return redirect()->route('admin1.pengajuan.index')->with('success', 'Pengajuan Pinjaman diterima oleh Admin1!');
}

public function reject($id)
{
    $pengajuan = PengajuanPinjaman::findOrFail($id);

    // Log pengajuan yang ditolak
    Log::info('Pengajuan Pinjaman ditolak oleh Admin1', ['id' => $pengajuan->id, 'status_admin1' => $pengajuan->status_admin1]);

    $pengajuan->status_admin1 = 'Ditolak';
    $pengajuan->status_admin2 = 'Ditolak';
    $pengajuan->save();

    return redirect()->route('admin1.pengajuan.index')->with('error', 'Pengajuan Pinjaman ditolak oleh Admin1!');
}
}