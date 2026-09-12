<?php

namespace App\Http\Controllers\penerimaan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\TbWadah;

class TbWadahController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Menampilkan daftar wadah dalam bentuk tabel menggunakan DataTables.
     */
    public function index()
    {
        $header = "Dashboard Wadah";

        if (request()->ajax()) {
            $wadah = TbWadah::all();

            return DataTables::of($wadah)
                ->addIndexColumn()
                ->addColumn('status_wadah', function ($row) {
                    return $row->status == 0 ? 'kosong' : 'terpakai';
                })
                ->addColumn('action', function ($row) {
                    return '<a href="' . route('master_wadah.edit', $row->id) . '" class="edit btn btn-primary btn-sm">Edit</a>
                            <a href="' . route('master_wadah.delete', $row->id) . '" class="btn btn-danger btn-sm delete-button" onclick="return confirm(\'Apakah Anda yakin ingin menghapus wadah ini?\')">Delete</a>';
                })
                ->rawColumns(['action', 'status_wadah'])
                ->make(true);
        }

        return view('penerimaan/wadah.index', compact('header'));
    }

    /**
     * Menampilkan halaman form tambah wadah.
     */
    public function create()
    {
        $header = "Form Tambah Wadah";
        return view('penerimaan/wadah.create', compact('header'));
    }

    /**
     * Menyimpan data wadah ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string|max:30|unique:tb_wadah',
        ]);

        TbWadah::create($request->all());

        return redirect()->route('master_wadah.index')->with('success', 'Wadah berhasil ditambahkan!');
    }

    /**
     * Menampilkan halaman edit wadah berdasarkan ID.
     */
    public function edit($id)
    {
        $header = "Form Edit Wadah";
        $wadah = TbWadah::findOrFail($id);
        return view('penerimaan/wadah.edit', compact('wadah', 'header'));
    }

    /**
     * Memperbarui data wadah berdasarkan ID.
     */
    public function update(Request $request, $id)
    {
        $wadah = TbWadah::findOrFail($id);

        $request->validate([
            'qr_code' => 'required|string|max:30|unique:tb_wadah,qr_code,' . $id,
        ]);

        $wadah->update($request->all());

        return redirect()->route('master_wadah.index')->with('success', 'Wadah berhasil diperbarui!');
    }

    /**
     * Menghapus data wadah berdasarkan ID.
     */
    public function destroy($id)
    {
        $wadah = TbWadah::findOrFail($id);
        $wadah->delete();

        return redirect()->route('master_wadah.index')->with('success', 'Wadah berhasil dihapus!');
    }
}
