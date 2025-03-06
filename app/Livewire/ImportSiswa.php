<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SiswaImport;
use App\Exports\SiswaExport;
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
            $import = new SiswaImport();
            Excel::import($import, $this->file->getRealPath());

            // Tambahkan debugging
            // dd($import->getHasilImport());

            // Ambil hasil import dari getHasilImport() pada class import
            $this->importSummary = $import->getHasilImport();
            // Simpan ke session supaya bisa diakses di halaman blade
            session()->flash('importSummary', $this->importSummary);

            // Pastikan data sukses bertambah, kalau tidak ada berarti gagal
            if ($this->importSummary['sukses'] > 0 || $this->importSummary['gagal'] > 0) {
                $this->dispatch('showMessage', [
                    'type' => 'success',
                    'message' => "Total: {$this->importSummary['total']} baris, Sukses: {$this->importSummary['sukses']} baris, Gagal: {$this->importSummary['gagal']} baris"
                ]);
            } else {
                throw new \Exception("Tidak ada data yang diproses. Periksa format file.");
            }

            // Tampilkan pesan notifikasi pada flash
            // session()->flash('message', "Total: {$this->importSummary['totalBaris']} baris, Sukses: {$this->importSummary['sukses']} baris, Gagal: {$this->importSummary['gagal']} baris");

            // **Tambahkan reset file setelah sukses**
            $this->reset('file');
        } catch (\Exception $e) {
            $this->dispatch('showMessage', [
                'type' => 'error',
                'message' => 'Terjadi kesalahan saat mengimport file: ' . $e->getMessage()
            ]);
            Log::error('Gagal import data siswa: ' . $e->getMessage());
        }
    }


    public function export_excel()
    {
        // return response()->streamDownload(function () {
        //     Excel::store(new SiswaExport, 'siswa.xlsx', 'local');
        // }, 'siswa.xlsx');
    }
}
