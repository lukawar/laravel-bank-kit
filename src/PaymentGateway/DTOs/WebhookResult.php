<?php

namespace lukawar\BankKit\PaymentGateway\DTOs;

use lukawar\BankKit\PaymentGateway\Enums\PaymentStatus;

class WebhookResult
{
    public function __construct(
        /** Your internal order identifier — matched from the webhook data. */
        public readonly string $orderId,
        /** Payment identifier in the gateway's system. */
        public readonly string $gatewayPaymentId,
        public readonly PaymentStatus $status,
        /** Amount confirmed by the gateway in grosze. */
        public readonly int $amountInGrosze,
    ) {}
}
