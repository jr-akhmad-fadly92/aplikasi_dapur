<?php

namespace App\Http\Controllers\office;

use App\Http\Controllers\Controller;
use App\Models\DataDapur;
use App\Models\TbSatuan;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
//import return type View
use Illuminate\View\View;
//import return type redirectResponse
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class TbSatuanController extends Controller
{
    public function index()
    {
        $header = "Master Satuan";
        if (request()->ajax()) {
            //$users = User::query();
            $satuan = TbSatuan::all();


            return DataTables::of($satuan)
                ->addIndexColumn() // Menambah index

                ->addColumn('action', function ($row) {
                    // $btn = '<a href="javascript:void(0)" class="edit btn btn-primary btn-sm " id="btn-edit-post" data-id="' . $row['id'] . '">Edit</a>';
                    /* $btn = '<a href="'. route('master_bahan.edit', $row['id']) .'" class="edit btn btn-primary btn-sm " id="btn-edit-post" data-id="' . $row['id'] . '">Edit</a>
                            <a href="' . route('master_bahan.delete', $row['id']) . '" class="edit btn btn-danger btn-sm delete-button" id="btn-delete-post " data-id="' . $row['id'] . '" >Delete</a>';
                    */
                    $btn = '<a href="' . route('master_satuan.edit', $row['id']) . '" class="edit btn btn-primary btn-sm " id="btn-edit-post" data-id="' . $row['id'] . '">Edit</a>
                            <a href="' . route('master_satuan.delete', $row->id) . '" 
                            class="edit btn btn-danger btn-sm delete-button" 
                            data-id="' . $row->id . '">Delete</a>';

                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('office/mastersatuan.index',compact('header'));
    }


    public function edit(string $id): View
    {
        $header = "Edit Satuan";
        //get product by ID
        $satuan = TbSatuan::findOrFail($id);
        
        //render view with product
        return view('office/mastersatuan.edit', compact( 'satuan','header'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        //validate form
        $request->validate([
            'satuan'         => 'required|min:1',
            

        ]);

        //get product by ID
        $satuan = TbSatuan::findOrFail($id);



        //update product without image
        $satuan->update([
            'satuan'             => $request->satuan,
        ]);


        //redirect to index
        return redirect()->route('master_satuan.index')->with(['success' => 'Data Berhasil Diubah!']);
    }

    public function create(): View
    {
        $header = "Tambah Satuan";
        $satuan = TbSatuan::all();
        return view('office/mastersatuan.create', compact('satuan','header'));
    }

    public function store(Request $request): RedirectResponse
    {
        //validate form
        $request->validate([
            'satuan'         => 'required|min:1',
           

        ]);


        //create product
        TbSatuan::create([
            'satuan'             => $request->satuan,
           
        ]);

        //redirect to index
        return redirect()->route('master_satuan.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }

    public function destroy($id)
    {
        //get product by ID
        $satuan = TbSatuan::findOrFail($id);


        //delete product
        $satuan->delete();

        //redirect to index
        return redirect()->route('master_satuan.index')->with(['success' => 'Data Berhasil Dihapus!']);
    }

    public function pdf_laporan_master_satuan()
    {

        $dapur = DataDapur::first();
        $satuan = TbSatuan::all();
        $pdf = Pdf::loadView('office/mastersatuan.template_laporan_master_satuan', compact(

            'dapur',
            'satuan',
            


        ));
        return $pdf->download('laporan_master_satuan.pdf');
    }
}
