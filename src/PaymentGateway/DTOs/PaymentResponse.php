<?php

namespace lukawar\BankKit\PaymentGateway\DTOs;

use lukawar\BankKit\PaymentGateway\Enums\PaymentStatus;

class PaymentResponse
{
    public function __construct(
        /** Payment identifier in the gateway's system. */
        public readonly string $gatewayPaymentId,
        /** URL to redirect the user to complete the payment. */
        public readonly string $redirectUrl,
        /** Initial status — typically Pending after creation. */
        public readonly PaymentStatus $status,
    ) {}
}
