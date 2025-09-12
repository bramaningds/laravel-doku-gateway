<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\Doku;
use App\Services\NetBill;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Log;
use Throwable;

class PaymentController extends Controller
{
    public function __invoke($invoiceId)
    {
        $hasPaid = Payment::query()
            ->where('invoice_number', $invoiceId)
            ->where('status', 'SUCCESS')
            ->exists();

        if ($hasPaid) {
            return response()->view('error', ['message' => 'Invoice has been paid'], 400);
        }

        try {
            $invoice = NetBill::fetchInvoice($invoiceId);

            $payment = Payment::create([
                'invoice_number' => $invoiceId,
            ]);

            $paymentUrl = Doku::fetchDokuPaymentUrl(
                paymentId: $payment->id,
                invoiceId: $invoiceId,
                amount: $invoice['product_price'] ?? null,
                customerId: $invoice['customer_id'] ?? null,
                customerName: $invoice['customer_name'] ?? null,
                customerPhone: $invoice['customer_phone'] ?? null,
                customerAddress: $invoice['service_address'] ?? null,
            );

            $payment->update([
                'payment_url' => $paymentUrl,
            ]);

            return redirect()->away($paymentUrl);
        } catch (ConnectionException $e) {
            Log::error($e);
            throw new Exception('Net Bill server error');
        } catch (Throwable $th) {
            Log::error($th);
            return response()->view('error', ['message' => $th->getMessage()], 500);
        }
    }
}
