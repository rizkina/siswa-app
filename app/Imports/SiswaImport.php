<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\Ibu;
use App\Models\Ayah;
use App\Models\Agama;
use App\Models\Jurusan;
use App\Models\Pendidikan;
use App\Models\Pekerjaan;
use App\Models\Penghasilan;
use App\Models\Kelas;
use App\Models\TahunPelajaran;
use App\Models\Tingkat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;


class SiswaImport implements ToCollection, WithHeadingRow
{
    public $sukses = 0;
    public $gagal = 0;
    public $totalBaris = 0;
    public $gagalData = [];
    protected $tahunPelajaranAktif;

    public function __construct()
    {
        // $this->tahunPelajaranAktif = TahunPelajaran::where('status', 'aktif')->first();
        $this->tahunPelajaranAktif = TahunPelajaran::getTahunAktif();
    }

    public function collection(Collection $rows)
    {
        // Hapus baris pertama (header) jika ada
        // $rows->shift();
        
        // Debugging untuk memastikan header sudah terhapus
        // dd($rows->take(5));
        
        $rows = $rows->toArray();
        $rows = collect($rows);
        // dd($rows->take(5));
        // dd(TahunPelajaran::getTahunAktif());

        // Ambil semua referensi untuk menghindari query berulang
        $existingAgama = Agama::pluck('id', 'agama');
        $existingTingkat = Tingkat::pluck('id', 'tingkat');
        $existingJurusan = Jurusan::pluck('id', 'kode_jurusan');
        $existingKelas = Kelas::pluck('id', 'kelas');
        
        $batchSiswa = [];
        $batchIbu = [];
        $batchAyah = [];

        
        foreach ($rows as $index => $row) {
            if (!is_array($row)) {
                continue; // Lewati baris jika bukan array
            }
    
            // Simpan total baris
            $this->totalBaris++;
            $row['nipd'] = (string) $row['nipd'];
            $row['tingkat'] = (string) $row['tingkat']; 

            try {
                // validari data input
                $validator = Validator::make($row, [
                    'nisn' => 'required|numeric|digits:10', // NISN
                    'nipd' => 'required|string', // NIPD
                    'nik' => 'required|numeric|digits:16', // NIK
                    'nama' => 'required|string', // Nama
                    'jns_kelamin' => 'nullable|in:L,P', // Jenis Kelamin
                    'tempat_lahir' => 'nullable|string', // Tempat Lahir
                    'tanggal_lahir' => 'nullable|date', // Tanggal Lahir
                    'agama' => 'nullable|string', // Agama
                    'alamat' => 'nullable|string', // Alamat
                    'nama_ibu' => 'nullable|string', // Nama Ibu
                    'nik_ibu' => 'nullable|numeric|digits:16', // NIK Ibu
                    'tahun_lahiribu' => 'nullable|numeric|digits:4', // Tahun Lahir Ibu
                    'pendidikan_ibu' => 'nullable|string', // Pendidikan Ibu
                    'pekerjaan_ibu' => 'nullable|string', // Pekerjaan Ibu
                    'penghasialan_ibu' => 'nullable|string', // Penghasilan Ibu
                    'nama_iyah' => 'nullable|string', // Nama Ayah
                    'nik_iyah' => 'nullable|numeric|digits:16', // NIK Ayah
                    'tahun_lahirayah' => 'nullable|numeric|digits:4', // Tahun Lahir Ayah
                    'pendidikan_ayah' => 'nullable|string', // Pendidikan Ayah
                    'pekerjaan_ayah' => 'nullable|string', // Pekerjaan Ayah
                    'penghasialan_ayah' => 'nullable|string', // Penghasilan Ayah
                    'tingkat' => 'nullable|string', // Tingkat
                    'jurusan' => 'nullable|string', // Jurusan
                    'kelas' => 'nullable|string', // Kelas
                ]);

                if ($validator->fails()) {
                    throw new \Exception($validator->errors()->first());
                }

               // Cek apakah siswa dengan NISN sudah ada
               if (Siswa::where('nisn', $row['nisn'])->exists()) {
                    throw new \Exception("Siswa dengan NISN {$row['nisn']} sudah ada");
                }

                // Ambil atau buat data yang belum ada
                $agamaId = $existingAgama[$row['agama']] ?? null;
                $tingkatId = $existingTingkat[$row['tingkat']] ?? Tingkat::firstOrCreate
                ([
                    'tingkat' => $row['tingkat']
                ])->id;
                // Cek apakah jurusan sudah ada
                $jurusanId = $existingJurusan[$row['jurusan']] ?? Jurusan::firstOrCreate
                ([
                    'kode_jurusan' => $row['jurusan'], 
                    'nama_jurusan' => $row['jurusan'], 
                    'kurikulum' => 'Merdeka'
                ])->id;
                // Cek apakah jurusan sudah ada
                $kelasId = $existingKelas[$row['kelas']] ?? Kelas::firstOrCreate
                ([
                    'kelas' => $row['kelas'], 
                    'id_tingkat' => $tingkatId, 
                    'id_jurusan' => $jurusanId, 
                    'id_tahun_pelajaran' => $this->tahunPelajaranAktif->id
                ])->id;

                // Tambah data siswa ke dalam batch
                $batchSiswa[] = [
                    'nisn' => $row['nisn'],
                    'nipd' => $row['nipd'],
                    'nik' => $row['nik'],
                    'nama' => $row['nama'],
                    'jns_kelamin' => $row['jns_kelamin'],
                    'tempat_lahir' => $row['tempat_lahir'],
                    'tanggal_lahir' => $row['tanggal_lahir'],
                    'agama_id' => $agamaId,
                    'alamat' => $row['alamat'],
                    'id_kelas' => $kelasId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                // Tambah data ibu ke dalam batch
                $batchIbu[] = [
                    'nisn' => $row['nisn'],
                    'nama' => $row['nama_ibu'],
                    'nik' => $row['nik_ibu'],
                    'tahun_lahir' => $row['tahun_lahiribu'],
                    'pendidikan_id' => Pendidikan::where('pendidikan', $row['pendidikan_ibu'])->value('id_pendidikan'),
                    'pekerjaan_id' => Pekerjaan::where('pekerjaan', $row['pekerjaan_ibu'])->value('id_pekerjaan'),
                    'penghasilan_id' => Penghasilan::where('penghasilan', $row['penghasilan_ibu'])->value('id_penghasilan'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                // Tambah data ayah ke dalam batch
                $batchAyah[] = [
                    'nisn' => $row['nisn'],
                    'nama' => $row['nama_ayah'],
                    'nik' => $row['nik_ayah'],
                    'tahun_lahir' => $row['tahun_lahirayah'],
                    'pendidikan_id' => Pendidikan::where('pendidikan', $row['pendidikan_ayah'])->value('id_pendidikan'),
                    'pekerjaan_id' => Pekerjaan::where('pekerjaan', $row['pekerjaan_ayah'])->value('id_pekerjaan'),
                    'penghasilan_id' => Penghasilan::where('penghasilan', $row['penghasilan_ayah'])->value('id_penghasilan'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                // dd($batchSiswa, $batchIbu, $batchAyah);

                $this->sukses++;
                
            } catch (\Exception $e) {
                // Jika gagal, tambah counter gagal dan simpan data gagal
                $this->gagal++;
                $this->gagalData[] = [
                    'baris' => $index + 1,
                    'data'  => $row,
                    'error' => $e->getMessage()
                ];
                // dd($this->gagal);
                // dd($this->gagalData);
                // Simpan log error
                Log::error("Gagal import data siswa di baris ke-" . ($index + 1) . " : " . $e->getMessage());
            }
        }

        // Bulk insert data siswa, ibu, dan ayah
        DB::beginTransaction();
        try {
            // Insert data siswa, ibu, dan ayah ke dalam database
            if (!empty($batchSiswa)) {
                Siswa::insert($batchSiswa);
                $insertedSiswa = Siswa::whereIn('nisn', array_column($batchSiswa, 'nisn'))->get();
                foreach ($insertedSiswa as $siswa) {
                    app(\App\Observers\SiswaObserver::class)->created($siswa);
                }
            }
            
            if (!empty($batchIbu)) Ibu::insert($batchIbu);
            if (!empty($batchAyah)) Ayah::insert($batchAyah);
            
            DB::commit();
            Log::info("Data siswa berhasil diimpor: {$this->sukses} berhasil, {$this->gagal} gagal");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal menyimpan data siswa: " . $e->getMessage());
            // dd($e->getMessage());
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
        dd($this->getHasilImport());
    }
}
