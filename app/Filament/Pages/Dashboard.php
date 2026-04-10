<?php

namespace App\Filament\Pages;

use App\Models\Booking;
use App\Models\Schedule;
use App\Models\Trip;
use App\Models\Branch;
use App\Models\VisitorLog;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static string $view = 'filament.pages.dashboard-custom';
    protected static ?string $title = '';
    protected static ?int $navigationSort = -1;

    public string $startDate = '';
    public string $endDate = '';

    public function mount(): void
    {
        $this->startDate = request()->query('start', now()->startOfMonth()->format('Y-m-d'));
        $this->endDate   = request()->query('end', now()->format('Y-m-d'));
    }

    public function getKpiProperty(): array
    {
        $s = Carbon::parse($this->startDate)->startOfDay();
        $e = Carbon::parse($this->endDate)->endOfDay();

        // Specific table targeting for 'status' and 'created_at' to prevent ambiguity
        $booking = Booking::query()
            ->whereBetween('bookings.created_at', [$s, $e])
            ->selectRaw('bookings.status, SUM(bookings.total_amount) as total_value, SUM(COALESCE(bookings.refund_amount, 0)) as total_refund, COUNT(*) as cnt')
            ->groupBy('bookings.status')->get()->keyBy('status');

        $allRefund = (float)$booking->sum('total_refund');
        $gross = (float)($booking->get('paid')?->total_value ?? 0);

        return [
            'gross'       => $gross,
            'net'         => $gross - $allRefund,
            'refund'      => $allRefund,
            'pending_val' => (float)($booking->get('pending')?->total_value ?? 0),
            'paid_count'  => $booking->get('paid')?->cnt ?? 0,
            'pending_count'  => $booking->get('pending')?->cnt ?? 0,
            'cancel_count'  => $booking->get('cancelled')?->cnt ?? 0,
        ];
    }

    public function getActiveSchedulesCountProperty(): int
    {
        return Schedule::where('departure_date', '>=', now())
            ->where('schedules.status', '!=', 'cancelled')
            ->count();
    }

    public function getUpcomingSchedulesProperty()
    {
        return Schedule::with('trip')
            ->where('departure_date', '>=', now())
            ->where('schedules.status', '!=', 'cancelled')
            ->orderBy('departure_date')
            ->limit(6)
            ->withCount('travelers')
            ->get();
    }

    public function getTopTripsProperty()
    {
        $s = Carbon::parse($this->startDate)->startOfDay();
        $e = Carbon::parse($this->endDate)->endOfDay();

        return Trip::withCount(['bookings as paid_count' => function($q) use ($s, $e) {
            $q->where('bookings.status', 'paid')
              ->whereBetween('bookings.created_at', [$s, $e]);
        }])->orderByDesc('paid_count')->limit(5)->get();
    }

    public function getTopBranchesProperty()
    {
        $s = Carbon::parse($this->startDate)->startOfDay();
        $e = Carbon::parse($this->endDate)->endOfDay();

        return Branch::withSum(['bookings as total_income' => function($q) use ($s, $e) {
            $q->where('bookings.status', 'paid')
              ->whereBetween('bookings.created_at', [$s, $e]);
        }], 'total_amount')->orderByDesc('total_income')->limit(5)->get();
    }

    public function getVisitorStatProperty(): array
    {
        return [
            'today'   => VisitorLog::whereDate('created_at', today())->count(),
        ];
    }

    public function getFollowupCountProperty(): int
    {
        return Booking::where('follow_up_status', 'needs_follow_up')->count();
    }
}
