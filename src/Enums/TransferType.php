<?php

namespace lukawar\BankKit\Enums;

enum TransferType: string
{
    case Standard = '51';
    case SplitPayment = '53';
    case TaxOffice = '71';

    public function fileExtension(): string
    {
        return match ($this) {
            self::SplitPayment => 'pls',
            default => 'pli',
        };
    }
}
