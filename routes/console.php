<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;

Artisan::command('doku', function () {
    $clientId = 'BRN-0236-1753864893365';
    $secretKey = 'SK-fyrjcuLG5ZGh8WqrV6AY';

    $requestId = now()->timestamp;
    $requestTimestamp = explode('.', now()->toISOString())[0].'Z';
    $requestTarget = '/checkout/v1/payment';

    $body = [
        'order' => [
            'amount' => 100000,
            'invoice_number' => 'INV-20250812',
        ],
        'payment' => [
            'payment_due_date' => 60,
        ],
        'additional_info' => [
            'override_notification_url' => 'https://ec9f0470c2a2.ngrok-free.app/callback',
        ],
    ];

    $digest = base64_encode(hash('sha256', json_encode($body), true));

    $signatureComponent = "Client-Id:{$clientId}"."\n".
        "Request-Id:{$requestId}"."\n".
        "Request-Timestamp:{$requestTimestamp}"."\n".
        "Request-Target:{$requestTarget}"."\n".
        "Digest:{$digest}";

    $this->info($signatureComponent);
    // return;
    $signature = base64_encode(hash_hmac('sha256', $signatureComponent, $secretKey, true));

    $response = Http::withHeaders([
        'accept' => 'application/json',
        'content-type' => 'application/json',
        'Client-Id' => $clientId,
        'Request-Id' => $requestId,
        'Request-Timestamp' => $requestTimestamp,
        'Signature' => 'HMACSHA256='.$signature,
    ])->post('https://api-sandbox.doku.com/checkout/v1/payment', $body);

    $this->info($response->body());
});
