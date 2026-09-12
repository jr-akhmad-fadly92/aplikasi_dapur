<?php

namespace App\Http\Controllers\kitchen;

use App\Http\Controllers\Controller;
use App\Models\Gramasi;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
//import return type View
use Illuminate\View\View;
//import return type redirectResponse
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


use App\Models\CaraMasak;
use App\Models\Menu;


use App\Models\HasilMasak;
use App\Models\HistoriMenu;

class KitchenController extends Controller
{
    //
    public function index()
    {
        $header = "Dashboard Kitchen";
        Carbon::setlocale('id');
        $tanggal = date('y-m-d');
        
        $menus = Menu::join('tb_resep as karbohidrat_bahan', 'tb_menu.karbohidrat', '=', 'karbohidrat_bahan.id')
            ->join('tb_resep as protein_bahan', 'tb_menu.protein', '=', 'protein_bahan.id')
            ->join('tb_resep as sayur_bahan', 'tb_menu.sayur', '=', 'sayur_bahan.id')
            ->join('tb_resep as buah_bahan', 'tb_menu.buah', '=', 'buah_bahan.id')
            ->join('tb_resep as susu_bahan', 'tb_menu.susu', '=', 'susu_bahan.id')
           ->where('tb_menu.tanggal_kirim', Carbon::now('Asia/Jakarta')->toDateString())

	    // ->where('tb_menu.tanggal_kirim', Carbon::now()->toDateString()) // Gunakan whereDate agar cocok dengan format Y-m-d
           //->where('tb_menu.tanggal_kirim', '2025-04-07') // Gunakan whereDate agar cocok dengan format Y-m-d
           ->select(
                'tb_menu.id as id_menu',
                'tb_menu.karbohidrat',
                'tb_menu.protein',
                'tb_menu.sayur',
                'tb_menu.buah',
                'tb_menu.susu',
                'karbohidrat_bahan.nama_resep as nama_karbohidrat',
                'protein_bahan.nama_resep as nama_protein',
                'sayur_bahan.nama_resep as nama_sayur',
                'buah_bahan.nama_resep as nama_buah',
                'susu_bahan.nama_resep as nama_susu'
            )->first();
        if ($menus) {
            $histori_masak_a = HistoriMenu::where('id_menu', $menus->id_menu)->where('kode', 'A')->first();
            $histori_masak_b = HistoriMenu::where('id_menu', $menus->id_menu)->where('kode', 'B')->first();
        } else {
            $histori_masak_a = null;
            $histori_masak_b = null;
        }

        // Pastikan jika histori tidak ditemukan, total tetap 0
        $total = ($histori_masak_a->total_porsi ?? 0) + ($histori_masak_b->total_porsi ?? 0);

        // Data ompreng berdasarkan User ID (Line 1-4) dan menu hari ini
        $ompreng_line_1 = 0;
        $ompreng_line_2 = 0;
        $ompreng_line_3 = 0;
        $ompreng_line_4 = 0;

        if ($menus) {
            // LANGKAH 2: Hitung transaksi ompreng per user_id
            // PERBAIKAN: Hilangkan whereDate('created_at', $today) 

            // Line 1 (User ID = 1)
            $ompreng_line_1 = DB::table('tb_ompreng_transaksi')
                // ->where('tb_menu_id', $menu->id)
                ->where('tb_menu_id', $menus->id_menu)
                ->where('user_id', 1)
                ->count();

            // Line 2 (User ID = 2)
            $ompreng_line_2 = DB::table('tb_ompreng_transaksi')
                ->where('tb_menu_id', $menus->id_menu)
                ->where('user_id', 2)
                ->count();

            // Line 3 (User ID = 3)
            $ompreng_line_3 = DB::table('tb_ompreng_transaksi')
                ->where('tb_menu_id', $menus->id_menu)
                ->where('user_id', 3)
                ->count();

            // Line 4 (User ID = 4)
            $ompreng_line_4 = DB::table('tb_ompreng_transaksi')
                ->where('tb_menu_id', $menus->id_menu)
                ->where('user_id', 4)
                ->count();

            
        }

        // Total dari semua line
        $total_ompreng_lines = $ompreng_line_1 + $ompreng_line_2 + $ompreng_line_3 + $ompreng_line_4;

        return view('kitchen.dashboard', compact(
            'header',
            'menus',
            'tanggal', 
            'histori_masak_a', 
            'histori_masak_b', 
            'total',
            'ompreng_line_1',
            'ompreng_line_2',
            'ompreng_line_3',
            'ompreng_line_4',
            'total_ompreng_lines',
        ));
    }

    public function index_tv()
    {
        return redirect('/dashboard_kitchen');
        $header = "Dashboard Kitchen";
        $menus = Menu::join('tb_resep as karbohidrat_bahan', 'tb_menu.karbohidrat', '=', 'karbohidrat_bahan.id')
            ->join('tb_resep as protein_bahan', 'tb_menu.protein', '=', 'protein_bahan.id')
            ->join('tb_resep as sayur_bahan', 'tb_menu.sayur', '=', 'sayur_bahan.id')
            ->join('tb_resep as buah_bahan', 'tb_menu.buah', '=', 'buah_bahan.id')
            ->join('tb_resep as susu_bahan', 'tb_menu.susu', '=', 'susu_bahan.id')
            ->whereDate('tb_menu.tanggal_kirim', Carbon::now()->toDateString()) // Gunakan whereDate agar cocok dengan format Y-m-d
            ->select(
                'tb_menu.id as id_menu',
                'tb_menu.karbohidrat',
                'tb_menu.protein',
                'tb_menu.sayur',
                'tb_menu.buah',
                'tb_menu.susu',
                'karbohidrat_bahan.nama_resep as nama_karbohidrat',
                'protein_bahan.nama_resep as nama_protein',
                'sayur_bahan.nama_resep as nama_sayur',
                'buah_bahan.nama_resep as nama_buah',
                'susu_bahan.nama_resep as nama_susu'
            )->first();

        if ($menus) {
            $histori_masak_a = HistoriMenu::where('id_menu', $menus->id_menu)->where('kode', 'A')->first();
            $histori_masak_b = HistoriMenu::where('id_menu', $menus->id_menu)->where('kode', 'B')->first();
        } else {
            $histori_masak_a = null;
            $histori_masak_b = null;
        }

        // Pastikan jika histori tidak ditemukan, total tetap 0
        $total = ($histori_masak_a->total_porsi ?? 0) + ($histori_masak_b->total_porsi ?? 0);

        return view('dashboard_tv.dashboard_kitchen', compact('header', 'menus', 'histori_masak_a', 'histori_masak_b', 'total'));
    }



    public function indexCaraMasak()
    {
        if ($request->ajax()) {
            $data = CaraMasak::with('menu')
                ->select('id', 'id_menu', 'durasi', 'keterangan_menu')
                ->get();  // Get the data immediately (avoid lazy loading issues)

            dd($data); // Dump the data to check what is being retrieved

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('id_menu', function ($row) {
                    return $row->menu ? $row->menu->nama_menu : '-';
                })
                ->addColumn('action', function ($row) {
                    $editUrl = route('caramasak.edit', $row->id);
                    $deleteUrl = route('caramasak.destroy', $row->id);
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
        return view('office/caramasak.index', ['header' => 'Tata Cara Masak']);
    }
}
