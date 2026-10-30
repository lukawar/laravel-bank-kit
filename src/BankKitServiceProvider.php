<?php

namespace lukawar\BankKit;

use Illuminate\Support\ServiceProvider;
use lukawar\BankKit\Formatters\ElixirFormatter;
use lukawar\BankKit\Generators\ElixirFileGenerator;
use lukawar\BankKit\QrPayment\Contracts\QrCodeRendererInterface;
use lukawar\BankKit\QrPayment\Contracts\QrPaymentFormatterInterface;
use lukawar\BankKit\QrPayment\DTOs\QrLogo;
use lukawar\BankKit\QrPayment\Formatters\PolishQrPaymentFormatter;
use lukawar\BankKit\QrPayment\Generators\QrPaymentGenerator;
use lukawar\BankKit\QrPayment\Logos\BlackSquareLogoGenerator;
use lukawar\BankKit\QrPayment\Renderers\EndroidSvgRenderer;
use lukawar\BankKit\Services\BankKitService;

class BankKitServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/bank-kit.php',
            'bank-kit'
        );

        $this->app->singleton(ElixirFormatter::class);
        $this->app->singleton(ElixirFileGenerator::class);

        // Defaults only — an application swaps the QR format or renderer by binding its own.
        $this->app->bindIf(QrPaymentFormatterInterface::class, PolishQrPaymentFormatter::class);
        $this->app->bindIf(QrCodeRendererInterface::class, EndroidSvgRenderer::class);
        $this->app->singleton(QrPaymentGenerator::class);

        // build(), not make(): the alias below points the class back at 'bank-kit'.
        $this->app->singleton('bank-kit', fn ($app) => $app->build(BankKitService::class));
        $this->app->alias('bank-kit', BankKitService::class);
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/bank-kit.php' => config_path('bank-kit.php'),
        ], 'bank-kit-config');
    }

    /**
     * Resolves a QrLogo from configuration.
     * Returns null when logo_path is null (no logo).
     * Returns a black square logo when logo_path is 'default'.
     * Returns a logo pointing to the configured path otherwise.
     */
    public static function resolveLogoFromConfig(): ?QrLogo
    {
        $logoPath = config('bank-kit.qr.logo_path');
        $logoWidth = (int) config('bank-kit.qr.logo_width', 60);

        if ($logoPath === null) {
            return null;
        }

        if ($logoPath === 'default') {
            $logoPath = (new BlackSquareLogoGenerator)->generate($logoWidth);
        }

        return new QrLogo(imagePath: $logoPath, width: $logoWidth);
    }
}
