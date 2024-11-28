<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class VirtualAccountController extends Controller
{
    public function showRequestPage()
    {
        return view('request');
    }

    public function createVirtualAccount(Request $request)
    {
        $client = new Client();
        $httpMethod = "POST";

        // Full URL Sandbox
        $url = "https://api-sandbox.doku.com/virtual-accounts/bi-snap-va/v1.1/transfer-va/create-va";

        // Endpoint URL
        $endpointUrl = "/virtual-accounts/bi-snap-va/v1.1/transfer-va/create-va";

        // Sandbox Token dan Secret
        $accessToken = "eyJhbGciOiJSUzI1NiJ9.eyJleHAiOjE3MzI2OTU1MDMsImlzcyI6IkRPS1UiLCJjbGllbnRJZCI6IkJSTi0wMjAyLTE3Mjg1ODAzOTIxMTIifQ.YLwgamITZziCsZFx59JzCVYp6v-03jrWkHutpBi9Gf-_hzJRv28Pyhb8BU2yJXZr3DjhAVWD3P2Dm4nds_fP6hBMFdFy0yvUDWJihpdSnhYcV2bPoLwDMeJ0OZnQhjv7PXj_SlOxIlWasUQkRELbcKyc5Nqv8Qe5JfMSGxrwc396OLBtg4IcRApAbl4rrnv7f4kQxfhCkjUIjE7QNUQujjO1DQFv2VuHSEAAhG97G2yQokT1BweuDYY9FHLvHEVhB9AmNTKTSXo6D5xJM_Uvx_SC_Tg5BBrwy85Zd0kyROzaFxzrwJCve3U1dGwUAZ6Q32krsRTMbnWkvD6ZuWu8eQ";
        $clientSecret = "SK-RIvFqc5KUswVVfduSTJb";

        // Timestamp
        $timestamp = now()->format('Y-m-d\TH:i:sP'); // Format ISO-8601

        // Sandbox BIN (partnerServiceId) dengan padding spasi
        $partnerServiceId = str_pad("8492", 8, " ", STR_PAD_LEFT);

        // Generate X-EXTERNAL-ID (Format: YYYYMMDDXXXXX)
$externalId = date('Ymd') . uniqid(); // Contoh: 202411260641b23

        // Parameter lainnya
        $customerNo = "3"; // Customer No
        $virtualAccountNo = $partnerServiceId . $customerNo; // Kombinasi partnerServiceId + customerNo
        $trxId = uniqid(); // Nomor unik untuk transaksi
        $expiredDate = now()->addDays(1)->format('Y-m-d\TH:i:sP'); // Expired dalam 1 hari

        // Request Body
        $body = [
            'partnerServiceId' => $partnerServiceId,
            'customerNo' => $customerNo,
            'virtualAccountNo' => $virtualAccountNo,
            'virtualAccountName' => 'Customer Name',
            'trxId' => $trxId,
            'virtualAccountTrxType' => 'C',
            'totalAmount' => [
                'value' => '11500.00',
                'currency' => 'IDR'
            ],
            'expiredDate' => $expiredDate,
            'additionalInfo' => [
                'channel' => 'VIRTUAL_ACCOUNT_BNI'
            ]
        ];

        // Minify JSON body
        $requestBody = json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        // SHA256 Hash
        $hashedBody = hash('sha256', $requestBody);

        // stringToSign
        $stringToSign = $httpMethod . ":" . $endpointUrl . ":" . $accessToken . ":" . strtolower($hashedBody) . ":" . $timestamp;

        // Generate Signature
        $signature = base64_encode(hash_hmac('sha512', $stringToSign, $clientSecret, true));

        // Headers
        $headers = [
            'X-TIMESTAMP' => $timestamp,
            'X-SIGNATURE' => $signature,
            'X-PARTNER-ID' => 'BRN-0202-1728580392112',
            'X-EXTERNAL-ID' => $externalId,
            'CHANNEL-ID' => 'H2H',
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json'
        ];

        try {
            // Kirim Request
            $response = $client->post($url, [
                'headers' => $headers,
                'body' => $requestBody
            ]);

            // Decode Response
            $responseData = json_decode($response->getBody(), true);

            return response()->json([
                'success' => true,
                'data' => $responseData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
