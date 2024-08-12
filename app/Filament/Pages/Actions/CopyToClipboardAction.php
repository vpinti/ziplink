<?php

namespace App\Filament\Pages\Actions;

use Closure;
use Filament\Tables\Actions\Action as BaseAction;
use Illuminate\Support\HtmlString;

class CopyToClipboardAction extends BaseAction
{
    protected string|Closure|null $copyText = null;

    public static function getDefaultName(): ?string
    {
        return 'copy-to-clipboard';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->extraAttributes(fn() => [
            'x-on:click' => new HtmlString(<<<JS
                () => {
                    const copyText = '{$this->getCopyText()}';

                    const copyFailNotification = new Notification()
                        .title('Copy Failed!')
                        .danger();

                    if (!window.navigator.clipboard || !copyText) {
                        copyFailNotification.send();

                        return;
                    }

                    window.navigator.clipboard.writeText(copyText)
                        .then(() => {
                            new Notification()
                                .title('Copied to Clipboard!')
                                .success()
                                .send();

                        }).catch(() => {
                            copyFailNotification.send();
                        });
                }
            JS),
        ]);
    }

    public function copyText(string|Closure|null $copyText = null): static
    {
        $this->copyText = $copyText;

        return $this;
    }

    public function getCopyText(): string|null
    {
        return $this->evaluate($this->copyText);
    }
}
