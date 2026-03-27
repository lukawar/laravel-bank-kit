<?php

namespace lukawar\BankKit\PaymentGateway\Contracts;

use lukawar\BankKit\PaymentGateway\DTOs\PaymentRequest;
use lukawar\BankKit\PaymentGateway\DTOs\PaymentResponse;
use lukawar\BankKit\PaymentGateway\DTOs\WebhookPayload;
use lukawar\BankKit\PaymentGateway\DTOs\WebhookResult;
use lukawar\BankKit\PaymentGateway\Enums\PaymentStatus;
use lukawar\BankKit\PaymentGateway\Exceptions\PaymentGatewayException;
use lukawar\BankKit\PaymentGateway\Exceptions\PaymentNotFoundException;
use lukawar\BankKit\PaymentGateway\Exceptions\WebhookVerificationException;

interface PaymentGatewayInterface
{
    /**
     * Unique machine-readable identifier for this gateway (e.g. 'tpay', 'przelewy24').
     */
    public function getIdentifier(): string;

    /**
     * Human-readable gateway name (e.g. 'Tpay', 'Przelewy24').
     */
    public function getName(): string;

    /**
     * Initiates a new payment and returns the response with a redirect URL.
     *
     * @throws PaymentGatewayException
     */
    public function createPayment(PaymentRequest $request): PaymentResponse;

    /**
     * Fetches the current status of a payment from the gateway.
     *
     * @throws PaymentNotFoundException
     * @throws PaymentGatewayException
     */
    public function getPaymentStatus(string $gatewayPaymentId): PaymentStatus;

    /**
     * Parses and verifies an incoming webhook notification from the gateway.
     * Implementations must verify the request signature before returning a result.
     *
     * @throws WebhookVerificationException
     * @throws PaymentGatewayException
     */
    public function handleWebhook(WebhookPayload $payload): WebhookResult;
}
