<?php

namespace App\Filament\Widgets;

use App\Actions\GenerateQRCodeAction;
use App\Filament\Pages\Actions\CopyToClipboardAction;
use App\Models\Url;
use Filament\Forms\Get;
use Filament\Tables\Actions\Action;
use Filament\Support\Enums\IconSize;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\ImageColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\Layout\Split;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\HtmlString;
use Webbingbrasil\FilamentCopyActions\Tables\Actions\CopyAction;

class UserLinks extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading(null)
            ->query(Url::where('user_id', auth()->id()))
            ->columns([
                Split::make([
                    Tables\Columns\TextColumn::make('qr')
                        ->default(fn(Url $record) => GenerateQRCodeAction::execute($record->original_url,))
                        ->grow(false)
                        ->extraAttributes(fn(): array => [
                            'class' => 'ring ring-blue-400'
                        ]),
                    Tables\Columns\ViewColumn::make('info')
                        ->searchable(['title', 'original_url'])
                        ->view('filament.tables.columns.custom-url-row-info'),
                ])
            ])
            ->actions([
                CopyAction::make()->copyable(fn($record) => 'https://' . config('ziplink.short_url_domain') . '/' . $record->short_url)
                    ->hiddenLabel(true)
                    ->icon('heroicon-o-square-2-stack')
                    ->iconSize(IconSize::Large)
                    ->color(Color::hex("#fff")),
                Action::make('copy')
                    ->hiddenLabel(true)
                    ->icon('heroicon-o-arrow-down-tray')
                    ->iconSize(IconSize::Large)
                    ->color(Color::hex("#fff"))
                    ->action(function ($record) {
                        // Implementazione della copia del link
                        dd('test');
                        // dd(CopyAction::make()->copyable(fn($record) => $record->original_url));

                        // $link = "https://zipplink.it/{$record->custom_url}";
                        // return "navigator.clipboard.writeText('$link')";
                    }),
                Action::make('delete')
                    ->hiddenLabel(true)
                    ->icon('heroicon-o-trash')
                    ->iconSize(IconSize::Large)
                    ->color(Color::hex("#fff"))
                    ->action(function (Url $record) {
                        $notification = \Filament\Notifications\Notification::make();
                        $title = 'Link deleted';
                        $body = 'The link has been deleted successfully.';

                        try {
                            $record->delete();
                            $notification->success();
                        } catch (\Exception $e) {
                            $title = 'Error';
                            $body = 'An error occurred while deleting the link.';
                            $notification->danger();
                        }

                        $notification
                            ->title($title)
                            ->body($body)
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Delete Link')
                    ->modalDescription('Are you sure you\'d like to delete this link? This cannot be undone.')
                    ->modalIconColor('danger')
                    ->modalSubmitActionLabel('Yes, delete it')
                    ->modalFooterActions(fn($action) => [
                        $action->getModalCancelAction(),
                        $action->getModalSubmitAction()->color('primary'),
                    ])

            ])
            ->headerActions([
                Action::make('create')
                    ->label('Create new Link')
                    ->modalHeading('Create New')
                    ->form([
                        \Filament\Forms\Components\Placeholder::make('qr-code')
                            ->label(false)
                            ->content(function (Get $get): ?HtmlString {
                                return empty($get('original_url')) ?
                                    null :
                                    GenerateQRCodeAction::execute(url: $get('original_url'), size: 256);
                            }),
                        \Filament\Forms\Components\TextInput::make('title')
                            ->label(false)
                            ->placeholder('Short Link\'s Title'),
                        \Filament\Forms\Components\TextInput::make('original_url')
                            ->label(false)
                            ->live()
                            ->debounce(500)
                            ->placeholder('Enter your Loooong URL'),
                        \Filament\Forms\Components\TextInput::make('custom_url')
                            ->label(false)
                            ->placeholder('Custom Link (optional)')
                            ->prefix(config('ziplink.short_url_domain') . '/'),
                    ])
                    ->modalWidth(MaxWidth::Medium)
                    ->modalCancelAction(false)
                    ->modalSubmitActionLabel('Create')
                    ->action(function (array $data): void {
                        Url::create($data);
                    })
            ]);
    }
}
