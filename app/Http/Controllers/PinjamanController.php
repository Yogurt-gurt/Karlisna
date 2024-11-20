<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use Nette\Utils\Random;
use App\Models\Pinjaman;
use Illuminate\Http\Request;
use App\Models\PinjamanCategory;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PinjamanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = [
            'title' => 'Data Pinjaman',
            'pinjamans' => auth()->user()->pinjaman
        ];
        return view('pages.anggota.pinjaman.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function angunan()
    {
        $users = Auth::user();
        $data = [
            'title' => 'Form Tambah Pinjaman Angunan',
            'tenor' => Pinjaman::STATUS_OPTIONS,
            'angunan' => Pinjaman::ANGUNAN_OPTIONS,
            'new_nmr' => $this->generateNomorPinjaman(),
            'noRekening' => $users->no_rekening
        ];
        return view('pages.anggota.pinjaman.add_angunan', $data);
    }
    public function emergency()
    {
        $users = Auth::user();
        $data = [
            'title' => 'Form Tambah Pinjaman Emergency',
            'tenor' => Pinjaman::STATUS_OPTIONS,
            'new_nmr' => $this->generateNomorPinjaman(),
            'noRekening' => $users->no_rekening

        ];
        return view('pages.anggota.pinjaman.add_emergency', $data);
    }
    public function noAngunan()
    {
        $users = Auth::user();
        $data = [
            'title' => 'Form Tambah Pinjaman No Angunan',
            'tenor' => Pinjaman::STATUS_OPTIONS,
            'new_nmr' => $this->generateNomorPinjaman(),
            'noRekening' => $users->no_rekening
        ];
        return view('pages.anggota.pinjaman.add_no_angunan', $data);
    }
    public function regular()
    {
        $users = Auth::user();
        $data = [
            'title' => 'Form Tambah Pinjaman Regular',
            'tenor' => Pinjaman::STATUS_OPTIONS,
            'new_nmr' => $this->generateNomorPinjaman(),
            'noRekening' => $users->no_rekening
        ];
        return view('pages.anggota.pinjaman.add_regular', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        // Validasi data
        $request->validate([
            'nomor_pinjaman' => 'required|string|unique:pinjamen',
            'nominal' => 'required|numeric',
            'nominal_angsuran' => 'required|numeric',
            // 'tujuan_pinjaman' => 'required|string',
            'jenis_pinjaman' => 'required|string',
            'jenis_angunan' => 'nullable|in:SERTIFIKAT TANAH,SERTIFIKAT RUMAH,BPKB KENDARAAN,SURAT BERHARGA LAINNYA',
            'tenor' => 'required|in:3 Bulan,6 Bulan,9 Bulan,12 Bulan,15 bulan,18 bulan,24 bulan,27 bulan',
            'image' => 'nullable|file|image:jpeg,png,jpg,gif|max:2048',
            'keterangan' => 'required|string',
        ]);

        // Menambahkan data user ke request
        $users = Auth::user();

        // Inisialisasi variabel $image
        $imagePath = null;

        // Periksa apakah ada gambar yang diunggah
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('bukti-upload');
        }

        // Menyimpan data ke database
        Pinjaman::create([
            'user_id' => $users->id,
            // 'no_rekening' => $users->no_rekening,
            'uuid' => uuid_create(),
            'nomor_pinjaman' => $request->nomor_pinjaman,
            'nominal' => $request->nominal,
            'tanggal_pinjaman' => now(),
            'nominal_angsuran' => $request->nominal_angsuran,
            // 'tujuan_pinjaman' => $request->tujuan_pinjaman,
            'jenis_pinjaman' => $request->jenis_pinjaman,
            'jenis_angungan' => $request->jenis_angungan,
            'tenor' => $request->tenor,
            'image' => $imagePath ? "storage/$imagePath" : null,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('pinjaman')->with('success', 'Data Pinjaman Berhasil Disimpan');
    }



    /**
     * Display the specified resource.
     */
    public function show(Pinjaman $pinjaman)
    {
        $data = [
            'title' => 'Detail Pinjaman',
            'pinjaman' => $pinjaman
        ];
        return view('pages.anggota.pinjaman.detail', $data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pinjaman $pinjaman)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pinjaman $pinjaman)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pinjaman $pinjaman)
    {
        //
    }

    public function generateNomorPinjaman()
    {
        // Ambil tanggal hari ini dengan format Ymd (contoh: 20231115)
        $tanggal = date('Ymd');

        // Ambil pinjaman terakhir berdasarkan nomor pinjaman
        $lastPinjaman = Pinjaman::where('nomor_pinjaman', 'like', '%' . $tanggal . '%')
            ->latest('nomor_pinjaman')->first();

        // Jika ada nomor pinjaman, ambil angkanya dan tambah 1, jika tidak mulai dari 1
        $lastNumber = $lastPinjaman ? (int) substr($lastPinjaman->nomor_pinjaman, 13) : 0;
        $newNumber = $lastNumber + 1;

        // Menghasilkan nomor pinjaman dengan format PNJ-20231115-00001, PNJ-20231115-00002, dst.
        return 'PNJ-' . $tanggal . '-' . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }
}
