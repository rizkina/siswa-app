<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Pendidikan;
use App\Models\Pekerjaan;
use App\Models\Penghasilan;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ibu extends Model
{
    protected $table = 'ibus';

    protected $fillable = [
        'nisn',
        'nik',
        'nama',
        'tahun_lahir',
        'pendidikan_id',
        'pekerjaan_id',
        'penghasilan_id',
    ];

    public function pendidikan()
    {
        return $this->belongsTo(Pendidikan::class, 'pendidikan_id', 'id_pendidikan');
    }

    public function pekerjaan()
    {
        return $this->belongsTo(Pekerjaan::class, 'pekerjaan_id', 'id_pekerjaan');
    }

    public function penghasilan()
    {
        return $this->belongsTo(Penghasilan::class, 'penghasilan_id', 'id_penghasilan');
    }
}
