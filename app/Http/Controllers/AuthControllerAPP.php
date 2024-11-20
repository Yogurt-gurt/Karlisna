<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthControllerAPP extends Controller
{
    public function showRegistrationForm(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Formulir registrasi ditampilkan',
            'data' => [
                'title' => 'Registrasi',
            ]
        ], 200);
    }

    public function register(Request $request)
{
    try {
        $validatedData = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat_domisili' => 'required|string|max:255',
            'tempat_lahir' => 'required|string|max:255',
            'tgl_lahir' => 'required|date',
            'alamat_ktp' => 'required|string|max:255',
            'nik' => 'required|string|max:255|unique:anggota',
            'email_kantor' => 'required|email|unique:anggota',
            'no_handphone' => 'required|string|max:15',
        ]);

        // Buat pengguna baru
        $user = Anggota::create([
            'nama' => $validatedData['nama'],
            'alamat_domisili' => $validatedData['alamat_domisili'],
            'tempat_lahir' => $validatedData['tempat_lahir'],
            'tgl_lahir' => $validatedData['tgl_lahir'],
            'alamat_ktp' => $validatedData['alamat_ktp'],
            'nik' => $validatedData['nik'],
            'email_kantor' => $validatedData['email_kantor'],
            'no_handphone' => $validatedData['no_handphone'],
            'password' => bcrypt('defaultpassword'), // Set password default atau bisa digenerate
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Registrasi berhasil!',
            'data' => $user,
        ], 201);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Terjadi kesalahan saat registrasi',
            'error' => $e->getMessage(),
        ], 500);
    }
}


    public function index(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Formulir login ditampilkan',
            'data' => [
                'title' => 'Login',
            ]
        ], 200);
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

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Periksa apakah pengguna memiliki role 'anggota'
            if ($user->role == 'anggota') {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Login berhasil!',
                    'data' => [
                        'id' => $user->id,
                        'name' => $user->name, // Pastikan `name` ada dalam respon JSON
                        'role' => $user->role,
                    ]
                ], 200);
            } else {
                // Logout pengguna jika role bukan 'anggota'
                Auth::logout();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Hanya anggota yang diizinkan login.',
                ], 403);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Username dan password yang dimasukkan tidak sesuai.',
            ], 401);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'status' => 'success',
            'message' => 'Logout berhasil!',
        ], 200);
    }
}
