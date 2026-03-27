<?php

namespace lukawar\BankKit\PaymentGateway\DTOs;

class RefundRequest
{
    public function __construct(
        /** Payment identifier in the gateway's system to refund. */
        public readonly string $gatewayPaymentId,
        /** Amount to refund in grosze. Null means full refund. */
        public readonly ?int $amountInGrosze = null,
        public readonly ?string $reason = null,
    ) {}
}
