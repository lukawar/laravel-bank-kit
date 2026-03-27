<?php

namespace lukawar\BankKit\PaymentGateway\Contracts;

use lukawar\BankKit\PaymentGateway\DTOs\RefundRequest;
use lukawar\BankKit\PaymentGateway\DTOs\RefundResult;
use lukawar\BankKit\PaymentGateway\Exceptions\PaymentGatewayException;
use lukawar\BankKit\PaymentGateway\Exceptions\PaymentNotFoundException;

/**
 * Implement this interface in addition to PaymentGatewayInterface
 * if the gateway supports payment refunds (full or partial).
 */
interface RefundableGatewayInterface
{
    /**
     * Issues a refund for an existing payment.
     * Pass null amountInGrosze in RefundRequest for a full refund.
     *
     * @throws PaymentNotFoundException
     * @throws PaymentGatewayException
     */
    public function refund(RefundRequest $request): RefundResult;
}
