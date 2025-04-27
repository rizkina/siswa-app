<?php

namespace App\Filament\Resources\FileUploadResource\Pages;

use App\Filament\Resources\FileUploadResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Siswa;
use App\Models\FileKategori;
use Illuminate\Support\Facades\Auth;
use App\HasCurrentSiswa;


class CreateFileUpload extends CreateRecord
{
    use HasCurrentSiswa;
    protected static string $resource = FileUploadResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // $siswa = Siswa::where('nisn', Auth::user()->username)->first();
        $siswa = static::getCurrentSiswa();
        $kategori = FileKategori::find($data['file_kategori_id']);

        if ($siswa) {
            $data['nisn'] = $siswa->nisn;
        }

       
        if (isset($data['file_kategori_id']) && $siswa && isset($data['file'])) {
            $kategori = \App\Models\FileKategori::find($data['file_kategori_id']);
            $kategoriNama = Str::slug($kategori?->nama ?? 'file');
            $namaSiswa = Str::slug($siswa->nama ?? 'noname');
    
            $newFileName = "{$kategoriNama}_{$siswa->nisn}_{$namaSiswa}";
    
            $uploadedFile = $data['file'];
    
            // Cek kalau file sudah ada
            if (Storage::disk('public')->exists($uploadedFile)) {
                $fileContent = Storage::disk('public')->get($uploadedFile);
                
                // Upload ke Google Drive
                $googleDrivePath = 'uploads/' . $newFileName . '.' . pathinfo($uploadedFile, PATHINFO_EXTENSION);
    
                Storage::disk('google')->put($googleDrivePath, $fileContent);
    
                // Setelah upload ke Google Drive, kita bisa simpan path Google Drive
                $data['path'] = $googleDrivePath; // 'uploads/namafile.pdf'
    
                // Kalau mau hapus file lokal setelah upload, bisa
                Storage::disk('public')->delete($uploadedFile);
            }
    
            // Simpan nama file ke database
            $data['nama_file'] = $newFileName;
        }

        return $data;
    }
}
