<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function homeadmin()
    {
        return view('pages.admin.home_admin', [
            'title' => 'Dashboard Admin'
        ]);
    }

    public function dataanggota()
    {
        return view('pages.admin.data_anggota', [
            'title' => 'Data Anggota',
            'anggota' => Anggota::all()
        ]);
    }
    public function news()
    {
        return view('pages.admin.kelola_news', [
            'title' => 'Laporan News'
        ]);
    }
    public function mutasisimpanan()
    {
        return view('pages.admin.mutasi_simpanan', [
            'title' => 'Mutasi Simpanan'
        ]);
    }
};
