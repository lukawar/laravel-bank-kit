<?php

namespace lukawar\BankKit\DTOs;

use DateTimeInterface;
use lukawar\BankKit\Contracts\BankTransferInterface;
use lukawar\BankKit\Enums\TransferType;

abstract class AbstractBankTransfer implements BankTransferInterface
{
    public function __construct(
        protected readonly string $senderAccount,
        protected readonly string $senderName,
        protected readonly string $senderAddress,
        protected readonly string $recipientAccount,
        protected readonly string $recipientName,
        protected readonly string $recipientAddress,
        protected readonly int $amountInGrosze,
        protected readonly DateTimeInterface $executionDate,
    ) {}

    abstract public function getTransferType(): TransferType;

    abstract public function getTitle(): string;

    public function getSenderAccount(): string
    {
        return $this->senderAccount;
    }

    public function getSenderName(): string
    {
        return $this->senderName;
    }

    public function getSenderAddress(): string
    {
        return $this->senderAddress;
    }

    public function getRecipientAccount(): string
    {
        return $this->recipientAccount;
    }

    public function getRecipientName(): string
    {
        return $this->recipientName;
    }

    public function getRecipientAddress(): string
    {
        return $this->recipientAddress;
    }

    public function getAmountInGrosze(): int
    {
        return $this->amountInGrosze;
    }

    public function getExecutionDate(): DateTimeInterface
    {
        return $this->executionDate;
    }
}
