<?php

namespace App\Http\Controllers\office;

use App\Http\Controllers\Controller;

use App\Models\SpesifikasiBahan;
use Illuminate\Http\Request;

class SpesifikasiBahanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        
        $spesifikasi = SpesifikasiBahan::where('id_bahan',$id)->first();
        if($spesifikasi)
        {
            $spesifikasi = $spesifikasi->keterangan;
        }else{
            $spesifikasi = ".";
        }
        return view('penerimaan.edit', compact('spesifikasi'));
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
        $request->validate([
            'keterangan' => 'nullable|string',
        ]);

        $spesifikasi = SpesifikasiBahan::where('id_bahan',$request->id_bahan)->first();
        if($spesifikasi)
        {
            $spesifikasi->update([
                'keterangan'             => $request->keterangan,
            ]);
            return redirect()->route('tb_penerimaan.index')->with('success', 'Data penerimaan berhasil diperbarui!');
        }else{
            SpesifikasiBahan::create([
                'id_bahan'              => $request->id_bahan,
                'keterangan'             => $request->keterangan,

            ]);
            return redirect()->route('tb_penerimaan.index')->with('success', 'Data penerimaan berhasil ditambah!');
            
        }
        
        
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

    public function simpan_spesifikasi_bahan_baku(Request $request)
    {
        try {
            // Validasi Input
            $request->validate([
                'keterangan' => 'nullable|string',
            ]);


            $spesifikasi = SpesifikasiBahan::where('id_bahan', $request->idbahan)->first();
            if ($spesifikasi) {
                $spesifikasi->update([
                    'spesifikasi'             => $request->keterangan,
                ]);
                return response()->json(['success' => true, 'message' => 'Data berhasil disimpan']);
            } else {
                SpesifikasiBahan::create([
                    'id_bahan'              => $request->idbahan,
                    'spesifikasi'             => $request->keterangan,

                ]);
                return response()->json(['success' => true, 'message' => 'Data berhasil disimpan']);
            }

            return response()->json(['success' => true, 'message' => 'Data berhasil disimpan']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
        //return redirect()->route('master_wadah.index')->with('success', 'Wadah berhasil ditambahkan!');
    }

}
