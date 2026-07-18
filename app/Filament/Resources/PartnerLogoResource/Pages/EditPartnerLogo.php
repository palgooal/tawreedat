<?php

namespace App\Filament\Resources\PartnerLogoResource\Pages;

use App\Filament\Resources\PartnerLogoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPartnerLogo extends EditRecord
{
    protected static string $resource = PartnerLogoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
