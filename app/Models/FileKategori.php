<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileKategori extends Model
{
    protected $fillable = [
        'nama',
        'folder_id',
    ];

    
    public function fileUpload()
    {
        return $this->hasMany(FileUpload::class);
    }
}
