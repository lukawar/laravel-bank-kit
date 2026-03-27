<?php

namespace lukawar\BankKit\Tests\Feature;

use lukawar\BankKit\QrPayment\DTOs\QrLogo;
use lukawar\BankKit\QrPayment\DTOs\QrPaymentData;
use lukawar\BankKit\QrPayment\Exceptions\InvalidIbanException;
use lukawar\BankKit\QrPayment\Formatters\PolishQrPaymentFormatter;
use lukawar\BankKit\QrPayment\Generators\QrPaymentGenerator;
use lukawar\BankKit\QrPayment\Logos\BlackSquareLogoGenerator;
use lukawar\BankKit\QrPayment\Renderers\EndroidSvgRenderer;
use PHPUnit\Framework\TestCase;

class QrPaymentGeneratorTest extends TestCase
{
    private QrPaymentGenerator $generator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->generator = new QrPaymentGenerator(
            new PolishQrPaymentFormatter,
            new EndroidSvgRenderer,
        );
    }

    public function test_generates_svg_output(): void
    {
        $result = $this->generator->generate($this->makeQrPaymentData());

        $this->assertStringContainsString('<svg', $result);
    }

    public function test_mime_type_is_svg(): void
    {
        $this->assertSame('image/svg+xml', $this->generator->mimeType());
    }

    public function test_polish_formatter_produces_correct_format(): void
    {
        $formatter = new PolishQrPaymentFormatter;
        $data = new QrPaymentData(
            iban: 'PL10105000997603123456789123',
            amountInGrosze: 53120,
            recipientName: 'Kontrahent Testowy',
            title: 'Faktura VAT 1/2024',
        );

        $formatted = $formatter->format($data);

        $this->assertSame('|PL|PL10105000997603123456789123|53120|Kontrahent Testowy|Faktura VAT 1/2024|||', $formatted);
    }

    public function test_iban_is_normalized_to_uppercase_without_spaces(): void
    {
        $data = new QrPaymentData(
            iban: 'pl 10 1050 0099 7603 1234 5678 9123',
            amountInGrosze: 10000,
            recipientName: 'Firma',
        );

        $this->assertSame('PL10105000997603123456789123', $data->iban);
    }

    public function test_throws_exception_for_invalid_iban(): void
    {
        $this->expectException(InvalidIbanException::class);

        new QrPaymentData(
            iban: 'NIEPOPRAWNY',
            amountInGrosze: 10000,
            recipientName: 'Firma',
        );
    }

    public function test_title_defaults_to_empty_string(): void
    {
        $formatter = new PolishQrPaymentFormatter;
        $data = new QrPaymentData(
            iban: 'PL10105000997603123456789123',
            amountInGrosze: 10000,
            recipientName: 'Firma',
        );

        $formatted = $formatter->format($data);

        $this->assertStringEndsWith('|Firma||||', $formatted);
    }

    public function test_amount_in_grosze_is_used_as_integer_string(): void
    {
        $formatter = new PolishQrPaymentFormatter;
        $data = new QrPaymentData(
            iban: 'PL10105000997603123456789123',
            amountInGrosze: 1000000,
            recipientName: 'Firma',
        );

        $formatted = $formatter->format($data);

        $this->assertStringContainsString('|1000000|', $formatted);
    }

    public function test_generated_qr_size_can_be_customized(): void
    {
        $data = $this->makeQrPaymentData();

        $resultDefault = $this->generator->generate($data, 300);
        $resultLarge = $this->generator->generate($data, 600);

        $this->assertNotSame($resultDefault, $resultLarge);
    }

    public function test_generates_svg_with_logo(): void
    {
        $logoPath = (new BlackSquareLogoGenerator)->generate(60);
        $logo = new QrLogo(imagePath: $logoPath, width: 60);

        $result = $this->generator->generate($this->makeQrPaymentData(), 300, $logo);

        $this->assertStringContainsString('<svg', $result);
    }

    public function test_black_square_logo_generator_creates_valid_png_file(): void
    {
        $logoPath = (new BlackSquareLogoGenerator)->generate(60);

        $this->assertFileExists($logoPath);
        $this->assertSame('image/png', mime_content_type($logoPath));
    }

    public function test_qr_with_logo_differs_from_qr_without_logo(): void
    {
        $data = $this->makeQrPaymentData();
        $logoPath = (new BlackSquareLogoGenerator)->generate(60);
        $logo = new QrLogo(imagePath: $logoPath, width: 60);

        $withoutLogo = $this->generator->generate($data, 300, null);
        $withLogo = $this->generator->generate($data, 300, $logo);

        $this->assertNotSame($withoutLogo, $withLogo);
    }

    private function makeQrPaymentData(): QrPaymentData
    {
        return new QrPaymentData(
            iban: 'PL10105000997603123456789123',
            amountInGrosze: 53120,
            recipientName: 'FORMAT Sp. z o.o.',
            title: 'Faktura VAT 1/2024',
        );
    }
}
