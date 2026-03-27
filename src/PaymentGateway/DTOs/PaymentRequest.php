<?php

namespace lukawar\BankKit\PaymentGateway\DTOs;

use lukawar\BankKit\PaymentGateway\Enums\Currency;

class PaymentRequest
{
    public function __construct(
        /** Your internal order identifier. */
        public readonly string $orderId,
        /** Amount in grosze (1 PLN = 100 grosze). */
        public readonly int $amountInGrosze,
        public readonly Currency $currency,
        public readonly string $description,
        public readonly CustomerData $customer,
        /** URL the user is redirected to after a successful payment. */
        public readonly string $returnUrl,
        /** URL the user is redirected to when they cancel the payment. */
        public readonly string $cancelUrl,
        /** Webhook URL the gateway calls to notify about payment status changes. */
        public readonly string $notificationUrl,
    ) {}
}
