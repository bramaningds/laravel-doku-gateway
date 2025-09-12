<?php

return [
    // client id
    'client_id' => env('DOKU_CLIENT_ID', 'BRN-0236-1753864893365'),

    // api key
    'secret_key' => env('DOKU_SECRET_KEY', 'SK-fyrjcuLG5ZGh8WqrV6AY'),

    // generate payment url
    'checkout_url' => env('DOKU_CHECKOUT_URL', 'https://api-sandbox.doku.com/checkout/v1/payment'),
];
