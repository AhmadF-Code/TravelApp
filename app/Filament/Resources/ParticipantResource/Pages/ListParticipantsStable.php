<?php

namespace App\Filament\Resources\ParticipantResource\Pages;

use App\Filament\Resources\ParticipantResource;
use App\Models\BookingTraveler;
use Filament\Resources\Pages\Page;
use Illuminate\Pagination\LengthAwarePaginator;

class ListParticipantsStable extends Page
{
    protected static string $resource = ParticipantResource::class;

    protected static string $view = 'filament.resources.participant-resource.pages.list-participants-stable';

    protected static ?string $title = 'Database Peserta (Stable Mode)';

    public string $search = '';

    public function mount(): void
    {
        $this->search = request()->query('search', '');
    }

    public function getRecordsProperty(): LengthAwarePaginator
    {
        $query = BookingTraveler::query()
            ->with(['booking.schedule.trip'])
            ->latest('id');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%")
                  ->orWhere('passport_number', 'like', "%{$this->search}%")
                  ->orWhere('ktp', 'like', "%{$this->search}%");
            });
        }

        // Apply status filter if present
        $statusFilter = request()->query('status', 'all');
        if($statusFilter !== 'all') {
            $query->whereHas('booking', function($b) use ($statusFilter) {
                $b->where('status', $statusFilter);
            });
        }

        return $query->paginate(15);
    }
}
