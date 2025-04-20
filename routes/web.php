<?php

use App\Http\Controllers\SiswaImportController;
use App\Imports\SiswaImport;
use Illuminate\Support\Facades\Route;
use App\Livewire\ImportSiswa;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\GoogleDriveAuthController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return redirect('admin/login');
})->name('login');

// Route import data siswa denganexcel
Route::group(['middleware' => 'auth'], function () {
    Route::get('import-siswa', ImportSiswa::class)->name('import_siswa');
    Route::post('import-siswa', ImportSiswa::class);
});

// Route::get('/siswa/import', [SiswaImportController::class, 'index'])->name('siswa.import');
// Route::post('/siswa/import', [SiswaImportController::class, 'import'])->name('siswa.import');

Route::get('/auth/google-drive', [GoogleDriveAuthController::class, 'redirect'])->name('google.drive.redirect');
Route::get('/auth/google-drive/callback', [GoogleDriveAuthController::class, 'callback'])->name('google.drive.callback');