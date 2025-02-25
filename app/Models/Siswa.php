<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\Agama;

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

    public function agama()
    {
        return $this->belongsTo(Agama::class, 'agama_id', 'id_agama');
    }
}
