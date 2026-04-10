<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\PromoCode;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Carbon;

class IncomeOverview extends BaseWidget
{
    use InteractsWithPageFilters;
    protected static ?int $sort = 1;
    
    protected function getStats(): array
    {
        $start = $this->filters['startDate'] ?? null;
        $end = $this->filters['endDate'] ?? null;

        $base = Booking::query()
            ->when($start, fn ($q) => $q->where('created_at', '>=', $start))
            ->when($end, fn ($q) => $q->where('created_at', '<=', Carbon::parse($end)->endOfDay()));

        $data = (clone $base)->selectRaw('
            status,
            SUM(total_amount) as total_value,
            SUM(refund_amount) as total_refund,
            COUNT(*) as count
        ')->groupBy('status')->get()->keyBy('status');

        $paid = $data->get('paid');
        $pending = $data->get('pending');
        $cancelled = $data->get('cancelled');
        
        $totalRefundValue = $data->sum('total_refund');
        $grossRevenue = $paid?->total_value ?? 0;
        $netRevenue = $grossRevenue - $totalRefundValue;

        return [
            Stat::make('Omset Kotor (Gross)', 'Rp ' . number_format($grossRevenue, 0, ',', '.'))
                ->description('Total dari ' . ($paid?->count ?? 0) . ' booking lunas')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),

            Stat::make('Omset Bersih (Net)', 'Rp ' . number_format($netRevenue, 0, ',', '.'))
                ->description('Settlement akhir setelah refund')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),

            Stat::make('Menunggu Bayar (Pending)', 'Rp ' . number_format($pending?->total_value ?? 0, 0, ',', '.'))
                ->description(($pending?->count ?? 0) . ' pesanan aktif')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Potensi Batal (Cancelled)', 'Rp ' . number_format($cancelled?->total_value ?? 0, 0, ',', '.'))
                ->description(($cancelled?->count ?? 0) . ' pembatalan tercatat')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
        ];
    }
}
