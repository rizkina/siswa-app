<?php

use App\Http\Controllers\SiswaImportController;
use App\Imports\SiswaImport;
use Illuminate\Support\Facades\Route;
use App\Livewire\ImportSiswa;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return redirect('admin/login');
})->name('login');

// Route import data siswa denganexcel
Route::group(['middleware' => 'auth'], function () {
    Route::get('import_siswa', ImportSiswa::class)->name('import_siswa');
    Route::post('import_siswa', ImportSiswa::class)->name('import_siswa');
});

// Route::get('/siswa/import', [SiswaImportController::class, 'index'])->name('siswa.import');
// Route::post('/siswa/import', [SiswaImportController::class, 'import'])->name('siswa.import');
