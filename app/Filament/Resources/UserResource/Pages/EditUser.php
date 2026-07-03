<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Explicit ->visible() on top of the resource's own canDelete()
            // gating (belt and suspenders): this is the one place in the
            // admin where clicking the wrong button deletes an admin
            // account, so it gets an extra, independent check here rather
            // than relying solely on the resource-level authorization wiring.
            Actions\DeleteAction::make()
                ->visible(fn (): bool => UserResource::canDelete($this->getRecord())),
        ];
    }
}
