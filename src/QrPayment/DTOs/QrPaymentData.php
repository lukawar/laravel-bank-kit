<?php

namespace lukawar\BankKit\QrPayment\DTOs;

use lukawar\BankKit\QrPayment\Exceptions\InvalidIbanException;

class QrPaymentData
{
    public readonly string $iban;

    /**
     * @throws InvalidIbanException
     */
    public function __construct(
        string $iban,
        public readonly int $amountInGrosze,
        public readonly string $recipientName,
        public readonly string $title = '',
    ) {
        $this->iban = $this->normalizeIban($iban);
        $this->validateIban($this->iban);
    }

    private function normalizeIban(string $iban): string
    {
        return strtoupper(str_replace([' ', '-'], '', $iban));
    }

    /**
     * @throws InvalidIbanException
     */
    private function validateIban(string $iban): void
    {
        if (! preg_match('/^[A-Z]{2}[0-9]{2}[A-Z0-9]{1,30}$/', $iban)) {
            throw new InvalidIbanException("Invalid IBAN format: {$iban}");
        }
    }
}
