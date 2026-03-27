<?php

namespace lukawar\BankKit\PaymentGateway\DTOs;

use lukawar\BankKit\PaymentGateway\Enums\PaymentStatus;

class RefundResult
{
    public function __construct(
        /** Refund identifier in the gateway's system. */
        public readonly string $gatewayRefundId,
        public readonly PaymentStatus $status,
        /** Amount actually refunded in grosze. */
        public readonly int $amountInGrosze,
    ) {}
}
