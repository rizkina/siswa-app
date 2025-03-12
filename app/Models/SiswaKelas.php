<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SiswaKelas extends Model
{
    use HasFactory;

    protected $table = 'siswa_kelas';
    protected $fillable = [
        'id_siswa',
        'id_kelas',
        'id_tahun_pelajaran',
    ];
}
