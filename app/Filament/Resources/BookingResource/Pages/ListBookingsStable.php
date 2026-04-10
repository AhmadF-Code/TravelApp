<?php

namespace App\Filament\Resources\BookingResource\Pages;

use App\Filament\Resources\BookingResource;
use App\Models\Booking;
use App\Services\BookingExpirationService;
use Filament\Resources\Pages\Page;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

class ListBookingsStable extends Page
{
    protected static string $resource = BookingResource::class;
    protected static string $view     = 'filament.resources.booking-resource.pages.list-bookings-stable';
    protected static ?string $title   = 'Daftar Booking';

    public string $search       = '';
    public string $statusFilter = 'all';
    public int    $expiredCount = 0;
    public string $checkedAt    = '';

    public function mount(): void
    {
        $this->search       = request()->query('search', '');
        $this->statusFilter = request()->query('status', 'all');

        // Auto-run expiration check every time this page is opened/refreshed
        $this->runExpirationCheck();
    }

    /**
     * Manual refresh action triggered by the "Refresh" button in blade.
     */
    public function refreshBookings(): void
    {
        $this->runExpirationCheck();
    }

    private function runExpirationCheck(): void
    {
        $adminName = auth()->user()?->name ?? 'admin';
        $result = (new BookingExpirationService(60))->run("admin:{$adminName}");

        $this->expiredCount = $result['expired_count'];
        $this->checkedAt    = Carbon::parse($result['checked_at'])
                                    ->setTimezone(config('app.timezone'))
                                    ->format('d M Y H:i:s');
    }

    public function getRecordsProperty(): LengthAwarePaginator
    {
        $query = Booking::query()
            ->with(['schedule.trip'])
            ->latest('id');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('booking_code', 'like', "%{$this->search}%")
                  ->orWhere('customer_name', 'like', "%{$this->search}%")
                  ->orWhere('customer_email', 'like', "%{$this->search}%");
            });
        }

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        return $query->paginate(15)->withQueryString();
    }
}
