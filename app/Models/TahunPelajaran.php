<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TahunPelajaran extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tahun_pelajarans';

    protected $fillable = [
        'tahun_pelajaran',
        'tahun',
        'semester',
        'aktif',
        'tanggal_mulai',
        'tanggal_selesai',
        'keterangan'
    ];

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($model) {
            if ($model->isDirty('aktif') && $model->aktif == 1) {
                // Set all other records to inactive
                self::where('id', '!=', $model->id)->update(['aktif' => 0]);
            }
        });
    }

    public static function getTahunAktif()
    {
        return self::where('aktif', true)->first();
    }

    public function kelas()
    {
        return $this->hasMany(Kelas::class, 'id_tahun_pelajaran');
    }
}
