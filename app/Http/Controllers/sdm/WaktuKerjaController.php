<?php

namespace App\Http\Controllers\sdm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WaktuKerjaController extends Controller
{
    public function index()
    {
        $waktu = DB::table('tb_waktu_kerja')->get();
        return view('office.sdm.waktu.waktukerja', compact('waktu'));
    }

    public function create()
    {
        return view('office.sdm.waktu.waktucreate');
    }

    public function store(Request $request)
    {
        $request->validate([
            'id' => 'required|unique:tb_waktu_kerja',
            'jam_kerja' => 'required'
        ]);

        DB::table('tb_waktu_kerja')->insert([
            'id' => $request->id,
            'jam_kerja' => $request->jam_kerja
        ]);

        return redirect()->route('waktu.index')->with('success', 'Data berhasil ditambahkan');
    }
}
