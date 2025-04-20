<?php

namespace App\Filament\Resources\FileKategoriResource\Pages;

use App\Filament\Resources\FileKategoriResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFileKategoris extends ListRecords
{
    protected static string $resource = FileKategoriResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
