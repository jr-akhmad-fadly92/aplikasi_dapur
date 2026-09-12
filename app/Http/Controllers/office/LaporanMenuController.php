<?php

namespace App\Http\Controllers\office;

use App\Http\Controllers\Controller;
use App\Models\LaporanMenuHarian;
use App\Models\Menu;
use App\Models\rincian_sekolah;
use App\Models\Resep;
use App\Models\TbPenerimaan;
use App\Models\TbPoBahan;
use App\Models\TbSatuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;

class LaporanMenuController extends Controller
{
    public function index()
    {
        $header = "Laporan Menu Harian";

        // Generate data laporan untuk bulan ini jika belum ada
        $this->generateLaporanBulanIni();

        return view('office.laporan_menu.index', compact('header'));
    }

    /**
     * Generate laporan menu untuk bulan ini
     */
    private function generateLaporanBulanIni()
    {
        try {
            $bulanIni = Carbon::now()->month;
            $tahunIni = Carbon::now()->year;

            // Hapus data laporan bulan ini untuk regenerate
            LaporanMenuHarian::whereYear('tgl_kirim', $tahunIni)
                ->whereMonth('tgl_kirim', $bulanIni)
                ->delete();

            // Ambil data menu dari tb_menu untuk bulan ini
            $menuBulanIni = Menu::with(['resepKarbohidrat', 'resepProtein', 'resepSayur', 'resepBuah', 'resepSusu'])
                ->whereYear('tanggal_kirim', $tahunIni)
                ->whereMonth('tanggal_kirim', $bulanIni)
                ->get();

            foreach ($menuBulanIni as $menu) {
                // Hitung total porsi dari rincian_sekolah
                $totalPorsi = rincian_sekolah::where('id_menu_harian', $menu->id)
                    ->sum('jumlah_penerima_total');

                // Tentukan periode berdasarkan tanggal
                $tanggal = Carbon::parse($menu->tanggal_kirim)->day;
                $periode = ($tanggal <= 14) ? '1-14' : '15-31';

                // Insert ke tabel laporan
                LaporanMenuHarian::create([
                    'tgl_kirim' => $menu->tanggal_kirim,
                    'karbo' => $menu->resepKarbohidrat ? $menu->resepKarbohidrat->nama_resep : '-',
                    'protein' => $menu->resepProtein ? $menu->resepProtein->nama_resep : '-',
                    'sayur' => $menu->resepSayur ? $menu->resepSayur->nama_resep : '-',
                    'buah' => $menu->resepBuah ? $menu->resepBuah->nama_resep : '-',
                    'susu' => $menu->resepSusu ? $menu->resepSusu->nama_resep : '-',
                    'porsi' => $totalPorsi ?: 0,
                    'periode' => $periode
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error generating laporan: ' . $e->getMessage());
        }
    }

    /**
     * DataTables untuk periode pertama dengan logika dinamis berdasarkan tanggal akses
     * - Tanggal 1-14: Menampilkan data periode pertama (1-14)
     * - Tanggal 15-31: Menampilkan data periode kedua (15-31)
     */
    public function dataPeriodePertama(Request $request)
    {
        if ($request->ajax()) {
            $bulan = $request->get('bulan', Carbon::now()->month);
            $tahun = $request->get('tahun', Carbon::now()->year);

            // Cek apakah ini request dari dashboard
            $isDashboardFilter = $request->get('dashboard_filter', false);

            if ($isDashboardFilter) {
                // Jika dari dashboard, tentukan periode berdasarkan tanggal hari ini
                $tanggalHariIni = Carbon::now()->day;

                if ($tanggalHariIni >= 1 && $tanggalHariIni <= 14) {
                    // Tanggal 1-14: tampilkan data periode pertama (1-14)
                    $data = LaporanMenuHarian::periodePertama()
                        ->bulan($bulan, $tahun)
                        ->orderBy('tgl_kirim', 'asc')
                        ->get();
                } else {
                    // Tanggal 15-31: tampilkan data periode kedua (15-31)
                    $data = LaporanMenuHarian::periodeKedua()
                        ->bulan($bulan, $tahun)
                        ->orderBy('tgl_kirim', 'asc')
                        ->get();
                }
            } else {
                // Jika bukan dari dashboard, tampilkan periode pertama seperti biasa
                $data = LaporanMenuHarian::periodePertama()
                    ->bulan($bulan, $tahun)
                    ->orderBy('tgl_kirim', 'asc')
                    ->get();
            }


            $today = Carbon::today();
            $day = $today->day;
            $month = $today->month;
            $year = $today->year;

            // Tentukan rentang tanggal berdasarkan hari ini
            $range = $day <= 14 ? [1, 14] : [15, 31];

            $data = DB::table('tb_menu')
                ->select(
                    'tb_menu.id',
                    'tb_menu.tanggal_kirim',
                    DB::raw('karbohidrat_resep.nama_resep as karbohidrat'),
                    DB::raw('protein_resep.nama_resep as protein'),
                    DB::raw('sayur_resep.nama_resep as sayur'),
                    DB::raw('buah_resep.nama_resep as buah'),
                    DB::raw('susu_resep.nama_resep as susu'),
                    DB::raw('SUM(rincian_sekolah.jumlah_penerima_total) as total_penerima')
                )
                // Join ke tb_resep untuk masing-masing kolom
                ->leftJoin('tb_resep as karbohidrat_resep', 'tb_menu.karbohidrat', '=', 'karbohidrat_resep.id')
                ->leftJoin('tb_resep as protein_resep', 'tb_menu.protein', '=', 'protein_resep.id')
                ->leftJoin('tb_resep as sayur_resep', 'tb_menu.sayur', '=', 'sayur_resep.id')
                ->leftJoin('tb_resep as buah_resep', 'tb_menu.buah', '=', 'buah_resep.id')
                ->leftJoin('tb_resep as susu_resep', 'tb_menu.susu', '=', 'susu_resep.id')
                // Join ke rincian_sekolah
                ->leftJoin('rincian_sekolah', 'tb_menu.id', '=', 'rincian_sekolah.id_menu_harian')
                // Filter bulan dan tahun ini
                ->whereMonth('tb_menu.tanggal_kirim', $month)
                ->whereYear('tb_menu.tanggal_kirim', $year)
                ->whereBetween(DB::raw('DAY(tb_menu.tanggal_kirim)'), $range)
                // Group by agar SUM berfungsi
                ->groupBy(
                    'tb_menu.id',
                    'tb_menu.tanggal_kirim',
                    'karbohidrat_resep.nama_resep',
                    'protein_resep.nama_resep',
                    'sayur_resep.nama_resep',
                    'buah_resep.nama_resep',
                    'susu_resep.nama_resep'
                )
                ->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('tanggal', function ($row) {
                    return Carbon::parse($row->tanggal_kirim)->format('d-m-Y');
                })
                ->addColumn('hari', function ($row) {
                    return Carbon::parse($row->tanggal_kirim)->locale('id')->dayName;
                })
                ->addColumn('porsi_formatted', function ($row) {
                    return number_format($row->total_penerima, 0, ',', '.');
                })
                ->addColumn('action', function ($row) {
                    return '<a href="javascript:void(0)" class="btn btn-info btn-sm btn-detail" data-id="' . $row->id . '" title="Lihat Detail Menu">
                                <i class="fas fa-eye"></i> Detail
                            </a>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    /**
     * DataTables untuk periode 15-31
     */
    public function dataPeriodeKedua(Request $request)
    {
        if ($request->ajax()) {
            $bulan = $request->get('bulan', Carbon::now()->month);
            $tahun = $request->get('tahun', Carbon::now()->year);

            $data = LaporanMenuHarian::periodeKedua()
                ->bulan($bulan, $tahun)
                ->orderBy('tgl_kirim', 'asc')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('tanggal', function ($row) {
                    return Carbon::parse($row->tgl_kirim)->format('d-m-Y');
                })
                ->addColumn('hari', function ($row) {
                    return Carbon::parse($row->tgl_kirim)->locale('id')->dayName;
                })
                ->addColumn('porsi_formatted', function ($row) {
                    return number_format($row->porsi, 0, ',', '.');
                })
                ->addColumn('action', function ($row) {
                    return '<a href="' . route('laporan.menu.detail', $row->id) . '" class="btn btn-info btn-sm">Detail</a>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    /**
     * Detail menu berdasarkan ID
     */
    public function detail($id)
    {
        try {
            $laporan = LaporanMenuHarian::findOrFail($id);

            // Ambil data detail dari tb_menu dan relasi terkait
            $menuDetail = Menu::with(['resepKarbohidrat', 'resepProtein', 'resepSayur', 'resepBuah', 'resepSusu'])
                ->where('tanggal_kirim', $laporan->tgl_kirim)
                ->first();

            // Ambil rincian sekolah - perbaiki query
            $rincianSekolah = collect([]); // Default empty collection

            if ($menuDetail) {
                $rincianSekolah = rincian_sekolah::with('data_sekolah')
                    ->where('id_menu_harian', $menuDetail->id)
                    ->get();
            }

            return view('office.laporan_menu.detail', compact('laporan', 'menuDetail', 'rincianSekolah'));

        } catch (\Exception $e) {
            return redirect()->route('laporan.menu.index')
                ->with('error', 'Data tidak ditemukan: ' . $e->getMessage());
        }
    }

    /**
     * Filter berdasarkan bulan dan tahun
     */
    public function filterBulan(Request $request)
    {
        $bulan = $request->get('bulan');
        $tahun = $request->get('tahun');

        if ($bulan && $tahun) {
            // Regenerate data untuk bulan yang dipilih
            $this->generateLaporanBulan($bulan, $tahun);
        }

        return redirect()->route('laporan.menu.index')
            ->with('success', 'Data laporan berhasil diperbarui');
    }

    /**
     * Generate laporan untuk bulan tertentu
     */
    private function generateLaporanBulan($bulan, $tahun)
    {
        try {
            // Hapus data laporan bulan tertentu
            LaporanMenuHarian::whereYear('tgl_kirim', $tahun)
                ->whereMonth('tgl_kirim', $bulan)
                ->delete();

            // Generate ulang seperti method generateLaporanBulanIni
            $menuBulan = Menu::with(['resepKarbohidrat', 'resepProtein', 'resepSayur', 'resepBuah', 'resepSusu'])
                ->whereYear('tanggal_kirim', $tahun)
                ->whereMonth('tanggal_kirim', $bulan)
                ->get();

            foreach ($menuBulan as $menu) {
                $totalPorsi = rincian_sekolah::where('id_menu_harian', $menu->id)
                    ->sum('jumlah_penerima_total');

                $tanggal = Carbon::parse($menu->tanggal_kirim)->day;
                $periode = ($tanggal <= 14) ? '1-14' : '15-31';

                LaporanMenuHarian::create([
                    'tgl_kirim' => $menu->tanggal_kirim,
                    'karbo' => $menu->resepKarbohidrat ? $menu->resepKarbohidrat->nama_resep : '-',
                    'protein' => $menu->resepProtein ? $menu->resepProtein->nama_resep : '-',
                    'sayur' => $menu->resepSayur ? $menu->resepSayur->nama_resep : '-',
                    'buah' => $menu->resepBuah ? $menu->resepBuah->nama_resep : '-',
                    'susu' => $menu->resepSusu ? $menu->resepSusu->nama_resep : '-',
                    'porsi' => $totalPorsi ?: 0,
                    'periode' => $periode
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error generating laporan bulan: ' . $e->getMessage());
        }
    }


    public function dt_data_bahan_datang()
    {
        
        $table =  DB::table('tb_po as p')
    	->join('tb_po_bahan as pb', 'pb.id_po', '=', 'p.id')
    	->join('tb_master_bahan as mb', 'mb.id', '=', 'pb.id_bahan')
    	->select(
		
        	'pb.id_bahan',
        	'mb.bahan',
        	DB::raw('MIN(pb.tanggal_kedatangan) as tanggal_kedatangan'), // sample 1 (ambil paling awal)
        	DB::raw('MAX(pb.satuan) as satuan'), 
        	DB::raw('MAX(p.nomor_po) as nomor_po'),
        	DB::raw('MAX(pb.id_po) as id_po')
    	)
    	->whereBetween('pb.tanggal_kedatangan', [
        	now()->startOfDay(),
        	now()->addDay()->startOfDay()
    	])
    	->whereIn('p.status_po', ['acc', 'close', 'bayar'])
    	->groupBy('pb.id_bahan', 'mb.bahan')
    	->get();

        return DataTables::of($table)
            ->addIndexColumn()
            ->addColumn('kedatangan', function ($row) {
            
            return Carbon::parse($row->tanggal_kedatangan)
                ->translatedFormat('l, d F Y H:i');
            })
            ->addColumn('nama_satuan', function ($row) {
                $data = TbSatuan::find($row->satuan);
                return $data->satuan ;
            })
            ->addColumn('jumlah_pesanan', function ($row) {
                $data = TbSatuan::find($row->satuan);
                 $jumlah = TbPoBahan::where('id_po',$row->id_po)->where('id_bahan',$row->id_bahan)->sum('jumlah_bahan');
                
               return number_format($jumlah , 0, ',', '.').' '. $data->satuan;
            })
            ->addColumn('jumlah_datang', function ($row) {
                $data = TbSatuan::find($row->satuan);
                $jumlah = TbPenerimaan::join('tb_po_bahan', 'tb_penerimaan.id_barang_po', 'tb_po_bahan.id')
                ->where('tb_po_bahan.id_po',$row->id_po)->where('tb_po_bahan.id_bahan',$row->id_bahan)->sum('tb_penerimaan.jumlah_datang');
                if( $data->satuan == 'kg' || $data->satuan == 'Kg' )
		{
			return number_format($jumlah ,0, ',', '.') . ' gram' ;
		}else{
			return number_format($jumlah ,0, ',', '.') . ' ' . $data->satuan;
		}
                
            })
	    ->addColumn('total_kolom', function ($row) {
                
                $jumlah = TbPenerimaan::join('tb_po_bahan', 'tb_penerimaan.id_barang_po', 'tb_po_bahan.id')
                ->where('tb_po_bahan.id_po',$row->id_po)->where('tb_po_bahan.id_bahan',$row->id_bahan)->count();
                return $jumlah . ' kemasan';             
            })

            ->rawColumns(['jumlah_pesanan', 'jumlah_datang', 'nama_satuan'])
            ->make(true);
    }
}