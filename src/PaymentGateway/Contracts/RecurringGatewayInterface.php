<?php

namespace lukawar\BankKit\PaymentGateway\Contracts;

use lukawar\BankKit\PaymentGateway\DTOs\PaymentRequest;
use lukawar\BankKit\PaymentGateway\DTOs\PaymentResponse;
use lukawar\BankKit\PaymentGateway\Exceptions\PaymentGatewayException;

/**
 * Implement this interface in addition to PaymentGatewayInterface
 * if the gateway supports recurring (subscription) payments.
 *
 * Flow:
 *   1. createPayment() — first payment, user authorizes recurring charges
 *   2. chargeRecurring() — subsequent charges using the stored token
 */
interface RecurringGatewayInterface
{
    /**
     * Charges a subsequent recurring payment using a previously stored token.
     * The token is typically obtained from the first authorized payment.
     *
     * @throws PaymentGatewayException
     */
    public function chargeRecurring(string $recurringToken, PaymentRequest $request): PaymentResponse;

    /**
     * Cancels a recurring token so no further charges can be made.
     *
     * @throws PaymentGatewayException
     */
    public function cancelRecurring(string $recurringToken): void;
}
