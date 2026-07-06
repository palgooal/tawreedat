<?php

namespace App\Filament\Resources\AdvertisementSlotResource\Pages;

use App\Filament\Resources\AdvertisementSlotResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAdvertisementSlots extends ListRecords
{
    protected static string $resource = AdvertisementSlotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
