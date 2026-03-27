<?php

namespace lukawar\BankKit\Enums;

enum TaxPeriodType: string
{
    case Year = 'R';
    case Quarter = 'K';
    case Month = 'M';
    case Decade = 'D';
    case Day = 'J';
    case None = '0';
}
