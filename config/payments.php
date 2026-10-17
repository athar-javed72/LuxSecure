<?php

return [
    'bank_transfer' => [
        'instructions' => env('BANK_TRANSFER_INSTRUCTIONS', 'Transfer to LuxSecure PKR account and upload proof.'),
    ],

    'jazzcash' => [
        'sandbox' => (bool) env('JAZZCASH_SANDBOX', true),
        'merchant_id' => env('JAZZCASH_MERCHANT_ID'),
        'password' => env('JAZZCASH_PASSWORD'),
        'integrity_salt' => env('JAZZCASH_INTEGRITY_SALT'),
        'sandbox_url' => env('JAZZCASH_SANDBOX_URL', 'https://sandbox.jazzcash.com.pk/CustomerPortal/transactionmanagement/merchantform'),
        'return_url' => env('JAZZCASH_RETURN_URL', env('APP_URL').'/payments/jazzcash/return'),
    ],

    'easypaisa' => [
        'sandbox' => (bool) env('EASYPAISA_SANDBOX', true),
        'store_id' => env('EASYPAISA_STORE_ID'),
        'secret' => env('EASYPAISA_SECRET'),
        'sandbox_url' => env('EASYPAISA_SANDBOX_URL', 'https://easypay.easypaisa.com.pk/easypay/Index.jsf'),
    ],
];
