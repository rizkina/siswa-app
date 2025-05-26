<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pendidikan extends Model
{
    protected $table = 'pendidikans';

    protected $fillable = [
        'id_pendidikan',
        'pendidikan',
    ];

    public function ibu()
    {
        return $this->hasMany(Ibu::class, 'pendidikan_id', 'id_pendidikan');
    }
    public function ayah()
    {
        return $this->hasMany(Ayah::class, 'pendidikan_id', 'id_pendidikan');
    }
}
