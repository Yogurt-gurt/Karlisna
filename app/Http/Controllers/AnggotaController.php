<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AnggotaController extends Controller
{
    public function index()
    {
        return view('pages.manager.approve_regis_manager', [
            'title' => 'Data Aprove Registrasi',
            'anggota' => Anggota::all()

        ]);
    }
    public function index2()
    {
        return view('pages.ketua.approve_regis_ketua', [
            'title' => 'Data Approve Registrasi',
            'anggota' => Anggota::all()

        ]);
    }
    public function laporanregisadmin()
    {
        return view('pages.admin.laporan_regis_admin', [
            'title' => 'Data Approve Registrasi',
            'anggota' => Anggota::all()

        ]);
    }

    public function homeanggota()
    {
        return view('pages.anggota.home_anggota', [
            'title' => 'Dashboard Anggota',
        ]);
    }

    public function tambahsimpanan()
    {
        return view('pages.anggota.simpanan.tambah_simpanan', [
            'title' => 'Tambah Simpanan',
        ]);
    }

    public function tranfersimpanan()
    {
        return view('pages.anggota.transfer_simpanan', [
            'title' => 'Simpanan',
        ]);
    }

    public function store(Request $request)
    {
        // Validasi input yang diperlukan
        $request->validate(
            [
                'nama' => 'required|string|max:255',
                'alamat_domisili' => 'required|string|max:255',
                'tempat_lahir' => 'required|string|max:255',
                'tgl_lahir' => 'required|date', // Perbaiki menjadi tipe 'date'
                'alamat_ktp' => 'required|string|max:255',
                'nik' => 'required|string|max:255',
                'email_kantor' => 'required|email|string|max:255|unique:anggota',
                'no_handphone' => 'required|string|max:255',
                // 'password' => [
                //     'required',
                //     'string',
                //     'min:8', // minimal 8 karakter
                //     'regex:/[A-Za-z]/', // setidaknya satu huruf
                //     'regex:/[0-9]/', // setidaknya satu angka
                //     'regex:/[@$!%*?&]/', // setidaknya satu simbol
                //     'confirmed'
                // ],
            ],
            [
                'nama.required' => 'Nama wajib diisi',
                'alamat_domisili.required' => 'Alamat domisili wajib diisi',
                'tempat_lahir.required' => 'Tempat Lahir wajib diisi',
                'tgl_lahir.required' => 'Tanggal Lahir wajib diisi',
                'tgl_lahir.date' => 'Tanggal Lahir harus berupa tanggal yang valid',
                'alamat_ktp.required' => 'Alamat KTP wajib diisi',
                'nik.required' => 'NIK wajib diisi',
                'email_kantor.required' => 'Email wajib diisi',
                'email_kantor.email' => 'Silahkan masukkan email yang valid',
                'email_kantor.unique' => 'Email sudah ada, silahkan pilih email lain',
                'no_handphone.required' => 'No handphone wajib diisi',
                // 'password.required' => 'Password wajib diisi',
                // 'password.min' => 'Password harus minimal 8 karakter',
                // 'password.regex' => 'Password harus mengandung setidaknya satu huruf, satu angka, dan satu simbol (@, $, !, %, *, ?, &)',
                // 'password.confirmed' => 'Konfirmasi password tidak cocok',
            ]
        );

        // Jika validasi lolos, data disimpan ke dalam database
        Anggota::create([
            'nama' => $request->nama,
            'alamat_domisili' => $request->alamat_domisili,
            'tempat_lahir' => $request->tempat_lahir,
            'tgl_lahir' => $request->tgl_lahir,
            'alamat_ktp' => $request->alamat_ktp,
            'nik' => $request->nik,
            'email_kantor' => $request->email_kantor,
            'no_handphone' => $request->no_handphone,
            'password' => Hash::make($request->password),
            // 'status_verifikasi' => 'pending',
        ]);
        return redirect()->back()->with('success', 'Data Berhasil ditambahkan!');

        $anggota->save();

        // Redirect atau return sesuai kebutuhan
    }

    public function edit($id)
    {
        $anggota = Anggota::findOrFail($id);
        return response()->json($anggota); // Mengembalikan data anggota untuk diisi dalam form
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'tempat_lahir' => 'required|string|max:255',
            'tgl_lahir' => 'required|date',
            'nik' => 'required|string|max:20',
            'email_kantor' => 'required|email|max:255',
            'no_handphone' => 'required|string|max:15',
            'alamat_domisili' => 'required|string|max:255',
            'alamat_ktp' => 'required|string|max:255',
        ]);

        $anggota = Anggota::findOrFail($id);

        // Update data anggota
        $anggota->update($request->all());

        return redirect()->back()->with('success', 'Data Berhasil diedit!');
    }
    public function delete($id)
    {
        $anggota = Anggota::findOrFail($id);
        $anggota->delete();

        return redirect()->back()->with('success', 'Data anggota berhasil dihapus!');
    }
}
