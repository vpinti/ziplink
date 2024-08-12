<?php

namespace App\Filament\Widgets;

use App\Models\Click;
use App\Models\Url;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LinkStats extends BaseWidget
{
    protected static ?string $pollingInterval = null;
    
    protected function getStats(): array
    {
        $userId = auth()->id();

        $totalClicks = Click::whereHas('url', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })->count();

        return [
            Stat::make('Links Created', Url::where('user_id', $userId)->count()),
            Stat::make('Total Clicks', $totalClicks),
        ];
    }

    protected function getColumns(): int
    {
        return 2;
    }
}
