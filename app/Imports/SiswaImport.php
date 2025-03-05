<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\Agama;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;

class SiswaImport implements ToCollection
{
    public $sukses = 0;
    public $gagal = 0;
    public $totalBaris = 0;
    public $gagalData = [];

    public function collection(Collection $rows)
    {
        // Hapus header jika ada
        $rows->shift();

        // Simpan total baris
        $this->totalBaris = $rows->count();

        foreach ($rows as $index => $row) {
            try {
                // Pastikan data minimal memiliki kolom yang dibutuhkan
                if (!isset($row[0]) || !isset($row[1]) || !isset($row[2]) || !isset($row[3])) {
                    throw new \Exception("Data tidak lengkap di baris ke-" . ($index + 1));
                }

                // Cari ID Agama (jika ada)
                $agamaId = null;
                if (!empty($row[7])) {
                    $agama = Agama::where('id_agama', $row[7])->first();
                    $agamaId = $agama ? $agama->id_agama : null;
                }

                // Cek apakah data sudah ada berdasarkan NISN
                $existingSiswa = Siswa::where('nisn', $row[0])->first();
                if ($existingSiswa) {
                    // Lewati data yang sudah ada (bukan dianggap gagal)
                    continue;
                }

                // Simpan data
                Siswa::create([
                    'nisn'          => $row[0] ?? null,
                    'nipd'          => $row[1] ?? null,
                    'nik'           => $row[2] ?? null,
                    'nama'          => $row[3] ?? null,
                    'jns_kelamin'   => $row[4] ?? null,
                    'tempat_lahir'  => $row[5] ?? null,
                    'tanggal_lahir' => $row[6] ?? null,
                    'agama_id'      => $agamaId,
                    'alamat'        => $row[8] ?? null,
                ]);

                // Jika berhasil, tambah counter sukses
                $this->sukses++;
            } catch (\Exception $e) {
                // Jika gagal, tambah counter gagal dan simpan data gagal
                $this->gagal++;
                $this->gagalData[] = [
                    'baris' => $index + 1,
                    'data'  => $row->toArray(),
                    'error' => $e->getMessage()
                ];

                // Simpan log error
                Log::error("Gagal import data siswa di baris ke-" . ($index + 1) . " : " . $e->getMessage());
            }
        }
    }

    public function getHasilImport()
    {
        return [
            'total'      => $this->totalBaris,
            'sukses'     => $this->sukses,
            'gagal'      => $this->gagal,
            'gagalData'  => $this->gagalData,
        ];
    }
}
