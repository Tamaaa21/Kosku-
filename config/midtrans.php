<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Midtrans Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration settings for Midtrans Payment Gateway.
    |
    */

    'server_key' => env('MIDTRANS_SERVER_KEY', 'SB-Mid-server-demo-key'),
    'client_key' => env('MIDTRANS_CLIENT_KEY', 'SB-Mid-client-demo-key'),
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
    'is_sanitized' => env('MIDTRANS_IS_SANITIZED', true),
    'is_3ds' => env('MIDTRANS_IS_3DS', true),

    'snap_url' => env('MIDTRANS_IS_PRODUCTION', false)
        ? 'https://app.midtrans.com/snap/v1/transactions'
        : 'https://app.sandbox.midtrans.com/snap/v1/transactions',

    'snap_js_url' => env('MIDTRANS_IS_PRODUCTION', false)
        ? 'https://app.midtrans.com/snap/snap.js'
        : 'https://app.sandbox.midtrans.com/snap/snap.js',

    'api_url' => env('MIDTRANS_IS_PRODUCTION', false)
        ? 'https://api.midtrans.com'
        : 'https://api.sandbox.midtrans.com',

    /*
    |--------------------------------------------------------------------------
    | Kostara Business Parameters
    |--------------------------------------------------------------------------
    */
    'dp_percentage' => (int) env('KOSTARA_DP_PERCENTAGE', 20),
    'commission_percentage' => (int) env('KOSTARA_COMMISSION_PERCENTAGE', 5),
    'gateway_fee' => (int) env('KOSTARA_GATEWAY_FEE', 4000), // Midtrans admin fee passed to tenant
];
