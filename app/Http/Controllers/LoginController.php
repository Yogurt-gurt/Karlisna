<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
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
            if (Auth::user()->role == 'anggota') {
                return redirect()->route('home-anggota')->with('success', 'Berhasil masuk!');
            } elseif (Auth::user()->role == 'manager') {
                return redirect()->route('home-manager')->with('success', 'Berhasil masuk!'); //INI

            } elseif (Auth::user()->role == 'ketua') {
                return redirect()->route('home-ketua')->with('success', 'Berhasil masuk!'); //INI

            } elseif (Auth::user()->role == 'admin') {
                return redirect()->route('home-admin')->with('success', 'Berhasil masuk!'); //INI
            };
        } else {
            return redirect()->back()->withErrors(['username dan password yang dimasukan tidak sesuai'])->withInput();
        }
    }
}
