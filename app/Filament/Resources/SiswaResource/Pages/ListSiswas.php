<?php

namespace App\Filament\Resources\SiswaResource\Pages;

use App\Filament\Resources\SiswaResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListSiswas extends ListRecords
{
    protected static string $resource = SiswaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('import')
                ->color('success')
                ->icon('heroicon-s-cloud-arrow-up')
                ->url(route('import_siswa'))
                ->label('Import'),
            Actions\CreateAction::make(),
        ];
    }
}
