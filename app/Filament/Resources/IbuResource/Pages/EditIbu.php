<?php

namespace App\Filament\Resources\IbuResource\Pages;

use App\Filament\Resources\IbuResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditIbu extends EditRecord
{
    protected static string $resource = IbuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
