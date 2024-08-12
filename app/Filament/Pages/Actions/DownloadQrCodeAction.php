<?php

declare(strict_types=1);

namespace App\Filament\Pages\Actions;

use App\Actions\GenerateQRCodeAction;
use Filament\Tables\Actions\Action;

class DownloadQrCodeAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'download-qr-code';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->icon('heroicon-o-arrow-down-tray');
        $this->label('Download QR Code');

        $this->extraAttributes(fn() => [
            'title' => 'Download QR Code',
        ]);

        $this->action(function ($record) {

            $qrCode = GenerateQRCodeAction::execute(url: $record->original_url, size: 256, type: 'png');

            $filename = 'qr-' . $record->short_url . '.png';

            return response()->streamDownload(function () use ($qrCode) {
                echo $qrCode;
            }, $filename);
        });
    }
}
