<?php

namespace lukawar\BankKit\Facades;

use Illuminate\Support\Facades\Facade;
use lukawar\BankKit\Services\BankKitService;

/**
 * @method static array<int, string> generateElixirFiles(\Illuminate\Support\Collection<int, \lukawar\BankKit\Contracts\BankTransferInterface> $transfers)
 * @method static string resolveElixirExtension(\Illuminate\Support\Collection<int, \lukawar\BankKit\Contracts\BankTransferInterface> $transfers)
 * @method static string generateQrPayment(\lukawar\BankKit\QrPayment\DTOs\QrPaymentData $data, int $size = 300, ?\lukawar\BankKit\QrPayment\DTOs\QrLogo $logo = null)
 * @method static string qrMimeType()
 * @method static void registerGateway(\lukawar\BankKit\PaymentGateway\Contracts\PaymentGatewayInterface $gateway)
 * @method static \lukawar\BankKit\PaymentGateway\Contracts\PaymentGatewayInterface gateway(string $identifier)
 * @method static array<string, \lukawar\BankKit\PaymentGateway\Contracts\PaymentGatewayInterface> getGateways()
 *
 * @see BankKitService
 */
class BankKit extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'bank-kit';
    }
}
