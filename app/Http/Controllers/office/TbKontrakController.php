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
use Carbon\Carbon;

use App\Models\Supplier;
use App\Models\TbKontrak;
use App\Models\TbRincianKontrak;

class TbKontrakController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $header = "Dashboard Kontrak";
        if (request()->ajax()) {
            //$users = User::query();
            $resep = TbKontrak::join('tb_supplier', 'tb_kontrak.id_supplier', 'tb_supplier.id')
                ->select(['tb_kontrak.*', 'tb_supplier.nama_supplier']);


            return DataTables::of($resep)
                ->addIndexColumn() // Menambah index

                ->addColumn('action', function ($row) {
                    // $btn = '<a href="javascript:void(0)" class="edit btn btn-primary btn-sm " id="btn-edit-post" data-id="' . $row['id'] . '">Edit</a>';
                    /* $btn = '<a href="'. route('master_bahan.edit', $row['id']) .'" class="edit btn btn-primary btn-sm " id="btn-edit-post" data-id="' . $row['id'] . '">Edit</a>
                            <a href="' . route('master_bahan.delete', $row['id']) . '" class="edit btn btn-danger btn-sm delete-button" id="btn-delete-post " data-id="' . $row['id'] . '" >Delete</a>';
                    */
                    $btn = '<a href="' . route('dashboard-rincian-kontrak', $row['id']) . '" class="edit btn btn-success btn-sm " id="btn-edit-post" data-id="' . $row['id'] . '">detail</a>
                            <a href="' . route('kontrak.edit', $row['id']) . '" class="edit btn btn-primary btn-sm " id="btn-edit-post" data-id="' . $row['id'] . '">Edit</a>
                            <a href="' . route('kontrak.delete', $row->id) . '" 
                            class="edit btn btn-danger btn-sm delete-button" 
                            data-id="' . $row->id . '">Delete</a>';

                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('office/kontrak_supplier.index', compact('header'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $header = 'Tambah Kontrak Supplier';
        $jumlah_kontrak = TbKontrak::count();
        if($jumlah_kontrak < 9)
        {
            $jumlah_kontrak = '00'. ($jumlah_kontrak+1);
        } else if($jumlah_kontrak < 99){
            $jumlah_kontrak = '0' . ($jumlah_kontrak + 1);
        }
        $bulan_kontrak = Carbon::now()->format('Ym');// Format YYYY-MM untuk filter
        $nomor_kontrak = 'SUPK-'. $bulan_kontrak. $jumlah_kontrak;
        $Supplier = Supplier::select('id','nama_supplier')->get();

        return view('office/kontrak_supplier.create', compact('header', 'nomor_kontrak', 'Supplier'));
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
            'nomor_kontrak'                     => 'required|min:1',
            'supplier'                          => 'required',
            'awal_kontrak'                      => 'required',
            'akhir_kontrak'                     => 'required',
            'cara_pembayaran'                   => 'required',
            'bank'                              => 'required_with:nomor_rekening_pembayaran,nama_rekening',
            'nomor_rekening_pembayaran'         => 'required_with:bank,nama_rekening',
            'nama_rekening'                     => 'required_with:bank,nomor_rekening_pembayaran',

        ]);
        $cek = TbKontrak::where('nomor_kontrak', $request->nomor_kontrak)->count();
        if($cek > 0)
        {
            return redirect()->route('kontrak.index')->with(['error' => 'Data Sudah Pernah Masuk!']);
        }
        TbKontrak::create([
            'nomor_kontrak'              => $request->nomor_kontrak,
            'id_supplier'                => $request->supplier,
            'awal_kontrak'               => $request->awal_kontrak,
            'akhir_kontrak'              => $request->akhir_kontrak,
            'cara_pembayaran'            => $request->cara_pembayaran,
            'bank'                       => $request->bank,
            'nomor_rekening_pembayaran'  => $request->nomor_rekening_pembayaran,
            'nama_rekening'              => $request->nama_rekening,
            'status'                     => '',
           
        ]);
        
        return redirect()->route('kontrak.index')->with(['success' => 'Data Berhasil Diubah!']);
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
        $header = "Edit Kontrak";
        //get product by ID
        $kontrak = TbKontrak::findOrFail($id);
        $Supplier = Supplier::all();
        return view('office/kontrak_supplier.edit', compact('kontrak', 'Supplier', 'header'));
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
        //validate form
        
        $request->validate([
            'nomor_kontrak'                     => 'required|min:1',
            'supplier'                          => 'required',
            'awal_kontrak'                      => 'required',
            'akhir_kontrak'                     => 'required',
            'cara_pembayaran'                   => 'required',
            'bank'                              => 'required_with:nomor_rekening_pembayaran,nama_rekening',
            'nomor_rekening_pembayaran'         => 'required_with:bank,nama_rekening',
            'nama_rekening'                     => 'required_with:bank,nomor_rekening_pembayaran',

        ]);

        //get product by ID
        $kontrak = TbKontrak::findOrFail($id);



        //update product without image
        $kontrak->update([
          
            'id_supplier'                   => $request->supplier,
            'awal_kontrak'                  => $request->awal_kontrak,
            'akhir_kontrak'                 => $request->akhir_kontrak,
            'cara_pembayaran'               => $request->cara_pembayaran,
            'bank'                          => $request->bank,
            'nomor_rekening_pembayaran'     => $request->nomor_rekening_pembayaran,
            'nama_rekening'                 => $request->nama_rekening,
            'status'                        => '',
        ]);


        //redirect to index
        return redirect()->route('kontrak.index')->with(['success' => 'Data Berhasil Diubah!']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //get product by ID
        $kontrak = TbKontrak::findOrFail($id);
        TbRincianKontrak::where('id_kontrak', $id)->delete();

        //delete product
        $kontrak->delete();

        //redirect to index
        return redirect()->route('kontrak.index')->with(['success' => 'Data Berhasil Dihapus!']);
    }

    
}
