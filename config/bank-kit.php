<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default sender account
    |--------------------------------------------------------------------------
    | NRB account number of the company (sender) used as default when generating
    | ELIXIR transfer files. Can be overridden per transfer in BankTransferData.
    */
    'sender' => [
        'account' => env('BANK_KIT_SENDER_ACCOUNT', ''),
        'name' => env('BANK_KIT_SENDER_NAME', ''),
        'address' => env('BANK_KIT_SENDER_ADDRESS', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Gateways
    |--------------------------------------------------------------------------
    | List of gateway class identifiers to register automatically on boot.
    | Each entry should be a fully qualified class name implementing
    | PaymentGatewayInterface.
    */
    /*
    |--------------------------------------------------------------------------
    | QR Payment
    |--------------------------------------------------------------------------
    | Configuration for QR code payment generation.
    |
    | logo_path: Path to the logo image embedded in the center of the QR code.
    |   - null        → no logo
    |   - 'default'   → built-in black square (generated at runtime via GD)
    |   - '/path/...' → custom image file (PNG recommended)
    |
    | logo_width: Width in pixels to which the logo will be resized.
    */
    'qr' => [
        'logo_path' => env('BANK_KIT_QR_LOGO_PATH', null),
        'logo_width' => env('BANK_KIT_QR_LOGO_WIDTH', 60),
    ],

    'gateways' => [
        // \App\PaymentGateways\Przelewy24Gateway::class,
        // \App\PaymentGateways\PayUGateway::class,
        // \App\PaymentGateways\BlueMediaGateway::class,
    ],
];
