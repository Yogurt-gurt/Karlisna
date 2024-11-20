<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function forgotpassword()
    {
        return view('auth.forgot_password', [
            'title' => 'Forgot Password',
        ]);
    }

    public function changePassword(Request $request)
    {
        $id = auth()->user()->id; // Ambil ID pengguna yang sedang login

        // Validasi input untuk kata sandi
        $this->validate($request, [
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        try {
            // Temukan pengguna berdasarkan ID
            $user = User::findOrFail($id);

            // Periksa apakah kata sandi lama cocok
            if (!Hash::check($request->input('current_password'), $user->password)) {
                return back()->withErrors(['current_password' => 'Kata sandi saat ini salah.']);
            }

            // Update kata sandi dengan kata sandi baru yang terenkripsi
            $user->update([
                'password' => Hash::make($request->input('new_password')),
            ]);
            $user->save();

            return redirect()->route('settingaccount')->with('success', 'Kata sandi berhasil diubah.');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Gagal mengubah kata sandi: ' . $e->getMessage()]);
        }
    }

    public function setting()
    {
        return view('layouts.partials.profil_setting', [
            'title' => 'Setting',
        ]);
    }
}
