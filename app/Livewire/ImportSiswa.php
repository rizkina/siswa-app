<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SiswaImport;
use App\Exports\SiswaExport;
use Illuminate\Support\Facades\Storage;

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

        $import = new SiswaImport();
        Excel::import($import, $this->file->getRealPath());

        $this->importSummary = [
            'total' => $import->totalRows,
            'berhasil' => $import->importedRows,
            'gagal' => $import->failedRows,
        ];

        session()->flash('message', "Total: {$import->totalRows}, Berhasil: {$import->importedRows}, Gagal: {$import->failedRows}");
    }

    public function export_excel()
    {
        // return response()->streamDownload(function () {
        //     Excel::store(new SiswaExport, 'siswa.xlsx', 'local');
        // }, 'siswa.xlsx');
    }
}
