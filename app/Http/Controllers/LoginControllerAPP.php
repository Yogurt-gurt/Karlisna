<?php

namespace App\Http\Controllers;

use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginControllerAPP extends Controller
{
    public function validasilogin(Request $request)
    {
        Log::info('Memulai proses login'); // Log untuk awal proses

        try {
            // Validasi input
            $credentials = $request->validate(
                [
                    'email' => 'required|email',
                    'password' => 'required'
                ],
                [
                    'email.required' => 'Email wajib diisi.',
                    'password.required' => 'Password wajib diisi.',
                ]
            );

            Log::info('Validasi berhasil', ['credentials' => $credentials]); // Log data yang divalidasi

            // Coba autentikasi
            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                Log::info('Autentikasi berhasil', ['user_id' => $user->id, 'role' => $user->role]);

                // Cek apakah role-nya 'anggota'
                if ($user->role === 'anggota') {
                    Log::info('Pengguna dengan role anggota berhasil login', ['user_id' => $user->id]);
                    return response()->json([
                        'success' => true,
                        'message' => 'Berhasil masuk!',
                        'data' => [
                            'id' => $user->id,
                            'email' => $user->email,
                            'role' => $user->role,
                        ]
                    ], 200);
                } else {
                    // Jika bukan 'anggota', logout dan berikan pesan error
                    Log::warning('Pengguna bukan anggota mencoba login', ['user_id' => $user->id, 'role' => $user->role]);
                    Auth::logout();
                    return response()->json([
                        'success' => false,
                        'message' => 'Hanya anggota yang dapat masuk.',
                    ], 403);
                }
            } else {
                // Log jika kredensial salah
                Log::error('Autentikasi gagal. Email atau password salah.', ['email' => $request->input('email')]);
                return response()->json([
                    'success' => false,
                    'message' => 'Email atau password yang dimasukkan tidak sesuai.',
                ], 401);
            }
        } catch (\Exception $e) {
            // Log jika terjadi error pada proses
            Log::error('Terjadi error saat proses login', [
                'error' => $e->getMessage(),
                'stack' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server. Silakan coba lagi nanti.',
            ], 500);
        }
    }
}
