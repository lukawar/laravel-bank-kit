<?php

namespace lukawar\BankKit\QrPayment\Generators;

use lukawar\BankKit\QrPayment\Contracts\QrCodeRendererInterface;
use lukawar\BankKit\QrPayment\Contracts\QrPaymentFormatterInterface;
use lukawar\BankKit\QrPayment\DTOs\QrLogo;
use lukawar\BankKit\QrPayment\DTOs\QrPaymentData;

class QrPaymentGenerator
{
    public function __construct(
        private readonly QrPaymentFormatterInterface $formatter,
        private readonly QrCodeRendererInterface $renderer,
    ) {}

    /**
     * Generates a QR code image for the given payment data.
     * Returns raw image content (SVG markup, PNG binary, etc.) depending on the renderer.
     * Optionally embeds a logo in the center of the QR code.
     */
    public function generate(QrPaymentData $data, int $size = 300, ?QrLogo $logo = null): string
    {
        $content = $this->formatter->format($data);

        return $this->renderer->render($content, $size, $logo);
    }

    /**
     * Returns the MIME type produced by the configured renderer.
     */
    public function mimeType(): string
    {
        return $this->renderer->mimeType();
    }
}
