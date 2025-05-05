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
            $isSiswa = $user->hasRole('Siswa');

            if ($isSiswa) {
                // Ambil data siswa berdasarkan username (diasumsikan sama dengan NISN)
                $siswa = \App\HasCurrentSiswa::getCurrentSiswa();

                if (!$siswa) {
                    throw new \Exception("Data siswa tidak ditemukan.");
                }

                $data['nisn'] = $siswa->nisn;
            } else {
                // Admin/SuperAdmin: pastikan 'nisn' tersedia dari form
                if (!isset($data['nisn'])) {
                    throw new \Exception("NISN wajib diisi oleh admin.");
                }

                $siswa = Siswa::where('nisn', $data['nisn'])->first();
                if (!$siswa) {
                    throw new \Exception("Siswa dengan NISN {$data['nisn']} tidak ditemukan.");
                }
            }

            // Validasi dan ambil kategori
            $kategori = FileKategori::find($data['file_kategori_id']);
            if (!$kategori) {
                throw new \Exception("Kategori file tidak ditemukan.");
            }

            // Ambil file dari disk lokal
            $storedFilename = $data['file']; // nama file sementara di storage
            $storedFilePath = Storage::disk('public')->path($storedFilename);

            if (!file_exists($storedFilePath)) {
                throw new \Exception("File tidak ditemukan di penyimpanan lokal.");
            }

            // Buat nama file akhir
            $kategoriSlug = Str::slug($kategori->nama);
            $namaSlug = Str::slug($siswa->nama ?? 'siswa');
            $ext = pathinfo($storedFilename, PATHINFO_EXTENSION);
            $namaFileFinal = "{$kategoriSlug}_{$siswa->nisn}_{$namaSlug}.{$ext}";

            // Upload ke Google Drive
            $uploadResult = GoogleDriveHelper::uploadFile(
                $storedFilePath,
                $namaFileFinal,
                $kategori->folder_id
            );

            // Simpan info ke database
            $data['nama_file'] = $namaFileFinal;
            $data['google_drive_url'] = $uploadResult['web_view_link'] ?? null;
            $data['google_drive_file_id'] = $uploadResult['id'] ?? null;

            // Hapus file dari lokal
            Storage::disk('public')->delete($storedFilename);

            // Hapus dari array agar tidak disimpan ke DB
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
