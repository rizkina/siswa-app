<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\Agama;

class Siswa extends Model
{
    use HasFactory, SoftDeletes;

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

    public function ibu()
    {
        return $this->hasOne(Ibu::class, 'nisn', 'nisn');
    }
    public function ayah()
    {
        return $this->hasOne(Ayah::class, 'nisn', 'nisn');
    }

    public function file()
    {
        return $this->hasMany(FileUpload::class, 'nisn', 'nisn');
    }

    // Mendapatkan file berdasarkan kategori
    public function getFileByCategory($kategori)
    {
        return $this->files()->where('kategori', $kategori)->first();
    }

    public function kelas()
    {
        // return $this->belongsToMany(Kelas::class, 'siswa_kelas', 'id_siswa', 'id_kelas')
        //     ->withPivot('id_tahun_pelajaran')
        //     ->withTimestamps();
        return $this->belongsTo(Kelas::class, 'id_kelas');
    }
}
