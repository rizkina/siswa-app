<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    protected $table = 'siswas';

    protected $fillable = [
        'nisn',
        'nipd',
        'nik',
        'nama',
        'jns_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'agama_id',
        'alamat',
        'foto',
    ];
}
