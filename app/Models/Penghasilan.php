<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penghasilan extends Model
{
    protected $table = 'penghasilans';

    protected $fillable = [
        'id_penghasilan',
        'penghasilan',
    ];

    public function ibu()
    {
        return $this->hasMany(Ibu::class, 'pendidikan_id', 'id_pendidikan');
    }
    // public function ayah()
    // {
    //     return $this->hasMany(Ayah::class, 'pendidikan_id', 'id_pendidikan');
    // }
}
