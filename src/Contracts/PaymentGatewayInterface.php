<?php

namespace lukawar\BankKit\Contracts;

interface PaymentGatewayInterface
{
    /**
     * Returns unique gateway identifier, e.g. 'przelewy24', 'payu', 'bluemedia'.
     */
    public function getIdentifier(): string;

    /**
     * Returns gateway display name.
     */
    public function getName(): string;
}
