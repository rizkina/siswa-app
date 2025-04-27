<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class FileDownloadController extends Controller
{
    public function download($file)
    {
        $filePath = 'uploads/' . $file; // Sesuaikan dengan path penyimpanan kamu

        if (!Storage::disk('google')->exists($filePath)) {
            abort(404, 'File tidak ditemukan.');
        }

        return Storage::disk('google')->download($filePath);
    }
}
