<?php

namespace App\Http\Controllers;

use App\Models\RekeningSimpananSukarela;
use App\Models\SimpananSukarela;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SimpananSukarelaController extends Controller
{
    // Fungsi untuk membuat simpanan sukarela baru
public function store(Request $request)
{
    // Log request data
    Log::info('Memulai proses pembuatan simpanan sukarela', ['request' => $request->all()]);

    // Validasi input
    $request->validate([
        'bank' => 'required|string',
        'nominal' => 'required|integer|min:1',
    ]);

    try {
        // Ambil data rekening pengguna
        $rekening = RekeningSimpananSukarela::where('user_id', auth()->id())->first();

        if (!$rekening) {
            // Jika belum ada rekening, buat rekening baru
            $rekening = RekeningSimpananSukarela::create([
                'user_id' => auth()->id(),
                'status' => 'pending',
                'approval_manager' => 'pending',
                'approval_ketua' => 'pending',
            ]);

            Log::info('Rekening simpanan sukarela berhasil dibuat', ['rekening' => $rekening]);
        }

        // Buat data simpanan sukarela baru
        $simpanan = SimpananSukarela::create([
            'no_simpanan' => $this->generateNomorSimpanan(),
            'user_id' => auth()->id(),
            'rekening_simpanan_sukarela_id' => $rekening->id,
            'bank' => $request->bank,
            'nominal' => $request->nominal,
            'status_payment' => 'pending',
        ]);

        Log::info('Simpanan sukarela berhasil dibuat', ['simpanan' => $simpanan]);

        // Cek status approval_ketua
        if ($rekening->approval_ketua === 'approved') {
            // Ajukan virtual account jika sudah disetujui
            $virtualAccountResponse = $this->requestVirtualAccountToDoku($simpanan);

        } else {
            // Log jika virtual account belum diajukan
            Log::info('Rekening belum disetujui oleh ketua, virtual account belum diajukan', ['rekening' => $rekening]);
        }

        // Berikan respons JSON ke Flutter
        return response()->json([
            'message' => $rekening->approval_ketua === 'approved'
                ? 'Simpanan sukarela berhasil dibuat dan virtual account diajukan.'
                : 'Simpanan sukarela berhasil dibuat. Harap menunggu persetujuan ketua untuk virtual account.',
            'data' => $simpanan,
        ], 201);
    } catch (\Exception $e) {
        Log::error('Gagal membuat simpanan sukarela', ['error' => $e->getMessage()]);

        return response()->json([
            'message' => 'Terjadi kesalahan saat membuat simpanan sukarela.',
            'error' => $e->getMessage(),
        ], 500);
    }
}




private function requestVirtualAccountToDoku($simpanan)
{
    try {
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

        $bank = strtoupper(trim($simpanan->bank));
        $customerNumber = $this->getCustomerNumber($simpanan->bank);

        $channelBank = $this->mapBankToChannel($bank); // Map bank name to channel
        if (!$channelBank) {
            throw new \Exception('Invalid bank name for channel format');
        }

        if ($simpanan->nominal <= 0) {
            throw new \Exception('Invalid nominal amount. Must be greater than zero.');
        }

        $totalAmountValue = number_format((float)$simpanan->nominal, 2, '.', '');
        $partnerServiceId = str_pad($this->getBankCode($bank), 8, " ", STR_PAD_LEFT);
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
        Log::info("Request Body to DOKU: " . json_encode($body, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));        
        $hashedBody = hash('sha256', $requestBody);

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

        // Periksa apakah respons adalah JSON valid
        $decodedResponse = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error("Invalid JSON response from DOKU: " . $response);
            throw new \Exception("Invalid JSON response from DOKU");
        }

        // Log Create Virtual Account Response
        Log::info("Create Virtual Account Response from DOKU: ", $decodedResponse);

        // Validasi dan update data jika respons sukses
        if (isset($decodedResponse['virtualAccountData']['virtualAccountNo'], $decodedResponse['virtualAccountData']['expiredDate'])) {
            $virtualAccountData = $decodedResponse['virtualAccountData'];

            $virtualAccountNo = trim($virtualAccountData['virtualAccountNo']);
            $expiredDate = trim($virtualAccountData['expiredDate']);

            if (empty($virtualAccountNo) || empty($expiredDate)) {
                throw new \Exception('Invalid virtual account data after trim.');
            }

            // Update tabel simpanan_sukarela
            $simpanan->update([
                'virtual_account' => $virtualAccountNo,
                'expired_at' => $expiredDate,
                'status_payment' => 'Menunggu Pembayaran',
            ]);

            Log::info("Simpanan sukarela berhasil diperbarui dengan virtual account.", [
                'simpanan_id' => $simpanan->id,
                'virtual_account' => $virtualAccountNo,
                'expired_at' => $expiredDate,
            ]);

            // Hentikan eksekusi karena berhasil
            return $virtualAccountNo;
        }

        // Jika tidak ada data valid dalam respons, tangani sebagai error
        $errorMessage = $decodedResponse['responseMessage'] ?? 'Unknown error from DOKU';
        $errorCode = $decodedResponse['responseCode'] ?? 'No code provided';

        Log::error('Failed to receive valid virtual account data from DOKU', [
            'response_code' => $errorCode,
            'response_message' => $errorMessage,
        ]);

        throw new \Exception("DOKU Error: $errorMessage (Code: $errorCode)");
    } catch (\Exception $e) {
        Log::error("Failed to process virtual account: " . $e->getMessage(), [
            'response' => $decodedResponse ?? $response,
        ]);

        throw new \Exception("Failed to process virtual account: " . $e->getMessage());
    }
}

