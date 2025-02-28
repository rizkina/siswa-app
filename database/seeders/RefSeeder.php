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
            ['id_pendidikan' => 0, 'pendidikan' => 'Tidak sekolah'],
            ['id_pendidikan' => 1, 'pendidikan' => 'PAUD'],
            ['id_pendidikan' => 2, 'pendidikan' => 'TK / sederajat'],
            ['id_pendidikan' => 3, 'pendidikan' => 'Putus SD'],
            ['id_pendidikan' => 4, 'pendidikan' => 'SD / sederajat'],
            ['id_pendidikan' => 5, 'pendidikan' => 'SMP / sederajat'],
            ['id_pendidikan' => 6, 'pendidikan' => 'SMA / sederajat'],
            ['id_pendidikan' => 7, 'pendidikan' => 'Paket A'],
            ['id_pendidikan' => 8, 'pendidikan' => 'Paket B'],
            ['id_pendidikan' => 9, 'pendidikan' => 'Paket C'],
            ['id_pendidikan' => 20, 'pendidikan' => 'D1'],
            ['id_pendidikan' => 21, 'pendidikan' => 'D2'],
            ['id_pendidikan' => 22, 'pendidikan' => 'D3'],
            ['id_pendidikan' => 23, 'pendidikan' => 'D4'],
            ['id_pendidikan' => 30, 'pendidikan' => 'S1'],
            ['id_pendidikan' => 31, 'pendidikan' => 'Profesi'],
            ['id_pendidikan' => 35, 'pendidikan' => 'S2'],
            ['id_pendidikan' => 36, 'pendidikan' => 'S2 Terapan'],
            ['id_pendidikan' => 37, 'pendidikan' => 'Sp-2'],
            ['id_pendidikan' => 40, 'pendidikan' => 'S3'],
            ['id_pendidikan' => 41, 'pendidikan' => 'S3 Terapan'],
            ['id_pendidikan' => 90, 'pendidikan' => 'Non formal'],
            ['id_pendidikan' => 91, 'pendidikan' => 'Informal'],
            ['id_pendidikan' => 99, 'pendidikan' => 'Lainnya'],
        ]);


        // Isi tabel pekerjaan
        DB::table('pekerjaans')->insert([
            ['id_pekerjaan' => 1, 'pekerjaan' => 'Tidak bekerja'],
            ['id_pekerjaan' => 2, 'pekerjaan' => 'Nelayan'],
            ['id_pekerjaan' => 3, 'pekerjaan' => 'Petani'],
            ['id_pekerjaan' => 4, 'pekerjaan' => 'Peternak'],
            ['id_pekerjaan' => 5, 'pekerjaan' => 'PNS/TNI/POLRI'],
            ['id_pekerjaan' => 6, 'pekerjaan' => 'Karyawan Swasta'],
            ['id_pekerjaan' => 7, 'pekerjaan' => 'Pedagang Kecil'],
            ['id_pekerjaan' => 8, 'pekerjaan' => 'Pedagang Besar'],
            ['id_pekerjaan' => 9, 'pekerjaan' => 'Wiraswasta'],
            ['id_pekerjaan' => 10, 'pekerjaan' => 'Wirausaha'],
            ['id_pekerjaan' => 11, 'pekerjaan' => 'Buruh'],
            ['id_pekerjaan' => 12, 'pekerjaan' => 'Pensiunan'],
            ['id_pekerjaan' => 13, 'pekerjaan' => 'Tenaga Kerja Indonesia'],
            ['id_pekerjaan' => 14, 'pekerjaan' => 'Karyawan BUMN'],
            ['id_pekerjaan' => 90, 'pekerjaan' => 'Tidak dapat diterapkan'],
            ['id_pekerjaan' => 98, 'pekerjaan' => 'Sudah Meninggal'],
            ['id_pekerjaan' => 99, 'pekerjaan' => 'Lainnya'],
        ]);

        // Isi tabel penghasilan
        DB::table('penghasilans')->insert([
            ['id_penghasilan' => 11, 'penghasilan' => 'Kurang dari Rp. 500,000'],
            ['id_penghasilan' => 12, 'penghasilan' => 'Rp. 500,000 - Rp. 999,999'],
            ['id_penghasilan' => 13, 'penghasilan' => 'Rp. 1,000,000 - Rp. 1,999,999'],
            ['id_penghasilan' => 14, 'penghasilan' => 'Rp. 2,000,000 - Rp. 4,999,999'],
            ['id_penghasilan' => 15, 'penghasilan' => 'Rp. 5,000,000 - Rp. 20,000,000'],
            ['id_penghasilan' => 16, 'penghasilan' => 'Lebih dari Rp. 20,000,000'],
            ['id_penghasilan' => 99, 'penghasilan' => 'Tidak Berpenghasilan'],
        ]);

        // Isi Tabel Agama
        DB::table('agamas')->insert([
            ['id_agama' => 1, 'agama' => 'Islam'],
            ['id_agama' => 2, 'agama' => 'Kristen'],
            ['id_agama' => 3, 'agama' => 'Katolik'],
            ['id_agama' => 4, 'agama' => 'Hindu'],
            ['id_agama' => 5, 'agama' => 'Budha'],
            ['id_agama' => 6, 'agama' => 'Konghucu'],
            ['id_agama' => 7, 'agama' => 'Kepercayaan kpd Tuhan YME'],
            ['id_agama' => 99, 'agama' => 'Lainnya'],
        ]);

        // Isi tabel User dengan super admin
        DB::table('users')->insert([
            'username' => 'superadmin',
            'name' => 'Super Admin',
            'email' => 'su@mail.com',
            'password' => bcrypt('Test_123'),
        ]);
    }
}
