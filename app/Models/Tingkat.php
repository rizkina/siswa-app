<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tingkat extends Model
{
    use HasFactory;

    protected $table = 'tingkats';

    protected $fillable = [
        'tingkat',
        'keterangan',
    ];

    public function kelas()
    {
        return $this->hasMany(Kelas::class, 'id_tingkat');
    }
}
