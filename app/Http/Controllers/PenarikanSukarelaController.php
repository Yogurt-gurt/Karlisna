<?php

namespace App\Http\Controllers;

use App\Models\PenarikanSukarela;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PenarikanSukarelaController extends Controller
{
    // Fungsi untuk menghasilkan nomor penarikan unik
    private function generateNomorSimpanan()
    {
        // Format nomor penarikan: PS-YYYYMMDD-RANDOM
        $date = now()->format('Ymd');
        $random = strtoupper(substr(md5(uniqid(rand(), true)), 0, 5)); // 5 karakter unik
        return "PS-{$date}-{$random}";
    }

    public function sendOTP(Request $request)
    {
        // Pastikan user login
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'User not authenticated.'], 401);
        }

        // Validasi input metode pengiriman
        $request->validate([
            'method' => 'required|in:email,whatsapp,sms', // Menambahkan opsi SMS
        ]);

        // Pastikan data yang dibutuhkan tersedia
        if ($request->method === 'whatsapp' && empty($user->anggota->no_handphone)) {
            return response()->json(['message' => 'No Handphone is required for WhatsApp OTP.'], 400);
        }
        if ($request->method === 'email' && empty($user->email)) {
            return response()->json(['message' => 'Email is required for Email OTP.'], 400);
        }
        if ($request->method === 'sms' && empty($user->anggota->no_handphone)) {
            return response()->json(['message' => 'No Handphone is required for SMS OTP.'], 400);
        }

        // Generate OTP dan nomor penarikan
        $otpCode = random_int(100000, 999999);
        $otpExpiry = now()->addMinutes(5); // OTP valid selama 5 menit
        $noPenarikan = $this->generateNomorSimpanan();

        // Simpan OTP ke database
        $penarikan = PenarikanSukarela::create([
            'user_id' => $user->id,
            'no_penarikan' => $this->generateNomorSimpanan(),
            'bank' => $request->input('bank', 'Menunggu OTP'), // Isi default jika tidak diberikan
            'nominal' => 0, // Nilai default // Isi default jika tidak diberikan
            'otp_code' => $otpCode,
            'otp_expired_at' => $otpExpiry,
            'status_manager' => 'pending',
            'status_ketua' => 'pending',
        ]);


        // Kirim OTP sesuai metode yang dipilih
        if ($request->method === 'whatsapp') {
            $this->sendWhatsApp($user->anggota->no_handphone, $otpCode);
        } elseif ($request->method === 'email') {
            Mail::raw("Your OTP code is: $otpCode", function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('Your OTP Code');
            });
        } elseif ($request->method === 'sms') {
            $this->sendSMS($user->anggota->no_handphone, $otpCode);
        }

        return response()->json([
            'message' => 'OTP sent successfully via ' . $request->method . '.',
            'data' => $penarikan,
        ]);
    }

public function verifyOTP(Request $request)
{
    // Pastikan user login
    $user = Auth::user();

    if (!$user) {
        Log::error('User not authenticated.');
        return response()->json(['message' => 'User not authenticated.'], 401);
    }

    Log::info('User authenticated', ['user_id' => $user->id]);

    try {
        // Validasi input
        $request->validate([
            'otp_code' => 'required',
            'bank' => 'required|string',
            'nominal' => 'required|numeric',
        ]);

        Log::info('Validation passed', $request->all());

        // Cari data penarikan berdasarkan OTP dan user ID
        $penarikan = PenarikanSukarela::where('user_id', $user->id)
            ->where('otp_code', $request->otp_code)
            ->where('otp_expired_at', '>=', now())
            ->first();

        if (!$penarikan) {
            Log::error('Invalid or expired OTP', [
                'user_id' => $user->id,
                'otp_code' => $request->otp_code,
            ]);
            return response()->json(['message' => 'Invalid or expired OTP.'], 400);
        }

        Log::info('OTP verified', ['penarikan_id' => $penarikan->id]);

        // Perbarui data setelah validasi
        $penarikan->update([
            'bank' => $request->bank,
            'nominal' => $request->nominal,
        ]);

        Log::info('Penarikan updated', $penarikan->toArray());

        return response()->json([
            'message' => 'Data saved successfully after OTP verification.',
            'data' => $penarikan,
        ]);
    } catch (\Exception $e) {
        // Log error detail
        Log::error('Error in verifyOTP', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
        return response()->json(['message' => 'Internal server error.'], 500);
    }
}


    private function sendWhatsApp($phone, $otpCode)
    {
        if (!$phone) {
            return false; // Jika nomor handphone tidak tersedia
        }

        $apiUrl = 'https://api.whatsapp.com/send'; // Ganti dengan API WhatsApp yang sesuai
        $response = Http::post($apiUrl, [
            'phone' => $phone,
            'message' => "Your OTP code is: $otpCode",
        ]);

        return $response->successful();
    }

    private function sendSMS($phone, $otpCode)
    {
        if (!$phone) {
            return false; // Jika nomor handphone tidak tersedia
        }

        $apiUrl = 'https://api.smsprovider.com/send'; // Ganti dengan API SMS yang sesuai
        $response = Http::post($apiUrl, [
            'phone' => $phone,
            'message' => "Your OTP code is: $otpCode",
        ]);

        return $response->successful();
    }


    public function getContactInfo(Request $request)
    {
        $user = Auth::user(); // Ambil user yang login

        if (!$user) {
            return response()->json(['message' => 'User not authenticated.'], 401);
        }

        // Ambil data nomor HP dari tabel anggota
        $noHandphone = $user->anggota->no_handphone ?? null;

        Log::info('User Data:', [
            'email' => $user->email,
            'whatsapp' => $noHandphone,
            'sms' => $noHandphone,
        ]);

        return response()->json([
            'email' => $user->email ?? 'Email tidak ditemukan',
            'whatsapp' => $noHandphone ?? 'WhatsApp tidak ditemukan',
            'sms' => $noHandphone ?? 'SMS tidak ditemukan',
        ]);
    }
}
