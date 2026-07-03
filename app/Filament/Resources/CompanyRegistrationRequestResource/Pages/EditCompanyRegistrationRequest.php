<?php

namespace App\Filament\Resources\CompanyRegistrationRequestResource\Pages;

use App\Filament\Resources\CompanyRegistrationRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCompanyRegistrationRequest extends EditRecord
{
    protected static string $resource = CompanyRegistrationRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
