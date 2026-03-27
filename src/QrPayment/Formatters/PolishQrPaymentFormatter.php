<?php

namespace lukawar\BankKit\QrPayment\Formatters;

use lukawar\BankKit\QrPayment\Contracts\QrPaymentFormatterInterface;
use lukawar\BankKit\QrPayment\DTOs\QrPaymentData;

/**
 * Builds QR payment content according to the Polish Bank Association (ZBP) standard.
 *
 * Format: |PL|{IBAN}|{AMOUNT_IN_GROSZE}|{RECIPIENT_NAME}|{TITLE}|||
 *
 * Fields:
 *   1. Service tag (empty — bank-specific identifier, optional)
 *   2. Country code: PL
 *   3. IBAN (without spaces)
 *   4. Amount in grosze (integer, no decimal point)
 *   5. Recipient name
 *   6. Payment title
 *   7–9. Reserved fields (empty)
 */
class PolishQrPaymentFormatter implements QrPaymentFormatterInterface
{
    public function format(QrPaymentData $data): string
    {
        return implode('|', [
            '',
            'PL',
            $data->iban,
            (string) $data->amountInGrosze,
            $data->recipientName,
            $data->title,
            '',
            '',
            '',
        ]);
    }
}
