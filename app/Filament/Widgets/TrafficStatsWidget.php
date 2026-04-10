<?php

namespace App\Filament\Widgets;

use App\Models\Visitor;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class TrafficStatsWidget extends BaseWidget
{
    use InteractsWithPageFilters;
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $start = $this->filters['startDate'] ?? null;
        $end = $this->filters['endDate'] ?? null;

        $query = Visitor::query();
        if ($start) $query->where('created_at', '>=', $start);
        if ($end) $query->where('created_at', '<=', Carbon::parse($end)->endOfDay());

        $summary = (clone $query)->selectRaw('
            COUNT(*) as total,
            (SELECT city FROM visitors v2 WHERE v2.city IS NOT NULL GROUP BY city ORDER BY COUNT(*) DESC LIMIT 1) as top_city,
            (SELECT country FROM visitors v3 WHERE v3.country IS NOT NULL GROUP BY country ORDER BY COUNT(*) DESC LIMIT 1) as top_country
        ')->first();

        return [
            Stat::make('Total Visit (Filtered)', number_format($summary->total ?? 0))
                ->description('Seluruh trafik sesuai rentang waktu')
                ->descriptionIcon('heroicon-m-globe-alt')
                ->color('primary'),
            Stat::make('Top Kota (Range)', $topCity->city ?? 'N/A')
                ->description('Trafik kota terbanyak')
                ->descriptionIcon('heroicon-m-map-pin')
                ->color('success'),
            Stat::make('Top Negara (Range)', $topCountry->country ?? 'N/A')
                ->description('Trafik negara terbanyak')
                ->descriptionIcon('heroicon-m-flag')
                ->color('gray'),
        ];
    }
}
