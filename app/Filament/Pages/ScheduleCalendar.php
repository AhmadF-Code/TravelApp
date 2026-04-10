<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Schedule;

class ScheduleCalendar extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static string $view = 'filament.pages.schedule-calendar';

    protected static ?string $navigationGroup = 'Operational';

    protected static ?string $navigationLabel = 'Kalender Keberangkatan';

    protected static ?string $title = 'Kalender Keberangkatan';

    public static function getUrl(array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?\Illuminate\Database\Eloquent\Model $tenant = null): string
    {
        return route('admin.calendar_standalone');
    }

    public static function getNavigationUrl(): string
    {
        return route('admin.calendar_standalone');
    }

    public function mount(): void
    {
        redirect()->route('admin.calendar_standalone');
    }

    protected function getViewData(): array
    {
        return [
            'schedules' => [], // Page is redirected anyway
        ];
    }
}
