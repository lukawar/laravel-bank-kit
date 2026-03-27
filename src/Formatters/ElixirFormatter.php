<?php

namespace lukawar\BankKit\Formatters;

class ElixirFormatter
{
    private const SEGMENT_LENGTH = 35;

    private const MAX_SEGMENTS = 4;

    /**
     * Extracts bank clearing number (digits 3–10) from a NRB account number.
     */
    public function extractBankCode(string $accountNumber): string
    {
        $digits = preg_replace('/[^0-9]/', '', $accountNumber);

        return substr($digits, 2, 8);
    }

    /**
     * Strips all non-digit characters from an account number.
     */
    public function normalizeAccount(string $accountNumber): string
    {
        $digits = preg_replace('/[^0-9PL\s]/', '', $accountNumber);

        return preg_replace('/[^0-9]/', '', $digits);
    }

    /**
     * Splits a string into up to 4 segments of 35 chars, joined by '|'.
     */
    public function formatMultiline(string $text): string
    {
        $text = substr($text, 0, self::SEGMENT_LENGTH * self::MAX_SEGMENTS);
        $segments = str_split($text, self::SEGMENT_LENGTH);

        while (count($segments) < self::MAX_SEGMENTS) {
            $segments[] = '';
        }

        return implode('|', array_slice($segments, 0, self::MAX_SEGMENTS));
    }

    /**
     * Formats amount: expects integer value in grosze (e.g. 531200 = 5312.00 PLN).
     */
    public function formatAmount(int $amountInGrosze): string
    {
        return (string) $amountInGrosze;
    }

    /**
     * Wraps value in double quotes.
     */
    public function quoted(string $value): string
    {
        return '"'.$value.'"';
    }

    /**
     * Encodes string content to CP852 using iconv (mbstring does not support CP852).
     */
    public function encodeToCP852(string $content): string
    {
        return iconv('UTF-8', 'CP852//TRANSLIT', $content);
    }
}
