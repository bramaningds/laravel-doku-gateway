<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\NetBill;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Log;
use Throwable;

class PaymentCallbackController extends Controller
{
    public function __invoke()
    {
        $payment = Payment::find(request()->query('id'));

        if (! $payment) {
            return response('Not found', 404);
        }

        try {
            $data = (object) request()->except('id');
            $status = request()->json('transaction.status');

            $payment->update([
                'payment_response' => $data,
                'status' => $status,
            ]);

            if ($status == 'SUCCESS') {
                NetBill::setPaid($payment->invoice_number);
            }

            return response()->noContent();
        } catch (ConnectionException $e) {
            Log::error($e);

            return response()->noContent();
        } catch (Throwable $th) {
            Log::error($th);

            return response()->noContent();
        }
    }
}
