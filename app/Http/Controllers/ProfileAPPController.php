<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class ProfileAPPController extends Controller
{
    // Mengambil data profil pengguna
    public function getProfile(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            // Ambil data anggota melalui relasi
            $anggota = $user->anggota;

            // Gabungkan data dari users dan anggota
            $profileData = [
                'id' => $user->id,
                'name' => $user->name,
                'roles' => $user->roles,
                'email' => $user->email,
                'tempat_lahir' => $anggota->tempat_lahir ?? null,
                'tanggal_lahir' => $anggota->tgl_lahir ?? null,
                'alamat_domisili' => $anggota->alamat_domisili ?? null,
                'alamat_ktp' => $anggota->alamat_ktp ?? null,
                'nik' => $anggota->nik ?? null,
                'no_handphone' => $anggota->no_handphone ?? null,
            ];

            return response()->json(['data' => $profileData], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to fetch profile', 'error' => $e->getMessage()], 500);
        }
    }

    // Mengupdate data profil pengguna
    public function updateProfile(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            // Validasi input
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'tempat_lahir' => 'nullable|string|max:255',
                'tanggal_lahir' => 'nullable|date',
                'alamat_domisili' => 'nullable|string|max:255',
                'alamat_ktp' => 'nullable|string|max:255',
                'nik' => 'nullable|string|max:20',
                'no_handphone' => 'nullable|string|max:15',
            ]);

            // Update data di tabel users
            $user->update([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
            ]);

            // Update data di tabel anggota
            $anggota = $user->anggota;
            if ($anggota) {
                $anggota->update([
                    'tempat_lahir' => $validatedData['tempat_lahir'],
                    'tgl_lahir' => $validatedData['tanggal_lahir'],
                    'alamat_domisili' => $validatedData['alamat_domisili'],
                    'alamat_ktp' => $validatedData['alamat_ktp'],
                    'nik' => $validatedData['nik'],
                    'no_handphone' => $validatedData['no_handphone'],
                ]);
            }

            return response()->json(['message' => 'Profile updated successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update profile', 'error' => $e->getMessage()], 500);
        }
    }

    // Mengubah kata sandi
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found.',
                ], 404);
            }

            if (!Hash::check($request->input('current_password'), $user->password)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Kata sandi saat ini salah.',
                ], 403);
            }

            $user->update([
                'password' => Hash::make($request->input('new_password')),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Kata sandi berhasil diubah.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengubah kata sandi.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
