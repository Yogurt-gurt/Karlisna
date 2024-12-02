<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthControllerAPP;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json($request->user());
});

// Route untuk menampilkan formulir registrasi (respon JSON)
Route::get('/register-form', [AuthControllerAPP::class, 'showRegistrationForm']);

// Route untuk registrasi pengguna baru (respon JSON)
Route::post('/register', [AuthControllerAPP::class, 'register']);

// Route untuk menampilkan formulir login (respon JSON)
Route::get('/login-form', [AuthControllerAPP::class, 'index']);

Route::post('/login', function (Request $request) {
    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    $anggota = $user->anggota; // Ambil data anggota melalui relasi

    $token = $user->createToken('authToken')->plainTextToken;

    return response()->json([
        'token' => $token,
        'data' => [
            'id' => $user->id,
            'name' => $user->name,
            'roles' => $user->roles,
            'tempat_lahir' => $anggota->tempat_lahir ?? null,
            'tanggal_lahir' => $anggota->tgl_lahir ?? null,
            'alamat_domisili' => $anggota->alamat_domisili ?? null,
            'alamat_ktp' => $anggota->alamat_ktp ?? null,
            'nik' => $anggota->nik ?? null,
            'nomor_handphone' => $anggota->nomor_handphone ?? null,
        ]
    ], 200);
});

// Route untuk logout pengguna (respon JSON)
Route::post('/logout', [AuthControllerAPP::class, 'logout']);

// Rute untuk mendapatkan informasi verifikasi
Route::get('/informasi-verifikasi', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'Registrasi berhasil! Silakan periksa email Anda untuk informasi lebih lanjut.'
    ]);
})->name('informasi.verifikasi');


use App\Http\Controllers\SimpananBerjangkaController;

// Route untuk Simpanan Berjangka
Route::middleware('auth:sanctum')->group(function () {
Route::prefix('simpanan-berjangka')->group(function () {
    Route::post('/', [SimpananBerjangkaController::class, 'store']); // Membuat simpanan berjangka baru
    Route::put('/{id}', [SimpananBerjangkaController::class, 'update']); // Memperbarui simpanan berjangka
});
});

use App\Http\Controllers\API\NewsController;


Route::apiResource('news', NewsController::class);



use App\Http\Controllers\SimpananSukarelaController;

// Group route untuk Simpanan Sukarela
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/simpanan-sukarela', [SimpananSukarelaController::class, 'index']); // Lihat semua simpanan sukarela
    Route::get('/simpanan-sukarela/{id}', [SimpananSukarelaController::class, 'show']); // Detail simpanan sukarela
    Route::post('/simpanan-sukarela/create', [SimpananSukarelaController::class, 'store']); // Buat simpanan sukarela baru
    Route::put('/simpanan-sukarela/{id}', [SimpananSukarelaController::class, 'update']); // Perbarui simpanan sukarela
    Route::delete('/simpanan-sukarela/{id}', [SimpananSukarelaController::class, 'destroy']); // Hapus simpanan sukarela
    Route::get('rekening-sukarela/check', [SimpananSukarelaController::class, 'checkRekening']);

});

use App\Http\Controllers\PengajuanPinjamanController;



Route::get('/pengajuan_pinjaman', [PengajuanPinjamanController::class, 'index'])->middleware('auth:sanctum');
Route::post('/pengajuan_pinjaman', [PengajuanPinjamanController::class, 'store'])->middleware('auth:sanctum');
Route::get('/pengajuan_pinjaman/{id}', [PengajuanPinjamanController::class, 'show'])->middleware('auth:sanctum');
Route::put('/pengajuan_pinjaman/{id}', [PengajuanPinjamanController::class, 'update'])->middleware('auth:sanctum');
Route::delete('/pengajuan_pinjaman/{id}', [PengajuanPinjamanController::class, 'destroy'])->middleware('auth:sanctum');


use App\Http\Controllers\RekeningController;

// Route untuk menyimpan rekening baru
Route::post('/rekenings', [RekeningController::class, 'store'])->middleware('auth:sanctum');

// Route untuk menampilkan daftar rekening
Route::get('/rekening', [RekeningController::class, 'index'])->middleware('auth:sanctum');


    use App\Http\Controllers\PengajuanPinjamanAnggunanController;
    

