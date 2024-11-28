<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class RegistrasiController extends Controller
{
    public function showRegistrationForm()
    {
        return view('registrasi', [
            'title' => 'Registrasi',
        ]);
    }

    public function register(Request $request)
    {
        // Session::flash('nama', $request->nama);
        // Session::flash('alamat_domisili', $request->alamat_domisili);
        // Session::flash('tempat_tgl_lahir', $request->tempat_tgl_lahir);
        // Session::flash('alamat_ktp', $request->alamat_ktp);
        // Session::flash('nik', $request->nik);
        // Session::flash('email_kantor', $request->email_kantor);
        // Session::flash('no_handphone', $request->no_handphone);

        $request->validate(
            [
                'nama' => 'required|string|max:255',
                'alamat_domisili' => 'required|string|max:255',
                'tempat_lahir' => 'required|string|max:255',
                'tgl_lahir' => 'required|date',
                'alamat_ktp' => 'required|string|max:255',
                'nik' => 'required|string|max:255',
                'email_kantor' => 'required|email|string|max:255|unique:anggota',
                'no_handphone' => 'required|string|max:255',
                // 'password' => [
                //     'required',
                //     'string',
                //     'min:8',
                //     'regex:/[A-Za-z]/',
                //     'regex:/[0-9]/',
                //     'regex:/[@$!%*?&]/',
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

        try {
            // Jika validasi lolos, data disimpan ke dalam database
            $anggota = Anggota::create([
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

            // EmailVerification::insert([
            //     'user_id' => $data,
            //     'token' => crypt($data . Random::generate(4, '0-9') . now(), 10),
            //     'method' => 'email-verif',
            //     'created_at' => now()
            // ]);

            // Mail::to($request->email)->send(new RegisterController($request->email, 'daftar/email-verification'));

            // Cek apakah permintaan adalah AJAX atau JSON (misalnya dari Flutter)
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Registrasi berhasil!',
                    'data' => [
                        'id' => $anggota->id,
                        'nama' => $anggota->nama,
                        'email_kantor' => $anggota->email_kantor,
                    ],
                ], 201);
            }

            // Jika bukan JSON, redirect ke halaman informasi-verifikasi
            return redirect('informasi-verifikasi')->with('success', 'Registrasi berhasil!');

        } catch (\Exception $e) {
            // Log error jika terjadi kegagalan
            Log::error('Registrasi gagal: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Terjadi kesalahan saat registrasi: ' . $e->getMessage(),
                ], 500);
            }

            return back()->withErrors(['msg' => 'Terjadi kesalahan, silakan coba lagi.']);
        }
    }
}
