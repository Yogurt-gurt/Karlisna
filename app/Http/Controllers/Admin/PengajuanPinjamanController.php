<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengajuanPinjaman;
use Illuminate\Http\Request;

class PengajuanPinjamanController extends Controller
{
   // Menampilkan daftar pengajuan pinjaman
    public function index()
    {
        $pengajuanPinjaman = PengajuanPinjaman::all();
        return view('admin.pengajuan.index', compact('pengajuanPinjaman'));
    }

    // Halaman untuk mengedit pengajuan pinjaman
    public function edit($id)
    {
        $pengajuan = PengajuanPinjaman::findOrFail($id);
        return view('admin.pengajuan.edit', compact('pengajuan'));
    }

    // Memperbarui pengajuan pinjaman
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

        return redirect()->route('admin.pengajuan.index')->with('success', 'Pengajuan pinjaman berhasil diperbarui!');
    }

    // Menyetujui pengajuan pinjaman
    public function approve($id)
    {
        $pengajuan = PengajuanPinjaman::findOrFail($id);
        $pengajuan->status = 'Diterima';
        $pengajuan->status_admin1 = 'Menunggu Persetujuan Admin2';
        $pengajuan->save();

        return redirect()->route('admin.pengajuan.index')->with('success', 'Pengajuan Pinjaman diterima oleh Admin!');
    }

    // Menolak pengajuan pinjaman
    public function reject($id)
    {
        $pengajuan = PengajuanPinjaman::findOrFail($id);
        $pengajuan->status = 'Ditolak';
        $pengajuan->status_admin1 = 'Ditolak';
        $pengajuan->status_admin2 = 'Ditolak';
        $pengajuan->save();

        return redirect()->route('admin.pengajuan.index')->with('success', 'Pengajuan Pinjaman ditolak oleh Admin!');
    }
}