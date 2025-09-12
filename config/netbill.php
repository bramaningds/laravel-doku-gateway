<?php

return [
    // get invoice detail
    'invoice_url' => env('NETBILL_INVOICE_URL', 'http://localhost:3000/payment/invoice'),

    // set invoice paid status
    'set_paid_url' => env('NETBILL_SET_PAID_URL', 'http://localhost:3000/payment/set-paid'),
];
