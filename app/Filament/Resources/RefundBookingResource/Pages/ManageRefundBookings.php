<?php

namespace App\Filament\Resources\RefundBookingResource\Pages;

use App\Filament\Resources\RefundBookingResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageRefundBookings extends ManageRecords
{
    protected static string $resource = RefundBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
