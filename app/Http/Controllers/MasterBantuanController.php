<?php

namespace App\Http\Controllers;

use App\Models\MasterBantuan;
use Illuminate\Http\Request;

class MasterBantuanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $header     = "Master Bantuan";
        $bantuan    = MasterBantuan::first();
        return view('office/master_bantuan.index', compact('header', 'bantuan'));
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
        // Validasi input dari form request
        $request->validate([
            'bantuan_pangan_A'        => 'required', // Wajib, harus ada di tabel tb_master_bahan kolom id
            'bantuan_pangan_B'        => 'required', // Wajib, harus ada di tabel tb_master_bahan kolom id
            'bantuan_operasional'   => 'required',             // Wajib, integer >= 1
            'bantuan_infra'         => 'required',             // Wajib, integer >= 1
        ]);
        $bantuan = MasterBantuan::first();
        if($bantuan)
        {
            $bantuan->update([
                'bantuan_pangan_A'        => $request->bantuan_pangan_A,
                'bantuan_pangan_B'        => $request->bantuan_pangan_B,       // relasi bahan baku
                'bantuan_operasional'   => $request->bantuan_operasional,        // jumlah isi per box
                'bantuan_infra'         => $request->bantuan_infra,   // jumlah hasil matang

            ]);
        }else{
            MasterBantuan::create([
                'bantuan_pangan_A'        => $request->bantuan_pangan_A,       // relasi bahan baku
                'bantuan_pangan_B'        => $request->bantuan_pangan_B,       // relasi bahan baku
                'bantuan_operasional'   => $request->bantuan_operasional,        // jumlah isi per box
                'bantuan_infra'         => $request->bantuan_infra,   // jumlah hasil matang
            ]);
        }
        // Kembalikan response JSON sukses
        return response()->json([
            'success' => true,
            'message' => 'Data Bantuan berhasil ditambahkan.'
        ]);
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
        //
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
