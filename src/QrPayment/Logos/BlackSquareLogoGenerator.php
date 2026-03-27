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

        $image = imagecreate($squareSize, $squareSize);
        imagecolorallocate($image, 0, 0, 0);

        $path = sys_get_temp_dir().'/bank-kit-default-logo.png';
        imagepng($image, $path);
        imagedestroy($image);

        return $path;
    }
}
