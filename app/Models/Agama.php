<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agama extends Model
{
    protected $table = 'agamas';

    protected $fillable = [
        'id_agama',
        'agama',
    ];

    public function siswa()
    {
        return $this->hasMany(Siswa::class, 'agama_id', 'id_agama');
    }
}
