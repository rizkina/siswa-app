<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\ImportSiswa;

Route::get('/', function () {
    return view('welcome');
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('import_siswa', ImportSiswa::class)->name('import_siswa');
});
