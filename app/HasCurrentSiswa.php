<?php

namespace App;
use App\Models\Siswa;
use Illuminate\Support\Facades\Auth;

trait HasCurrentSiswa
{
    public static function getCurrentSiswa(): ?Siswa
    {
        $user = Auth::user();

        if (!$user) {
            return null;
        }

        return Siswa::where('nisn', $user->username)->first();
    }
    
}
