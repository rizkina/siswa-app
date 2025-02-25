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
}
