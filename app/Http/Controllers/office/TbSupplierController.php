<?php

namespace App\Http\Controllers\office;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\TbMasterBahan;
use DataTables;

class tbSupplierController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Supplier::select('id', 'nama_supplier',  'no_telp', 'alamat_supplier', 'nama_PIC');
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('jenis_supplier', function ($row) {
                    return $row->bahan ? $row->bahan->bahan : '-';
                })
                ->addColumn('action', function ($row) {
                    $editUrl = route('mastersupplier.edit', $row->id);
                    $deleteUrl = route('mastersupplier.destroy', $row->id);
                    return '
                        <a href="'.$editUrl.'" class="btn btn-warning btn-sm">Edit</a>
                        <form action="'.$deleteUrl.'" method="POST" style="display:inline;">
                            '.csrf_field().'
                            '.method_field("DELETE").'
                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                        </form>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('office/mastersupplier.index', ['header' => 'Master Supplier']);
    }

    public function create()
    {
        $jenisSupplier = TbMasterBahan::all(); // Ambil semua data dari tb_master_bahan
        return view('office/mastersupplier.create', ['header' => 'Daftar Supplier'], compact('jenisSupplier'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'nama_barang' => 'required|string|max:255',
           // 'jenis_supplier' => 'required|exists:tb_master_bahan,id',
            'no_telp' => 'required',
            'alamat_supplier' => 'required|string',
            'nama_PIC' => 'required|string',
        ]);

        // Simpan data supplier baru
        $supplier = new Supplier();
        $supplier->nama_supplier = $request->nama_barang; // Perbaikan nama field
        //$supplier->jenis_supplier = $request->jenis_supplier;
        $supplier->jenis_supplier = 0;
        $supplier->no_telp = $request->no_telp;
        $supplier->alamat_supplier = $request->alamat_supplier;
        $supplier->nama_PIC = $request->nama_PIC;
        $supplier->save();

        // Redirect dengan pesan sukses
        return redirect()->route('mastersupplier.index')->with('success', 'Supplier berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);
        $jenisSupplier = TbMasterBahan::all(); // Ambil semua jenis supplier
        return view('office/mastersupplier.edit', ['header' => 'Edit Supplier'], compact('supplier', 'jenisSupplier'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_supplier' => 'required|string|max:255',
            //'jenis_supplier' => 'required|exists:tb_master_bahan,id',
            'no_telp' => 'required',
            'alamat_supplier' => 'required|string',
            'nama_PIC' => 'required|string',
        ]);

        $supplier = Supplier::findOrFail($id);
        $supplier->update([
            'nama_supplier' => $request->nama_supplier,
            //'jenis_supplier' => $request->jenis_supplier,
            'no_telp' => $request->no_telp,
            'alamat_supplier' => $request->alamat_supplier,
            'nama_PIC' => $request->nama_PIC,
        ]);

        return redirect()->route('mastersupplier.index')->with('success', 'Supplier berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();

        return redirect()->route('mastersupplier.index')->with('success', 'Supplier berhasil dihapus!');
    }
}
