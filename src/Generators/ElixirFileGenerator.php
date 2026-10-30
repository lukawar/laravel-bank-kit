<?php

namespace lukawar\BankKit\Generators;

use Illuminate\Support\Collection;
use lukawar\BankKit\Contracts\BankTransferInterface;
use lukawar\BankKit\Enums\TransferType;
use lukawar\BankKit\Formatters\ElixirFormatter;

class ElixirFileGenerator
{
    private const ORDER_CODE = 110;

    private const TRANSFERS_PER_FILE = 50;

    public function __construct(
        private readonly ElixirFormatter $formatter,
    ) {}

    /**
     * Generates one or more ELIXIR file contents from a collection of transfers.
     * Returns an array of encoded file contents (each max 50 transfers / 10 KB).
     *
     * @param  Collection<int, BankTransferInterface>  $transfers
     * @return array<int, string>
     */
    public function generate(Collection $transfers): array
    {
        return $transfers
            ->chunk(self::TRANSFERS_PER_FILE)
            ->map(fn (Collection $chunk) => $this->buildFileContent($chunk))
            ->values()
            ->all();
    }

    /**
     * Returns file extension based on transfer types in the collection.
     * If any transfer is SplitPayment, returns 'pls', otherwise 'pli'.
     *
     * @param  Collection<int, BankTransferInterface>  $transfers
     */
    public function resolveExtension(Collection $transfers): string
    {
        $hasSplitPayment = $transfers->contains(
            fn (BankTransferInterface $transfer) => $transfer->getTransferType() === TransferType::SplitPayment
        );

        return ($hasSplitPayment ? TransferType::SplitPayment : TransferType::Standard)->fileExtension();
    }

    /**
     * @param  Collection<int, BankTransferInterface>  $chunk
     */
    private function buildFileContent(Collection $chunk): string
    {
        $lines = $chunk->map(fn (BankTransferInterface $transfer) => $this->buildLine($transfer));
        $content = $lines->implode("\r\n")."\r\n";

        return $this->formatter->encodeToCP852($content);
    }

    private function buildLine(BankTransferInterface $transfer): string
    {
        $senderAccount = $this->formatter->normalizeAccount($transfer->getSenderAccount());
        $recipientAccount = $this->formatter->normalizeAccount($transfer->getRecipientAccount());
        $senderBankCode = $this->formatter->extractBankCode($senderAccount);
        $recipientBankCode = $this->formatter->extractBankCode($recipientAccount);
        $senderNameAndAddress = $transfer->getSenderName().'|'.$transfer->getSenderAddress();
        $recipientNameAndAddress = $transfer->getRecipientName().'|'.$transfer->getRecipientAddress();

        $fields = [
            self::ORDER_CODE,
            $transfer->getExecutionDate()->format('Ymd'),
            $this->formatter->formatAmount($transfer->getAmountInGrosze()),
            $senderBankCode,
            0,
            $this->formatter->quoted($senderAccount),
            $this->formatter->quoted($recipientAccount),
            $this->formatter->quoted($this->formatter->formatMultiline($senderNameAndAddress)),
            $this->formatter->quoted($this->formatter->formatMultiline($recipientNameAndAddress)),
            0,
            $recipientBankCode,
            $this->formatter->quoted($this->formatter->formatMultiline($transfer->getTitle())),
            $this->formatter->quoted(''),
            $this->formatter->quoted(''),
            $this->formatter->quoted($transfer->getTransferType()->value),
        ];

        return implode(',', $fields);
    }
}
