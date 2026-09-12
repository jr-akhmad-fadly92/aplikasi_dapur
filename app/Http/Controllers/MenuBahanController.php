<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MenuBahan;
use App\Models\BahanMasak;
use App\Models\Menu;
use App\Models\Satuan;
use DataTables;
use DB;

class MenuBahanController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = MenuBahan::with(['menu', 'bahan', 'satuan'])
                ->select('id', 'menu_id', 'bahan_id', 'jumlah', 'id_satuan');

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('menu_id', function ($row) {
                    return $row->menu ? $row->menu->nama_menu : '-';
                })
                ->editColumn('bahan_id', function ($row) {
                    return $row->bahan ? $row->bahan->masterBahan->bahan : '-'; // Ambil dari tb_master_bahan
                })
                ->editColumn('id_satuan', function ($row) {
                    return $row->satuan ? $row->satuan->satuan : '-';
                })
                ->addColumn('action', function ($row) {
                    $btn = ''; // Tambahkan tombol jika perlu
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('menubahan.index', ['header' => 'Master Menu Bahan']);
    }

    public function create()
    {
        $menus = Menu::all();

        $bahanMasak = DB::table('tb_bahan_masak')
            ->join('tb_master_bahan', 'tb_bahan_masak.bahan_id', '=', 'tb_master_bahan.id')
            ->join('tb_satuan', 'tb_master_bahan.satuan_bahan', '=', 'tb_satuan.id')
            ->select('tb_bahan_masak.id', 'tb_master_bahan.bahan', 'tb_satuan.satuan', 'tb_satuan.id as id_satuan')
            ->get();

        return view('menubahan.create', ['header' => 'Master Menu Bahan'], compact('menus', 'bahanMasak'));
    }

    public function store(Request $request)
{
    // Validasi input
    $validated = $request->validate([
        'menu_id' => 'required|exists:tb_menu,id',
        'bahan_id' => 'required|exists:tb_master_bahan,id',
        'jumlah' => 'required|numeric',
        'id_satuan' => 'required|exists:tb_satuan,id', 
    ]);

    // Debugging: Check if validated data is correct
    dd($validated);

    // Simpan data ke dalam tabel menu_bahan
    try {
        MenuBahan::create([
            'menu_id' => $validated['menu_id'],
            'bahan_id' => $validated['bahan_id'],
            'jumlah' => $validated['jumlah'],
            'id_satuan' => $validated['id_satuan'],
        ]);

        return redirect()->route('menubahan.index')->with('success', 'Data Menu Bahan berhasil disimpan');
    } catch (\Exception $e) {
        // Log the error message
        \Log::error('Error inserting into menu_bahan: ' . $e->getMessage());
        return back()->withErrors(['error' => 'There was an error saving the data.']);
    }
}


}

