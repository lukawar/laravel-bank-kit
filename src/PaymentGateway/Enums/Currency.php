<?php

namespace lukawar\BankKit\PaymentGateway\Enums;

enum Currency: string
{
    case PLN = 'PLN';
    case EUR = 'EUR';
    case USD = 'USD';
}
