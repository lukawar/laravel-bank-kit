<?php

namespace lukawar\BankKit\QrPayment\Contracts;

use lukawar\BankKit\QrPayment\DTOs\QrLogo;

interface QrCodeRendererInterface
{
    /**
     * Renders the given content as a QR code image.
     * Returns the raw image string (e.g. SVG markup or PNG binary).
     * Optionally embeds a logo in the center of the QR code.
     */
    public function render(string $content, int $size = 300, ?QrLogo $logo = null): string;

    /**
     * Returns the MIME type produced by this renderer (e.g. image/svg+xml, image/png).
     */
    public function mimeType(): string;
}
