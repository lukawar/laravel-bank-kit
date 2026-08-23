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
     *
     * Counted in characters, not bytes. A Polish letter takes two bytes in
     * UTF-8, so a byte-based cut lands inside the character whenever one
     * straddles a segment boundary and leaves a broken byte sequence — which
     * encodeToCP852() then rejects, failing the whole file. Characters are also
     * what the format means: the file is written in CP852, where every Polish
     * letter is a single byte, so 35 characters here are 35 bytes on disk.
     */
    public function formatMultiline(string $text): string
    {
        $text = mb_substr($text, 0, self::SEGMENT_LENGTH * self::MAX_SEGMENTS);
        $segments = $text === '' ? [] : mb_str_split($text, self::SEGMENT_LENGTH);

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
