<?php

return [
    'mode' => env('NAGAD_MODE', 'sandbox'),
    'base_url' => env('NAGAD_BASE_URL', 'http://sandbox.mynagad.com:10080/remote-payment-gateway-1.0'),
    'merchant_id' => env('NAGAD_MERCHANT_ID'),
    'merchant_private_key' => env('NAGAD_MERCHANT_PRIVATE_KEY'),
    'pg_public_key' => env('NAGAD_PG_PUBLIC_KEY'),
    'callback_url' => env('NAGAD_CALLBACK_URL'),
];
