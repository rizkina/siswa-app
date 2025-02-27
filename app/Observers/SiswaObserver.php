<?php

namespace App\Observers;

use App\Models\Ibu;
use App\Models\Ayah;
use App\Models\Siswa;
use App\Models\User;

class SiswaObserver
{
    /**
     * Handle the Siswa "created" event.
     */
    public function created(Siswa $siswa): void
    {
        Ibu::create([
            'nisn' => $siswa->nisn,
            'nama' => null,
            'nik' => null,
            'tahun_lahir' => null,
            'pendidikan_id' => null,
            'pekerjaan_id' => null,
            'penghasilan_id' => null,
        ]);

        Ayah::create([
            'nisn' => $siswa->nisn,
            'nama' => null,
            'nik' => null,
            'tahun_lahir' => null,
            'pendidikan_id' => null,
            'pekerjaan_id' => null,
            'penghasilan_id' => null,
        ]);

        $user = User::create([
            'username' => $siswa->nisn, // Menggunakan NISN sebagai username
            'name' => $siswa->nama,
            'email' => $siswa->nisn . '@sekolah.sch.id', // Menggunakan NISN sebagai email
            'password' => bcrypt($siswa->tanggal_lahir),
        ]);

        // Berikan role siswa ke user
        $user->assignRole('Siswa');
    }

    /**
     * Handle the Siswa "updated" event.
     */
    public function updated(Siswa $siswa): void
    {
        //
    }

    /**
     * Handle the Siswa "deleted" event.
     */
    public function deleted(Siswa $siswa): void
    {
        //
    }

    /**
     * Handle the Siswa "restored" event.
     */
    public function restored(Siswa $siswa): void
    {
        //
    }

    /**
     * Handle the Siswa "force deleted" event.
     */
    public function forceDeleted(Siswa $siswa): void
    {
        //
    }
}
