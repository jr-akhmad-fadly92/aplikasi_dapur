<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransaksiInventori;
use App\Models\Inventori;
use DataTables;

class TransaksiInventoriController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $header = "Master Transaksi Inventori";
        if (request()->ajax()) {
            //$transaksiinventori = TransaksiInventori::select('id','kode_barang', 'kondisi','pic','jumlah', 'status')->get();
            $transaksiinventori = TransaksiInventori::all();

            return DataTables::of($transaksiinventori)
                ->addIndexColumn()

                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('transaksiinventori.index') . '" class="edit btn btn-success btn-sm " id="btn-detail-post" data-id="' . $row['id'] . '">Detail</a>
                            <a href="' . route('transaksiinventori.edit', $row['id']) . '" class="edit btn btn-primary btn-sm " id="btn-edit-post" data-id="' . $row['id'] . '">Edit</a>
                            <a href="' . route('transaksiinventori.destroy', $row->id) . '"
                            class="edit btn btn-danger btn-sm delete-button" 
                            data-id="' . $row->id . '">Delete</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('transaksiinventori.index',compact('header'));
    }

    public function create(Request $request)
    {
        $kode_barang = $request->query('kode_barang');

        if (!$kode_barang) {
            return redirect()->route('inventori.index')->with('error', 'Kode Barang tidak ditemukan');
        }

        $barang = Inventori::where('kode_barang', $kode_barang)->first();

        if (!$barang) {
            return redirect()->route('inventori.index')->with('error', 'Barang tidak ditemukan');
        }

        return view('transaksiinventori.create', compact('barang', 'kode_barang'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'kode_barang' => 'required|exists:tb_inventori,kode_barang',
            'kondisi' => 'required|string|max:255',
            'pic' => 'required|numeric',
            'jumlah' => 'required|numeric',
            'status' => 'required|string|max:255',
        ]);

        // Simpan ke database
        TransaksiInventori::create([
            'kode_barang' => $request->kode_barang,
            'kondisi' => $request->kondisi,
            'pic' => $request->pic,
            'jumlah' => $request->jumlah,
            'status' => $request->status,
        ]);

        return redirect()->route('inventori.index')->with('success', 'Data Transaksi Inventori berhasil ditambahkan');
    }

    public function edit($id)
    {
        $transaksi = TransaksiInventori::findOrFail($id);

        return view('transaksiInventori.edit', compact('transaksi'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kode_barang' => 'required|string|max:255',
            'kondisi' => 'required|string|max:255',
            'pic' => 'required|numeric',
            'jumlah' => 'required|numeric',
            'status' => 'required|string|max:255',
        ]);

        $transaksiinventori = TransaksiInventori::findOrFail($id);
        $transaksiinventori->update([
            'kode_barang' => $request->kode_barang,
            'kondisi' => $request->kondisi,
            'pic' => $request->pic,
            'jumlah' => $request->jumlah,
            'status' => $request->status,
        ]);

        return redirect()->route('inventori.index')->with('success', 'Data berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $transaksiinventori = TransaksiInventori::findOrFail($id);

        $transaksiinventori->delete();

        return redirect()->route('transaksiinventori.index')->with(['success' => 'Data Berhasil Dihapus!']);
    }

}
