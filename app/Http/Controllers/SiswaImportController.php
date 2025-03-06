<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SiswaImport;
use App\Models\Siswa;

class SiswaImportController extends Controller
{
    public function index()
    {
        $siswa = Siswa::all();
        return view('siswa.import', compact('siswa'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,xls,xlsx'
        ]);

        $import = new SiswaImport();
        Excel::import(new SiswaImport, $request->file('file'));

        return redirect()->back()->with('importResult', $import->getHasilImport());
    }
}
