<?php

namespace lukawar\BankKit\QrPayment\Renderers;

use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\SvgWriter;
use lukawar\BankKit\QrPayment\Contracts\QrCodeRendererInterface;
use lukawar\BankKit\QrPayment\DTOs\QrLogo;

class EndroidSvgRenderer implements QrCodeRendererInterface
{
    public function render(string $content, int $size = 300, ?QrLogo $logo = null): string
    {
        $qrCode = QrCode::create($content)
            ->setEncoding(new Encoding('UTF-8'))
            ->setSize($size);

        $endroidLogo = $logo !== null
            ? Logo::create($logo->imagePath)->setResizeToWidth($logo->width)
            : null;

        return (new SvgWriter)->write($qrCode, $endroidLogo)->getString();
    }

    public function mimeType(): string
    {
        return 'image/svg+xml';
    }
}
