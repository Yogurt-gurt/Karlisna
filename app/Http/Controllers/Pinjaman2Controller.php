<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use Nette\Utils\Random;
use App\Models\Pinjaman;
use Illuminate\Http\Request;
use App\Models\PinjamanCategory;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;


class Pinjaman2Controller extends Controller
{
    public function laporanpinjaman()
    {
        $data = [
            'title' => 'Data Pinjaman',
            'pinjamans' => auth()->user()->pinjaman
        ];

        return view('pages.admin.pinjaman.laporan_pinjaman', $data);
    }

    private function generateNomorPinjaman()
    {
        // Format nomor pinjaman: PIN-YYYYMMDD-RANDOM
        $date = now()->format('Ymd');
        $random = strtoupper(substr(md5(uniqid(rand(), true)), 0, 5));
        return "PIN-{$date}-{$random}";
    }

    public function nonangunan()
    {
        $users = Auth::user();
        $data = [
            'title' => 'Form Tambah Pinjaman No Angunan',
            'tenor' => Pinjaman::STATUS_OPTIONS,
            'new_nmr' => $this->generateNomorPinjaman(),
            'noRekening' => $users->no_rekening
        ];
        return view('pages.anggota.pinjaman.add_no_angunan2', $data);
    }
    public function store2(Request $request)
    {
        // Validasi data input
        $request->validate([
            'nomor_pinjaman' => 'required|string|unique:pinjamen',  // Validasi nomor pinjaman harus unik
            'nominal' => 'required|numeric',  // Validasi nominal pinjaman
            'nominal_angsuran' => 'required|numeric',  // Validasi nominal angsuran
            'tenor' => 'required|numeric',  // Validasi tenor sebagai angka

        ]);

        // Ambil data user yang sedang login
        $users = Auth::user();

        // Menyimpan data ke dalam database
        Pinjaman::create([
            'user_id' => $users->id,  // Menyimpan ID user yang sedang login
            'rekening_id' => $users->rekening_id,  // Menyimpan rekening_id user
            'nomor_pinjaman' => $request->nomor_pinjaman,  // Menyimpan nomor pinjaman dari input
            'nominal_pinjaman' => $request->nominal,  // Menyimpan nominal pinjaman
            'jangka_waktu' => $request->tenor,  // Menyimpan tenor pinjaman
            'nominal_angsuran' => $request->nominal_angsuran,  // Menyimpan nominal angsuran

        ]);

        return redirect()->route('pinjaman-nonangunan')->with('success', 'Data Pinjaman Berhasil Disimpan!');
    }
}
