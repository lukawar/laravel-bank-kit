<?php

namespace lukawar\BankKit\DTOs;

use DateTimeInterface;
use lukawar\BankKit\Enums\TransferType;

class StandardTransfer extends AbstractBankTransfer
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
        private readonly string $title,
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

    public function getTitle(): string
    {
        return $this->title;
    }
}
