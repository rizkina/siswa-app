<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kelas extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kelas';

    protected $fillable = [
        'kelas',
        'id_tingkat',
        'id_jurusan',
        'id_tahun_pelajaran'
    ];

    public function siswa()
    {
        return $this->hasMany(Siswa::class, 'siswa_kelas', 'id_kelas', 'id_siswa')
            ->withPivot('id_tahun_ajaran')
            ->withTimestamps();
    }

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'id_jurusan');
    }

    public function tingkat()
    {
        return $this->belongsTo(Tingkat::class, 'id_tingkat');
    }

    public function tahun_pelajaran()
    {
        return $this->belongsTo(TahunPelajaran::class, 'id_tahun_pelajaran');
    }
}
