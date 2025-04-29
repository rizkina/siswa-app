<?php

namespace App\Filament\Resources\FileKategoriResource\Pages;

use App\Filament\Resources\FileKategoriResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Helpers\GoogleDriveHelper;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use App\Models\FileKategori;

class CreateFileKategori extends CreateRecord
{
    protected static string $resource = FileKategoriResource::class;

    protected ?Model $existingKategori = null;
    protected bool $folderAlreadyExists = false;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $folderName = $data['nama'];

        // Cegah nama duplikat pada tabel meskipun folder_id berbeda
        $existingName = FileKategori::where('nama', $folderName)->first();
        if ($existingName) {
            Notification::make()
                ->title('Nama Kategori Duplikat')
                ->body('Kategori dengan nama tersebut sudah ada di database.')
                ->danger()
                ->send();
                // Hentikan proses pembuatan tanpa error
            $this->halt();
            // abort(400, 'Kategori dengan nama tersebut sudah ada.');
        }

        $folder = GoogleDriveHelper::createFolder($folderName);

        if (!$folder) {
            Notification::make()
                ->title('Gagal membuat folder')
                ->body('Terjadi kesalahan saat membuat folder di Google Drive.')
                ->danger()
                ->send();
            // abort(500, 'Gagal membuat folder Google Drive.');
            // Hentikan proses pembuatan tanpa error
            $this->halt();  
        }

        $data['folder_id'] = $folder['id'];
        $this->folderAlreadyExists = $folder['is_existing'];

        // Cek apakah sudah ada record dengan nama dan folder_id yang sama
        $existing = FileKategori::where('nama', $folderName)
            ->where('folder_id', $folder['id'])
            ->first();

        if ($existing) {
            $this->existingKategori = $existing;
        }

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        if ($this->existingKategori) {
            return $this->existingKategori;
        }

        return FileKategori::create($data);
    }

    protected function afterCreate(): void
    {
        if ($this->existingKategori) {
            Notification::make()
                ->title('Kategori Sudah Ada')
                ->body('Folder sudah ada di database. Data tidak dibuat ulang.')
                ->info()
                ->send();
        } elseif ($this->folderAlreadyExists) {
            Notification::make()
                ->title('Folder Google Drive Sudah Ada')
                ->body('Folder tidak dibuat ulang. Data kategori berhasil disimpan.')
                ->info()
                ->send();
        } else {
            Notification::make()
                ->title('Berhasil')
                ->body('Kategori dan folder berhasil dibuat.')
                ->success()
                ->send();
        }
    }
}
