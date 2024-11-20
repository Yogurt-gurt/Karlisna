<?php

use App\Models\Anggota;
use App\Models\News;
use App\Mail\Mailkonfir;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Database\Capsule\Manager;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\KetuaController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\SimpananSukarelaController;
use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PinjamanController;
use App\Http\Controllers\Pinjaman2Controller;
use App\Http\Controllers\RegistrasiController;
use App\Http\Controllers\VerifikasiController;
use App\Http\Controllers\LandingpageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SimpananController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/', [LandingpageController::class, 'page'])->name('landingpage');

Route::controller(AuthController::class)->group(function () {
    Route::get('/register', 'showRegistrationForm')->name('register');
    Route::post('/register', 'register')->name('register-verifikasi');
    Route::get('/informasi-verifikasi', [VerifikasiController::class, 'verifikasi'])->name('informasi-verifikasi');
    Route::get('/login', 'index')->name('login');
    Route::post('/login', 'validasilogin')->name('login-verifikasi');
    Route::post('/logout',  'logout')->name('logout');
    Route::get('/mutasi-simpanan', [AdminController::class, 'mutasisimpanan'])->name('mutasi-simpanan');
});

Route::prefix('profile')->middleware('auth')->group(function () {
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/forgot-password', 'forgotpassword')->name('forgot-password');
        Route::post('/forgot-password', 'changePassword')->name('change-password');
        Route::get('/setting-profile', 'setting')->name('setting-profile');
    });
});

//Route::post('/simpanan-sukarela', [SimpananSukarelaController::class, 'simpanansukarela'])->name('simpanan-sukarela');


//Anggota//
Route::prefix('anggota')->middleware('auth', 'role:anggota')->group(function () {
    Route::get('/home-anggota', [AnggotaController::class, 'homeanggota'])->name('home-anggota');
    Route::get('/tambah-simpanan', [AnggotaController::class, 'tambahsimpanan'])->name('anggota.tambah-simpanan');
    // Route::resource('pinjaman', PinjamanController::class);
    Route::controller(SimpananController::class)->group(function () {
        Route::get('/simpanan-wajib', 'simpananwajib')->name('simpanan-wajib');
        Route::get('/simpanan-pokok', 'simpananpokok')->name('simpanan-pokok');
        Route::get('/simpanan-sukarela', 'simpanansukarela')->name('simpanan-sukarela');
        Route::post('/payment-sukarela', [PaymentController::class, 'processPaymentSukarela'])->name('payment-sukarela');
        Route::get('/simpanan-berjangka', 'simpananberjangka')->name('simpanan-berjangka');
    });
    Route::controller(PinjamanController::class)->group(function () {
        Route::get('/data-pinjaman', 'index')->name('pinjaman');
        Route::get('/pinjaman-regular', 'regular')->name('pinjaman-regular');
        Route::get('/pinjaman-emergency', 'emergency')->name('pinjaman-emergency');
        Route::get('/pinjaman-angunan', 'angunan')->name('pinjaman-angunan');
        Route::get('/pinjaman-no-angunan', 'noAngunan')->name('pinjaman-noangunan');
        Route::post('pinjaman/store', 'store')->name('pinjaman.store');
    });
    Route::controller(Pinjaman2Controller::class)->group(function () {
        // Route::get('/data-pinjaman', 'index')->name('pinjaman');
        Route::get('/laporan-pinjaman', 'laporanpinjaman')->name('laporan-pinjaman');
        // Route::get('/pinjaman-regular', 'regular')->name('pinjaman-regular');
        // Route::get('/pinjaman-emergency', 'emergency')->name('pinjaman-emergency');
        // Route::get('/pinjaman-angunan', 'angunan')->name('pinjaman-angunan');
        Route::get('/pinjaman-non-angunan', 'nonangunan')->name('pinjaman-nonangunan');
        Route::post('pinjaman/store', 'store2')->name('pinjaman.storeNonAngunan');
    });
});

//Route::get('/laporan-pinjaman', [Pinjaman2Controller::class, 'laporanpinjaman'])->name('laporan-pinjaman');

//Route::prefix('payment')->middleware('auth')->group(function () {
// Route::controller(PaymentController::class)->group(function () {
//   Route::post('/create-payment', 'createPayment')->name('createPayment');
// Route::post('/cancel-callback', 'handlePaymentCancelCallback')->name('payment.cancel.callback');
//Route::post('/success-callback', 'handlePaymentSuccessCallback')->name('payment.success.callback');
//Route::post('/expired-callback', 'handlePaymentExpiredCallback')->name('payment.expired.callback');
//});
//});
Route::get('/pilih-pembayaran', [AnggotaController::class, 'pembayaran'])->name('pilih-pembayaran');

