<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileUpload extends Model
{
    protected $table = 'file_uploads';
    protected $fillable = [
        'nisn',
        'file_kategori_id',
        'nama_file',
        'google_drive_file_id',
        'google_drive_url',
    ];
    public function fileKategori()
    {
        return $this->belongsTo(FileKategori::class, 'file_kategori_id');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'nisn', 'nisn');
    }
}
