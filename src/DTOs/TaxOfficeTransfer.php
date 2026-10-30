<?php

namespace lukawar\BankKit\DTOs;

use DateTimeInterface;
use lukawar\BankKit\Enums\TaxPeriodType;
use lukawar\BankKit\Enums\TransferType;

class TaxOfficeTransfer extends AbstractBankTransfer
{
    public function __construct(
        string $senderAccount,
        string $senderName,
        string $senderAddress,
        string $recipientAccount,
        string $recipientName,
        string $recipientAddress,
        int $amountInGrosze,
        DateTimeInterface $executionDate,
        private readonly string $taxpayerNip,
        private readonly TaxPeriodType $periodType,
        private readonly string $periodNumber,
        private readonly string $formSymbol,
        private readonly string $additionalInfo = '',
    ) {
        parent::__construct(
            senderAccount: $senderAccount,
            senderName: $senderName,
            senderAddress: $senderAddress,
            recipientAccount: $recipientAccount,
            recipientName: $recipientName,
            recipientAddress: $recipientAddress,
            amountInGrosze: $amountInGrosze,
            executionDate: $executionDate,
        );
    }

    public function getTransferType(): TransferType
    {
        return TransferType::TaxOffice;
    }

    /**
     * Tax office title format: "N[NIP]|[PeriodType][PeriodNumber]|[FormSymbol]|[AdditionalInfo]"
     * Example: "N1234567890|M12|PIT37|DEKLARACJA"
     */
    public function getTitle(): string
    {
        $normalizedNip = preg_replace('/[^0-9]/', '', $this->taxpayerNip);

        return sprintf(
            'N%s|%s%s|%s|%s',
            $normalizedNip,
            $this->periodType->value,
            $this->periodNumber,
            $this->formSymbol,
            $this->additionalInfo,
        );
    }

    public function getTaxpayerNip(): string
    {
        return $this->taxpayerNip;
    }

    public function getPeriodType(): TaxPeriodType
    {
        return $this->periodType;
    }

    public function getPeriodNumber(): string
    {
        return $this->periodNumber;
    }

    public function getFormSymbol(): string
    {
        return $this->formSymbol;
    }

    public function getAdditionalInfo(): string
    {
        return $this->additionalInfo;
    }
}
