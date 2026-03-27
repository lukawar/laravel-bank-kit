<?php

namespace lukawar\BankKit\Facades;

use Illuminate\Support\Facades\Facade;
use lukawar\BankKit\Services\BankKitService;

/**
 * @method static array generateElixirFiles(\Illuminate\Support\Collection $transfers)
 * @method static string resolveElixirExtension(\Illuminate\Support\Collection $transfers)
 * @method static void registerGateway(\lukawar\BankKit\Contracts\PaymentGatewayInterface $gateway)
 * @method static \lukawar\BankKit\Contracts\PaymentGatewayInterface gateway(string $identifier)
 * @method static array getGateways()
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
