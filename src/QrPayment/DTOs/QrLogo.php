<?php

namespace lukawar\BankKit\QrPayment\DTOs;

class QrLogo
{
    public function __construct(
        public readonly string $imagePath,
        public readonly int $width = 60,
    ) {}
}
