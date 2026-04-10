<?php

namespace App\Filament\Resources\FollowUpBookingResource\Pages;

use App\Filament\Resources\FollowUpBookingResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageFollowUpBookings extends ManageRecords
{
    protected static string $resource = FollowUpBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
