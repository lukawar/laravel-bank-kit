<?php

namespace lukawar\BankKit\QrPayment\Contracts;

use lukawar\BankKit\QrPayment\DTOs\QrPaymentData;

interface QrPaymentFormatterInterface
{
    /**
     * Builds the text content to be encoded in the QR code.
     */
    public function format(QrPaymentData $data): string;
}
