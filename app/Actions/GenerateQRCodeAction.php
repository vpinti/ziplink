<?php

declare(strict_types=1);

namespace App\Actions;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\HtmlString;

final class GenerateQRCodeAction
{
    /**
     * Executes the action of generating a QR code.
     *
     * @param string $url The URL for which to generate the QR code.
     * @param string $type The format of the QR code (svg, png, eps).
     * @return HtmlString The generated QR code as an HTML string.
     * @throws \InvalidArgumentException
     */
    public static function execute(string $url, int $size = 128, string $type = 'svg'): HtmlString
    {
        // Validazione del tipo di formato
        if (!in_array($type, ['svg', 'png', 'eps'])) {
            throw new \InvalidArgumentException('Invalid QR code format type.');
        }

        // Generazione del QR code
        return
            QrCode::format($type)
            ->style('square')
            ->size($size)
            ->generate($url)
        ;
    }
}
