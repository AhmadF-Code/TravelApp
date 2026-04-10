<?php

namespace App\Filament\Widgets;

use App\Models\Schedule;
use Filament\Widgets\Widget;

class ActiveTripsWidget extends Widget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Jadwal Aktif Terdekat (Stable Mode)';
    protected static string $view = 'filament.widgets.active-trips-widget-stable';

    public function getSchedulesProperty()
    {
        return Schedule::query()
            ->with('trip')
            ->where('departure_date', '>=', now())
            ->where('status', '!=', 'cancelled')
            ->orderBy('departure_date', 'asc')
            ->limit(5)
            ->get();
    }
}
