<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pekerjaan extends Model
{
    protected $table = 'pekerjaans';

    protected $fillable = [
        'id_pekerjaan',
        'pekerjaan',
    ];

    public function ibu()
    {
        return $this->hasMany(Ibu::class, 'pekerjaan_id', 'id_pekerjaan');
    }
    public function ayah()
    {
        return $this->hasMany(Ayah::class, 'pekerjaan_id', 'id_pekerjaan');
    }
}
