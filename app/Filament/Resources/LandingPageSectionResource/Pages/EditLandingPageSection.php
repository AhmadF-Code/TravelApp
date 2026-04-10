<?php

namespace App\Filament\Resources\LandingPageSectionResource\Pages;

use App\Filament\Resources\LandingPageSectionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLandingPageSection extends EditRecord
{
    protected static string $resource = LandingPageSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
