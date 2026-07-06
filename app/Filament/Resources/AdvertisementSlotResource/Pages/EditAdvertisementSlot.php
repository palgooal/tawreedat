<?php

namespace App\Filament\Resources\AdvertisementSlotResource\Pages;

use App\Filament\Resources\AdvertisementSlotResource;
use Filament\Resources\Pages\EditRecord;

class EditAdvertisementSlot extends EditRecord
{
    protected static string $resource = AdvertisementSlotResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
