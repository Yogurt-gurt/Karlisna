<?php

namespace App\Http\Controllers;

use App\Models\Rekening;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RekeningController extends Controller
{
    public function store(Request $request)
{
    // Mendapatkan user yang sedang login
    $user = auth()->user();
    
    if (!$user) {
        Log::error('User not authenticated.');
        return response()->json([
            'status' => 'error',
            'pesan' => 'User not authenticated.'
        ], 401);
    }

    Log::info('User authenticated', ['user_id' => $user->id]);

    // Validasi jumlah rekening untuk setiap pengguna (maksimal 2)
    $existingRekenings = Rekening::where('user_id', $user->id)->get();
    Log::info('Existing rekening count', ['count' => $existingRekenings->count()]);

    if ($existingRekenings->count() >= 2) {
        Log::error('User exceeded maximum rekening limit.', ['user_id' => $user->id]);
        return response()->json([
            'status' => 'error',
            'pesan' => 'Pengguna hanya boleh memiliki maksimal dua rekening'
        ], 400);
    }

    // Validasi rekening utama (hanya satu rekening utama)
    if ($request->is_rekening_utama) {
        $mainRekeningExists = $existingRekenings->where('is_rekening_utama', true)->isNotEmpty();
        Log::info('Main rekening check', ['mainRekeningExists' => $mainRekeningExists]);

        if ($mainRekeningExists) {
            Log::error('User already has a main rekening.', ['user_id' => $user->id]);
            return response()->json([
                'status' => 'error',
                'pesan' => 'Pengguna hanya boleh memiliki satu rekening utama'
            ], 400);
        }
    }

    // Validasi input
    try {
        $request->validate([
            'nomor_rekening' => 'required|string|max:255|unique:rekenings,nomor_rekening',
            'jenis_bank' => 'required|string|max:255',
            'is_rekening_utama' => 'required|boolean',
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::error('Validation failed', ['errors' => $e->errors()]);
        return response()->json([
            'status' => 'error',
            'pesan' => 'Data tidak valid',
            'errors' => $e->errors()
        ], 422);
    }

    // Membuat rekening baru dengan user_id dan nama pengguna yang login
    try {
        $rekening = Rekening::create([
            'nomor_rekening' => $request->nomor_rekening,
            'jenis_bank' => $request->jenis_bank,
            'is_rekening_utama' => $request->is_rekening_utama,
            'user_id' => $user->id,
            'nama' => $user->name,
        ]);

        Log::info('Rekening created successfully', ['rekening_id' => $rekening->id]);

        return response()->json([
            'status' => 'berhasil',
            'pesan' => 'Rekening berhasil ditambahkan',
            'data' => $rekening
        ], 201);
    } catch (\Exception $e) {
        Log::error('Failed to create rekening', ['error' => $e->getMessage()]);
        return response()->json([
            'status' => 'error',
            'pesan' => 'Gagal menambahkan rekening'
        ], 500);
    }
}


    /**
     * Menampilkan daftar nomor rekening.
     */
    public function index(Request $request)
{
    Log::info('Rekening index request initiated');

    $user = auth()->user();

    if (!$user) {
        Log::error('User not authenticated for index.');
        return response()->json([
            'status' => 'error',
            'pesan' => 'User not authenticated.'
        ], 401);
    }

    try {
        // Mengambil data rekening lengkap berdasarkan user yang sedang login
        $rekenings = Rekening::where('user_id', $user->id)
    ->toBase()
    ->get(['nomor_rekening', 'jenis_bank', 'is_rekening_utama', 'nama']);

Log::info('Base Query Data', $rekenings->toArray());



        Log::info('Rekening fetched successfully', [
            'user_id' => $user->id,
            'rekenings_count' => $rekenings->count()
        ]);

       $rekenings = $rekenings->map(function ($rekening) {
    Log::info('Mapping Rekening Data', [
        'nomor_rekening' => $rekening->nomor_rekening,
        'jenis_bank' => $rekening->jenis_bank,
        'nama' => $rekening->nama,
        'is_rekening_utama' => $rekening->is_rekening_utama,
    ]);
    return [
        'nomor_rekening' => $rekening->nomor_rekening,
        'jenis_bank' => $rekening->jenis_bank,
        'nama' => $rekening->nama ?? 'Nama Tidak Tersedia', // Fallback jika null
        'is_rekening_utama' => (bool) $rekening->is_rekening_utama,
    ];
});



        return response()->json([
            'status' => 'berhasil',
            'pesan' => 'Data rekening berhasil dimuat',
            'data' => $rekenings
        ], 200);
    } catch (\Exception $e) {
        Log::error('Failed to fetch rekening data', ['error' => $e->getMessage()]);
        return response()->json([
            'status' => 'error',
            'pesan' => 'Gagal memuat data rekening'
        ], 500);
    }
}

public function updateRekening(Request $request, $id)
{
    $user = auth()->user();

    if (!$user) {
        Log::error('User not authenticated for updating rekening.', [
            'request_data' => $request->all(),
        ]);
        return response()->json([
            'status' => 'error',
            'pesan' => 'User not authenticated.',
        ], 401);
    }

    try {
        // Validasi input
        $validatedData = $request->validate([
            'nomor_rekening' => "required|string|max:255|unique:rekenings,nomor_rekening,{$id}",
            'jenis_bank' => 'required|string|max:255',
            'is_rekening_utama' => 'required|boolean',
        ]);

        // Cari rekening berdasarkan id dan user_id
        $rekening = Rekening::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$rekening) {
            Log::error('Rekening not found or does not belong to the user.', [
                'user_id' => $user->id,
                'rekening_id' => $id,
            ]);
            return response()->json([
                'status' => 'error',
                'pesan' => 'Rekening tidak ditemukan atau bukan milik pengguna.',
            ], 404);
        }

        // Jika rekening akan dijadikan rekening utama
        if ($validatedData['is_rekening_utama']) {
            Log::info('Updating main rekening for user.', [
                'user_id' => $user->id,
                'rekening_id' => $id,
            ]);

            // Reset semua rekening pengguna menjadi bukan rekening utama
            Rekening::where('user_id', $user->id)
                ->update(['is_rekening_utama' => false]);
        }

        // Update data rekening
        $rekening->update([
            'nomor_rekening' => $validatedData['nomor_rekening'],
            'jenis_bank' => $validatedData['jenis_bank'],
            'is_rekening_utama' => $validatedData['is_rekening_utama'],
        ]);

        Log::info('Rekening updated successfully.', [
            'user_id' => $user->id,
            'rekening_id' => $rekening->id,
            'updated_data' => $rekening,
        ]);

        return response()->json([
            'status' => 'berhasil',
            'pesan' => 'Rekening berhasil diperbarui.',
            'data' => $rekening,
        ], 200);
    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::error('Validation failed for updating rekening.', [
            'errors' => $e->errors(),
            'user_id' => $user->id,
        ]);
        return response()->json([
            'status' => 'error',
            'pesan' => 'Data tidak valid.',
            'errors' => $e->errors(),
        ], 422);
    } catch (\Exception $e) {
        Log::error('Failed to update rekening.', [
            'error' => $e->getMessage(),
            'user_id' => $user->id,
        ]);
        return response()->json([
            'status' => 'error',
            'pesan' => 'Gagal memperbarui rekening.',
        ], 500);
    }
}



}
