<?php

namespace App\Http\Controllers\sdm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BagianController extends Controller
{
    public function create()
    {
        return view('office.sdm.bagian.bagiancreate');
    }

    public function store(Request $request)
    {
        $request->validate([
       //     'id_bagian'   => 'required',
            'nama_bagian' => 'required|string|max:100',
        ]);
        
        // cek data id
        $data = DB::table('tb_bagian')->last();
        // Simpan data manual
        if($data)
        {
            DB::table('tb_bagian')->insert([
                'id_bagian'   => ($data->id_bagian+1),
                'nama_bagian' => $request->nama_bagian,
            ]);    
        }else{
            DB::table('tb_bagian')->insert([
                'id_bagian'   => 1,
                'nama_bagian' => $request->nama_bagian,
            ]);
        }
        

        return redirect()->back()->with('success', 'Bagian berhasil disimpan!');
    }

        // tampilkan semua data
        public function index()
    {
        $bagian = DB::table('tb_bagian')->get();
        return view('office.sdm.bagian.bagian', compact('bagian'));
    }
}
