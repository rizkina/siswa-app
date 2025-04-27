<?php

namespace App\Filament\Resources\FileKategoriResource\Pages;

use App\Filament\Resources\FileKategoriResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Helpers\GoogleDriveHelper;
use Filament\Notifications\Notification;

class CreateFileKategori extends CreateRecord
{
    protected static string $resource = FileKategoriResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Nama folder dari input form
        $folderName = $data['nama'];

        // Pakai GoogleDriveHelper untuk buat/cari folder
        $folderId = GoogleDriveHelper::createFolder($folderName);

        // Tambahkan ID folder ke data sebelum simpan
        $data['folder_id'] = $folderId;

        return $data;
    }

    protected function afterCreate(): void
    {
        Notification::make()
            ->title('Folder berhasil dibuat')
            ->body('Folder Google Drive telah dibuat dan data kategori disimpan.')
            ->success()
            ->send();
    }
}
