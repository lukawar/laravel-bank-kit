<?php

namespace lukawar\BankKit\Tests\Feature;

use lukawar\BankKit\Exceptions\ElixirEncodingException;
use lukawar\BankKit\Formatters\ElixirFormatter;
use PHPUnit\Framework\TestCase;

class ElixirFormatterTest extends TestCase
{
    private ElixirFormatter $formatter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->formatter = new ElixirFormatter;
    }

    public function test_short_text_is_padded_with_empty_segments(): void
    {
        $this->assertSame('Kowalski|||', $this->formatter->formatMultiline('Kowalski'));
    }

    public function test_text_is_split_into_four_segments_of_thirty_five_characters(): void
    {
        $segments = explode('|', $this->formatter->formatMultiline(str_repeat('a', 200)));

        $this->assertCount(4, $segments);
        $this->assertSame([35, 35, 35, 35], array_map('strlen', $segments));
    }

    public function test_a_multibyte_character_on_a_segment_boundary_is_not_cut_in_half(): void
    {
        $text = str_repeat('a', 34).'ł'.str_repeat('b', 10);

        $result = $this->formatter->formatMultiline($text);

        $this->assertTrue(mb_check_encoding($result, 'UTF-8'));
        $this->assertSame(35, mb_strlen(explode('|', $result)[0]));
    }

    public function test_multibyte_segments_survive_the_cp852_encoding(): void
    {
        $encoded = $this->formatter->encodeToCP852(
            $this->formatter->formatMultiline(str_repeat('ą', 40))
        );

        $decoded = iconv('CP852', 'UTF-8', $encoded);

        $this->assertSame(str_repeat('ą', 35).'|'.str_repeat('ą', 5).'||', $decoded);
    }

    public function test_segments_are_counted_in_characters_not_bytes(): void
    {
        $segments = explode('|', $this->formatter->formatMultiline(str_repeat('ó', 200)));

        $this->assertSame([35, 35, 35, 35], array_map('mb_strlen', $segments));
    }

    public function test_an_empty_text_yields_four_empty_segments(): void
    {
        $this->assertSame('|||', $this->formatter->formatMultiline(''));
    }

    public function test_a_double_quote_inside_a_value_cannot_close_the_field(): void
    {
        $this->assertSame('"Firma \'Alfa\' Sp. z o.o."', $this->formatter->quoted('Firma "Alfa" Sp. z o.o.'));
    }

    public function test_a_line_break_inside_a_value_cannot_start_a_new_record(): void
    {
        $this->assertSame('"FV 1/2024  110,20240326"', $this->formatter->quoted("FV 1/2024\r\n110,20240326"));
    }

    public function test_invalid_utf8_fails_with_an_explicit_exception(): void
    {
        $this->expectException(ElixirEncodingException::class);

        $this->formatter->encodeToCP852("Faktura \xC3\x28");
    }
}
