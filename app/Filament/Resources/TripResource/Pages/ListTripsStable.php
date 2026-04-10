<?php

namespace App\Filament\Resources\TripResource\Pages;

use App\Filament\Resources\TripResource;
use App\Models\Trip;
use Filament\Resources\Pages\Page;
use Illuminate\Pagination\LengthAwarePaginator;

class ListTripsStable extends Page
{
    protected static string $resource = TripResource::class;

    protected static string $view = 'filament.resources.trip-resource.pages.list-trips-stable';

    protected static ?string $title = 'Paket Trip (Stable Mode)';

    public string $search = '';

    public function mount(): void
    {
        $this->search = request()->query('search', '');
    }

    public function getRecordsProperty(): LengthAwarePaginator
    {
        $query = Trip::query()
            ->latest('id');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', "%{$this->search}%")
                  ->orWhere('destination_country', 'like', "%{$this->search}%");
            });
        }

        return $query->paginate(15)->withQueryString();
    }
}
