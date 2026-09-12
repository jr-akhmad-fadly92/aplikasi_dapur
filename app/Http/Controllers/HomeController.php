<?php

namespace App\Http\Controllers;

use App\Models\DataDapur;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\DataSekolah;
use App\Models\Supplier;
use App\Models\TbOmpreng;
use App\Models\rincian_sekolah;
use App\Models\Menu;
use App\Models\tbOmprengTransaksi;

class HomeController extends Controller
{
    public function __construct()
    {
        // Middleware auth será controlado via routes
    }

    /**
     * Menampilkan halaman dashboard utama
     */
    public function index()
    {
        $header = "Dashboard";
        
        return view('Home', compact('header'));
    }

    /**
     * Menampilkan dashboard office dengan data sekolah dan supplier
     */
    public function v_office()
    {
        $header = "Dashboard";
        $sekolahAktifQuery      = DataSekolah::where('status_aktif', 1);
        $jumlah_sekolah_all     = (clone $sekolahAktifQuery)->count();
        $penerima_sekolah_all   = (clone $sekolahAktifQuery)->sum('jumlah_siswa');

        $jumlah_sekolah_tk      = (clone $sekolahAktifQuery)->where('jenjang_sekolah', 'TK/Sederajat')->count();
        $penerima_sekolah_tk    = (clone $sekolahAktifQuery)->where('jenjang_sekolah', 'TK/Sederajat')->sum('jumlah_siswa');

        $jumlah_sekolah_sd      = (clone $sekolahAktifQuery)->where('jenjang_sekolah', 'SD/Sederajat')->count();
        $penerima_sekolah_sd    = (clone $sekolahAktifQuery)->where('jenjang_sekolah', 'SD/Sederajat')->sum('jumlah_siswa');

        $jumlah_sekolah_smp     = (clone $sekolahAktifQuery)->where('jenjang_sekolah', 'SMP/Sederajat')->count();
        $penerima_sekolah_smp   = (clone $sekolahAktifQuery)->where('jenjang_sekolah', 'SMP/Sederajat')->sum('jumlah_siswa');

        $jumlah_sekolah_sma     = (clone $sekolahAktifQuery)->where('jenjang_sekolah', 'SMA/Sederajat')->count();
        $penerima_sekolah_sma   = (clone $sekolahAktifQuery)->where('jenjang_sekolah', 'SMA/Sederajat')->sum('jumlah_siswa');

        $jumlah_sekolah_ibu     = (clone $sekolahAktifQuery)->where('jenjang_sekolah', 'ibu')->count();
        $penerima_sekolah_ibu   = (clone $sekolahAktifQuery)->where('jenjang_sekolah', 'ibu')->sum('jumlah_siswa');

        $supplier           = Supplier::count();
        // Data Ompreng untuk Dashboard - Query berdasarkan User ID 1-4
        $today = Carbon::today()->format('Y-m-d');
        //$header = "Dashboard";
        //$today = '2025-08-4';
        // LANGKAH 1: Ambil menu berdasarkan tanggal_kirim hari
        // Prioritaskan menu dengan status 'approved' atau ambil yang ID terbesar
        $menu = DB::table('tb_menu')
            ->where('tanggal_kirim', $today)
            //->where('tanggal_kirim', '2025-07-31')
            //->where('status_pengajuan', 'approved')
            ->orderBy('id', 'asc')
            ->first();
        $menu2 = DB::table('tb_menu')
            ->where('tanggal_kirim', $today)
            //->where('tanggal_kirim', '2025-07-31')
            //->where('status_pengajuan', 'approved')
            ->orderBy('id', 'desc')
            ->first();

        // Jika tidak ada menu approved, ambil menu apapun untuk tanggal tersebut
        if (!$menu) {
            $menu = DB::table('tb_menu')
                ->where('tanggal_kirim', $today)
                //->where('tanggal_kirim', '2025-07-31')

                ->orderBy('id', 'desc')
                ->first();
            $menu2 = DB::table('tb_menu')
                ->where('tanggal_kirim', $today)
                //->where('tanggal_kirim', '2025-07-31')

                ->orderBy('id', 'asc')
                ->first();
        }

        // Debug: Log untuk melihat data
        Log::info('Debug Ompreng Dashboard', [
            'today' => $today,
            'menu' => $menu ? $menu->id : 'No menu found'
        ]);

        // Data ompreng berdasarkan User ID (Line 1-4) dan menu hari ini
        $ompreng_line_1 = 0;
        $ompreng_line_2 = 0;
        $ompreng_line_3 = 0;
        $ompreng_line_4 = 0;

        if ($menu) {
            // LANGKAH 2: Hitung transaksi ompreng per user_id
            // PERBAIKAN: Hilangkan whereDate('created_at', $today) 

            // Line 1 (User ID = 1)
            $ompreng_line_1 = DB::table('tb_ompreng_transaksi')
                // ->where('tb_menu_id', $menu->id)
                ->where('tb_menu_id', $menu->id)
                ->where('user_id', 1)
                ->count();

            // Line 2 (User ID = 2)
            $ompreng_line_2 = DB::table('tb_ompreng_transaksi')
                ->where('tb_menu_id', $menu->id)
                ->where('user_id', 2)
                ->count();

            // Line 3 (User ID = 3)
            $ompreng_line_3 = DB::table('tb_ompreng_transaksi')
                ->where('tb_menu_id', $menu->id)
                ->where('user_id', 3)
                ->count();

            // Line 4 (User ID = 4)
            $ompreng_line_4 = DB::table('tb_ompreng_transaksi')
                ->where('tb_menu_id', $menu->id)
                ->where('user_id', 4)
                ->count();

            // Debug: Log hasil query
            Log::info('Ompreng Line Results', [
                'menu_id' => $menu->id,
                'line_1' => $ompreng_line_1,
                'line_2' => $ompreng_line_2,
                'line_3' => $ompreng_line_3,
                'line_4' => $ompreng_line_4
            ]);
        }

        // Total dari semua line
        $total_ompreng_lines = $ompreng_line_1 + $ompreng_line_2 + $ompreng_line_3 + $ompreng_line_4;

        // Data untuk informasi tambahan
        $ompreng_total = DB::table('tb_ompreng')->where('jenis', 'Ompreng')->count();
        $rantang_total = DB::table('tb_ompreng')->where('jenis', 'Rantang')->count();

        //$ompreng_keluar = $total_ompreng_lines;
        /*DB::table('tb_ompreng_transaksi')
            ->where('status', 0)
            ->whereNotNull('kode_ompreng')
            ->count();
        */
        $rantang_keluar = DB::table('tb_ompreng_transaksi')
            ->where('status', 0)
            ->whereNotNull('kode_rantang')
            ->count();

        // Total penerima dari rincian sekolah (TOTAL PORSI)
        $total_porsi = 0;
        if ($menu) {
            $total_porsi = DB::table('rincian_sekolah')
                ->where('id_menu_harian', $menu->id)
                
                ->sum('jumlah_penerima_total');
            $total_porsi2 = DB::table('rincian_sekolah')
                ->where('id_menu_harian', $menu2->id)
                ->sum('jumlah_penerima_total');
                if($menu2->id == $menu->id)
                {
                $total_porsi = $total_porsi;
                }else{
                $total_porsi = $total_porsi2 + $total_porsi;
                }
            
        }
        //$ompreng_keluar = $total_porsi;
        $ompreng_keluar = DB::table('surat_jalan')
            ->join('surat_jalan_item', 'surat_jalan.referensi', '=', 'surat_jalan_item.surat_jalan_referensi')
            ->where('surat_jalan.id_menu_harian', ($menu->id??0))
            ->sum('surat_jalan_item.jumlah')??0;
        // Data sekolah dan supplier untuk dashboard
        $dataSekolah = (clone $sekolahAktifQuery)->get();
        $jumlahSekolah = (clone $sekolahAktifQuery)->count();
        $totalSupplier = Supplier::count();
        $cek_tanggal = Carbon::today()->format('d');
        $keterangan_periode = 'Periode 1';
        if ($cek_tanggal > 15) {
            $keterangan_periode = 'Periode 2';
        } 
        return view('office.dashboard', compact(
            'keterangan_periode',
            'header',
            'jumlah_sekolah_all',
            'penerima_sekolah_all',
            'jumlah_sekolah_tk',
            'penerima_sekolah_tk',
            'jumlah_sekolah_sd',
            'penerima_sekolah_sd',
            'jumlah_sekolah_smp',
            'penerima_sekolah_smp',
            'jumlah_sekolah_sma',
            'penerima_sekolah_sma',
            'jumlah_sekolah_ibu',
            'penerima_sekolah_ibu',
            'supplier',
            'dataSekolah',
            'jumlahSekolah',
            'totalSupplier',
            'ompreng_line_1',
            'ompreng_line_2',
            'ompreng_line_3',
            'ompreng_line_4',
            'total_ompreng_lines',
            'ompreng_total',
            'rantang_total',
            'ompreng_keluar',
            'rantang_keluar',
            'total_porsi',
            'menu'
        ));
    }

