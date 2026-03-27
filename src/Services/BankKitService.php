<?php

namespace lukawar\BankKit\Services;

use Illuminate\Support\Collection;
use InvalidArgumentException;
use lukawar\BankKit\Contracts\BankTransferInterface;
use lukawar\BankKit\Contracts\PaymentGatewayInterface;
use lukawar\BankKit\Generators\ElixirFileGenerator;
use lukawar\BankKit\QrPayment\DTOs\QrLogo;
use lukawar\BankKit\QrPayment\DTOs\QrPaymentData;
use lukawar\BankKit\QrPayment\Generators\QrPaymentGenerator;

class BankKitService
{
    /** @var array<string, PaymentGatewayInterface> */
    private array $gateways = [];

    public function __construct(
        private readonly ElixirFileGenerator $elixirFileGenerator,
        private readonly QrPaymentGenerator $qrPaymentGenerator,
    ) {}

    /**
     * Generates ELIXIR file contents from a collection of transfers.
     * Returns array of encoded file strings (chunked by 50 transfers per file).
     *
     * @param  Collection<int, BankTransferInterface>  $transfers
     * @return array<int, string>
     */
    public function generateElixirFiles(Collection $transfers): array
    {
        return $this->elixirFileGenerator->generate($transfers);
    }

    /**
     * Resolves the correct file extension for the given transfers.
     *
     * @param  Collection<int, BankTransferInterface>  $transfers
     */
    public function resolveElixirExtension(Collection $transfers): string
    {
        return $this->elixirFileGenerator->resolveExtension($transfers);
    }

    /**
     * Generates a QR code image for the given payment data.
     * Returns raw image content — type depends on the configured renderer.
     */
    public function generateQrPayment(QrPaymentData $data, int $size = 300, ?QrLogo $logo = null): string
    {
        return $this->qrPaymentGenerator->generate($data, $size, $logo);
    }

    /**
     * Returns the MIME type produced by the configured QR renderer.
     */
    public function qrMimeType(): string
    {
        return $this->qrPaymentGenerator->mimeType();
    }

    /**
     * Registers a payment gateway.
     */
    public function registerGateway(PaymentGatewayInterface $gateway): void
    {
        $this->gateways[$gateway->getIdentifier()] = $gateway;
    }

    /**
     * Returns a registered gateway by identifier.
     */
    public function gateway(string $identifier): PaymentGatewayInterface
    {
        if (! isset($this->gateways[$identifier])) {
            throw new InvalidArgumentException("Payment gateway [{$identifier}] is not registered.");
        }

        return $this->gateways[$identifier];
    }

    /**
     * Returns all registered gateways.
     *
     * @return array<string, PaymentGatewayInterface>
     */
    public function getGateways(): array
    {
        return $this->gateways;
    }
}
