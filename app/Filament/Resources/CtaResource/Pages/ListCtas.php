<?php

namespace App\Filament\Resources\CtaResource\Pages;

use App\Filament\Resources\CtaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCtas extends ListRecords
{
    protected static string $resource = CtaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
