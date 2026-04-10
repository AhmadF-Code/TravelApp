<?php

namespace App\Filament\Resources\ScheduleResource\Pages;

use App\Filament\Resources\ScheduleResource;
use App\Models\Schedule;
use Filament\Resources\Pages\Page;
use App\Models\BookingTraveler;

class ViewManifest extends Page
{
    protected static string $resource = ScheduleResource::class;

    protected static string $view = 'filament.resources.schedule-resource.pages.view-manifest-simple';

    protected static ?string $title = 'Manifest Peserta (Stable Mode)';

    public ?int $record = null;
    public string $statusFilter = 'all';

    public function mount($record): void
    {
        $this->record = $record;
        $this->statusFilter = request()->query('status', 'all');
    }

    public function getScheduleProperty()
    {
        return Schedule::with('trip')->findOrFail($this->record);
    }

    public function getTravelersProperty()
    {
        $query = BookingTraveler::query()
            ->with('booking')
            ->whereHas('booking', fn ($q) => $q->where('schedule_id', $this->record));

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        return $query->get();
    }
}
