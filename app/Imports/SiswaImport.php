<?php

namespace App\Imports;

use App\Models\{
    Ayah, Ibu, Siswa, Agama, Jurusan, Kelas, Pendidikan,
    Pekerjaan, Penghasilan, TahunPelajaran, Tingkat, User
};
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\{
    ToCollection, WithHeadingRow, WithChunkReading
};
use Illuminate\Contracts\Queue\ShouldQueue;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class SiswaImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    protected $tahunPelajaranAktif;
    protected $referensi;

    public int $sukses = 0;
    public int $gagal = 0;
    public int $totalBaris = 0;
    protected $currentRow = 1;
    public array $gagalData = [];

    protected array $columnMap = [
        'nisn', 'nipd', 'nik', 'nama', 'jns_kelamin', 'tempat_lahir', 'tanggal_lahir', 'agama', 'alamat',
        'nama_ibu', 'nik_ibu', 'tahun_lahiribu', 'pendidikan_ibu', 'pekerjaan_ibu', 'penghasilan_ibu',
        'nama_ayah', 'nik_ayah', 'tahun_lahirayah', 'pendidikan_ayah', 'pekerjaan_ayah', 'penghasilan_ayah',
        'tingkat', 'jurusan', 'kelas'
    ];

    protected array $validationRules = [
        'nisn' => 'required|numeric|digits:10|unique:siswas,nisn',
        'nipd' => 'required|string|max:20',
        'nik' => 'required|numeric|digits:16',
        'nama' => 'required|string|max:100',
        'jns_kelamin' => 'nullable|in:L,P',
        'tempat_lahir' => 'nullable|string',
        'tanggal_lahir' => 'nullable|date',
        'agama' => 'nullable|string',
        'alamat' => 'nullable|string',
        'nama_ibu' => 'nullable|string',
        'nik_ibu' => 'nullable|numeric|digits:16',
        'tahun_lahiribu' => 'nullable|numeric|digits:4',
        'pendidikan_ibu' => 'nullable|string',
        'pekerjaan_ibu' => 'nullable|string',
        'penghasilan_ibu' => 'nullable|string',
        'nama_ayah' => 'nullable|string',
        'nik_ayah' => 'nullable|numeric|digits:16',
        'tahun_lahirayah' => 'nullable|numeric|digits:4',
        'pendidikan_ayah' => 'nullable|string',
        'pekerjaan_ayah' => 'nullable|string',
        'penghasilan_ayah' => 'nullable|string',
        'tingkat' => 'required|string',
        'jurusan' => 'required|string',
        'kelas' => 'required|string',
    ];

    public function __construct()
    {
        $this->tahunPelajaranAktif = TahunPelajaran::getTahunAktif();
        $this->loadReferensi();
    }

    protected function loadReferensi()
    {
        $this->referensi = [
            'agama'       => Agama::pluck('id', 'agama')->mapWithKeys(function($id, $agama) {
                return [strtolower($agama) => $id];}),
            'tingkat'     => Tingkat::pluck('id', 'tingkat')->mapWithKeys(function($id, $tingkat) {
                return [strtolower($tingkat) => $id];}),
            'jurusan'     => Jurusan::pluck('id', 'kode_jurusan')->mapWithKeys(function($id, $kode) {
                return [strtolower($kode) => $id];}),
            'kelas'       => Kelas::pluck('id', 'kelas')->mapWithKeys(function($id, $kelas) {
                return [strtolower($kelas) => $id];}),
            'pendidikan'  => Pendidikan::pluck('id_pendidikan', 'pendidikan')->mapWithKeys(function($id, $pendidikan) { 
                return [strtolower($pendidikan) => $id];}),
            'pekerjaan'   => Pekerjaan::pluck('id_pekerjaan', 'pekerjaan')->mapWithKeys(function($id, $pekerjaan) {
                return [strtolower($pekerjaan) => $id];}),
            'penghasilan' => Penghasilan::pluck('id_penghasilan', 'penghasilan')->mapWithKeys(function($id, $penghasilan) {
                return [strtolower($penghasilan) => $id];}),
        ];
    }

    public function chunkSize(): int
    {
        return 200;
    }

    public function collection(Collection $rows): void
    {
        Log::info('Memproses file import siswa', ['rows' => $rows->count()]);

        if ($rows->isEmpty()) {
            throw new \Exception("File Excel kosong - tidak ada data yang dapat diproses");
        }

        $firstRow = $rows->first()->toArray();
        if (count($firstRow) < count($this->columnMap)) {
            throw new \Exception("Format kolom tidak valid - pastikan file memiliki " . count($this->columnMap) . " kolom");
        }

        $batchSiswa = [];
        $batchIbu = [];
        $batchAyah = [];

        // Tambahkan di awal collection()
        $uniqueNisn = $rows->pluck('nisn')->unique();
        if ($uniqueNisn->count() !== $rows->count()) {
            throw new \Exception("Duplikasi NISN ditemukan dalam file");
        }

        foreach ($rows as $index => $row) {
            $this->totalBaris++;
            try {
                $mappedData = $this->mapRow($row->toArray());
                $this->validateRow($mappedData);
                if (Siswa::where('nisn', $mappedData['nisn'])->exists()) {
                    throw new \Exception("Siswa dengan NISN {$mappedData['nisn']} sudah ada.");
                }

                $agamaId = $this->getReferensiId('agama', strtolower($mappedData['agama']));
                if ($agamaId === null) {
                    throw new \Exception("Agama '{$mappedData['agama']}' tidak ditemukan di referensi.");
                }
                $tingkatId = $this->getReferensiId('tingkat', strtoupper($mappedData['tingkat']), fn($v) => $this->createTingkat($v));
                if ($tingkatId === null) {
                    throw new \Exception("Tingkat '{$mappedData['tingkat']}' tidak ditemukan di referensi.");
                }
                $jurusanId = $this->getReferensiId('jurusan', strtoupper($mappedData['jurusan']), fn($v) => $this->createJurusan($v));
                if ($jurusanId === null) {
                    throw new \Exception("Jurusan '{$mappedData['jurusan']}' tidak ditemukan di referensi.");
                }
                $kelasId = $this->getReferensiId('kelas', strtoupper($mappedData['kelas']), fn($v) => $this->createKelas($v, $tingkatId, $jurusanId));
                if ($kelasId === null) {
                    throw new \Exception("Kelas '{$mappedData['kelas']}' tidak ditemukan di referensi.");
                }
                
                $batchSiswa[] = $this->mapSiswaData($mappedData, $agamaId, $kelasId);
                $batchIbu[] = $this->mapOrtuData($mappedData, 'ibu');
                $batchAyah[] = $this->mapOrtuData($mappedData, 'ayah');
                
                $this->sukses++;
            } catch (\Exception $e) {
                $this->gagal++;
                $this->gagalData[] = [
                    'baris' => $this->currentRow,
                    'nisn' => $mappedData['nisn'] ?? null,
                    'nama' => $mappedData['nama']?? null,
                    // 'data'  => $row->toArray(),
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ];
                Log::error("Gagal import baris {$this->currentRow} (NISN: ".($mappedData['nisn'] ?? '-')."): " . $e->getMessage());
            }
            $this->currentRow++;
        }
        if (!empty($this->gagalData)) {
            session()->flash('gagalImport', $this->gagalData);
        }
        if ($batchSiswa) {
            $this->insertData($batchSiswa, $batchIbu, $batchAyah);
        }
    }

    protected function mapRow(array $rowData): array
    {
        $mapped = [];
        foreach ($this->columnMap as $key) {
            $mapped[$key] = $rowData[$key] ?? null;
        }
        if (isset($mapped['nipd'])) {
            $mapped['nipd'] = (string) $mapped['nipd'];
        }
        if (isset($mapped['nisn'])) {
            $mapped['nisn'] = (string) $mapped['nisn'];
        }
        if (isset($mapped['nik'])) {
            $mapped['nik'] = (string) $mapped['nik'];
        }
        if (is_numeric($mapped['tanggal_lahir'])) {
            $mapped['tanggal_lahir'] = Date::excelToDateTimeObject($mapped['tanggal_lahir'])->format('Y-m-d');
        }
        if (isset($mapped['tingkat'])) {
            $mapped['tingkat'] = strtoupper($mapped['tingkat']);
        }
        if (isset($mapped['jurusan'])) {
            $mapped['jurusan'] = strtoupper($mapped['jurusan']);
        }
        if (isset($mapped['kelas'])) {
            $mapped['kelas'] = strtoupper($mapped['kelas']);
        }
        return $mapped;
    }

    protected function validateRow(array $row): void
    {
        $validator = Validator::make($row, $this->validationRules);
        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }
    }

    protected function getReferensiId($type, $key, $createCallback = null)
    {
        if (isset($this->referensi[$type][$key])) {
            return $this->referensi[$type][$key];
        }
        if ($createCallback) {
            return $createCallback($key);
        }
        return null;
    }

    protected function createTingkat(string $tingkat): int
    {
        $model = Tingkat::firstOrCreate(['tingkat' => $tingkat]);
        return $this->referensi['tingkat'][$tingkat] = $model->id;
    }

    protected function createJurusan(string $kode): int
    {
        $model = Jurusan::firstOrCreate(
            ['kode_jurusan' => $kode],
            ['nama_jurusan' => $kode, 'kurikulum' => 'Merdeka']
        );
        return $this->referensi['jurusan'][$kode] = $model->id;
    }

    protected function createKelas(string $kelas, int $tingkatId, int $jurusanId): int
    {
        $cacheKey = "{$kelas}-{$tingkatId}-{$jurusanId}";
        if (!isset($this->referensi['kelas'][$cacheKey])) {
            $model = Kelas::firstOrCreate(
                [
                    'kelas' => $kelas,
                    'id_tingkat' => $tingkatId,
                    'id_jurusan' => $jurusanId,
                    'id_tahun_pelajaran' => $this->tahunPelajaranAktif->id
                ],
                ['created_at' => now(), 'updated_at' => now()]
            );
            $this->referensi['kelas'][$cacheKey] = $model->id;
        }
        return $this->referensi['kelas'][$cacheKey];
    }

    protected function mapSiswaData(array $row, int $agamaId, int $kelasId): array
    {
        return [
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
        ];
    }

    protected function mapOrtuData(array $row, string $tipe): array
    {
        return [
            'nisn' => $row['nisn'],
            'nama' => $row["nama_{$tipe}"],
            'nik' => $row["nik_{$tipe}"],
            'tahun_lahir' => $row["tahun_lahir{$tipe}"],
            'pendidikan_id' => $this->referensi['pendidikan'][strtolower($row["pendidikan_{$tipe}"])] ?? null,
            'pekerjaan_id' => $this->referensi['pekerjaan'][strtolower($row["pekerjaan_{$tipe}"])] ?? null,
            'penghasilan_id' => $this->referensi['penghasilan'][strtolower($row["penghasilan_{$tipe}"])] ?? null,
        ];
    }

    protected function insertData(array $siswas, array $ibus, array $ayahs): void
    {
        try {
            DB::transaction(function () use ($siswas, $ibus, $ayahs) {
                collect($siswas)->chunk(200)->each(function ($chunk) {
                    Siswa::insert($chunk->toArray());
                    // Log::info('Inserting Siswa:', $chunk);
                });
                // Siswa::insert($siswas);
                collect($ibus)->chunk(200)->each(function ($chunk) {
                    Ibu::insert($chunk->toArray());
                    // Log::info('Inserting Ibu:', $chunk->toArray());
                });
                collect($ayahs)->chunk(200)->each(function ($chunk) {
                    Ayah::insert($chunk->toArray());
                    // Log::info('Inserting Ayah:', $chunk->toArray());
                });
    
                // Membuat User untuk setiap siswa
                $users = collect($siswas)->map(function ($siswa) {
                    return [
                        'username' => $siswa['nisn'], // Menggunakan NISN sebagai username
                        'name' => $siswa['nama'],
                        'email' => $siswa['nisn']. '@sekolah.sch.id', // Menggunakan NISN sebagai email
                        'password' => bcrypt($siswa['tanggal_lahir'])
                    ];
                })->toArray();
                // Log::info('Inserting User:', collect($users)->toArray());
                // Bulk Insert
                User::insert($users);
    
                $nisnList = collect($siswas)->pluck('nisn');
    
                User::whereIn('username', $nisnList)
                    ->lazyById()
                    ->each(function ($user) {
                        $user->update(['role' => 'Siswa']);
                    });
            });
            DB::commit();
        }catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal impor data siswa: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getHasilImport(): array
    {
        return [
            'total'      => $this->totalBaris,
            'sukses'     => $this->sukses,
            'gagal'      => $this->gagal,
            'gagalData'  => $this->gagalData,
        ];
    }
}