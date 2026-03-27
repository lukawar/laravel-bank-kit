<?php

namespace lukawar\BankKit\PaymentGateway\DTOs;

class CustomerData
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $phone = null,
    ) {}
}
