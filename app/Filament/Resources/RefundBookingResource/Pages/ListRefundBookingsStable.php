<?php

namespace App\Filament\Resources\RefundBookingResource\Pages;

use App\Filament\Resources\RefundBookingResource;
use App\Models\Booking;
use Filament\Resources\Pages\Page;
use Illuminate\Pagination\LengthAwarePaginator;

class ListRefundBookingsStable extends Page
{
    protected static string $resource = RefundBookingResource::class;

    protected static string $view = 'filament.resources.refund-booking-resource.pages.list-refund-bookings-stable';

    protected static ?string $title = 'Refund Management (Stable Mode)';

    public string $search = '';

    public function mount(): void
    {
        $this->search = request()->query('search', '');
    }

    public function getRecordsProperty(): LengthAwarePaginator
    {
        $query = Booking::query()
            ->with(['schedule.trip'])
            ->whereIn('follow_up_status', ['refund_processed', 'refund_completed'])
            ->latest('updated_at');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('booking_code', 'like', "%{$this->search}%")
                  ->orWhere('customer_name', 'like', "%{$this->search}%");
            });
        }

        return $query->paginate(15)->withQueryString();
    }
}
