<?php

namespace lukawar\BankKit\Tests\Feature;

use Carbon\Carbon;
use DateTimeImmutable;
use lukawar\BankKit\DTOs\SplitPaymentTransfer;
use lukawar\BankKit\DTOs\StandardTransfer;
use lukawar\BankKit\DTOs\TaxOfficeTransfer;
use lukawar\BankKit\DTOs\ZusTransfer;
use lukawar\BankKit\Enums\TaxPeriodType;
use lukawar\BankKit\Formatters\ElixirFormatter;
use lukawar\BankKit\Generators\ElixirFileGenerator;
use PHPUnit\Framework\TestCase;

class ElixirFileGeneratorTest extends TestCase
{
    private ElixirFileGenerator $generator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->generator = new ElixirFileGenerator(new ElixirFormatter);
    }

    public function test_generates_single_file_for_small_collection(): void
    {
        $transfers = collect([$this->makeStandardTransfer()]);

        $files = $this->generator->generate($transfers);

        $this->assertCount(1, $files);
    }

    public function test_splits_into_multiple_files_when_exceeds_50_transfers(): void
    {
        $transfers = collect(array_fill(0, 55, $this->makeStandardTransfer()));

        $files = $this->generator->generate($transfers);

        $this->assertCount(2, $files);
    }

    public function test_generated_line_starts_with_order_code_110(): void
    {
        $transfers = collect([$this->makeStandardTransfer()]);

        $files = $this->generator->generate($transfers);
        $decodedContent = iconv('CP852', 'UTF-8', $files[0]);

        $this->assertStringStartsWith('110,', $decodedContent);
    }

    public function test_generated_line_contains_execution_date(): void
    {
        $transfer = new StandardTransfer(
            senderAccount: '58249000050000460073649930',
            senderName: 'Firma Testowa',
            senderAddress: 'ul. Testowa 1|00-001 Warszawa',
            recipientAccount: '12345678901234567890123456',
            recipientName: 'Kontrahent',
            recipientAddress: 'ul. Odbiorcza 2|00-002 Warszawa',
            amountInGrosze: 531200,
            executionDate: Carbon::parse('2024-03-26'),
            title: 'Faktura VAT 1/2024',
        );

        $files = $this->generator->generate(collect([$transfer]));
        $decodedContent = iconv('CP852', 'UTF-8', $files[0]);

        $this->assertStringContainsString('20240326', $decodedContent);
    }

    public function test_execution_date_accepts_any_date_time_implementation(): void
    {
        $transfer = new StandardTransfer(
            senderAccount: '58249000050000460073649930',
            senderName: 'Firma Testowa',
            senderAddress: 'ul. Testowa 1|00-001 Warszawa',
            recipientAccount: '12345678901234567890123456',
            recipientName: 'Kontrahent',
            recipientAddress: 'ul. Odbiorcza 2|00-002 Warszawa',
            amountInGrosze: 531200,
            executionDate: new DateTimeImmutable('2024-03-26'),
            title: 'Faktura VAT 1/2024',
        );

        $files = $this->generator->generate(collect([$transfer]));

        $this->assertStringContainsString(',20240326,', iconv('CP852', 'UTF-8', $files[0]));
    }

    public function test_a_quote_and_a_line_break_in_user_data_keep_one_record_of_15_fields(): void
    {
        $transfer = new StandardTransfer(
            senderAccount: '58249000050000460073649930',
            senderName: 'Firma Testowa',
            senderAddress: 'ul. Testowa 1|00-001 Warszawa',
            recipientAccount: '12345678901234567890123456',
            recipientName: 'Kontrahent "Alfa"',
            recipientAddress: 'ul. Odbiorcza 2|00-002 Warszawa',
            amountInGrosze: 531200,
            executionDate: Carbon::parse('2024-03-26'),
            title: "FV 1/2024\r\n110,20240326,1",
        );

        $files = $this->generator->generate(collect([$transfer]));
        $lines = explode("\r\n", trim(iconv('CP852', 'UTF-8', $files[0])));

        $this->assertCount(1, $lines);
        $this->assertCount(15, str_getcsv($lines[0], escape: '\\'));
    }

    public function test_generated_line_contains_amount_in_grosze(): void
    {
        $transfer = new StandardTransfer(
            senderAccount: '58249000050000460073649930',
            senderName: 'Firma',
            senderAddress: 'Adres',
            recipientAccount: '12345678901234567890123456',
            recipientName: 'Kontrahent',
            recipientAddress: 'Adres',
            amountInGrosze: 531200,
            executionDate: Carbon::today(),
            title: 'Tytuł',
        );

        $files = $this->generator->generate(collect([$transfer]));
        $decodedContent = iconv('CP852', 'UTF-8', $files[0]);

        $this->assertStringContainsString(',531200,', $decodedContent);
    }

    public function test_standard_transfer_uses_classification_51(): void
    {
        $transfers = collect([$this->makeStandardTransfer()]);

        $files = $this->generator->generate($transfers);
        $decodedContent = iconv('CP852', 'UTF-8', $files[0]);

        $this->assertStringContainsString('"51"', $decodedContent);
    }

    public function test_split_payment_transfer_uses_classification_53(): void
    {
        $transfer = new SplitPaymentTransfer(
            senderAccount: '58249000050000460073649930',
            senderName: 'Firma',
            senderAddress: 'Adres',
            recipientAccount: '12345678901234567890123456',
            recipientName: 'Kontrahent',
            recipientAddress: 'Adres',
            amountInGrosze: 100000,
            executionDate: Carbon::today(),
            title: 'Faktura split',
        );

        $files = $this->generator->generate(collect([$transfer]));
        $decodedContent = iconv('CP852', 'UTF-8', $files[0]);

        $this->assertStringContainsString('"53"', $decodedContent);
    }

    public function test_tax_office_transfer_uses_classification_71(): void
    {
        $transfer = $this->makeTaxOfficeTransfer();

        $files = $this->generator->generate(collect([$transfer]));
        $decodedContent = iconv('CP852', 'UTF-8', $files[0]);

        $this->assertStringContainsString('"71"', $decodedContent);
    }

    public function test_tax_office_transfer_title_contains_structured_nip_format(): void
    {
        $transfer = $this->makeTaxOfficeTransfer();

        $files = $this->generator->generate(collect([$transfer]));
        $decodedContent = iconv('CP852', 'UTF-8', $files[0]);

        $this->assertStringContainsString('N1234567890', $decodedContent);
        $this->assertStringContainsString('M12', $decodedContent);
        $this->assertStringContainsString('PIT37', $decodedContent);
    }

    public function test_zus_transfer_uses_classification_51(): void
    {
        $transfer = $this->makeZusTransfer();

        $files = $this->generator->generate(collect([$transfer]));
        $decodedContent = iconv('CP852', 'UTF-8', $files[0]);

        $this->assertStringContainsString('"51"', $decodedContent);
    }

    public function test_zus_transfer_title_contains_skladka_prefix(): void
    {
        $transfer = $this->makeZusTransfer();

        $files = $this->generator->generate(collect([$transfer]));
        $decodedContent = iconv('CP852', 'UTF-8', $files[0]);

        $this->assertStringContainsString('SKLADKA ZA STYCZEN 2024', $decodedContent);
    }

    public function test_generated_line_uses_crlf_line_endings(): void
    {
        $transfers = collect([$this->makeStandardTransfer()]);

        $files = $this->generator->generate($transfers);
        $decodedContent = iconv('CP852', 'UTF-8', $files[0]);

        $this->assertStringContainsString("\r\n", $decodedContent);
    }

    public function test_each_line_has_exactly_15_fields(): void
    {
        $transfers = collect([$this->makeStandardTransfer()]);

        $files = $this->generator->generate($transfers);
        $decodedContent = iconv('CP852', 'UTF-8', $files[0]);

        $firstLine = explode("\r\n", trim($decodedContent))[0];
        $fieldCount = count(str_getcsv($firstLine, escape: '\\'));

        $this->assertSame(15, $fieldCount);
    }

    public function test_resolves_pli_extension_for_standard_transfers(): void
    {
        $transfers = collect([$this->makeStandardTransfer()]);

        $extension = $this->generator->resolveExtension($transfers);

        $this->assertSame('pli', $extension);
    }

    public function test_resolves_pls_extension_when_split_payment_present(): void
    {
        $splitTransfer = new SplitPaymentTransfer(
            senderAccount: '58249000050000460073649930',
            senderName: 'Firma',
            senderAddress: 'Adres',
            recipientAccount: '12345678901234567890123456',
            recipientName: 'Kontrahent',
            recipientAddress: 'Adres',
            amountInGrosze: 100000,
            executionDate: Carbon::today(),
            title: 'Split',
        );

        $transfers = collect([$this->makeStandardTransfer(), $splitTransfer]);

        $extension = $this->generator->resolveExtension($transfers);

        $this->assertSame('pls', $extension);
    }

    public function test_file_content_is_encoded_in_cp852(): void
    {
        $transfer = new StandardTransfer(
            senderAccount: '58249000050000460073649930',
            senderName: 'Firma Łódź',
            senderAddress: 'ul. Ząbkowska 1',
            recipientAccount: '12345678901234567890123456',
            recipientName: 'Kontrahent Ółźćę',
            recipientAddress: 'Adres',
            amountInGrosze: 100000,
            executionDate: Carbon::today(),
            title: 'Tytuł z polskimi znakami: ąęćńó',
        );

        $files = $this->generator->generate(collect([$transfer]));
        $rawContent = $files[0];

        // CP852 encoded content should differ from UTF-8 for Polish characters
        $this->assertNotSame(
            $rawContent,
            iconv('CP852', 'UTF-8', $rawContent)
        );
    }

    private function makeStandardTransfer(): StandardTransfer
    {
        return new StandardTransfer(
            senderAccount: '58249000050000460073649930',
            senderName: 'FORMAT Sp. z o.o.',
            senderAddress: 'ul. Chojnowska 76|59-220 Legnica',
            recipientAccount: '12345678901234567890123456',
            recipientName: 'Kontrahent Testowy',
            recipientAddress: 'ul. Odbiorcza 1|00-001 Warszawa',
            amountInGrosze: 531200,
            executionDate: Carbon::parse('2024-03-26'),
            title: 'Zapłata za fakturę VAT 1/02/2024',
        );
    }

    private function makeTaxOfficeTransfer(): TaxOfficeTransfer
    {
        return new TaxOfficeTransfer(
            senderAccount: '58249000050000460073649930',
            senderName: 'Firma Testowa',
            senderAddress: 'ul. Testowa 1',
            recipientAccount: '12345678901234567890123456',
            recipientName: 'Urząd Skarbowy',
            recipientAddress: 'ul. Skarbowa 1',
            amountInGrosze: 100000,
            executionDate: Carbon::parse('2024-03-26'),
            taxpayerNip: '1234567890',
            periodType: TaxPeriodType::Month,
            periodNumber: '12',
            formSymbol: 'PIT37',
            additionalInfo: 'DEKLARACJA',
        );
    }

    private function makeZusTransfer(): ZusTransfer
    {
        return new ZusTransfer(
            senderAccount: '58249000050000460073649930',
            senderName: 'Firma Testowa',
            senderAddress: 'ul. Testowa 1',
            recipientAccount: '12345678901234567890123456',
            recipientName: 'ZUS',
            recipientAddress: 'ul. Składkowa 1',
            amountInGrosze: 150000,
            executionDate: Carbon::parse('2024-03-26'),
            contributionPeriod: 'STYCZEN 2024',
        );
    }
}
