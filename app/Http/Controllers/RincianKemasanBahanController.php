<?php

namespace App\Http\Controllers;

use App\Models\RincianMasterBahan;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
//import return type View
use Illuminate\View\View;
//import return type redirectResponse
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use App\Models\TbMasterBahan;

class RincianKemasanBahanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function v_dashboard($id)
    {
        $data_bahan = TbMasterBahan::where('id', $id)->first();
        $header = "Kemasan " . $data_bahan->bahan;
        
        $id_bahan = $id;
        if (request()->ajax()) {

            $data =  DB::table('tb_rincian_master_bahan')
                ->join('tb_master_bahan', 'tb_rincian_master_bahan.id_bahan', '=', 'tb_master_bahan.id')
                ->join('tb_satuan', 'tb_master_bahan.satuan_bahan', '=', 'tb_satuan.id')
                ->where('tb_master_bahan.id', $id)
                ->select([
                    'tb_rincian_master_bahan.id',
                    'tb_rincian_master_bahan.jumlah',
                    'tb_master_bahan.bahan',
                    'tb_satuan.satuan'
                ])
                ->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('jumlah_bahan', function ($row) {
                    return number_format($row->jumlah, 0, ',', '.') . ' ' . $row->satuan;
                })
                ->addColumn('action', function ($row) {
                    return '
                        <button  class="btn btn-warning btn-sm btn-edit" data-id="' . $row->id . '" data-jumlah="' . $row->jumlah . '">Edit</button>
                        <button class="btn btn-danger btn-sm btn-delete" data-id="' . $row->id . '">Hapus</button>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('office/rincian_kemasan_bahan.rincian_master_bahan', compact('header', 'id_bahan', 'data_bahan'));
        //return view('office/kandungan_gizi.rincian_kandungan', compact('header', 'kandungan', 'id_bahan', 'header'));
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
        $request->validate([
            'jumlah_tambah' => 'required'

        ]);
        $data = TbMasterBahan::find($request->id_bahan);
        RincianMasterBahan::create([
            'jumlah' => $request->jumlah_tambah,
            'id_bahan' => $request->id_bahan,
            'satuan' => $data->satuan_bahan,

        ]);
        

        return response()->json(['success' => 'Data berhasil ditambahkan']);
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
        $data = RincianMasterBahan::find($id);
        return response()->json($data);
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
        try {
            $request->validate([
                'jumlah_update' => 'required|numeric',
            ]);

            $RincianMasterBahan = RincianMasterBahan::find($id);
            $RincianMasterBahan->update([
                'jumlah' => $request->jumlah_update,
            ]);

            return response()->json(['message' => 'Data berhasil diperbarui']);
        } catch (\Exception $e) {

            return response()->json(['message' => 'Terjadi kesalahan'], 500);
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
        $data = RincianMasterBahan::find($id);

        if (!$data) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        $data->delete();

        return response()->json(['success' => 'Data berhasil dihapus']);
    }
}
