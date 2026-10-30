<?php

namespace lukawar\BankKit\Tests\Feature;

use lukawar\BankKit\BankKitServiceProvider;
use lukawar\BankKit\Facades\BankKit;
use lukawar\BankKit\QrPayment\Contracts\QrCodeRendererInterface;
use lukawar\BankKit\QrPayment\DTOs\QrPaymentData;
use lukawar\BankKit\QrPayment\Generators\QrPaymentGenerator;
use lukawar\BankKit\QrPayment\Renderers\EndroidPngRenderer;
use lukawar\BankKit\Services\BankKitService;
use Orchestra\Testbench\TestCase;

class BankKitServiceProviderTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [BankKitServiceProvider::class];
    }

    protected function getPackageAliases($app): array
    {
        return ['BankKit' => BankKit::class];
    }

    public function test_it_merges_the_package_config(): void
    {
        $this->assertSame(60, (int) config('bank-kit.qr.logo_width'));
    }

    public function test_it_resolves_the_service_as_a_singleton_under_both_names(): void
    {
        $this->assertSame($this->app->make('bank-kit'), $this->app->make(BankKitService::class));
    }

    public function test_the_facade_generates_elixir_files(): void
    {
        $this->assertSame('pli', BankKit::resolveElixirExtension(collect()));
    }

    public function test_the_qr_generator_renders_svg_by_default(): void
    {
        $generator = $this->app->make(QrPaymentGenerator::class);

        $this->assertSame('image/svg+xml', $generator->mimeType());
        $this->assertStringContainsString('<svg', $generator->generate($this->makeQrPaymentData()));
    }

    public function test_an_application_can_swap_the_qr_renderer(): void
    {
        $this->app->bind(QrCodeRendererInterface::class, EndroidPngRenderer::class);
        $this->app->forgetInstance(QrPaymentGenerator::class);

        $this->assertSame('image/png', $this->app->make(QrPaymentGenerator::class)->mimeType());
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
