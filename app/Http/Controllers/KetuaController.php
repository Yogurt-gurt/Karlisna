<?php

namespace App\Http\Controllers;

use App\Mail\information_registrasi;
use App\Mail\Mailkonfir;
use App\Models\Anggota;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\Pinjaman;
use Illuminate\Support\Facades\Log;

class KetuaController extends Controller
{
    public function pinjaman()
    {
        return view('pages.ketua.pinjaman.approve_pinjaman', [
            'title' => 'Data Approve Pinjaman',
            'pinjamans' => Pinjaman::all()
        ]);
    }
    public function diterima($id, $status)
    {
        try {
            // Temukan anggota berdasarkan ID
            $anggota = Anggota::findOrFail($id);

            // Update status anggota
            $anggota->status_ketua = $status;
            $anggota->save(); // Simpan pembaruan status terlebih dahulu

            // Jika status_ketua adalah 'Diterima', kirim email konfirmasi
            if ($anggota->status_ketua === 'Diterima') {
                $email = $anggota->email_kantor; // Ambil email dari objek anggota
                $user = User::where('email', $email)->first();

                $user = User::where('email', $email)->first();
                if (!$user) {
                    // 5. Buat password acak
                    $random_pass = rand(111111, 999999);

                    // 6. Simpan user baru ke tabel `users`
                    $user = User::create([
                        'name' => $anggota->nama,
                        'email' => $email,
                        'password' => Hash::make($random_pass),
                        'role' => 'anggota',// Set 'role' sesuai kebutuhan, atau default sebagai 'user'
                        'anggota_id' => $anggota->id, 
                    ]);

                    // 7. Kirim email konfirmasi kepada anggota yang berisi username dan password baru
                    Mail::to($email)->send(new Mailkonfir($email, $random_pass)); // Kirim email dengan username dan password baru

                    return response()->json(['message' => 'Status berhasil diperbarui, akun berhasil dibuat, dan email berhasil dikirim!'], 200);
                } else {
                    return response()->json(['message' => 'Akun sudah ada!'], 400);
                }
            } else {
                return response()->json(['message' => 'Status bukan Diterima!'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal memperbarui status!', 'error' => $e->getMessage()], 500);
        }
    }

    public function ditolak($id, $status)
    {
        try {
            // Temukan anggota berdasarkan ID
            $anggota = Anggota::findOrFail($id);

            // Update status anggota
            $anggota->status_ketua = $status;
            $anggota->save();

            return response()->json(['message' => 'Status updated successfully!'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update status!', 'error' => $e->getMessage()], 500);
        }
    }
    public function updateStatusKetua($id, $status)
    {
        try {
            // Temukan anggota berdasarkan ID
            $anggota = Anggota::findOrFail($id);

            // Update status dari ketua
            $anggota->status_ketua = $status;
            $anggota->save();

            // Simpan perubahan ke database
            $anggota->save();
            // Cek status keseluruhan
            $this->FinalStatus($anggota);

            return response()->json(['message' => 'Status ketua updated successfully!'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update status!', 'error' => $e->getMessage()], 500);
        }
    }

    public function FinalStatus($anggota)
    {

        $anggota->status_ketua = 'Diterima';
        // Logika untuk menentukan status final berdasarkan status manager dan ketua
        if ($anggota) {
            $email = $anggota->email_kantor; // Retrieve the email
            $nama = $anggota->nama;
            Mail::to($email)->send(new Mailkonfir($email)); // Assuming the email class is named Mailkonfir
        } else {
            // Handle the case where no email is found
            return back()->with('error', 'Anggota not found or does not have an email address.');
        }

        // Simpan perubahan ke database
        $anggota->save();
    }


    // Fungsi lainnya tetap sama

    public function email($id)
    {
        $email = Anggota::where('id', $id)->first()->email_kantor;

        // Mail::to($email)->send(new information_registrasi($email));
    }

    public function homeketua()
    {
        return view('pages.ketua.home_ketua', [
            'title' => 'Dashboard Ketua',
        ]);
    }

    // public function approveregisketua()
    // {
    //     return view('ketua.approve_regis_ketua', [
    //         'anggota' => Anggota::all()
    //     ]);
    // }

    public function detail_regis()
    {
        return view('pages.admin.detail_laporanregis', [
            'title' => 'Data Anggota Registrasi',
            'anggota' => Anggota::all()
        ]);
    }
    public function updateStatusPinjamanKetua($id, $status)
    {
        try {
            // Cari data pinjaman berdasarkan ID
            $pinjaman = Pinjaman::findOrFail($id);

            // Pastikan pinjaman sudah diterima oleh manager sebelum diproses
            if ($pinjaman->status_manager !== 'Diterima') {
                return response()->json(['message' => 'Pinjaman belum diterima oleh manager'], 400);
            }

            // Update status ketua
            $pinjaman->status_ketua = $status;
            $pinjaman->save();

            return response()->json(['message' => 'Status ketua updated successfully!'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update status!', 'error' => $e->getMessage()], 500);
        }
    }

    public function countData($status)
    {
        if ($status == 'all') {
            // Total semua data
            $count = Anggota::count();
        } elseif ($status == 'diterima') {
            // Data yang diterima oleh ketua atau manager
            $count = Anggota::where(function ($query) {
                $query->where('status_ketua', 'Diterima')
                    ->orWhere('status_manager', 'Diterima');
            })->count();
        } elseif ($status == 'pengajuan') {
            // Data yang masih dalam proses (belum diterima/ditolak oleh ketua atau manager)
            $count = Anggota::whereNull('status_ketua')
                ->orWhereNull('status_manager')
                ->where(function ($query) {
                    $query->where('status_ketua', '!=', 'Diterima')
                        ->where('status_ketua', '!=', 'Ditolak')
                        ->orWhere('status_manager', '!=', 'Diterima');
                })->count();
        } elseif ($status == 'ditolak') {
            // Data yang ditolak oleh ketua atau manager
            $count = Anggota::where(function ($query) {
                $query->where('status_ketua', 'Ditolak');
            })->count();
        }

        // Kembalikan hasil dalam format JSON
        return response()->json(['count' => $count]);
    }
};
