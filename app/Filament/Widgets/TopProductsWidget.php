<?php

namespace App\Filament\Widgets;

use App\Models\Trip;
use Filament\Widgets\Widget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class TopProductsWidget extends Widget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'half';
    protected static ?string $heading = 'Top Trip Product (Stable Mode)';
    protected static string $view = 'filament.widgets.top-products-widget-stable';

    public function getTripsProperty()
    {
        $start = $this->filters['startDate'] ?? null;
        $end = $this->filters['endDate'] ?? null;

        return Trip::query()
            ->withCount(['bookings as filtered_bookings_count' => function (Builder $query) use ($start, $end) {
                $q = $query->where('bookings.status', 'paid');
                if ($start) $q->where('bookings.created_at', '>=', $start);
                if ($end) $q->where('bookings.created_at', '<=', Carbon::parse($end)->endOfDay());
            }])
            ->orderByDesc('filtered_bookings_count')
            ->limit(5)
            ->get();
    }
}
