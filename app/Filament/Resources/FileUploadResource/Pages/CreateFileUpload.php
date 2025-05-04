<?php

namespace App\Filament\Resources\FileUploadResource\Pages;

use App\Filament\Resources\FileUploadResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Siswa;
use App\Models\FileKategori;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Helpers\GoogleDriveHelper;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class CreateFileUpload extends CreateRecord
{
    protected static string $resource = FileUploadResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        try {
            $user = Auth::user();
            $siswa = \App\HasCurrentSiswa::getCurrentSiswa();

            if (!$siswa) {
                throw new \Exception("Data siswa tidak ditemukan.");
            }

            $kategori = FileKategori::find($data['file_kategori_id']);
            if (!$kategori) {
                throw new \Exception("Kategori file tidak ditemukan.");
            }

            // Ambil nama file yang sudah di-upload ke disk 'public'
            $storedFilename = $data['file']; // Contoh: "abc123xyz.pdf"
            $storedFilePath = Storage::disk('public')->path($storedFilename);

            if (!file_exists($storedFilePath)) {
                throw new \Exception("File tidak ditemukan di penyimpanan lokal.");
            }

            // Siapkan nama file final yang akan digunakan di Google Drive
            $kategoriSlug = Str::slug($kategori->nama);
            $namaSlug = Str::slug($siswa->nama);
            $ext = pathinfo($storedFilename, PATHINFO_EXTENSION);
            $namaFileFinal = "{$kategoriSlug}_{$siswa->nisn}_{$namaSlug}.{$ext}";

            // Upload ke Google Drive
            $uploadResult = GoogleDriveHelper::uploadFile(
                $storedFilePath,
                $namaFileFinal,
                $kategori->folder_id
            );

            // Simpan data yang diperlukan ke database
            $data['nisn'] = $siswa->nisn;
            $data['nama_file'] = $namaFileFinal;
            $data['google_drive_url'] = $uploadResult['web_view_link'] ?? null;
            $data['google_drive_file_id'] = $uploadResult['id'] ?? null;

            // Hapus file lokal setelah berhasil upload
            Storage::disk('public')->delete($storedFilename);

            // Hilangkan field 'file' agar tidak disimpan ulang ke lokal
            unset($data['file']);

        } catch (\Exception $e) {
            Notification::make()
                ->title('Gagal Upload')
                ->body($e->getMessage())
                ->danger()
                ->send();

            throw $e;
        }

        return $data;
    }
}
