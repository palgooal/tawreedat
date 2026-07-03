<?php

namespace App\Filament\Resources\CompanyRegistrationRequestResource\Pages;

use App\Filament\Resources\CompanyRegistrationRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCompanyRegistrationRequests extends ListRecords
{
    protected static string $resource = CompanyRegistrationRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
