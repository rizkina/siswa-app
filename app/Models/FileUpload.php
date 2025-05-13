<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Siswa;
use App\Models\FileKategori;

class FileUpload extends Model
{
    use HasFactory, SoftDeletes;

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
