<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventori;
use App\Models\TransaksiInventori;
use DataTables;
use Barryvdh\DomPDF\Facade\Pdf;


class InventoriController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $header = "Master Inventori";
        if (request()->ajax()) {
            $inventori = Inventori::select('id','kode_barang', 'nama_barang','jenis_barang','jumlah_stok')->get();


            return DataTables::of($inventori)
                ->addIndexColumn()

                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('inventori.show', $row['id']) . '" class="edit btn btn-success btn-sm " id="btn-detail-post" data-id="' . $row['id'] . '">Detail</a>
                            <a href="' . route('inventori.edit', $row['id']) . '" class="edit btn btn-primary btn-sm " id="btn-edit-post" data-id="' . $row['id'] . '">Edit</a>
                            <a href="' . route('inventori.destroy', $row->id) . '" class="edit btn btn-danger btn-sm delete-button" data-id="' . $row->id . '">Delete</a>
                            <a href="' . route('download.pdf') . '" class="edit btn btn-secondary btn-sm " id="btn-download-post">Download</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('inventori.index',compact('header'));
    }

    public function create()
    {
        return view('inventori.create', ['header' => 'Tambah Inventori']);
    }

    /**
     * Menyimpan data tingkatan sekolah ke database.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'kode_barang' => 'required|string|max:255',
            'nama_barang' => 'required|string|max:255',
            'jenis_barang' => 'required|string|max:255',
            'jumlah_stok' => 'required|numeric',
        ]);

        // Menyimpan data ke database
        inventori::create([
            'kode_barang' => $validated['kode_barang'],
            'nama_barang' => $validated['nama_barang'],
            'jenis_barang' => $validated['jenis_barang'],
            'jumlah_stok' => $validated['jumlah_stok'],
        ]);

        // Redirect ke halaman lain setelah sukses
        return redirect()->route('inventori.index')->with('success', 'Data inventori berhasil ditambahkan');
    }

    public function edit($id)
    {
        $inventori = Inventori::findOrFail($id);
        return view('inventori.edit', ['header' => 'Edit Data'], compact('inventori'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kode_barang' => 'required|string|max:255',
            'nama_barang' => 'required|string|max:255',
            'jenis_barang' => 'required|string|max:255',
            'jumlah_stok' => 'required|numeric',
        ]);

        $inventori = Inventori::findOrFail($id);
        $inventori->update([
            'kode_barang' => $request->kode_barang,
            'nama_barang' => $request->nama_barang,
            'jenis_barang' => $request->jenis_barang,
            'jumlah_stok' => $request->jumlah_stok,
        ]);

        return redirect()->route('inventori.index')->with('success', 'Data berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $inventori = Inventori::findOrFail($id);

        $inventori->delete();

        return redirect()->route('inventori.index')->with(['success' => 'Data Berhasil Dihapus!']);
    }

    public function show($id)
    {
        $inventori = Inventori::findOrFail($id);

        $transaksi = TransaksiInventori::where('kode_barang', $inventori->kode_barang)->get();

        return view('inventori.detail', compact('inventori', 'transaksi'));
    }

    
    public function template()
    {
        return view('inventori.template');
    }

    public function downloadPDF()
    {
        // Render file template.blade.php tanpa data
        $pdf = Pdf::loadView('inventori.template');

        // Unduh sebagai PDF dengan nama file "Formulir_Pengajuan_Menu_Harian.pdf"
        return $pdf->download('Formulir_Pengajuan_Menu_Harian.pdf');
    }

}
