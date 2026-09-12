<?php

namespace App\Http\Controllers\sdm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TugasController extends Controller
{
    // Menampilkan daftar tugas
    public function index()
    {
        $tugas = DB::table('tb_tugas')->get(); // ambil semua data
        return view('office.sdm.tugas.tugas', compact('tugas'));
    }

    // Form tambah tugas
    public function create()
    {
        return view('office.sdm.tugas.tugascreate');
    }

    // Simpan tugas baru
    public function store(Request $request)
    {
        $request->validate([
           // 'id_tugas' => 'required|unique:tb_tugas,id_tugas',
            'kegiatan' => 'required|string|max:255',
        ]);

        // cek data id
        $cek = DB::table('tb_tugas')->where('kegiatan',$request->kegiatan)->count();
        if($cek > 0)
        {
            return redirect()->route('tugas.index')->with('success', 'Sudah Ada');
        }
        //$data = DB::table('tb_tugas')->orderBy('id_tugas', 'desc')->first();
        DB::table('tb_tugas')->insert([
            //   'id_tugas' => $data->id_tugas,
            'kegiatan' => $request->kegiatan,
            'terlampir' => null, // kosong dulu
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        // Simpan data manual
        

        

        return redirect()->route('tugas.index')->with('success', 'Tugas berhasil ditambahkan!');
    }
}
