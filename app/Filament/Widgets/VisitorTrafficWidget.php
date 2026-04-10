<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

use App\Models\Visitor;

class VisitorTrafficWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Visitors', Visitor::count())
                ->description('All time visitors')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Unique Countries', Visitor::distinct('country')->count('country'))
                ->description('Global reach')
                ->descriptionIcon('heroicon-m-globe-americas')
                ->color('primary'),
            Stat::make('Today Visitors', Visitor::whereDate('created_at', today())->count())
                ->description('Visitors today')
                ->descriptionIcon('heroicon-m-users')
                ->color('warning'),
        ];
    }
}
