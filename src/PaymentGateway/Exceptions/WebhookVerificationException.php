<?php

namespace lukawar\BankKit\PaymentGateway\Exceptions;

/**
 * Thrown when a webhook signature or authenticity check fails.
 * The incoming request should be rejected (HTTP 400/401).
 */
class WebhookVerificationException extends PaymentGatewayException {}
