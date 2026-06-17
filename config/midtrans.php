<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Midtrans Payment Gateway Configuration (REQ-20)
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk integrasi Midtrans Snap API.
    | Sandbox: https://app.sandbox.midtrans.com
    | Production: https://app.midtrans.com
    |
    */

    'merchant_id'   => env('MIDTRANS_MERCHANT_ID', ''),
    'server_key'    => env('MIDTRANS_SERVER_KEY', ''),
    'client_key'    => env('MIDTRANS_CLIENT_KEY', ''),
    'is_production' => (bool) env('MIDTRANS_IS_PRODUCTION', false),
    'is_sanitized'  => true,
    'is_3ds'        => true, // 3D Secure untuk kartu kredit

    /*
    |--------------------------------------------------------------------------
    | Metode Pembayaran yang Diaktifkan
    |--------------------------------------------------------------------------
    |
    | Daftar metode pembayaran Midtrans yang diizinkan.
    | Referensi: https://docs.midtrans.com/reference/enabled-payments
    |
    */
    'enabled_payments' => [
        // Transfer Bank
        'bank_transfer',
        'bca_va',
        'bni_va',
        'bri_va',
        'permata_va',

        // E-Wallet
        'gopay',
        'shopeepay',

        // Kartu Kredit
        'credit_card',
    ],

    /*
    |--------------------------------------------------------------------------
    | Snap API URLs
    |--------------------------------------------------------------------------
    */
    'snap_url' => (bool) env('MIDTRANS_IS_PRODUCTION', false)
        ? 'https://app.midtrans.com/snap/snap.js'
        : 'https://app.sandbox.midtrans.com/snap/snap.js',

    'api_url' => (bool) env('MIDTRANS_IS_PRODUCTION', false)
        ? 'https://app.midtrans.com/snap/v1/transactions'
        : 'https://app.sandbox.midtrans.com/snap/v1/transactions',
];
