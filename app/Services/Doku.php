<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Doku
{
    public static function fetchDokuPaymentUrl($paymentId, $invoiceId, $amount, $customerId, $customerName, $customerPhone, $customerAddress)
    {
        $requestId = now()->timestamp;
        $requestTimestamp = explode('.', now()->toISOString())[0] . 'Z';
        $requestTarget = '/checkout/v1/payment';

        $body = [
            'order' => [
                'invoice_number' => "{$invoiceId}-{$requestId}",
                'amount' => $amount,
            ],
            'payment' => [
                'payment_due_date' => 60,
            ],
            'customer' => [
                'id' => $customerId,
                'name' => $customerName,
                'phone' => $customerPhone,
                'address' => $customerAddress,
            ],
            'additional_info' => [
                'override_notification_url' => config('app.url') . "/callback?id={$paymentId}",
            ],
        ];

        $headers = [
            'Client-Id' => config('doku.client_id'),
            'Request-Id' => $requestId,
            'Request-Timestamp' => $requestTimestamp,
            'Signature' => 'HMACSHA256=' . static::buildSignature($requestId, $requestTimestamp, $requestTarget, $body),
        ];

        $response = Http::withHeaders($headers)
            ->asJson()
            ->acceptJson()
            ->withBody(json_encode($body))
            ->post(config('doku.checkout_url'));

        if ($response->failed()) {
            Log::error($response->status() . ' ' . $response->body());
            throw new Exception('Cannot proceed the request');
        }

        return $response->json('response.payment.url');
    }

    private static function buildSignature($requestId, $requestTimestamp, $requestTarget, $body)
    {
        $digest = base64_encode(hash('sha256', json_encode($body), true));

        $signatureComponent = 'Client-Id:' . config('doku.client_id') . "\n" .
            "Request-Id:{$requestId}" . "\n" .
            "Request-Timestamp:{$requestTimestamp}" . "\n" .
            "Request-Target:{$requestTarget}" . "\n" .
            "Digest:{$digest}";

        return base64_encode(hash_hmac('sha256', $signatureComponent, config('doku.secret_key'), true));
    }
}
