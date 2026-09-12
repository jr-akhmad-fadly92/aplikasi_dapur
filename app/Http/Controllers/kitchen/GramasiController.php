<?php


namespace App\Http\Controllers\kitchen;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
//import return type View
use Illuminate\View\View;
//import return type redirectResponse
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

use App\Models\Gramasi;



class GramasiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $header = "Master Gramasi";
        if (request()->ajax()) {
            //$users = User::query();
            $Gramasi = Gramasi::all();


            return DataTables::of($Gramasi)
                ->addIndexColumn() // Menambah index

                ->addColumn('action', function ($row) {
                    
                    return '<button class="btn btn-warning btn-sm edit" data-id="' . $row->id . '">Edit</button>
                        <!--button class="btn btn-danger btn-sm delete" data-id="' . $row->id . '">Hapus</!--button-->';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('kitchen/gramasi.index', compact('header'));
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
            'kode' => 'required',
            'karbohidrat' => 'required|numeric',
            'protein' => 'required|numeric',
            'sayur' => 'required|numeric',
            'buah' => 'required|numeric',
            'susu' => 'required|numeric',
        ]);

        Gramasi::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil ditambahkan!'
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
        $gramasi = Gramasi::findOrFail($id);
        return response()->json($gramasi);
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
        Gramasi::findOrFail($id)->update($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Data berhasil Dirubah!'
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
        Gramasi::destroy($id);
        return response()->json([
            'success' => true,
            'message' => 'Data berhasil Dihapus!'
        ]);
    }
}
