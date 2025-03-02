<?php

namespace App\Imports;

use App\Models\Siswa;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Illuminate\Support\Facades\Log;

class SiswaImport implements ToModel, WithHeadingRow, WithEvents
{
    public $totalRows = 0;
    public $importedRows = 0;
    public $failedRows = 0;

    public function model(array $row)
    {
        try {
            Siswa::create([
                'nisn' => $row['nisn'] ?? null,
                'nama' => $row['nama'] ?? null,
                'kelas' => $row['kelas'] ?? null,
                'ayah_nama' => $row['ayah_nama'] ?? null,
                'ibu_nama' => $row['ibu_nama'] ?? null,
            ]);

            $this->importedRows++; // Tambah hitungan berhasil
        } catch (\Exception $e) {
            Log::error("Gagal import data: " . $e->getMessage());
            $this->failedRows++; // Tambah hitungan gagal
        }
    }

    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function (BeforeImport $event) {
                $this->totalRows = count($event->getReader()->getSheet(0)->toArray());
            },
        ];
    }
}
