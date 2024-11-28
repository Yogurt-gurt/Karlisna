<?php

namespace App\Http\Controllers;

use App\Models\Simpanan;
use Illuminate\Http\Request;

class SimpananController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function simpananwajib()
    {
        return view('pages.anggota.simpanan.simpanan_wajib', [
            'title' => 'Simpanan Wajib',
        ]);
    }

    public function simpananpokok()
    {
        return view('pages.anggota.simpanan.simpanan_pokok', [
            'title' => 'Simpanan Pokok',
        ]);
    }

    public function simpanansukarela()
    {
        return view('pages.anggota.simpanan.simpanan_sukarela', [
            'title' => 'Simpanan Sukarela',
        ]);
    }
    public function simpananberjangka()
    {
        return view('pages.anggota.simpanan.simpanan_berjangka', [
            'title' => 'Simpanan Berjangka',
        ]);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Simpanan $simpanan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Simpanan $simpanan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Simpanan $simpanan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Simpanan $simpanan)
    {
        //
    }
}
