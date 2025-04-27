<?php

namespace App\Filament\Resources\FileUploadResource\Pages;

use App\Filament\Resources\FileUploadResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Siswa;
use App\Models\FileKategori;
use Illuminate\Support\Facades\Auth;
use App\HasCurrentSiswa;

class EditFileUpload extends EditRecord
{
    use HasCurrentSiswa;
    protected static string $resource = FileUploadResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $record = $this->record; // Data lama
        $siswa = static::getCurrentSiswa();

        if (isset($data['file']) && $siswa) {
            $kategori = \App\Models\FileKategori::find($data['file_kategori_id']);
            $kategoriNama = Str::slug($kategori?->nama ?? 'file');
            $namaSiswa = Str::slug($siswa->nama ?? 'noname');

            $newFileName = "{$kategoriNama}_{$siswa->nisn}_{$namaSiswa}";

            $uploadedFile = $data['file'];

            // Cek kalau user upload file baru (bukan path lama)
            if (is_string($uploadedFile) && Storage::disk('public')->exists($uploadedFile)) {
                $fileContent = Storage::disk('public')->get($uploadedFile);

                $googleDrivePath = 'uploads/' . $newFileName . '.' . pathinfo($uploadedFile, PATHINFO_EXTENSION);

                // Upload file baru ke Google Drive
                Storage::disk('google')->put($googleDrivePath, $fileContent);

                // Hapus file lokal kalau mau
                Storage::disk('public')->delete($uploadedFile);

                // Hapus file lama di Google Drive (opsional, pastikan path lama disimpan di database)
                if (!empty($record->path) && Storage::disk('google')->exists($record->path)) {
                    Storage::disk('google')->delete($record->path);
                }

                // Update path baru di database
                $data['path'] = $googleDrivePath;
            } else {
                // Tidak upload file baru, tetap pakai path lama
                $data['path'] = $record->path;
            }

            $data['nama_file'] = $newFileName;
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