Route::prefix('pengajuan-pinjaman-anggunan')->group(function () {
    // Route untuk membuat pengajuan pinjaman dengan anggunan baru
    Route::post('/create', [PengajuanPinjamanAnggunanController::class, 'store'])->middleware('auth:sanctum');

    // Route untuk melihat semua pengajuan pinjaman anggunan
    Route::get('/', [PengajuanPinjamanAnggunanController::class, 'index'])->middleware('auth:sanctum');

    // Route untuk melihat detail pengajuan pinjaman anggunan berdasarkan ID
    Route::get('/{id}', [PengajuanPinjamanAnggunanController::class, 'show'])->middleware('auth:sanctum');

    // Route untuk memperbarui pengajuan pinjaman anggunan
    Route::put('/{id}', [PengajuanPinjamanAnggunanController::class, 'update'])->middleware('auth:sanctum');

    // Route untuk menghapus pengajuan pinjaman anggunan
    Route::delete('/{id}', [PengajuanPinjamanAnggunanController::class, 'destroy'])->middleware('auth:sanctum');
});

use App\Http\Controllers\PinjamanEmergencyController;

Route::middleware('auth:sanctum')->group(function () {
    // Rute untuk pengajuan pinjaman emergency
    Route::get('/pinjaman-emergency', [PinjamanEmergencyController::class, 'index']); // Melihat semua pengajuan
    Route::post('/pinjaman-emergency/create', [PinjamanEmergencyController::class, 'store']); // Membuat pengajuan baru
    Route::get('/pinjaman-emergency/{id}', [PinjamanEmergencyController::class, 'show']); // Melihat detail pengajuan berdasarkan ID
    Route::put('/pinjaman-emergency/{id}', [PinjamanEmergencyController::class, 'update']); // Memperbarui pengajuan
    Route::delete('/pinjaman-emergency/{id}', [PinjamanEmergencyController::class, 'destroy']); // Menghapus pengajuan
});



use App\Http\Controllers\PenarikanSukarelaController;

// Grup rute dengan middleware autentikasi
Route::middleware('auth:sanctum')->group(function () {
    // Rute untuk mengirim OTP
    Route::post('/send-otp', [PenarikanSukarelaController::class, 'sendOTP']);

    // Rute untuk memverifikasi OTP
    Route::post('/verify-otp', [PenarikanSukarelaController::class, 'verifyOTP']);

    // Rute untuk mendapatkan informasi kontak user
    Route::get('/user/contact-info', [PenarikanSukarelaController::class, 'getContactInfo']);
});


use App\Http\Controllers\PenarikanBerjangkaController;

// Grup rute dengan middleware autentikasi
Route::middleware('auth:sanctum')->group(function () {
    // Rute untuk mengirim OTP
    Route::post('/send-otp-berjangka', [PenarikanBerjangkaController::class, 'sendOTP']);

    // Rute untuk memverifikasi OTP
    Route::post('/verify-otp-berjangka', [PenarikanBerjangkaController::class, 'verifyOTP']);

    // Rute untuk mendapatkan informasi kontak user
    Route::get('/user/contact-info-berjangka', [PenarikanBerjangkaController::class, 'getContactInfo']);
    Route::put('/rekenings/{id}/set-main', [RekeningController::class, 'setRekeningUtama']);
    
});

use App\Http\Controllers\ProfileAPPController;

Route::middleware('auth:sanctum')->get('/profile', [ProfileAPPController::class, 'getProfile']);
Route::middleware('auth:sanctum')->put('/update-profile', [ProfileAPPController::class, 'updateProfile']);
// Route untuk mengganti password
Route::middleware('auth:sanctum')->post('/change-password', [ProfileAPPController::class, 'changePassword']);

use App\Http\Controllers\DokuController;

Route::post('/get-token', [DokuController::class, 'getToken']);
// Route untuk melihat respons token (untuk debugging atau pengujian)
Route::get('/view-token-response', [DokuController::class, 'getToken']);



use App\Http\Controllers\KetuaController;

Route::post('/notifications', [KetuaController::class, 'handleNotification']);

