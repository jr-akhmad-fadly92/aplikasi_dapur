<?php

namespace App\Http\Controllers;

use App\Models\GramasiResep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Resep;


class GramasiResepController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $header = 'Gramasi Resep';
        

        $karbohidrat = Resep::where('id_komponen_sehat', 1)->get();
        $lauk        = Resep::where('id_komponen_sehat', 2)->get();
        $sayur       = Resep::where('id_komponen_sehat', 3)->get();
        $buah        = Resep::where('id_komponen_sehat', 4)->get();
        $suplemen    = Resep::where('id_komponen_sehat', 5)->get();


        return view('office.gramasi_resep.index', compact(
            
            'header',
            'karbohidrat',
            'lauk',
            'sayur',
            'buah',
            'suplemen'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Validasi input dari form request
        $request->validate([
            'status_bahan_baku'      => 'required',      
            'gramasi_a'              => 'required',     
            'gramasi_b'              => 'required',     
        ]);

        // Ambil data gramasi berdasarkan id, jika tidak ditemukan akan error 404
        $gramasi = GramasiResep::find($id);
        if(!$gramasi)
        {
            GramasiResep::create([
                'id_resep'              => $id,
                'status_bahan'          => $request->status_bahan_baku,
                'gramasi_a'             => $request->gramasi_a,
                'gramasi_b'             => $request->gramasi_b,
            ]);
        }else{
            // Update kolom-kolom sesuai input
            $gramasi->status_bahan          = $request->status_bahan_baku;
            $gramasi->gramasi_a             = $request->gramasi_a;
            $gramasi->gramasi_b             = $request->gramasi_b;
            $gramasi->save();
        }
        

        // Kembalikan response JSON sukses
        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diperbarui.'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
