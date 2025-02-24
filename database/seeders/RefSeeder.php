<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RefSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Isi tabel pendidikan
        DB::table('pendidikans')->insert([
            ['id' => 0, 'pendidikan' => 'Tidak sekolah'],
            ['id' => 1, 'pendidikan' => 'PAUD'],
            ['id' => 2, 'pendidikan' => 'TK / sederajat'],
            ['id' => 3, 'pendidikan' => 'Putus SD'],
            ['id' => 4, 'pendidikan' => 'SD / sederajat'],
            ['id' => 5, 'pendidikan' => 'SMP / sederajat'],
            ['id' => 6, 'pendidikan' => 'SMA / sederajat'],
            ['id' => 7, 'pendidikan' => 'Paket A'],
            ['id' => 8, 'pendidikan' => 'Paket B'],
            ['id' => 9, 'pendidikan' => 'Paket C'],
            ['id' => 20, 'pendidikan' => 'D1'],
            ['id' => 21, 'pendidikan' => 'D2'],
            ['id' => 22, 'pendidikan' => 'D3'],
            ['id' => 23, 'pendidikan' => 'D4'],
            ['id' => 30, 'pendidikan' => 'S1'],
            ['id' => 31, 'pendidikan' => 'Profesi'],
            ['id' => 35, 'pendidikan' => 'S2'],
            ['id' => 36, 'pendidikan' => 'S2 Terapan'],
            ['id' => 37, 'pendidikan' => 'Sp-2'],
            ['id' => 40, 'pendidikan' => 'S3'],
            ['id' => 41, 'pendidikan' => 'S3 Terapan'],
            ['id' => 90, 'pendidikan' => 'Non formal'],
            ['id' => 91, 'pendidikan' => 'Informal'],
            ['id' => 99, 'pendidikan' => 'Lainnya'],
        ]);

        // Isi tabel pekerjaan
        DB::table('pekerjaans')->insert([
            ['id' => 1, 'pekerjaan' => 'Tidak bekerja'],
            ['id' => 2, 'pekerjaan' => 'Nelayan'],
            ['id' => 3, 'pekerjaan' => 'Petani'],
            ['id' => 4, 'pekerjaan' => 'Peternak'],
            ['id' => 5, 'pekerjaan' => 'PNS/TNI/POLRI'],
            ['id' => 6, 'pekerjaan' => 'Karyawan Swasta'],
            ['id' => 7, 'pekerjaan' => 'Pedagang Kecil'],
            ['id' => 8, 'pekerjaan' => 'Pedagang Besar'],
            ['id' => 9, 'pekerjaan' => 'Wiraswasta'],
            ['id' => 10, 'pekerjaan' => 'Wirausaha'],
            ['id' => 11, 'pekerjaan' => 'Buruh'],
            ['id' => 12, 'pekerjaan' => 'Pensiunan'],
            ['id' => 13, 'pekerjaan' => 'Tenaga Kerja Indonesia'],
            ['id' => 14, 'pekerjaan' => 'Karyawan BUMN'],
            ['id' => 90, 'pekerjaan' => 'Tidak dapat diterapkan'],
            ['id' => 98, 'pekerjaan' => 'Sudah Meninggal'],
            ['id' => 99, 'pekerjaan' => 'Lainnya'],
        ]);

        // Isi tabel penghasilan
        DB::table('penghasilans')->insert([
            ['id' => 11, 'penghasilan' => 'Kurang dari Rp. 500,000'],
            ['id' => 12, 'penghasilan' => 'Rp. 500,000 - Rp. 999,999'],
            ['id' => 13, 'penghasilan' => 'Rp. 1,000,000 - Rp. 1,999,999'],
            ['id' => 14, 'penghasilan' => 'Rp. 2,000,000 - Rp. 4,999,999'],
            ['id' => 15, 'penghasilan' => 'Rp. 5,000,000 - Rp. 20,000,000'],
            ['id' => 16, 'penghasilan' => 'Lebih dari Rp. 20,000,000'], 
            ['id' => 99, 'penghasilan' => 'Tidak Berpenghasilan'],
        ]);

        DB::table('agamas')->insert([
            ['id' => 1, 'agama' => 'Islam'],
            ['id' => 2, 'agama' => 'Kristen'],
            ['id' => 3, 'agama' => 'Katolik'],
            ['id' => 4, 'agama' => 'Hindu'],
            ['id' => 5, 'agama' => 'Budha'],
            ['id' => 6, 'agama' => 'Konghucu'],
            ['id' => 7, 'agama' => 'Kepercayaan kpd Tuhan YME'],
            ['id' => 99, 'agama' => 'Lainnya'],
        ]);
    }
}
