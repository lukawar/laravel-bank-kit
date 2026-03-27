<?php

namespace lukawar\BankKit\PaymentGateway\DTOs;

class WebhookPayload
{
    public function __construct(
        /** Raw HTTP headers from the webhook request. */
        public readonly array $headers,
        /** Raw unparsed request body — used for signature verification. */
        public readonly string $rawBody,
        /** Parsed body (e.g. json_decode or parse_str result). */
        public readonly array $parsedBody,
    ) {}
}
