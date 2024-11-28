<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Mail\Mailkonfir;
use App\Models\Pinjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Mail\information_registrasi;
use Illuminate\Support\Facades\Mail;

class ManagerController extends Controller
{
    public function indexpinjaman()
    {
    return view('pages.manager.pinjaman.index', [
        'title' => 'Data Pengajuan Pinjaman',
        'pinjamans' => Pinjaman::with('rekening')->get()
    ]);
    }

    public function index()
    {
        // Mengambil semua data anggota dari database


        // Mengirimkan data anggota ke view
        return view('pages.manager.approve_regis_manager', [
            'title' => 'Data Aprove Registrasi',
            'anggota' => Anggota::all()

        ]);
    }

    public function updateStatus($id, $status)
    {
        try {
            // Temukan anggota berdasarkan ID
            $anggota = Anggota::findOrFail($id);

            // Update status anggota berdasarkan parameter $status
            $anggota->status_manager = $status;
            $anggota->save();

            return response()->json(['message' => 'Status updated successfully!'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update status!', 'error' => $e->getMessage()], 500);
        }
    }


    public function updateStatusPinjaman($id, $status)
    {
        try {
            // Temukan anggota berdasarkan ID
            $anggota = Pinjaman::findOrFail($id);

            // Update status anggota berdasarkan parameter $status
            $anggota->status_manager = $status;
            $anggota->save();

            return response()->json(['message' => 'Status updated successfully!'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update status!', 'error' => $e->getMessage()], 500);
        }
    }

    // Fungsi lainnya tetap sama
    public function email($id)
    {
        $email = Anggota::where('id', $id)->first()->email_kantor;
        Mail::to($email)->send(new Mailkonfir($email));

        // dd($email);
        // Mail::to($email)->send(new information_($email));
    }

    public function homemanager()
    {
        return view('pages.manager.home_manager',[
            'title' => 'Dashboard Manager',
        ]);
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
                        ->orWhere('status_manager', '!=', 'Diterima')
                        ->orWhere('status_manager', '!=', 'Ditolak');
                })->count();
        } elseif ($status == 'ditolak') {
            // Data yang ditolak oleh ketua atau manager
            $count = Anggota::where(function ($query) {
                $query->where('status_ketua', 'Ditolak')
                    ->orWhere('status_manager', 'Ditolak');
            })->count();
        }

        // Kembalikan hasil dalam format JSON
        return response()->json(['count' => $count]);
    }
};
