<?php

namespace lukawar\BankKit\DTOs;

use DateTimeInterface;
use lukawar\BankKit\Enums\TransferType;

class ZusTransfer extends AbstractBankTransfer
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
        private readonly string $contributionPeriod,
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
        return TransferType::Standard;
    }

    /**
     * ZUS title format: "SKLADKA ZA [PERIOD]"
     * The multiline formatter will pad remaining segments with empty values.
     */
    public function getTitle(): string
    {
        return 'SKLADKA ZA '.$this->contributionPeriod;
    }

    public function getContributionPeriod(): string
    {
        return $this->contributionPeriod;
    }
}