    public function ajax_ompreng_per_line()
    {
        $today = Carbon::today()->format('Y-m-d');

        $menu = DB::table('tb_menu')
            ->where('tanggal_kirim', $today)
            ->orderBy('id', 'asc')
            ->first();

        if (!$menu) {
            $menu = DB::table('tb_menu')
                ->where('tanggal_kirim', $today)
                ->orderBy('id', 'desc')
                ->first();
        }

        $menuId = $menu->id ?? 0;
        $line1 = 0;
        $line2 = 0;
        $line3 = 0;
        $line4 = 0;

        if ($menuId > 0) {
            $line1 = DB::table('tb_ompreng_transaksi')
                ->where('tb_menu_id', $menuId)
                ->where('user_id', 1)
                ->count();

            $line2 = DB::table('tb_ompreng_transaksi')
                ->where('tb_menu_id', $menuId)
                ->where('user_id', 2)
                ->count();

            $line3 = DB::table('tb_ompreng_transaksi')
                ->where('tb_menu_id', $menuId)
                ->where('user_id', 3)
                ->count();

            $line4 = DB::table('tb_ompreng_transaksi')
                ->where('tb_menu_id', $menuId)
                ->where('user_id', 4)
                ->count();
        }

        return response()->json([
            'success' => true,
            'menu_id' => $menuId,
            'tanggal_kirim' => $menu->tanggal_kirim ?? '-',
            'date_label' => Carbon::now()->translatedFormat('d F Y'),
            'ompreng_line_1' => $line1,
            'ompreng_line_2' => $line2,
            'ompreng_line_3' => $line3,
            'ompreng_line_4' => $line4,
            'total_ompreng_lines' => $line1 + $line2 + $line3 + $line4,
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Menampilkan dashboard penerimaan dengan jumlah PO bahan yang datang hari ini
     */
    public function v_penerimaan()
    {
        $header = "Dashboard";

        // Menghitung jumlah PO bahan yang datang hari ini
        $jumlah_po_datang = DB::table('tb_po_bahan')
            ->leftJoin('tb_penerimaan', 'tb_po_bahan.id', '=', 'tb_penerimaan.id_barang_po')
            ->leftJoin('tb_master_bahan', 'tb_po_bahan.id_bahan', '=', 'tb_master_bahan.id')
            ->leftJoin('tb_po', 'tb_po_bahan.id_po', '=', 'tb_po.id')
            ->leftJoin('tb_kontrak', 'tb_po.id_kontrak', '=', 'tb_kontrak.id')
            ->leftJoin('tb_supplier', 'tb_kontrak.id_supplier', '=', 'tb_supplier.id')
            ->join('tb_satuan', 'tb_master_bahan.satuan_bahan', '=', 'tb_satuan.id')
            ->whereRaw("DATE(tb_po_bahan.tanggal_kedatangan) = CURDATE()")
            ->count();
        $wadah_total    =  DB::table('tb_wadah')->count();
        $wadah_terpakai =  DB::table('tb_wadah')->where('status',1)->count();
        $wadah_belum_terpakai =  DB::table('tb_wadah')->where('status', 0)->count();
        /*$data = DB::table('tb_po_bahan')
            //->where('id_po', 6)
            ->where('tanggal_kedatangan',now())
            ->get();
        */
        $start = Carbon::yesterday()->setTime(16, 0, 0); // tanggal kemarin, jam 16:00:00
        $end   = Carbon::today()->setTime(16, 0, 0);     // tanggal hari ini, jam 16:00:00


        $data =  DB::table('tb_po_bahan')
            ->whereBetween('tanggal_kedatangan', [$start, $end])
            ->get();   
        $po = 8;
        return view('penerimaan.dashboard', compact('data','header', 'po','jumlah_po_datang', 'wadah_total', 'wadah_terpakai', 'wadah_belum_terpakai'));
    }

    public function v_penerimaan_tv()
    {
        $header = "Dashboard";

        // Menghitung jumlah PO bahan yang datang hari ini
        $jumlah_po_datang = DB::table('tb_po_bahan')
            ->leftJoin('tb_penerimaan', 'tb_po_bahan.id', '=', 'tb_penerimaan.id_barang_po')
            ->leftJoin('tb_master_bahan', 'tb_po_bahan.id_bahan', '=', 'tb_master_bahan.id')
            ->leftJoin('tb_po', 'tb_po_bahan.id_po', '=', 'tb_po.id')
            ->leftJoin('tb_kontrak', 'tb_po.id_kontrak', '=', 'tb_kontrak.id')
            ->leftJoin('tb_supplier', 'tb_kontrak.id_supplier', '=', 'tb_supplier.id')
            ->join('tb_satuan', 'tb_master_bahan.satuan_bahan', '=', 'tb_satuan.id')
            ->whereRaw("DATE(tb_po_bahan.tanggal_kedatangan) = CURDATE()")
            ->count();
        $wadah_total    =  DB::table('tb_wadah')->count();
        $wadah_terpakai =  DB::table('tb_wadah')->where('status', 1)->count();
        $wadah_belum_terpakai =  DB::table('tb_wadah')->where('status', 0)->count();
        return redirect('/dashboard_penerimaan');
        //return view('dashboard_tv.dashboard_penerimaan', compact('header', 'jumlah_po_datang', 'wadah_total', 'wadah_terpakai', 'wadah_belum_terpakai'));
    }

    /**
     * Menampilkan dashboard warehouse dengan daftar bahan baku
     */
    public function v_warehouse()
    {
        $header = "List Bahan Baku Tersimpan";
        $header2 = "List Bahan Baku yang Digunakan";
        $now = Carbon::now('Asia/Jakarta'); // pastikan waktu Indonesia

        // Default: hari ini atau besok
        if ($now->hour >= 13) {
            $tanggalKirim = $now->copy()->addDay();
            $keterangan_menu = 'hari besok';
        } else {
            $tanggalKirim = $now->copy();
            $keterangan_menu = 'hari ini';
        }

        // Cek jika hari Minggu, maka ubah ke Senin
        if ($tanggalKirim->isSunday()) {
            $tanggalKirim->addDay(); // jadi Senin
            $keterangan_menu = 'hari Senin';
        }

        $tanggalKirimFormatted = $tanggalKirim->translatedFormat('l, j F Y');

        $menus = Menu::join('tb_resep as karbohidrat_bahan', 'tb_menu.karbohidrat', '=', 'karbohidrat_bahan.id')
            ->join('tb_resep as protein_bahan', 'tb_menu.protein', '=', 'protein_bahan.id')
            ->join('tb_resep as sayur_bahan', 'tb_menu.sayur', '=', 'sayur_bahan.id')
            ->join('tb_resep as buah_bahan', 'tb_menu.buah', '=', 'buah_bahan.id')
            ->join('tb_resep as susu_bahan', 'tb_menu.susu', '=', 'susu_bahan.id')
            ->whereDate('tb_menu.tanggal_kirim', $tanggalKirim)
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
            )
            ->first();
        if($menus == null) {
            return view('warehouse.dashboard', compact('header', 'header2','menus', 'tanggalKirimFormatted', 'keterangan_menu'));
            
        }
        $total = rincian_sekolah::where('id_menu_harian', $menus->id_menu)->sum('jumlah_penerima_total');
        return view('warehouse.dashboard', compact('header', 'header2','menus','total', 'tanggalKirimFormatted', 'keterangan_menu'));
        

    }

    /**
     * Menampilkan dashboard packaging
     */
    public function v_packaging()
    {
        $header = "Dashboard";

        return view('packaging.dashboard', compact('header'));
    }
//-------------------------------------------------------------------------------------
    public function v_packaging_tv()
    {

        $header = "Dashboard Packaging";
        //$menu = Menu::where('tanggal_kirim', date('Y-m-d'))->first();
        $dapur = DataDapur::first();
        $menu = Menu::where('tanggal_kirim', Carbon::now('Asia/Jakarta')->toDateString())->first();
        $menu2 = Menu::where('tanggal_kirim', Carbon::now('Asia/Jakarta')->toDateString())
            ->latest('id') // atau latest('created_at') kalau ada kolom timestamp
            ->first();
        //return $menu;
        if($menu){
            $total_ompreng_keluar = $this->hitungOmprengKeluar($menu->id);
            $jumlah_kirim = rincian_sekolah::where('id_menu_harian', $menu->id)
            ->sum('jumlah_penerima_total');
            $jumlah_kirim2 = rincian_sekolah::where('id_menu_harian', $menu2->id)
                ->sum('jumlah_penerima_total');
            if($menu->id != $menu2->id)
            {
                $jumlah_kirim = $jumlah_kirim + $jumlah_kirim2;
            }
            $jumlah_kirim = number_format($jumlah_kirim, 0, ',', '.');
            $waktuKeluarList = tbOmprengTransaksi::where('tb_menu_id', $menu->id)
            ->whereNotNull('tanggal_keluar')
            ->orderBy('tanggal_keluar', 'desc')
            ->pluck('tanggal_keluar')
            ->take(10); // Ambil 10 data terakhir

            $totalSelisih = 0;
            $jumlahSelisih = 0;
            
            for ($i = 1; $i < $waktuKeluarList->count(); $i++) {
                $sebelumnya = strtotime($waktuKeluarList[$i - 1]);
                $sekarang = strtotime($waktuKeluarList[$i]);
                $selisih = $sekarang - $sebelumnya;
            
                $totalSelisih += $selisih;
                $jumlahSelisih++;
            }
            
            $rataRataDetik = $jumlahSelisih > 0 ? $totalSelisih / $jumlahSelisih : 0;
            $rataRataDetik = number_format($rataRataDetik, 2);
            $rataDetikUser = $this->hitungRataWaktuPerUser($menu->id);
            $detikUser = $rataDetikUser['rataRataWaktu'];
            $jumlahPerUser = $rataDetikUser['jumlahDataPerUser'];
            $estimasiSelesai = 0;//$this->hitungEstimasiSelesai($jumlah_kirim, $rataRataDetik);
            
        }else{
            return view('dashboard_tv.dashboard_packaging', compact('header', 'dapur', 'menu'));
        }
        //return $menu;
        return view('dashboard_tv.dashboard_packaging', compact('header', 'dapur','menu', 'total_ompreng_keluar', 'rataRataDetik', 'jumlah_kirim', 'detikUser', 'jumlahPerUser', 'estimasiSelesai'));
      //return redirect('/packing/formPacking-b');


    }

    /*private function hitungEstimasiSelesai($total_packing, $rataRataDetik)
    {
        // Estimasi waktu dalam detik
        $estimasiDetik = $total_packing * $rataRataDetik;

        // Konversi estimasi waktu ke dalam format jam, menit, detik
        $estimasiJam = floor($estimasiDetik / 3600);
        $estimasiDetik = $estimasiDetik % 3600;
        $estimasiMenit = floor($estimasiDetik / 60);
        $estimasiDetik = $estimasiDetik % 60;

        // Kembalikan estimasi dalam format string
        return sprintf("%02d:%02d:%02d", $estimasiJam, $estimasiMenit, $estimasiDetik);
    }*/

    private function hitungRataWaktuPerUser($menuId) {
        // Ambil daftar user_id berdasarkan menu_id yang diberikan
        $users = tbOmprengTransaksi::where('tb_menu_id', $menuId)
            ->whereNotNull('tanggal_keluar')
            ->groupBy('user_id') // Kelompokkan berdasarkan user_id
            ->pluck('user_id');
    
        $totalRataWaktu = [];
        $jumlahDataPerUser = [];
    
        // Loop untuk setiap user_id tota;
        foreach ($users as $userId) {
            // Ambil 10 data terbaru untuk user tersebut
            $waktuKeluarList = tbOmprengTransaksi::where('tb_menu_id', $menuId)
                ->where('user_id', $userId) // Filter berdasarkan user_id
                ->whereNotNull('tanggal_keluar')
                ->orderBy('tanggal_keluar') // Urutkan secara descending untuk 10 data terbaru
                ->pluck('tanggal_keluar');
                //->take(10); // Ambil 10 data terakhir
    
            // Balik urutan waktu sehingga kita bisa hitung dari yang lebih lama ke lebih baru
            //$waktuKeluarList = $waktuKeluarList->reverse();
    
            $totalSelisih = 0;
            $jumlahSelisih = 0;
    
            // Hitung selisih waktu antar transaksi
            for ($i = 1; $i < $waktuKeluarList->count(); $i++) {
                $sebelumnya = strtotime($waktuKeluarList[$i - 1]);
                $sekarang = strtotime($waktuKeluarList[$i]);
                $selisih = $sekarang - $sebelumnya;
            
                $totalSelisih += $selisih;
                $jumlahSelisih++;
            }

            //per xx terakhir (ganti angka di take())
            $waktuKeluarList = tbOmprengTransaksi::where('tb_menu_id', $menuId)
                ->where('user_id', $userId) // Filter berdasarkan user_id
                ->whereNotNull('tanggal_keluar')
                ->orderBy('tanggal_keluar', 'desc') // Urutkan secara descending untuk 10 data terbaru
                ->pluck('tanggal_keluar')
                ->take(100); // Ambil 10 data terakhir
    
            // Balik urutan waktu sehingga kita bisa hitung dari yang lebih lama ke lebih baru
            //$waktuKeluarList = $waktuKeluarList->reverse();
    
            $totalSelisih = 0;
            $jumlahSelisih = 0;
    
            // Hitung selisih waktu antar transaksi
            for ($i = $waktuKeluarList->count()-2; $i >= 0; $i--) {
                $sebelumnya = strtotime($waktuKeluarList[$i + 1]);
                $sekarang = strtotime($waktuKeluarList[$i]);
                $selisih = $sekarang - $sebelumnya;
            
                $totalSelisih += $selisih;
                $jumlahSelisih++;
            }
    
            // Hitung rata-rata waktu untuk user tersebut
            $rataRataDetik = $jumlahSelisih > 0 ? $totalSelisih / $jumlahSelisih : 0;
            $rataRataDetik = number_format($rataRataDetik, 2);
    
            // Simpan hasil rata-rata waktu per user
            $totalRataWaktu[$userId] = $rataRataDetik;
    
            // Hitung jumlah data transaksi per user
            $jumlahDataPerUser[$userId] = tbOmprengTransaksi::where('tb_menu_id', $menuId)
                ->where('user_id', $userId)
                ->whereNotNull('tanggal_keluar')
                ->count();
        }
    
        return [
            'rataRataWaktu' => $totalRataWaktu,
            'jumlahDataPerUser' => $jumlahDataPerUser
        ];
    }
    

    private function hitungOmprengKeluar($id)
    {
        $total_ompreng_keluar = tbOmprengTransaksi::where('tb_menu_id', $id)
            ->count();
        $jumlah_ompreng_porsi_A = tbOmprengTransaksi::where('tb_menu_id', $id)
            ->where('porsi', 'A')
            ->count();

        $jumlah_ompreng_porsi_B = tbOmprengTransaksi::where('tb_menu_id', $id)
            ->where('porsi', 'B')
            ->count();

        return [
            'jumlah_ompreng_porsi_a' => $jumlah_ompreng_porsi_A,
            'jumlah_ompreng_porsi_b' => $jumlah_ompreng_porsi_B,
            'total_ompreng_keluar' => $total_ompreng_keluar
        ];
    }

    public function ajax_getData()
    {
        //$menu = Menu::where('tanggal_kirim', date('Y-m-d'))->first();
        $menu = Menu::where('tanggal_kirim', Carbon::now('Asia/Jakarta')->toDateString())->first();
        //return $menu;
        if($menu){
            $total_ompreng_keluar = $this->hitungOmprengKeluar($menu->id);
            

            $waktuKeluarList = tbOmprengTransaksi::where('tb_menu_id', $menu->id)
            ->whereNotNull('tanggal_keluar')
            ->orderBy('tanggal_keluar')
            ->pluck('tanggal_keluar');

            $totalSelisih = 0;
            $jumlahSelisih = 0;
            
            for ($i = 1; $i < $waktuKeluarList->count(); $i++) {
                $sebelumnya = strtotime($waktuKeluarList[$i - 1]);
                $sekarang = strtotime($waktuKeluarList[$i]);
                $selisih = $sekarang - $sebelumnya;
            
                $totalSelisih += $selisih;
                $jumlahSelisih++;
            }
            
            $rataRataDetik = $jumlahSelisih > 0 ? $totalSelisih / $jumlahSelisih : 0;
            $rataDetikUser = $this->hitungRataWaktuPerUser($menu->id);
            $detikUser = $rataDetikUser['rataRataWaktu'];
            $jumlahPerUser = $rataDetikUser['jumlahDataPerUser'];
            return response()->json([
                'total_ompreng_keluar' => $total_ompreng_keluar['total_ompreng_keluar'],
                'jumlah_ompreng_porsi_a' => $total_ompreng_keluar['jumlah_ompreng_porsi_a'],
                'jumlah_ompreng_porsi_b' => $total_ompreng_keluar['jumlah_ompreng_porsi_b'],
                //'kode_rantang' => $kode_rantang,
                'rataRataDetik' => number_format($rataRataDetik,2),
                'detikUser' => $detikUser,
                'jumlahPerUser' => $jumlahPerUser,
                'type' => 'success',
                
            ]);
        }
    }
//-----------------------------------------------------------------------------------------------
    /**
     * Menampilkan dashboard laboratorium
     */
    public function v_laboratorium()
    {
        $header = "Dashboard";

        return view('office.dashboard', compact('header'));
    }


    public function getPONotifAjax()
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        $data = DB::table('tb_po')
            ->where('status_po', 'bayar')
            //->whereBetween('tanggal_po', [$yesterday, $today])
            ->orderBy('tanggal_po', 'desc')
            ->limit(5)
            ->get();

        return response()->json($data);
    }


    public function v_akuntan()
    {
        $header = "Dashboard";
        $jumlah_sekolah_all     = DataSekolah::count();
        $penerima_sekolah_all   = DataSekolah::sum('jumlah_siswa');

        $jumlah_sekolah_tk      = DataSekolah::where('jenjang_sekolah', 'TK/Sederajat')->count();
        $penerima_sekolah_tk    = DataSekolah::where('jenjang_sekolah', 'TK/Sederajat')->sum('jumlah_siswa');

        $jumlah_sekolah_sd      = DataSekolah::where('jenjang_sekolah', 'SD/Sederajat')->count();
        $penerima_sekolah_sd    = DataSekolah::where('jenjang_sekolah', 'SD/Sederajat')->sum('jumlah_siswa');

        $jumlah_sekolah_smp     = DataSekolah::where('jenjang_sekolah', 'SMP/Sederajat')->count();
        $penerima_sekolah_smp   = DataSekolah::where('jenjang_sekolah', 'SMP/Sederajat')->sum('jumlah_siswa');

        $jumlah_sekolah_sma     = DataSekolah::where('jenjang_sekolah', 'SMA/Sederajat')->count();
        $penerima_sekolah_sma   = DataSekolah::where('jenjang_sekolah', 'SMA/Sederajat')->sum('jumlah_siswa');

        $jumlah_sekolah_ibu     = DataSekolah::where('jenjang_sekolah', 'ibu')->count();
        $penerima_sekolah_ibu   = DataSekolah::where('jenjang_sekolah', 'ibu')->sum('jumlah_siswa');

        $supplier           = Supplier::count();
        // Data Ompreng untuk Dashboard - Query berdasarkan User ID 1-4
        $today = Carbon::today()->format('Y-m-d');
    
        $menu = DB::table('tb_menu')
            ->where('tanggal_kirim', $today)
           
            ->first();

        // Jika tidak ada menu approved, ambil menu apapun untuk tanggal tersebut
        if (!$menu) {
            $menu = DB::table('tb_menu')
                ->where('tanggal_kirim', $today)
                //->where('tanggal_kirim', '2025-07-31')

                ->orderBy('id', 'desc')
                ->first();
        }

        // Debug: Log untuk melihat data
        Log::info('Debug Ompreng Dashboard', [
            'today' => $today,
            'menu' => $menu ? $menu->id : 'No menu found'
        ]);

        // Data ompreng berdasarkan User ID (Line 1-4) dan menu hari ini
        $ompreng_line_1 = 0;
        $ompreng_line_2 = 0;
        $ompreng_line_3 = 0;
        $ompreng_line_4 = 0;

        if ($menu) {
            // LANGKAH 2: Hitung transaksi ompreng per user_id
            // PERBAIKAN: Hilangkan whereDate('created_at', $today) 

            // Line 1 (User ID = 1)
            $ompreng_line_1 = DB::table('tb_ompreng_transaksi')
                // ->where('tb_menu_id', $menu->id)
                ->where('tb_menu_id', $menu->id)
                ->where('user_id', 1)
                ->count();

            // Line 2 (User ID = 2)
            $ompreng_line_2 = DB::table('tb_ompreng_transaksi')
                ->where('tb_menu_id', $menu->id)
                ->where('user_id', 2)
                ->count();

            // Line 3 (User ID = 3)
            $ompreng_line_3 = DB::table('tb_ompreng_transaksi')
                ->where('tb_menu_id', $menu->id)
                ->where('user_id', 3)
                ->count();

            // Line 4 (User ID = 4)
            $ompreng_line_4 = DB::table('tb_ompreng_transaksi')
                ->where('tb_menu_id', $menu->id)
                ->where('user_id', 4)
                ->count();

            // Debug: Log hasil query
            Log::info('Ompreng Line Results', [
                'menu_id' => $menu->id,
                'line_1' => $ompreng_line_1,
                'line_2' => $ompreng_line_2,
                'line_3' => $ompreng_line_3,
                'line_4' => $ompreng_line_4
            ]);
        }

        // Total dari semua line
        $total_ompreng_lines = $ompreng_line_1 + $ompreng_line_2 + $ompreng_line_3 + $ompreng_line_4;

        // Data untuk informasi tambahan
        $ompreng_total = DB::table('tb_ompreng')->where('jenis', 'Ompreng')->count();
        $rantang_total = DB::table('tb_ompreng')->where('jenis', 'Rantang')->count();

        
        $rantang_keluar = DB::table('tb_ompreng_transaksi')
            ->where('status', 0)
            ->whereNotNull('kode_rantang')
            ->count();

        // Total penerima dari rincian sekolah (TOTAL PORSI)
        $total_porsi = 0;
        if ($menu) {
            $total_porsi = DB::table('rincian_sekolah')
                ->where('id_menu_harian', $menu->id)
                ->sum('jumlah_penerima_total');
        }
        //$ompreng_keluar = $total_porsi;
        $ompreng_keluar = DB::table('surat_jalan')
            ->join('surat_jalan_item', 'surat_jalan.referensi', '=', 'surat_jalan_item.surat_jalan_referensi')
            ->where('surat_jalan.id_menu_harian', ($menu->id ?? 0))
            ->sum('surat_jalan_item.jumlah') ?? 0;
        // Data sekolah dan supplier untuk dashboard
        $dataSekolah = DataSekolah::all();
        $jumlahSekolah = DataSekolah::count();
        $totalSupplier = Supplier::count();
        $cek_tanggal = Carbon::today()->format('d');
        $keterangan_periode = 'Periode 1';
        if ($cek_tanggal > 15) {
            $keterangan_periode = 'Periode 2';
        }
        return view('akutansi.dashboard', compact(
            'keterangan_periode',
            'header',
            'jumlah_sekolah_all',
            'penerima_sekolah_all',
            'jumlah_sekolah_tk',
            'penerima_sekolah_tk',
            'jumlah_sekolah_sd',
            'penerima_sekolah_sd',
            'jumlah_sekolah_smp',
            'penerima_sekolah_smp',
            'jumlah_sekolah_sma',
            'penerima_sekolah_sma',
            'jumlah_sekolah_ibu',
            'penerima_sekolah_ibu',
            'supplier',
            'dataSekolah',
            'jumlahSekolah',
            'totalSupplier',
            'ompreng_line_1',
            'ompreng_line_2',
            'ompreng_line_3',
            'ompreng_line_4',
            'total_ompreng_lines',
            'ompreng_total',
            'rantang_total',
            'ompreng_keluar',
            'rantang_keluar',
            'total_porsi',
            'menu',
            
        ));
    }

    
}
