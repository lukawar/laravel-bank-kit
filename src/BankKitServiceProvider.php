<?php

namespace lukawar\BankKit;

use Illuminate\Support\ServiceProvider;
use lukawar\BankKit\Formatters\ElixirFormatter;
use lukawar\BankKit\Generators\ElixirFileGenerator;
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

        $this->app->singleton(ElixirFileGenerator::class, function ($app) {
            return new ElixirFileGenerator(
                formatter: $app->make(ElixirFormatter::class),
            );
        });

        $this->app->singleton(QrPaymentGenerator::class, function () {
            return new QrPaymentGenerator(
                formatter: new PolishQrPaymentFormatter,
                renderer: new EndroidSvgRenderer,
            );
        });

        $this->app->singleton('bank-kit', function ($app) {
            return new BankKitService(
                elixirFileGenerator: $app->make(ElixirFileGenerator::class),
                qrPaymentGenerator: $app->make(QrPaymentGenerator::class),
            );
        });

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
