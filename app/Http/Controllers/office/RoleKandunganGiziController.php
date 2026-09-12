<?php


namespace App\Http\Controllers\office;

use App\Http\Controllers\Controller;


use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
//import return type View
use Illuminate\View\View;
//import return type redirectResponse
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

use App\Models\MasterKandunganGizi;
use App\Models\RoleKandunganGizi;
use App\Models\TbMasterBahan;

class RoleKandunganGiziController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = TbMasterBahan::where('id', $request->id_bahan)->first();
        $header = $data->bahan ?? '-';
        $id_bahan = $request->id_bahan;
        $kandungan = MasterKandunganGizi::all();
        if ($request->ajax()) {
            $data =  DB::table('tb_role_kandungan_gizi')
                ->join('tb_master_kandungan_gizi', 'tb_role_kandungan_gizi.id_kandungan_gizi', '=', 'tb_master_kandungan_gizi.id')
                ->join('tb_master_bahan', 'tb_role_kandungan_gizi.id_bahan', '=', 'tb_master_bahan.id')
                ->where('tb_master_bahan.id', $request->id_bahan)
                ->select([
                    'tb_role_kandungan_gizi.id',
                    'tb_role_kandungan_gizi.id_kandungan_gizi',
                    'tb_role_kandungan_gizi.jumlah',
                    'tb_master_bahan.bahan',
                    'tb_master_kandungan_gizi.kandungan'
                ])
                ->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '
                        <button class="btn btn-sm btn-warning editButton" data-id="' . $row->id . '" data-kandungan="' . $row->kandungan . '">Edit</button>
                        <button class="btn btn-sm btn-danger deleteButton" data-id="' . $row->id . '">Hapus</button>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('office/kandungan_gizi.rincian_kandungan', ['kandungan' => $kandungan,'id_bahan' => $id_bahan,'header' => 'Master Kandungan Gizi '. $header]);
    }

    public function index_dashboard($id)
    {
        
        $data = TbMasterBahan::where('id', $id)->first();
        $header = "Master Kandungan Gizi ". $data->bahan;
        $id_bahan = $id;
        $kandungan = MasterKandunganGizi::all();
        if (request()->ajax()) {

            $data =  DB::table('tb_role_kandungan_gizi')
                ->join('tb_master_kandungan_gizi', 'tb_role_kandungan_gizi.id_kandungan_gizi', '=', 'tb_master_kandungan_gizi.id')
                ->join('tb_master_bahan', 'tb_role_kandungan_gizi.id_bahan', '=', 'tb_master_bahan.id')
                ->where('tb_master_bahan.id', $id)
                ->select([
                    'tb_role_kandungan_gizi.id',
                    'tb_role_kandungan_gizi.id_kandungan_gizi',
                    'tb_role_kandungan_gizi.jumlah',
                    'tb_master_bahan.bahan',
                    'tb_master_kandungan_gizi.kandungan'
                ])
                ->get();
            return DataTables::of($data)
                ->addIndexColumn() // Menambah index

                ->addColumn('action', function ($row) {

                    return '<button data-id="' . $row->id . '" data-kandungan="' . $row->kandungan . '" class="btn btn-warning btn-sm btn-edit">Edit</button> '
                        . '<button data-id="' . $row->id . '" class="btn btn-danger btn-sm btn-delete">Hapus</button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('office/kandungan_gizi.rincian_kandungan', compact('header', 'kandungan', 'id_bahan', 'header'));
     
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
            'kandungan_create' => 'required',
            'jumlah_tambah' => 'required'

        ]);

        RoleKandunganGizi::create([
            'id_kandungan_gizi' => $request->kandungan_create,
            'jumlah' => $request->jumlah_tambah,
            'id_bahan' => $request->id_bahan,

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
        $data = RoleKandunganGizi::findOrFail($id);
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
        /*$request->validate([
            'kandungan_update' => 'required',
            'jumlah_update' => 'required'

        ]);

        RoleKandunganGizi::create([
            'id_kandungan_gizi' => $request->kandungan_update,
            'jumlah' => $request->jumlah_update,
            'id_bahan' => $request->id_bahan,

        ]);

        return response()->json(['success' => 'Data berhasil diperbarui']);*/
        try {
            $request->validate([
                'kandungan_update' => 'required',
                'jumlah_update' => 'required|numeric',
            ]);

            $roleKandunganGizi = RoleKandunganGizi::findOrFail($id);
            $roleKandunganGizi->update([
                'id_kandungan_gizi' => $request->kandungan_update,
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
        $data = RoleKandunganGizi::find($id);

        if (!$data) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        $data->delete();

        return response()->json(['success' => 'Data berhasil dihapus']);
    }
}
