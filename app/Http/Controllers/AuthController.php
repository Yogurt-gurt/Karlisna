<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;

class AuthController extends Controller
{

    public function showRegistrationForm()
    {
        return view('auth.register', [
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


        // anggota::create($data);

        // EmailVerification::insert([
        //     'user_id' => $data,
        //     'token' => crypt($data . Random::generate(4, '0-9') . now(), 10),
        //     'method' => 'email-verif',
        //     'created_at' => now()
        // ]);

        // Mail::to($request->email)->send(new RegisterController($request->email, 'daftar/email-verification'));

        return redirect()->route('informasi-verifikasi')->with('success', 'Registrasi berhasil!');
        // } catch (Exception $e) {
        //     Log::error('Registrasi gagal: ' . $e->getMessage());

        //     return back()->withErrors(['msg' => 'Terjadi kesalahan, silahkan coba lagi.']);
        // }
    }

    public function index()
    {

        return view('auth.login', [
            'title' => 'Login',
        ]);
    }

    public function validasilogin(Request $request)
    {
        $credentials = $request->validate(
            [
                'email' => 'required',
                'password' => 'required'
            ],

            [
                'email.required' => 'Email Wajib Di Isi',
                'password.required' => 'Password Wajib Di Isi',
            ]
        );

        // dd($credentials);

        if (Auth::attempt($credentials)) {
            $userRole = Auth::user()->roles;

            switch ($userRole) {
                case 'anggota':
                    return redirect()->route('home-anggota')->with('success', 'Anda Berhasil Login!');
                case 'manager':
                    return redirect()->route('home-manager')->with('success', 'Anda Berhasil Login!');
                case 'ketua':
                    return redirect()->route('home-ketua')->with('success', 'Anda Berhasil Login!');
                case 'admin':
                    return redirect()->route('home-admin')->with('success', 'Anda Berhasil Login!');
                default:
                    Auth::logout(); // Untuk keamanan jika role tidak sesuai
                    return redirect()->back()->withErrors(['Role pengguna tidak valid.'])->withInput();
            }
        } else {
            return redirect()->back()->withErrors(['username dan password yang dimasukan tidak sesuai'])->withInput();
        }
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