// Helper function to map bank to channel
private function mapBankToChannel($bank)
{
    $mapping = [
        'MANDIRI' => 'BANK_MANDIRI',
        'BRI' => 'BRI',
        'BNI' => 'BNI',
        'BCA' => 'BCA',
    ];

    return $mapping[$bank] ?? null;
}






public function checkRekening()
{
    try {
        Log::info("Memeriksa data rekening untuk user ID: " . auth()->id());

        // Cek apakah data rekening ada
        $hasRekening = RekeningSimpananSukarela::where('user_id', auth()->id())->exists();

        Log::info("Hasil pengecekan rekening", ['user_id' => auth()->id(), 'hasRekening' => $hasRekening]);

        // Berikan respons JSON ke Flutter
        return response()->json([
            'message' => $hasRekening ? 'Rekening ditemukan.' : 'Rekening tidak ditemukan.',
            'hasRekening' => $hasRekening,
        ], 200);
    } catch (\Exception $e) {
        Log::error("Gagal memeriksa data rekening untuk user ID: " . auth()->id(), ['error' => $e->getMessage()]);

        return response()->json([
            'message' => 'Terjadi kesalahan saat memeriksa rekening.',
            'error' => $e->getMessage(),
        ], 500);
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



    // Fungsi untuk melihat semua simpanan sukarela
    public function index()
    {
        Log::info('Menampilkan semua simpanan sukarela');
        $simpananSukarela = SimpananSukarela::with('user')->get();
        return response()->json($simpananSukarela);
    }

    // Fungsi untuk melihat detail simpanan sukarela berdasarkan ID
    public function show($id)
    {
        try {
            Log::info("Menampilkan detail simpanan sukarela dengan ID: $id");
            $simpanan = SimpananSukarela::with('user')->findOrFail($id);
            return response()->json($simpanan);
        } catch (\Exception $e) {
            Log::error("Simpanan sukarela dengan ID: $id tidak ditemukan", ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Simpanan sukarela tidak ditemukan'], 404);
        }
    }

    // Fungsi untuk memperbarui simpanan sukarela
    public function update(Request $request, $id)
    {
        Log::info("Memperbarui simpanan sukarela dengan ID: $id", ['request' => $request->all()]);

        $request->validate([
            'bank' => 'sometimes|string',
            'nominal' => 'sometimes|integer|min:1',
            'status_manager' => 'sometimes|in:pending,approved,rejected',
            'status_ketua' => 'sometimes|in:pending,approved,rejected',
        ]);

        try {
            $simpanan = SimpananSukarela::findOrFail($id);
            $simpanan->update($request->only([
                'bank',
                'nominal',
                'status_manager',
                'status_ketua'
            ]));

            Log::info("Simpanan sukarela dengan ID: $id berhasil diperbarui", ['simpanan' => $simpanan]);
            return response()->json($simpanan);
        } catch (\Exception $e) {
            Log::error("Gagal memperbarui simpanan sukarela dengan ID: $id", ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Gagal memperbarui simpanan sukarela'], 500);
        }
    }

    // Fungsi untuk menghapus simpanan sukarela
    public function destroy($id)
    {
        try {
            Log::info("Menghapus simpanan sukarela dengan ID: $id");
            $simpanan = SimpananSukarela::findOrFail($id);
            $simpanan->delete();
            Log::info("Simpanan sukarela dengan ID: $id berhasil dihapus");
            return response()->json(['message' => 'Simpanan sukarela berhasil dihapus']);
        } catch (\Exception $e) {
            Log::error("Gagal menghapus simpanan sukarela dengan ID: $id", ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Gagal menghapus simpanan sukarela'], 500);
        }
    }

    // Fungsi untuk menghasilkan nomor simpanan unik
    private function generateNomorSimpanan()
    {
        // Format nomor simpanan: SS-YYYYMMDD-RANDOM
        $date = now()->format('Ymd');
        $random = strtoupper(substr(md5(uniqid(rand(), true)), 0, 5));
        return "SS-{$date}-{$random}";
    }
}
