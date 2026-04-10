<?php

namespace App\Filament\Widgets;

use App\Models\Branch;
use Filament\Widgets\Widget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class TopBranchesWidget extends Widget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 'half';
    protected static ?string $heading = 'Top Branches Profit (Stable Mode)';
    protected static string $view = 'filament.widgets.top-branches-widget-stable';

    public function getBranchesProperty()
    {
        $start = $this->filters['startDate'] ?? null;
        $end = $this->filters['endDate'] ?? null;

        return Branch::query()
            ->withSum(['bookings as total_income' => function (Builder $query) use ($start, $end) {
                $q = $query->where('bookings.status', 'paid');
                if ($start) $q->where('bookings.created_at', '>=', $start);
                if ($end) $q->where('bookings.created_at', '<=', Carbon::parse($end)->endOfDay());
            }], 'total_amount')
            ->orderByDesc('total_income')
            ->limit(5)
            ->get();
    }
}
