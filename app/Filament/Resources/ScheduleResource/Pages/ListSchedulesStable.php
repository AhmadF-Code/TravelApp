<?php

namespace App\Filament\Resources\ScheduleResource\Pages;

use App\Filament\Resources\ScheduleResource;
use App\Models\Schedule;
use Filament\Resources\Pages\Page;
use Illuminate\Pagination\LengthAwarePaginator;

class ListSchedulesStable extends Page
{
    protected static string $resource = ScheduleResource::class;

    protected static string $view = 'filament.resources.schedule-resource.pages.list-schedules-stable';

    protected static ?string $title = 'Jadwal Keberangkatan (Stable Mode)';

    public string $search = '';

    public function mount(): void
    {
        $this->search = request()->query('search', '');
    }

    public function getRecordsProperty(): LengthAwarePaginator
    {
        $query = Schedule::query()
            ->with(['trip'])
            ->withCount('travelers')
            ->latest('departure_date');

        if ($this->search) {
            $query->whereHas('trip', function ($q) {
                $q->where('title', 'like', "%{$this->search}%");
            });
        }

        return $query->paginate(15)->withQueryString();
    }
}
