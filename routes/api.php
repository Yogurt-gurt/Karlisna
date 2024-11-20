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

    $token = $user->createToken('authToken')->plainTextToken;

    // Tambahkan informasi pengguna dalam respons
    return response()->json([
        'token' => $token,
        'data' => [
            'id' => $user->id,
            'name' => $user->name,
            'role' => $user->role,  // Pastikan kolom ini ada di tabel pengguna
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





use App\Http\Controllers\API\NewsController;


Route::apiResource('news', NewsController::class);



use App\Http\Controllers\PaymentController;

Route::any('/handle-payment-status', [PaymentController::class, 'handlePaymentStatus']);
Route::any('/process-payment', [PaymentController::class, 'processPayment']);
Route::get('/payment-result/{invoice}', [PaymentController::class, 'showPaymentResult']);
// Route untuk membuat simpanan berjangka
Route::post('/simpanan-berjangka', [PaymentController::class, 'createSimpananBerjangka']);

// Route untuk mendapatkan simpanan berjangka berdasarkan nomor invoice
Route::get('/simpanan-berjangka/{invoice}', [PaymentController::class, 'getSimpananBerjangka'])
;

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





