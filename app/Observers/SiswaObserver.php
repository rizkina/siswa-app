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
        // Ibu::create([
        //     'nisn' => $siswa->nisn,
        //     'nama' => null,
        //     'nik' => null,
        //     'tahun_lahir' => null,
        //     'pendidikan_id' => null,
        //     'pekerjaan_id' => null,
        //     'penghasilan_id' => null,
        // ]);

        // Ayah::create([
        //     'nisn' => $siswa->nisn,
        //     'nama' => null,
        //     'nik' => null,
        //     'tahun_lahir' => null,
        //     'pendidikan_id' => null,
        //     'pekerjaan_id' => null,
        //     'penghasilan_id' => null,
        // ]);

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
    public function updated(Siswa $siswa)
    {
        $user = User::where('username', $siswa->getOriginal('nisn'))->first();
        if ($user) {
            $user->update([
                'name'     => $siswa->nama,
                'username' => $siswa->nisn, // Update username jika NISN berubah
                'email' => $siswa->nisn . '@sekolah.sch.id',
            ]);
        }
        $ibu = Ibu::where('nisn', $siswa->getOriginal('nisn'))->first();
        if ($ibu) {
            $ibu->update([
                'nisn' => $siswa->nisn,
            ]);
        }
        $ayah = Ayah::where('nisn', $siswa->getOriginal('nisn'))->first();
        if ($ayah) {
            $ayah->update([
                'nisn' => $siswa->nisn,
            ]);
        }
    }

    /**
     * Handle the Siswa "deleted" event.
     */
    public function deleted(Siswa $siswa)
    {
        User::where('username', $siswa->nisn)->delete();
        Ibu::where('nisn', $siswa->nisn)->delete();
        Ayah::where('nisn', $siswa->nisn)->delete();
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
