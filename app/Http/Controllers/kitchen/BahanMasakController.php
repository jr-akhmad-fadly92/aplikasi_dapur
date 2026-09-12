<?php

namespace App\Http\Controllers\kitchen;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\BahanMasak;
use App\Models\TbMasterBahan;
use App\Models\TbSatuan;
use Illuminate\Support\Facades\DB;
use DataTables;

class BahanMasakController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = BahanMasak::with('tb_master_bahan', 'tb_satuan')
                ->select('id', 'bahan_id', 'jumlah', 'id_satuan', 'kategori_bahan', 'status_bahan');

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('bahan_id', function ($row) {
                    return $row->tb_master_bahan ? $row->tb_master_bahan->bahan : '-';
                })
                ->editColumn('id_satuan', function ($row) {
                    return $row->tb_satuan ? $row->tb_satuan->satuan : '-';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('bahanmasak.edit', $row['id']) . '" class="edit btn btn-primary btn-sm " id="btn-edit-post" data-id="' . $row['id'] . '">Edit</a>
                            <a href="' . route('bahanmasak.destroy', $row->id) . '"
                            class="edit btn btn-danger btn-sm delete-button" 
                            data-id="' . $row->id . '">Delete</a>';
                    
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('kitchen/bahanmasak.index', ['header' => 'Master Bahan Masak']);
    }

    public function create()
    {
        $bahan = DB::table('tb_master_bahan')
                ->join('tb_satuan', 'tb_master_bahan.satuan_bahan', '=', 'tb_satuan.id')
                ->select('tb_master_bahan.id', 'tb_master_bahan.bahan', 'tb_satuan.satuan')
                ->get();
        $satuan = TbSatuan::all();
        return view('kitchen/bahanmasak.create', ['header' => 'Tambah Bahan Masak'], compact('bahan', 'satuan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'bahan_id' => 'required|exists:tb_master_bahan,id',
            'jumlah' => 'required|numeric|min:1',
        ]);

        // Cari satuan berdasarkan bahan yang dipilih
        $satuan = DB::table('tb_master_bahan')
            ->where('tb_master_bahan.id', $request->bahan_id) // Menyebutkan tabel secara eksplisit
            ->join('tb_satuan', 'tb_master_bahan.satuan_bahan', '=', 'tb_satuan.id')
            ->select('tb_satuan.id as id_satuan')
            ->first();

        // Simpan ke dalam tabel bahan_masak
        DB::table('tb_bahan_masak')->insert([
            'bahan_id' => $request->bahan_id,
            'jumlah' => $request->jumlah,
            'id_satuan' => $satuan->id_satuan,
            'kategori_bahan' => $request->kategori_bahan,
            'status_bahan' => $request->status_bahan,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('bahanmasak.index')->with('success', 'Data berhasil ditambahkan');
    }

    public function edit($id)
    {
        $bahanmasak = BahanMasak::findOrFail($id);
        $bahan = TbMasterBahan::all();
        $satuan = TbSatuan::all();
        return view('kitchen/bahanmasak.edit',['header' => 'Edit Bahan Masak'], compact('bahanmasak', 'bahan', 'satuan'));
    }

    public function update(Request $request, $id)
    {
        // Validate the incoming data
        $validated = $request->validate([
            'bahan_id' => 'required|exists:tb_master_bahan,id',
            'jumlah' => 'required|numeric',
            'id_satuan' => 'required|exists:tb_satuan,id',
            'kategori_bahan' => 'required|string',
            'status_bahan' => 'required|string',
        ]);

        // Find the BahanMasak record by ID and update it
        $bahanmasak = BahanMasak::findOrFail($id);
        $bahanmasak->update($validated);

        // Redirect back with success message
        return redirect()->route('bahanmasak.index')->with('success', 'Bahan Masak berhasil diupdate!');
    }

    public function destroy($id)
    {
        // Find the BahanMasak record by ID
        $bahanmasak = BahanMasak::findOrFail($id);

        // Delete the BahanMasak record
        $bahanmasak->delete();

        // Redirect back with success message
        return redirect()->route('bahanmasak.index')->with('success', 'Bahan Masak deleted successfully');
    }
}
