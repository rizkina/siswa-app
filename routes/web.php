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
// Route::group(['middleware' => 'auth'], function () {
//     Route::get('import-siswa', ImportSiswa::class)->name('import_siswa');
//     Route::post('import-siswa', ImportSiswa::class);
// });
Route::group(['middleware' => 'auth'], function () {
    Route::get('import-siswa', ImportSiswa::class)->name('import_siswa');
    Route::post('import-siswa', [ImportSiswa::class, 'import_excel'])->name('import_siswa.process');
});

Route::get('admin/auth', [GoogleDriveAuthController::class, 'redirect'])->name('google.drive.auth');
Route::get('auth/google-drive/callback', [GoogleDriveAuthController::class, 'callback'])->name('google.drive.callback');
