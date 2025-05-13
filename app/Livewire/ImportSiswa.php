<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SiswaImport;
use Illuminate\Support\Facades\Log;

class ImportSiswa extends Component
{
    use WithFileUploads;

    public $file;
    public $importSummary = [];

    public function render()
    {
        return view('livewire.import-siswa');
    }

    public function import_excel()
    {
        $this->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        try {
            // Ambil nama asli file
            $originalName = $this->file->getClientOriginalName();
            
            // Tentukan target path penyimpanan
            $targetPath = storage_path('app/private/file_uploads/' . $originalName);
            
            // Simpan file ke lokasi sementara
            $tempPath = $this->file->getRealPath();
            
            // Salin ke lokasi permanen
            copy($tempPath, $targetPath);
            Log::info('Cek file import:', [
                'original_name' => $originalName,
                'target_path' => $targetPath,
                'exists' => file_exists($targetPath)
            ]);
    
            $import = new SiswaImport();
            Excel::import($import, $targetPath);

            $this->importSummary = $import->getHasilImport();
            session()->flash('importSummary', $this->importSummary);

            if ($this->importSummary['sukses'] > 0 || $this->importSummary['gagal'] > 0) {
                $this->dispatch('showMessage', [
                    'type' => 'success',
                    'message' => "Total: {$this->importSummary['total']} baris, Sukses: {$this->importSummary['sukses']} baris, Gagal: {$this->importSummary['gagal']} baris"
                ]);
            } else {
                session()->flash('error', 'Tidak ada data yang diproses. Periksa format file dan pastikan header sesuai template.');
                throw new \Exception("Tidak ada data yang diproses. Periksa format file dan pastikan header sesuai template.");
            }

            $this->reset('file');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            // Log::error('Validasi Excel gagal: ' . json_encode($failures));

            // $this->dispatch('showMessage', [
            //     'type' => 'error',
            //     'message' => 'Validasi data gagal. Periksa format dan isi file.'
            // ]);
            $errorMessages = [];
            foreach ($failures as $failure) {
                $errorMessages[] = "Baris {$failure->row()}: {$failure->errors()[0]}";
            }
            $this->dispatch('showMessage', [
                'type' => 'error',
               'message' => 'Validasi data gagal: ' . implode(', ', $errorMessages)
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal import data siswa: ' . $e->getMessage());

            $this->dispatch('showMessage', [
                'type' => 'error',
                'message' => 'Terjadi kesalahan saat mengimport file: ' . $e->getMessage()
            ]);
        }
    }
}