<?php

namespace App\Http\Controllers\office;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\tb_karyawan;
use DataTables;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
class KaryawanController extends Controller
{
    //
    public function index()
    {
        $header = "Dashboard Karyawan";
        
        if (request()->ajax()) {
            //$users = User::query();
            $karyawan = tb_karyawan::all();
            return DataTables::of($karyawan)
                ->addIndexColumn() // Menambah index

                ->addColumn('action', function ($row) {
                    // $btn = '<a href="javascript:void(0)" class="edit btn btn-primary btn-sm " id="btn-edit-post" data-id="' . $row['id'] . '">Edit</a>';
                    /* $btn = '<a href="'. route('master_bahan.edit', $row['id']) .'" class="edit btn btn-primary btn-sm " id="btn-edit-post" data-id="' . $row['id'] . '">Edit</a>
                            <a href="' . route('master_bahan.delete', $row['id']) . '" class="edit btn btn-danger btn-sm delete-button" id="btn-delete-post " data-id="' . $row['id'] . '" >Delete</a>';
                    */
                    $btn = '<a href="' . route('karyawan.edit', $row['id']) . '" class="edit btn btn-primary btn-sm " id="btn-edit-post" data-id="' . $row['id'] . '">Edit</a>
                            <a href="' . route('karyawan.delete', $row->id) . '" 
                            class="edit btn btn-danger btn-sm delete-button" 
                            data-id="' . $row->id . '">Delete</a>';

                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('office/masterkaryawan.index', compact('header'));
    }

    public function edit(string $id): View
    {
        $header = "Edit Karyawan";
        //get product by ID
        $karyawan = tb_karyawan::findOrFail($id);

        //render view with product
        return view('office/masterkaryawan.edit', compact('karyawan', 'header'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        //validate form
        $request->validate([
            'nama'              => 'required|min:1',
            'jabatan'           => 'required|min:1',
            'tanggal_masuk'     => 'required|min:1',
            'status'            => 'required|min:1',
            'kontak'            => 'required|min:1',
            'id_karyawan '      => 'required|min:1',
        ]);

        //get product by ID
        $karyawan = TbSatuan::findOrFail($id);



        //update product without image
        $karyawan->update([
            'nama'                  => $request->nama,
            'jabatan'               => $request->jabatan,
            'tanggal_masuk'         => $request->tanggal_masuk,
            'status'                => $request->status,
            'kontak'                => $request->kontak,
        ]);


        //redirect to index
        return redirect()->route('masterkaryawan.index')->with(['success' => 'Data Berhasil Diubah!']);
    }

    public function create(): View
    {
        $header = "Tambah Karyawan";
        $karyawan = tb_karyawan::all();
        return view('office/masterkaryawan.create', compact('karyawan', 'header'));
    }

    public function store(Request $request): RedirectResponse
    {
        //validate form
        $request->validate([
            
                'nama'          => 'required|min:1',
                'jabatan'       => 'required|in:kepala,sopir,analis',
                'tanggal_masuk' => 'required|date',
                'status'        => 'required|in:kontrak,tetap',
                'kontak'        => 'required|min:1',
                'id_karyawan'   => 'required|min:1',


        ]);


        //create product
        tb_karyawan::create([
            'id_karyawan'           => $request->id_karyawan,
            'nama'                  => $request->nama,
            'jabatan'               => $request->jabatan,
            'tanggal_masuk'         => $request->tanggal_masuk,
            'status'                => $request->status,
            'kontak'                => $request->kontak,
        ]);

        //redirect to index
        return redirect()->route('karyawan.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }

    public function destroy($id)
    {
        //get product by ID
        $karyawan = tb_karyawan::findOrFail($id);


        //delete product
        $karyawan->delete();

        //redirect to index
        return redirect()->route('karyawan.index')->with(['success' => 'Data Berhasil Dihapus!']);
    }
}
