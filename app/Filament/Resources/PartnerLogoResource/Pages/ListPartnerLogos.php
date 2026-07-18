<?php

namespace App\Filament\Resources\PartnerLogoResource\Pages;

use App\Filament\Resources\PartnerLogoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPartnerLogos extends ListRecords
{
    protected static string $resource = PartnerLogoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
