<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\Ibu;
use App\Models\Ayah;
use App\Models\Agama;
use App\Models\Pendidikan;
use App\Models\Pekerjaan;
use App\Models\Penghasilan;
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
                if (empty($row[0]) || empty($row[1]) || empty($row[2]) || empty($row[3])) {
                    throw new \Exception("Data tidak lengkap di baris ke-" . ($index + 1));
                }

                // Cari ID Agama (jika ada)
                $agamaId = null;
                if (!empty($row[7])) {
                    $agama = Agama::find($row[7]);
                    $agamaId = $agama ? $agama->id_agama : null;
                }

                // Cek apakah data sudah ada berdasarkan NISN
                $existingSiswa = Siswa::where('nisn', $row[0])->exists();
                if ($existingSiswa) {
                    // Lewati data yang sudah ada (bukan dianggap gagal)
                    $this->gagal++;
                    $this->gagalData[] = [
                        'baris' => $index + 1,
                        'data'  => $row->toArray(),
                        'error' => 'Data sudah ada'
                    ];
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

                $pendidikanIb = null;
                $pendidikanAy = null;
                if (!empty($row[12]) || !empty($row[18])) {
                    $pendidikanIbu = Pendidikan::find($row[12]);
                    $pendidikanIb = $pendidikanIbu ? $pendidikanIbu->id_pendidikan : null;
                    $pendidikanAyah = Pendidikan::find($row[18]);
                    $pendidikanAy = $pendidikanAyah ? $pendidikanAyah->id_pendidikan : null;
                }

                $pekerjaanIb = null;
                $pekerjaanAy = null;
                if (!empty($row[13]) || !empty($row[19])) {
                    $pekerjaanIbu = Pekerjaan::find($row[13]);
                    $pekerjaanIb = $pekerjaanIbu ? $pekerjaanIbu->id_pekerjaan : null;
                    $pekerjaanAyah = Pekerjaan::find($row[19]);
                    $pekerjaanAy = $pekerjaanAyah ? $pekerjaanAyah->id_pekerjaan : null;
                }

                $penghasilanIb = null;
                $penghasilanAy = null;
                if (!empty($row[14]) || !empty($row[20])) {
                    $penghasilanIbu = Penghasilan::find($row[14]);
                    $penghasilanIb = $penghasilanIbu ? $penghasilanIbu->id_penghasilan : null;
                    $penghasilanAyah = Penghasilan::find($row[20]);
                    $penghasilanAy = $penghasilanAyah ? $penghasilanAyah->id_penghasilan : null;
                }

                // Simpan data ibu
                Ibu::create([
                    'nisn'          => $row[0] ?? null,
                    'nama'          => $row[9] ?? null,
                    'nik'           => $row[10] ?? null,
                    'tahun_lahir'   => $row[11] ?? null,
                    'pendidikan_id' => $pendidikanIb,
                    'pekerjaan_id'  => $pekerjaanIb,
                    'penghasilan_id' => $penghasilanIb,
                ]);

                // Simpan data ayah
                Ayah::create([
                    'nisn'          => $row[0] ?? null,
                    'nama'          => $row[15] ?? null,
                    'nik'           => $row[16] ?? null,
                    'tahun_lahir'   => $row[17] ?? null,
                    'pendidikan_id' => $pendidikanAy,
                    'pekerjaan_id'  => $pekerjaanAy,
                    'penghasilan_id' => $penghasilanAy,
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
