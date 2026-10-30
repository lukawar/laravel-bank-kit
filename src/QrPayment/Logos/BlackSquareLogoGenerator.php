<?php

namespace lukawar\BankKit\QrPayment\Logos;

use RuntimeException;

/**
 * Generates a temporary black square PNG image to be used as a default QR code logo.
 * Requires the GD extension.
 */
class BlackSquareLogoGenerator
{
    public function generate(int $squareSize = 60): string
    {
        if (! extension_loaded('gd')) {
            throw new RuntimeException('The GD extension is required to generate the default black square logo.');
        }

        $path = sys_get_temp_dir()."/bank-kit-default-logo-{$squareSize}.png";

        if (is_file($path)) {
            return $path;
        }

        $image = imagecreate($squareSize, $squareSize);
        imagecolorallocate($image, 0, 0, 0);

        // Written aside and renamed, so a concurrent request never reads a half-written file.
        $temporaryPath = tempnam(sys_get_temp_dir(), 'bank-kit-logo-');
        imagepng($image, $temporaryPath);
        rename($temporaryPath, $path);

        return $path;
    }
}
