<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GoogleDriveSetup extends Model
{
    use HasFactory;

    protected $table = 'google_drive_setups';
    protected $fillable = [
        'client_id',
        'client_secret',
        'refresh_token',
        'folder_id',
        'is_active',
    ];
    protected $casts = [
        'is_active' => 'boolean',
    ];
}
