<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;

class NetBill
{
    public static function fetchInvoice($id): mixed
    {
        $url = config('netbill.invoice_url') . '/' . $id;

        $response = Http::get($url);

        switch ($response->status()) {
            case 404:
                throw new Exception('Invoice not found');
            case 500:
                throw new Exception('Server error');
        }

        $invoice = $response->json();

        switch ($invoice['status']) {
            case 1:
                throw new Exception('Invoice has been paid');
            case 2:
                throw new Exception('Invoice has been refunded');
            case 3:
                throw new Exception('Invoice has been canceled');
        }

        return $invoice;
    }

    public static function setPaid($id): bool
    {
        $response = Http::post(config('netbill.set_paid_url') . '/' . $id);

        return $response->ok();
    }
}
