<?php

namespace App\Http\Controllers\warehouse;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\StokGudang;
use App\Models\TbMasterBahan;
use App\Models\TbSatuan;
use Illuminate\Support\Facades\DB;
use DataTables;

class StokGudangController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = StokGudang::with('tb_master_bahan', 'tb_satuan')
                ->select('id', 'nama_barang', 'jumlah', 'id_satuan', 'kategori_bahan', 'status_bahan');

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('nama_barang', function ($row) {
                    return $row->tb_master_bahan ? $row->tb_master_bahan->bahan : '-';
                })
                ->editColumn('id_satuan', function ($row) {
                    return $row->tb_satuan ? $row->tb_satuan->satuan : '-';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('stokgudang.edit', $row['id']) . '" class="edit btn btn-primary btn-sm " id="btn-edit-post" data-id="' . $row['id'] . '">Edit</a>
                            <a href="' . route('stokgudang.destroy', $row->id) . '"
                            class="edit btn btn-danger btn-sm delete-button" 
                            data-id="' . $row->id . '">Delete</a>';
                    
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('warehouse/stokgudang.index', ['header' => 'Master Stok Gudang']);
    }

    public function create()
    {
        $bahan = DB::table('tb_master_bahan')
                ->join('tb_satuan', 'tb_master_bahan.satuan_bahan', '=', 'tb_satuan.id')
                ->select('tb_master_bahan.id', 'tb_master_bahan.bahan', 'tb_satuan.satuan')
                ->get();
        $satuan = TbSatuan::all();
        return view('warehouse/stokgudang.create', ['header' => 'Tambah Stok Gudang'], compact('bahan', 'satuan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required|exists:tb_master_bahan,id',
            'jumlah' => 'required|numeric|min:1',
        ]);

        $satuan = DB::table('tb_master_bahan')
            ->where('tb_master_bahan.id', $request->nama_barang)
            ->join('tb_satuan', 'tb_master_bahan.satuan_gudang', '=', 'tb_satuan.id')
            ->select('tb_satuan.id as id_satuan')
            ->first();

        DB::table('tb_stok_gudang')->insert([
            'nama_barang' => $request->nama_barang,
            'jumlah' => $request->jumlah,
            'id_satuan' => $satuan->id_satuan,
            'kategori_bahan' => $request->kategori_bahan,
            'status_bahan' => $request->status_bahan,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('stokgudang.index')->with('success', 'Data berhasil ditambahkan');
    }

    public function edit($id)
    {
        $stokgudang = StokGudang::findOrFail($id);
        $bahan = TbMasterBahan::all();
        $satuan = TbSatuan::all();
        return view('warehouse/stokgudang.edit',['header' => 'Edit Stok Gudang'], compact('stokgudang', 'bahan', 'satuan'));
    }

    public function update(Request $request, $id)
    {
        // Validate the incoming data
        $validated = $request->validate([
            'nama_barang' => 'required|exists:tb_master_bahan,id',
            'jumlah' => 'required|numeric',
            'id_satuan' => 'required|exists:tb_satuan,id',
            'kategori_bahan' => 'required|string',
            'status_bahan' => 'required|string',
        ]);

        // Find the StokGudang record by ID and update it
        $stokgudang = StokGudang::findOrFail($id);
        $stokgudang->update($validated);

        // Redirect back with success message
        return redirect()->route('stokgudang.index')->with('success', 'Stok Gudang berhasil diupdate!');
    }

    public function destroy($id)
    {
        // Find the StokGudang record by ID
        $stokgudang = StokGudang::findOrFail($id);

        // Delete the StokGudang record
        $stokgudang->delete();

        // Redirect back with success message
        return redirect()->route('stokgudang.index')->with('success', 'Stok Gudang deleted successfully');
    }
}
