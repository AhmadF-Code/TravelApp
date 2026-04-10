<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class DashboardWelcomeWidget extends Widget
{
    protected static ?int $sort = -1;
    protected int | string | array $columnSpan = 'full';
    protected static string $view = 'filament.widgets.dashboard-welcome-widget';

    public function getSummaryProperty()
    {
        return [
            'active_trip' => \App\Models\Schedule::where('departure_date', '>=', now())->count(),
            'upcoming_travelers' => \App\Models\BookingTraveler::where('status', 'paid')->count(),
            'pending_followup' => \App\Models\Booking::where('follow_up_status', 'needs_follow_up')->count(),
        ];
    }
}
