<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TahunPelajaran extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tahun_pelajaran';

    protected $fillable = [
        'tahun_pelajaran',
        'tahun',
        'semester',
        'aktif',
        'tanggal_mulai',
        'tanggal_selesai',
        'keterangan'
    ];

    public function kelas()
    {
        return $this->hasMany(Kelas::class, 'id_tahun_pelajaran');
    }
}
