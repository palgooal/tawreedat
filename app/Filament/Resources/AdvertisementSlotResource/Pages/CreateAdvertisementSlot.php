<?php

namespace App\Filament\Resources\AdvertisementSlotResource\Pages;

use App\Filament\Resources\AdvertisementSlotResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAdvertisementSlot extends CreateRecord
{
    protected static string $resource = AdvertisementSlotResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
