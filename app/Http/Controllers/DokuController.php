<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DokuController extends Controller
{
    public function submitAndCreateVirtualAccount(Request $request)
    {
        // Step 1: Get Access Token
        $clientId = env('DOKU_CLIENT_ID'); // Client ID
        $privateKey = str_replace("\\n", "\n", env('DOKU_PRIVATE_KEY')); // Private Key

        if (empty($clientId) || empty($privateKey)) {
            Log::error('Client ID or Private Key is missing');
            return response()->json(['error' => 'Client ID or Private Key is missing']);
        }

        $timestamp = gmdate("Y-m-d\TH:i:s\Z");
        $stringToSign = $clientId . "|" . $timestamp;

        Log::info('Step 1 - String-to-Sign: ' . $stringToSign);

        $privateKeyResource = openssl_pkey_get_private($privateKey);
        if (!$privateKeyResource) {
            Log::error('Invalid Private Key');
            return response()->json(['error' => 'Invalid Private Key']);
        }

        openssl_sign($stringToSign, $signature, $privateKeyResource, OPENSSL_ALGO_SHA256);
        $xSignature = base64_encode($signature);

        Log::info('Step 1 - X-SIGNATURE: ' . $xSignature);

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

        Log::info('Step 1 - Token Response: ' . $response);

        if (curl_errno($ch)) {
            $errorMessage = curl_error($ch);
            curl_close($ch);
            Log::error('Step 1 - cURL Error: ' . $errorMessage);
            return response()->json(['error' => $errorMessage]);
        }

        curl_close($ch);

        $decodedResponse = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('Step 1 - Invalid JSON response from DOKU');
            return response()->json(['error' => 'Invalid JSON response from DOKU']);
        }

        if (!isset($decodedResponse['accessToken'])) {
            Log::error('Step 1 - Missing accessToken in response');
            return response()->json(['error' => 'Missing accessToken in response']);
        }

        $accessToken = $decodedResponse['accessToken'];

        // Step 2: Create Virtual Account

        $httpMethod = "POST";
        $partnerId = env('DOKU_PARTNER_ID'); // X-PARTNER-ID
        $channelId = 'H2H'; // Tetap sesuai dokumentasi
        $externalId = date('Ymd') . uniqid(); // Contoh: 202411260641b23

        // Generate timestamp with current local time
        $timestamp = now()->format('Y-m-d\TH:i:sP'); // Format ISO-8601

        Log::info('Timestamp in ZD Format: ' . $timestamp);

        $endpointUrl = "/virtual-accounts/bi-snap-va/v1.1/transfer-va/create-va";
        $fullUrl = 'https://api-sandbox.doku.com' . $endpointUrl;
                // Sandbox BIN (partnerServiceId) dengan padding spasi
        $partnerServiceId = str_pad("8492", 8, " ", STR_PAD_LEFT);
        $customerNo = "3"; // Customer No
        $virtualAccountNo = $partnerServiceId . $customerNo; // Kombinasi partnerServiceId + customerNo
        $trxId = uniqid(); // Nomor unik untuk transaksi
        $expiredDate = now()->addDays(1)->format('Y-m-d\TH:i:sP'); // Expired dalam 1 hari

        $body = [
            'partnerServiceId' => $partnerServiceId,
            'customerNo' => $customerNo,
            'virtualAccountNo' => $virtualAccountNo,
            "virtualAccountName" => "Jessica Tessalonika",
            "virtualAccountEmail" => "jessica@email.com",
            "virtualAccountPhone" => "6281828384700",
            'trxId' => $trxId,
            'virtualAccountTrxType' => 'C',
            "totalAmount" => [
                "value" => "100000.00",
                "currency" => "IDR"
            ],
            'expiredDate' => $expiredDate,
            'additionalInfo' => [
                'channel' => 'VIRTUAL_ACCOUNT_BNI'
            ]
        ];

        $requestBody = json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        // SHA256 Hash
        $hashedBody = hash('sha256', $requestBody);

        Log::info('Step 2 - Minified Body: ' . $requestBody);
        Log::info('Step 2 - Hashed Body: ' . $hashedBody);

        $stringToSign = $httpMethod . ":" . $endpointUrl . ":" . $accessToken . ":" . strtolower($hashedBody) . ":" . $timestamp;

        Log::info('Step 2 - String-to-Sign: ' . $stringToSign);

        $clientSecret = env('DOKU_SECRET_KEY');
        $signature = base64_encode(hash_hmac('sha512', $stringToSign, $clientSecret, true));

        Log::info('Step 2 - X-SIGNATURE: ' . $signature);

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

        Log::info('Step 2 - VA Response: ' . $response);

        if (curl_errno($ch)) {
            $errorMessage = curl_error($ch);
            curl_close($ch);
            return response()->json(['error' => $errorMessage]);
        }

        curl_close($ch);

        $decodedResponse = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('Step 2 - Invalid JSON response from DOKU');
            return response()->json(['error' => 'Invalid JSON response from DOKU']);
        }

        return response()->json($decodedResponse);
    }
}
