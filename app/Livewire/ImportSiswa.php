<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SiswaImport;
use App\Exports\SiswaExport;
<<<<<<< HEAD
use Illuminate\Support\Facades\Log;
=======
use Illuminate\Support\Facades\Storage;
>>>>>>> f1a31d58d9967a29ceba2fce99bfd0feb9d0cfb0

class ImportSiswa extends Component
{
    use WithFileUploads;

    public $file;
<<<<<<< HEAD
    public $importSummary;
=======
    public $importSummary = [];
>>>>>>> f1a31d58d9967a29ceba2fce99bfd0feb9d0cfb0

    public function render()
    {
        return view('livewire.import-siswa');
    }

    public function import_excel()
    {
        $this->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

<<<<<<< HEAD
        try {
            $import = new SiswaImport();
            Excel::import($import, $this->file->getRealPath());

            // Tambahkan debugging
            // dd($import->getHasilImport());

            // Ambil hasil import dari getHasilImport() pada class import
            $this->importSummary = $import->getHasilImport();
            // Simpan ke session supaya bisa diakses di halaman blade
            session()->flash('importSummary', $this->importSummary);

            // Tampilkan pesan notifikasi pada flash
            session()->flash('message', "Total: {$this->importSummary['totalBaris']} baris, Sukses: {$this->importSummary['sukses']} baris, Gagal: {$this->importSummary['gagal']} baris");

            // **Tambahkan reset file setelah sukses**
            $this->reset('file');
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan saat mengimport file.');
            Log::error('Gagal import data siswa : ' . $e->getMessage());
        }
    }


=======
        $import = new SiswaImport();
        Excel::import($import, $this->file->getRealPath());

        $this->importSummary = [
            'total' => $import->totalRows,
            'berhasil' => $import->importedRows,
            'gagal' => $import->failedRows,
        ];

        session()->flash('message', "Total: {$import->totalRows}, Berhasil: {$import->importedRows}, Gagal: {$import->failedRows}");
    }

>>>>>>> f1a31d58d9967a29ceba2fce99bfd0feb9d0cfb0
    public function export_excel()
    {
        // return response()->streamDownload(function () {
        //     Excel::store(new SiswaExport, 'siswa.xlsx', 'local');
        // }, 'siswa.xlsx');
    }
}
