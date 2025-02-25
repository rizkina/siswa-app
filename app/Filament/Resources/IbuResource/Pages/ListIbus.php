<?php

namespace App\Filament\Resources\IbuResource\Pages;

use App\Filament\Resources\IbuResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListIbus extends ListRecords
{
    protected static string $resource = IbuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
