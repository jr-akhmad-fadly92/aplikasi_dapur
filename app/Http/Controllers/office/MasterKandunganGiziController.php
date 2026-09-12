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

class MasterKandunganGiziController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $header = "Master Kandungan Gizi";
        if (request()->ajax()) {

            $MasterKandunganGizi = MasterKandunganGizi::all();


            return DataTables::of($MasterKandunganGizi)
                ->addIndexColumn() // Menambah index

                ->addColumn('action', function ($row) {

                return '<button data-id="' . $row->id . '" class="btn btn-warning btn-sm btn-edit">Edit</button> '
                    . '<button data-id="' . $row->id . '" class="btn btn-danger btn-sm btn-delete">Hapus</button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('office/kandungan_gizi.index', compact('header'));
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
            'kandungan' => 'required|string|max:255'
        ]);

        MasterKandunganGizi::create(['kandungan' => $request->kandungan]);

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
        $data = MasterKandunganGizi::findOrFail($id);
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
        $request->validate([
            'kandungan' => 'required|string|max:255'
        ]);

        $data = MasterKandunganGizi::findOrFail($id);
        $data->update(['kandungan' => $request->kandungan]);

        return response()->json(['success' => 'Data berhasil diperbarui']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = MasterKandunganGizi::find($id);

        if (!$data) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        $data->delete();

        return response()->json(['success' => 'Data berhasil dihapus']);
    }
}
