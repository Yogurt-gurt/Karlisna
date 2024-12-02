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
use App\Models\RekeningSimpananSukarela;
use App\Models\SimpananSukarela;
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


    public function indexsimpanansukarela()
    {
        return view('pages.ketua.simpanan.index', [
            'title' => 'Data Pengajuan Simpanan Sukarela',
            'simpananSukarelas' => SimpananSukarela::with('rekeningSimpananSukarela', 'user')->get(),
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



public function updateApprovalKetuaSimpananSukarela($id, $status)
{
    try {
        // Validasi status
        if (!in_array($status, ['approved', 'rejected', 'pending'])) {
            return response()->json(['message' => 'Invalid status provided!'], 400);
        }

        // Temukan data berdasarkan ID
        $rekening = RekeningSimpananSukarela::findOrFail($id);

        // Update status approval ketua
        $rekening->approval_ketua = $status;
        $rekening->status = $status;
        $rekening->save();

        // Jika status disetujui oleh ketua
        if ($status === 'approved') {
            // Cek data Simpanan Sukarela terkait
            $simpanan = SimpananSukarela::where('rekening_simpanan_sukarela_id', $rekening->id)->first();

            

            if (!$simpanan) {
                return response()->json([
                    'message' => 'Simpanan Sukarela not found for this rekening!',
                ], 404);
            }

            // Step 1: Get Access Token
            $clientId = env('DOKU_CLIENT_ID');
            $privateKey = str_replace("\\n", "\n", env('DOKU_PRIVATE_KEY'));

            $timestamp = gmdate("Y-m-d\TH:i:s\Z");
            $stringToSign = $clientId . "|" . $timestamp;

            $privateKeyResource = openssl_pkey_get_private($privateKey);
            openssl_sign($stringToSign, $signature, $privateKeyResource, OPENSSL_ALGO_SHA256);
            $xSignature = base64_encode($signature);

            $headers = [
                'X-SIGNATURE: ' . $xSignature,
                'X-TIMESTAMP: ' . $timestamp,
                'X-CLIENT-KEY: ' . $clientId,
                'Content-Type: application/json',
            ];

            $body = [
                "grantType" => "client_credentials",
                "additionalInfo" => ""
            ];

            $url = "https://api-sandbox.doku.com/authorization/v1/access-token/b2b";

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));

            $response = curl_exec($ch);
            curl_close($ch);

            $decodedResponse = json_decode($response, true);

            // Log Access Token Response
            Log::info("Access Token Response from DOKU: ", $decodedResponse);

            if (!isset($decodedResponse['accessToken'])) {
                throw new \Exception('Failed to get access token from DOKU');
            }

            $accessToken = $decodedResponse['accessToken'];

            // Step 2: Create Virtual Account
            $httpMethod = "POST";
            $partnerId = env('DOKU_PARTNER_ID');
            $channelId = 'H2H';
            $externalId = uniqid();
            $timestamp = now()->format('Y-m-d\TH:i:sP');

            $endpointUrl = "/virtual-accounts/bi-snap-va/v1.1/transfer-va/create-va";
            $fullUrl = 'https://api-sandbox.doku.com' . $endpointUrl;

            $bankCode = $this->getBankCode($simpanan->bank);
            $customerNumber = $this->getCustomerNumber($simpanan->bank);

 
            // Proses nama bank
            Log::info("Original Bank Name from Database: " . $simpanan->bank);

            $bank = strtoupper(trim($simpanan->bank)); // Ubah semua huruf menjadi huruf besar dan hilangkan spasi

            Log::info("Trimmed and Uppercase Bank Name: " . $bank);

            // Periksa jika bank adalah Mandiri
            if ($bank === 'MANDIRI') {
                $channelBank = 'BANK_MANDIRI';
            } else {
                $channelBank = $bank; // Bank lain tetap sama
            }

            Log::info("Channel Bank: " . $channelBank);

            // Validasi jika bank tidak ditemukan
            if (empty($channelBank)) {
                throw new \Exception('Invalid bank name for channel format');
            }

                        // Validasi nominal
            if ($simpanan->nominal <= 0) {
                Log::error("Invalid nominal value: " . $simpanan->nominal);
                throw new \Exception('Invalid nominal amount. Must be greater than zero.');
            }

            // Format nominal menjadi desimal dengan dua digit
            $totalAmountValue = number_format((float) $simpanan->nominal, 2, '.', '');

            Log::info("Formatted totalAmount.value: " . $totalAmountValue);

            $partnerServiceId = str_pad($bankCode, 8, " ", STR_PAD_LEFT);
            $trxId = uniqid();
            $expiredDate = now()->addDays(1)->format('Y-m-d\TH:i:sP');

            $body = [
                'partnerServiceId' => $partnerServiceId,
                'customerNo' => $customerNumber,
                'virtualAccountNo' => $partnerServiceId . $customerNumber,
                "virtualAccountName" => $simpanan->user->name,
                "virtualAccountEmail" => $simpanan->user->email,
                "virtualAccountPhone" => $simpanan->user->phone,
                'trxId' => $trxId,
                'virtualAccountTrxType' => 'C',
                "totalAmount" => [
                    "value" => $totalAmountValue,
                    "currency" => "IDR"
                ],
                'expiredDate' => $expiredDate,
                'additionalInfo' => [
                    'channel' => "VIRTUAL_ACCOUNT_" . $channelBank,
                ]
            ];

            $requestBody = json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            $hashedBody = hash('sha256', $requestBody);
            // Log request body
            Log::info("Request Body to DOKU: " . json_encode($body, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            $stringToSign = $httpMethod . ":" . $endpointUrl . ":" . $accessToken . ":" . strtolower($hashedBody) . ":" . $timestamp;

            $clientSecret = env('DOKU_SECRET_KEY');
            $signature = base64_encode(hash_hmac('sha512', $stringToSign, $clientSecret, true));

            $headers = [
                'Authorization: Bearer ' . $accessToken,
                'X-TIMESTAMP: ' . $timestamp,
                'X-SIGNATURE: ' . $signature,
                'X-PARTNER-ID: ' . $partnerId,
                'X-EXTERNAL-ID: ' . $externalId,
                'CHANNEL-ID: ' . $channelId,
                'Content-Type: application/json',
            ];

            $ch = curl_init($fullUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);

            $response = curl_exec($ch);
            curl_close($ch);

            $decodedResponse = json_decode($response, true);

            // Log Create Virtual Account Response
            Log::info("Create Virtual Account Response from DOKU: ", $decodedResponse);

            if (!isset($decodedResponse['virtualAccountData']['virtualAccountNo'], $decodedResponse['virtualAccountData']['expiredDate'])) {
                Log::error("Invalid virtual account data: ", $decodedResponse);
                throw new \Exception('Failed to receive valid virtual account data from DOKU');
            }
                // Ambil data virtual account
            $virtualAccountData = $decodedResponse['virtualAccountData'];

            Log::info("Virtual Account Data: ", $virtualAccountData);

            // Simpan data Virtual Account jika respon valid
            $simpanan->virtual_account = $virtualAccountData['virtualAccountNo'];
            $simpanan->expired_at = $virtualAccountData['expiredDate'];
            $simpanan->status_payment = 'pending';
        }
                    $simpanan->save();
        return response()->json(['message' => 'Approval Ketua status updated and virtual account created successfully!'], 200);
    } catch (\Exception $e) {
        Log::error("Failed to process: " . $e->getMessage());
        return response()->json(['message' => 'Failed to update status or create virtual account!', 'error' => $e->getMessage()], 500);
    }
}




protected $bankCodes = [
    'BNI' => '8492',
    'BRI' => '13925',
    'BCA' => '19008',
    'MANDIRI' => '88899',
    // Tambahkan mapping bank lainnya di sini
];



protected $customerNumbers = [
    'BNI' => '3',
    'BRI' => '6',
    'BCA' => '0',
    'MANDIRI' => '4',
    // Tambahkan mapping bank lainnya di sini
];




protected function getCustomerNumber($bankName)
{
    $customerNumbers = $this->customerNumbers;

    // Ubah nama bank menjadi uppercase untuk konsistensi
    $bankName = strtoupper($bankName);

    // Kembalikan nomor customer atau default jika tidak ditemukan
    return $customerNumbers[$bankName] ?? null; // `null` jika bank tidak ditemukan
}




protected function getBankCode($bankName)
{
    $bankCodes = $this->bankCodes;

    // Ubah nama bank menjadi uppercase untuk konsistensi
    $bankName = strtoupper($bankName);

    // Kembalikan kode bank atau default jika bank tidak ditemukan
    return $bankCodes[$bankName] ?? null; // `null` jika bank tidak ditemukan
}


public function handlePaymentStatus(Request $request)
{
    // Log semua data yang diterima untuk debugging
    Log::info('DOKU Webhook received', ['data' => $request->all()]);

    try {
        // Ambil data yang relevan dari request
        $partnerServiceId = $request->input('partnerServiceId');
        $customerNo = $request->input('customerNo');
        $virtualAccountNo = $request->input('virtualAccountNo');
        $trxId = $request->input('trxId');
        $paymentStatus = $request->input('transaction.status'); // Status pembayaran dari DOKU
        $amountPaid = $request->input('paidAmount.value'); // Nominal pembayaran

        Log::info('Received payment details from DOKU', [
            'partnerServiceId' => $partnerServiceId,
            'customerNo' => $customerNo,
            'virtualAccountNo' => $virtualAccountNo,
            'trxId' => $trxId,
            'paymentStatus' => $paymentStatus,
            'amountPaid' => $amountPaid,
        ]);

        // Cari transaksi berdasarkan virtualAccountNo
        $transaction = SimpananSukarela::where('virtual_account', $virtualAccountNo)->first();

        if ($transaction) {
            Log::info('Transaction found in database', ['transaction' => $transaction]);

            // Perbarui status transaksi dan jumlah pembayaran berdasarkan data yang diterima
            $transaction->update([
                'status_payment' => $paymentStatus,
                'paid_amount' => $amountPaid,
            ]);

            Log::info('Transaction status updated successfully', [
                'transaction_id' => $transaction->id,
                'new_status' => $paymentStatus,
                'paid_amount' => $amountPaid,
            ]);
        } else {
            Log::error('Transaction not found for virtual_account', ['virtualAccountNo' => $virtualAccountNo]);

            return response()->json([
                'message' => 'Transaction not found',
                'virtualAccountNo' => $virtualAccountNo,
            ], 404);
        }

        return response()->json([
            'message' => 'Transaction status updated successfully',
            'status' => $paymentStatus,
        ], 200);
    } catch (\Exception $e) {
        Log::error('Error processing payment status', ['error' => $e->getMessage()]);

        return response()->json([
            'message' => 'Failed to process payment status',
            'error' => $e->getMessage(),
        ], 500);
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


    public function countDataRekeningSimpananSukarela($status)
{
    if ($status == 'all') {
        // Total semua data
        $count = RekeningSimpananSukarela::count();
    } elseif ($status == 'diterima') {
        // Data yang diterima oleh ketua atau manager
        $count = RekeningSimpananSukarela::where(function ($query) {
            $query->where('approval_ketua', 'approved')
                ->orWhere('approval_manager', 'approved');
        })->count();
    } elseif ($status == 'pengajuan') {
        // Data yang masih dalam proses (belum diterima/ditolak oleh ketua atau manager)
        $count = RekeningSimpananSukarela::where('approval_ketua', 'pending')
            ->orWhere('approval_manager', 'pending')
            ->count();
    } elseif ($status == 'ditolak') {
        // Data yang ditolak oleh ketua atau manager
        $count = RekeningSimpananSukarela::where(function ($query) {
            $query->where('approval_ketua', 'rejected')
                ->orWhere('approval_manager', 'rejected');
        })->count();
    }

    // Kembalikan hasil dalam format JSON
    return response()->json(['count' => $count]);
}


};
