<?php

namespace lukawar\BankKit\Contracts;

use Carbon\Carbon;
use lukawar\BankKit\Enums\TransferType;

interface BankTransferInterface
{
    public function getTransferType(): TransferType;

    public function getTitle(): string;

    public function getSenderAccount(): string;

    public function getSenderName(): string;

    public function getSenderAddress(): string;

    public function getRecipientAccount(): string;

    public function getRecipientName(): string;

    public function getRecipientAddress(): string;

    public function getAmountInGrosze(): int;

    public function getExecutionDate(): Carbon;
}