//Payment gateway
Route::prefix('payment')->middleware('auth')->group(function () {
    Route::controller(PaymentController::class)->group(function () {
        Route::get('/transfer', 'index')->name('transfer-form');
        Route::any('/create-payment', 'createPayment')->name('createPayment');
        // Route untuk callback pembatalan pembayaran
        Route::post('/cancel-callback', 'handlePaymentCancelCallback')->name('payment.cancel.callback');
        // Route untuk callback pembayaran sukses
        Route::post('/success-callback', 'handlePaymentSuccessCallback')->name('payment.success.callback');
        // Route untuk callback pembayaran yang kedaluwarsa
        Route::post('/expired-callback', 'handlePaymentExpiredCallback')->name('payment.expired.callback');
        Route::post('/process-payment', 'processPayment')->name('process-payment');
        Route::get('/payment-result',  'showPaymentResult')->name('payment.result');
    });
});

//admin
Route::prefix('admin')->middleware('auth', 'role:admin')->group(function () {
    Route::get('/home-admin', [AdminController::class, 'homeadmin'])->name('home-admin');
    Route::get('/data-anggota', [AdminController::class, 'dataanggota'])->name('data-anggota');
    Route::get('/kelola-news', [NewsController::class, 'news'])->name('kelola-news');
    Route::post('/store', [AnggotaController::class, 'store'])->name('anggota.store');
    Route::get('/{id}/edit', [AnggotaController::class, 'edit'])->name('anggota.edit');
    Route::put('/{id}/edit', [AnggotaController::class, 'update'])->name('anggota.update');
    // In routes/web.php
    Route::delete('/anggota/{id}/delete', [AnggotaController::class, 'delete'])->name('anggota.delete');

    Route::post('/create-news', [NewsController::class, 'create'])->name('create-news');
    Route::get('/news/{id}/edit', [NewsController::class, 'edit'])->name('news.edit');
    Route::put('/news/{id}', [NewsController::class, 'update'])->name('news.update');
    Route::delete('/{id}/delete', [NewsController::class, 'delete'])->name('news.delete');
    Route::controller(AnggotaController::class)->group(function () {
        Route::get('/laporan-regis', 'laporanregisadmin')->name('laporan-regis');
    });
});

//manager
Route::prefix('manager')->middleware('auth', 'role:manager')->group(function () {
    Route::get('/home-manager', [ManagerController::class, 'homemanager'])->name('home-manager');
    Route::get('/data-pinjaman', [ManagerController::class, 'indexpinjaman'])->name('data.pinjaman');
    Route::get('/approve-manager', [ManagerController::class, 'index'])->name('approve-manager');
    Route::post('/verif_berhasil', [KetuaController::class, 'FinalStatus'])->name('FinalStatus');
    Route::post('/pinjaman/{id}/{status}', [ManagerController::class, 'updateStatusPinjaman'])->name('pinjaman.status');
    Route::post('/approve/{id}/{status}', [ManagerController::class, 'updateStatus'])->name('approve.update-status-manager');
    Route::get('/count-data/{status}', [ManagerController::class, 'countData']);
    Route::get('/send-registration-email/{id}', [ManagerController::class, 'email'])->name('send.email');
});

//ketua
Route::prefix('ketua')->middleware('auth', 'role:ketua')->group(function () {
    Route::get('/home-ketua', [KetuaController::class, 'homeketua'])->name('home-ketua');
    Route::get('/pinjaman-ketua', [KetuaController::class, 'pinjaman'])->name('ketua.pinjaman');
    Route::get('/approve-ketua', [AnggotaController::class, 'index2'])->name('approve-ketua');
    Route::post('/ketua/diterima/{id}/{status}', [KetuaController::class, 'diterima'])->name('approve.diterima-ketua');
    Route::post('/ketua/ditolak/{id}/{status}', [KetuaController::class, 'ditolak'])->name('approve.ditolak-ketua');

    Route::post('/ketua/update-status-ketua/{id}', [KetuaController::class, 'updateStatusKetua']);
    Route::post('/manager/update-status-manager/{id}', [ManagerController::class, 'updateStatusManager']);
    Route::post('/ketua/final-status/{id}', [KetuaController::class, 'FinalStatus']);
    Route::get('/count-data/{status}', [KetuaController::class, 'countData']);
    // Route::post('/pinjaman/approve/{id}/{status}', [KetuaController::class, 'approvePinjaman'])->name('approve.pinjaman.diterima');
    // routes/web.php
    Route::post('update-status-pinjaman-ketua/{id}/{status}', [KetuaController::class, 'updateStatusPinjamanKetua'])->name('update.status.ketua');
});