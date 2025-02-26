<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    protected $table = 'files';

    protected $fillable = [
        'nisn',
        'kategori',
        'file',
        'path',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'nisn');
    }

    // Validasi kategori saat menyimpan data
    public function setKategoriAttribute($value)
    {
        $allowedCategories = ['Ijazah', 'Kartu Keluarga', 'Akta Kelahiran'];

        if (!in_array($value, $allowedCategories)) {
            throw new \InvalidArgumentException("Kategori tidak valid.");
        }

        $this->attributes['kategori'] = $value;
    }

    // Akses URL file (jika disimpan di storage)
    public function getUrlAttribute()
    {
        $value = $this->attributes['path'];

        if (!$value) {
            return null;
        }

        // Jika path sudah berupa URL (misal dari Google Drive)
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }

        // Jika file disimpan di storage
        return Storage::url($value);
    }

    // Simpan file dengan path otomatis
    public function setFileAttribute($file)
    {
        if ($file) {
            $this->attributes['file'] = $file->getClientOriginalName();
            $this->attributes['path'] = $file->store('uploads/files', 'public'); // Simpan di storage/public/uploads/files
        }
    }
}
