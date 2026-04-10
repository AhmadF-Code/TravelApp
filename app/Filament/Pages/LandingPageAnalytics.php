<?php

namespace App\Filament\Pages;

use App\Models\Booking;
use App\Models\VisitorLog;
use App\Models\VisitorEvent;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LandingPageAnalytics extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Analytics Landing Page';
    protected static string $view = 'filament.pages.landing-page-analytics';
    protected static ?string $title = 'Landing Page Insights';
    protected static ?string $navigationGroup = 'Marketing';
    protected static ?int $navigationSort = 2;

    public $startDate;
    public $endDate;

    public function mount(): void
    {
        $this->startDate = request()->query('start', now()->subDays(30)->format('Y-m-d'));
        $this->endDate   = request()->query('end', now()->format('Y-m-d'));
    }

    public function getAnalyticsProperty(): array
    {
        $s = Carbon::parse($this->startDate)->startOfDay();
        $e = Carbon::parse($this->endDate)->endOfDay();

        $visitors = VisitorLog::whereBetween('created_at', [$s, $e]);
        
        $trafficTrend = VisitorLog::whereBetween('created_at', [$s, $e])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $paidCount = Booking::whereBetween('created_at', [$s, $e])->where('status', 'paid')->count();

        return [
            'total_visitors' => (clone $visitors)->count(),
            'unique_sessions' => (clone $visitors)->distinct('session_id')->count(),
            'total_pv' => VisitorEvent::whereBetween('created_at', [$s, $e])->where('event_type', 'page_view')->count(),
            'traffic_trend' => $trafficTrend,
            'paid_count'    => $paidCount,
            
            'funnel' => [
                'visitors' => (clone $visitors)->distinct('session_id')->count(),
                'cta_clicks' => VisitorEvent::whereBetween('created_at', [$s, $e])->where('event_type', 'cta_click')->distinct('visitor_log_id')->count(),
                'bookings' => Booking::whereBetween('created_at', [$s, $e])->count(),
                'paid' => $paidCount,
            ],

            'top_countries' => (clone $visitors)->select('country', DB::raw('count(*) as count'))->groupBy('country')->orderByDesc('count')->limit(5)->get(),
            'top_cities' => (clone $visitors)->select('city', DB::raw('count(*) as count'))->groupBy('city')->orderByDesc('count')->limit(5)->get(),
            'devices' => (clone $visitors)->select('device_type', DB::raw('count(*) as count'))->groupBy('device_type')->get(),
            'sources' => (clone $visitors)->select('source', DB::raw('count(*) as count'))->groupBy('source')->get(),
            'top_cta' => VisitorEvent::whereBetween('created_at', [$s, $e])->where('event_type', 'cta_click')->select('event_name', DB::raw('count(*) as count'))->groupBy('event_name')->orderByDesc('count')->limit(5)->get(),
        ];
    }
}
