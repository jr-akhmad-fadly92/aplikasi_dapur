<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OmprengManagementController extends Controller
{
    public function index()
    {
        try {
            // Step 1: Query Menu Harian berdasarkan tanggal hari ini
            $today = Carbon::today()->format('Y-m-d');
            
            $menu = DB::table('tb_menu')
                ->where('tanggal_kirim', $today)
                ->first();

            // Data ompreng berdasarkan User ID (Line 1-4) dan menu hari ini
            $ompreng_line_1 = 0;
            $ompreng_line_2 = 0;
            $ompreng_line_3 = 0;
            $ompreng_line_4 = 0;
            
            if ($menu) {
                // Line 1 (User ID = 1) - menghitung tb_menu_id yang dipesan
                $ompreng_line_1 = DB::table('tb_ompreng_transaksi')
                    ->where('tb_menu_id', $menu->id)
                    ->where('user_id', 1)
                    ->whereDate('created_at', $today)
                    ->count();

                // Line 2 (User ID = 2)
                $ompreng_line_2 = DB::table('tb_ompreng_transaksi')
                    ->where('tb_menu_id', $menu->id)
                    ->where('user_id', 2)
                    ->whereDate('created_at', $today)
                    ->count();

                // Line 3 (User ID = 3)
                $ompreng_line_3 = DB::table('tb_ompreng_transaksi')
                    ->where('tb_menu_id', $menu->id)
                    ->where('user_id', 3)
                    ->whereDate('created_at', $today)
                    ->count();

                // Line 4 (User ID = 4)
                $ompreng_line_4 = DB::table('tb_ompreng_transaksi')
                    ->where('tb_menu_id', $menu->id)
                    ->where('user_id', 4)
                    ->whereDate('created_at', $today)
                    ->count();
            }

            // Total dari semua line
            $total_ompreng_lines = $ompreng_line_1 + $ompreng_line_2 + $ompreng_line_3 + $ompreng_line_4;

            // Data untuk statistik tambahan
            $ompreng_total = DB::table('tb_ompreng')->where('jenis', 'Ompreng')->count();
            $rantang_total = DB::table('tb_ompreng')->where('jenis', 'Rantang')->count();
            
            $ompreng_keluar = DB::table('tb_ompreng_transaksi')
                ->where('status', 0)
                ->whereNotNull('kode_ompreng')
                ->count();
                
            $ompreng_masuk = DB::table('tb_ompreng_transaksi')
                ->where('status', 1)
                ->whereNotNull('kode_ompreng')
                ->count();

            $rantang_keluar = DB::table('tb_ompreng_transaksi')
                ->where('status', 0)
                ->whereNotNull('kode_rantang')
                ->count();

            $rantang_masuk = DB::table('tb_ompreng_transaksi')
                ->where('status', 1)
                ->whereNotNull('kode_rantang')
                ->count();

            // Total penerima dari rincian sekolah (TOTAL PORSI)
            $total_porsi = 0;
            if ($menu) {
                $total_porsi = DB::table('rincian_sekolah')
                    ->where('id_menu_harian', $menu->id)
                    ->sum('jumlah_penerima_total');
            }

            return view('ompreng-management.index', [
                'menu' => $menu,
                'message' => $menu ? null : 'Tidak ada menu untuk hari ini',
                'ompreng_line_1' => $ompreng_line_1,
                'ompreng_line_2' => $ompreng_line_2,
                'ompreng_line_3' => $ompreng_line_3,
                'ompreng_line_4' => $ompreng_line_4,
                'total_ompreng_lines' => $total_ompreng_lines,
                'ompreng_total' => $ompreng_total,
                'rantang_total' => $rantang_total,
                'ompreng_keluar' => $ompreng_keluar,
                'ompreng_masuk' => $ompreng_masuk,
                'rantang_keluar' => $rantang_keluar,
                'rantang_masuk' => $rantang_masuk,
                'total_porsi' => $total_porsi,
                'tanggal' => $today
            ]);

        } catch (\Exception $e) {
            return view('ompreng-management.index', [
                'menu' => null,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'ompreng_line_1' => 0,
                'ompreng_line_2' => 0,
                'ompreng_line_3' => 0,
                'ompreng_line_4' => 0,
                'total_ompreng_lines' => 0,
                'ompreng_total' => 0,
                'rantang_total' => 0,
                'ompreng_keluar' => 0,
                'ompreng_masuk' => 0,
                'rantang_keluar' => 0,
                'rantang_masuk' => 0,
                'total_porsi' => 0,
                'tanggal' => now()->format('Y-m-d')
            ]);
        }
    }
}
