<?php

namespace App\Http\Controllers\Admin;

use App\Models\PengajuanPinjaman;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Admin2PengajuanPinjamanController extends Controller
{
    // Menampilkan daftar pengajuan pinjaman yang telah disetujui oleh Admin1
    public function index()
    {
        $pengajuanPinjaman = PengajuanPinjaman::whereIn('status_admin1', ['Diterima', 'Ditolak'])->get();// Filter pengajuan yang diterima Admin1
        return view('admin2.pengajuan.index', compact('pengajuanPinjaman'));
    }

    // Halaman untuk mengedit pengajuan pinjaman
    public function edit($id)
    {
        $pengajuan = PengajuanPinjaman::findOrFail($id);
        if ($pengajuan->status_admin1 !== 'Diterima') {
            return redirect()->route('admin2.pengajuan.index')->with('error', 'Pengajuan ini belum disetujui oleh Admin1');
        }
        return view('admin2.pengajuan.edit', compact('pengajuan'));
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

        return redirect()->route('admin2.pengajuan.index')->with('success', 'Pengajuan Pinjaman berhasil diperbarui oleh Admin2!');
    }

    // Fungsi untuk menerima pengajuan pinjaman
    public function approve($id)
    {
        $pengajuan = PengajuanPinjaman::findOrFail($id);

        // Set status untuk Admin2
        $pengajuan->status_admin2 = 'Diterima'; // Menyetujui pengajuan
        $pengajuan->save();

        return redirect()->route('admin2.pengajuan.index')->with('success', 'Pengajuan Pinjaman diterima oleh Admin2!');
    }

    // Fungsi untuk menolak pengajuan pinjaman
    public function reject($id)
    {
        $pengajuan = PengajuanPinjaman::findOrFail($id);

        // Set status untuk Admin2
        $pengajuan->status_admin2 = 'Ditolak'; // Menolak pengajuan
        $pengajuan->save();

        return redirect()->route('admin2.pengajuan.index')->with('error', 'Pengajuan Pinjaman ditolak oleh Admin2!');
    }
}
