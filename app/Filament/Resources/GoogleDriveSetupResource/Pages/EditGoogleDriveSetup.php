<?php

namespace App\Filament\Resources\GoogleDriveSetupResource\Pages;

use App\Filament\Resources\GoogleDriveSetupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGoogleDriveSetup extends EditRecord
{
    protected static string $resource = GoogleDriveSetupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
