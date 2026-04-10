<?php

namespace App\Filament\Resources\RefundBookingResource\Pages;

use App\Filament\Resources\RefundBookingResource;
use Filament\Resources\Pages\ListRecords;

class ListRefundBookings extends ListRecords
{
    protected static string $resource = RefundBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
