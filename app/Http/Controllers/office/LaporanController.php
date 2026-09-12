<?php

namespace App\Http\Controllers\office;

use App\Http\Controllers\Controller;
use Carbon\Carbon;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
//import return type View
use Illuminate\View\View;
//import return type redirectResponse
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
//excel 
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\exportExcelRekapHasilMasak;
use App\Exports\CounterPaxExport;
use App\Exports\LaporanRekapMenuSimpleExport;
use App\Models\DataDapur;
use App\Models\KasKecilTransaksi;
use App\Models\MasterBahanSayur;
use App\Models\Menu;
use App\Models\TbMasterBahan;

class LaporanController extends Controller
{
    private function getCounterPaxData(string $tanggal)
    {
        return DB::table('tb_menu as m')
            ->join('rincian_sekolah as rs', 'rs.id_menu_harian', '=', 'm.id')
            ->join('tb_data_sekolah as s', 's.id', '=', 'rs.id_sekolah')
            ->whereDate('m.tanggal_kirim', $tanggal)
            ->select(
                's.id as id_sekolah',
                's.nama_sekolah',
                DB::raw('MAX(COALESCE(rs.jumlah_penerima_a,0)) as jumlah_penerima_a'),
                DB::raw('MAX(COALESCE(rs.jumlah_penerima_b,0)) as jumlah_penerima_b'),
                DB::raw('MAX(COALESCE(rs.jumlah_penerima_total,0)) as jumlah_penerima_total')
            )
            ->groupBy('s.id', 's.nama_sekolah')
            ->orderBy('s.nama_sekolah')
            ->get();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function v_laporan(Request $request)
    {
        $header = "Laporan Dapur";
        $tanggal = $tanggal ?? Carbon::now()->toDateString(); // Format: YYYY-MM-DD

        // Cek apakah format tanggal valid
        try {
            $tanggalValid = Carbon::parse($tanggal)->toDateString();
        } catch (\Exception $e) {
            return abort(404, "Format tanggal tidak valid");
        }

        // Contoh ambil data dari database atau olah data berdasarkan tanggal
        // $data = ModelLaporan::whereDate('created_at', $tanggalValid)->get();

       
        return view('office.laporan.laporan', compact('header', 'tanggal'));
    }

    public function dt_Laporan(Request $request)
    {
        $tanggal    = Carbon::parse($request->tanggal)->format('Y-m-d')??date('Y-m-d');
        $menu       = Menu::where('tanggal_kirim', $tanggal)->first();
        $menu       = $menu->id ?? 0;
        $laporanItems = [
            'Menu',
            'PO',
            'Penerimaan',
            'Gudang',
            'Hasil Masak',
            'Surat Jalan'
        ];
        if($menu == 0 )
        {
            $data = [
                ['Menu', ''],
                ['PO', ''],
                ['Penerimaan', ''],
                ['Gudang', ''],
                ['Hasil Masak', ''],
                ['Surat Jalan',  ''],

            ];
        }else{
            $data = [
                ['Menu', ''],
                ['PO', ''],
                ['Penerimaan', ''],
                ['Gudang', ''],
                ['Hasil Masak', '<a href="' . route('export-hasil-masak', $menu) . '" class="btn btn-sm btn-success">Excel</a>'],
                ['Surat Jalan',  ''],

            ];
        }
        

       
       

        
        
        return response()->json(['data' => $data]);
    }

    public function rekap_po_PDF(Request $request)
    {
        $request->validate([
            'tanggal_awal' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_awal',
        ]);

        $data = DB::table('tb_po')
            ->select(
                'tb_po.nomor_po',
                'tb_po.tanggal_po',
                'tb_po.status_po',
                DB::raw('(SELECT SUM(jumlah_po) FROM tb_po_bahan WHERE tb_po_bahan.id_po = tb_po.id) as total_jumlah_po')
            )
            ->whereBetween('tanggal_po', [$request->tanggal_awal, $request->tanggal_akhir])
            ->get();

        $tanggal_awal_formatted  = Carbon::parse($request->tanggal_awal)->translatedFormat('l, j F Y');
        $tanggal_akhir_formatted = Carbon::parse($request->tanggal_akhir)->translatedFormat('l, j F Y');

        $dapur = DataDapur::first();
        $pdf = PDF::loadView('office/laporan.template_rekap_po', [
            'data' => $data,
            'tanggal_awal' => $tanggal_awal_formatted,
            'tanggal_akhir' => $tanggal_akhir_formatted,
            'dapur' => $dapur

        ]);

        return $pdf->download('laporan_rekap_po.pdf');
    }

        public function rekap_menu_PDF(Request $request)
        {
            $request->validate([
                'tanggal_awal' => 'required|date',
                'tanggal_akhir' => 'required|date|after_or_equal:tanggal_awal',
            ]);
            $tanggal_awal_formatted  = Carbon::parse($request->tanggal_awal)->translatedFormat('l, j F Y');
            $tanggal_akhir_formatted = Carbon::parse($request->tanggal_akhir)->translatedFormat('l, j F Y');
            $tanggal_awal_formatted1 = Carbon::parse($request->tanggal_awal)->translatedFormat('Y-m-d');
            $tanggal_akhir_formatted1 = Carbon::parse($request->tanggal_akhir)->translatedFormat('Y-m-d');
            $data =   DB::select("
            SELECT 
                tb_menu.id,
                tb_menu.menu,
                tb_menu.karbohidrat,
                tb_menu.protein,
                tb_menu.sayur,
                tb_menu.buah,
                tb_menu.susu,
                tb_menu.status_pengajuan,
                tb_menu.tanggal_kirim,
                tb_menu.created_at,
                tb_menu.updated_at,
                m_karbo.nama_resep AS nama_karbohidrat,
                m_protein.nama_resep AS nama_protein,
                m_sayur.nama_resep AS nama_sayur,
                m_buah.nama_resep AS nama_buah,
                m_susu.nama_resep AS nama_susu,
                COALESCE(mgh.energi, 0) AS energi,
                COALESCE(mgh.protein, 0) AS protein_gizi,
                COALESCE(mgh.lemak, 0) AS lemak,
                COALESCE(mgh.karbohidrat, 0) AS karbohidrat_gizi,
                COALESCE(mgh.serat, 0) AS serat,
                COALESCE(mgh.natrium, 0) AS natrium,
                SUM(rk.jumlah_penerima_total) AS total_penerima,
                SUM(rk.jumlah_penerima_a) AS total_penerima_a,
                SUM(rk.jumlah_penerima_b) AS total_penerima_b
            FROM tb_menu
            JOIN tb_resep AS m_karbo ON tb_menu.karbohidrat = m_karbo.id
            JOIN tb_resep AS m_protein ON tb_menu.protein = m_protein.id
            JOIN tb_resep AS m_sayur ON tb_menu.sayur = m_sayur.id
            JOIN tb_resep AS m_buah ON tb_menu.buah = m_buah.id
            JOIN tb_resep AS m_susu ON tb_menu.susu = m_susu.id
            LEFT JOIN tb_menu_gizi_harian AS mgh ON tb_menu.id = mgh.id_menu
            LEFT JOIN rincian_sekolah AS rk ON tb_menu.id = rk.id_menu_harian
            WHERE tb_menu.status_pengajuan != 'rejected'
                AND tb_menu.tanggal_kirim BETWEEN '". $tanggal_awal_formatted1. "' AND '" . $tanggal_akhir_formatted1. "'
            GROUP BY 
                tb_menu.id,
                tb_menu.menu,
                tb_menu.karbohidrat,
                tb_menu.protein,
                tb_menu.sayur,
                tb_menu.buah,
                tb_menu.susu,
                tb_menu.status_pengajuan,
                tb_menu.tanggal_kirim,
                tb_menu.created_at,
                tb_menu.updated_at,
                m_karbo.nama_resep,
                m_protein.nama_resep,
                m_sayur.nama_resep,
                m_buah.nama_resep,
                m_susu.nama_resep,
                mgh.energi,
                mgh.protein,
                mgh.lemak,
                mgh.karbohidrat,
                mgh.serat,
                mgh.natrium
            ORDER BY tb_menu.created_at ASC
        ");

        

            $dapur = DataDapur::first();
            $pdf = PDF::loadView('office/laporan.template_rekap_menu', [
                'data' => $data,
                'tanggal_awal' => $tanggal_awal_formatted,
                'tanggal_akhir' => $tanggal_akhir_formatted,
                'dapur' => $dapur

            ]);

            return $pdf->download('laporan_rekap_menu.pdf');
        }

        public function rekap_menu_Excel(Request $request)
        {
            $request->validate([
                'tanggal_awal' => 'required|date',
                'tanggal_akhir' => 'required|date|after_or_equal:tanggal_awal',
            ]);

            $tanggalAwal = Carbon::parse($request->tanggal_awal)->format('Y-m-d');
            $tanggalAkhir = Carbon::parse($request->tanggal_akhir)->format('Y-m-d');

            $data = collect(DB::select(
                "
                SELECT
                    tb_menu.id,
                    tb_menu.tanggal_kirim,
                    m_karbo.nama_resep AS nama_karbohidrat,
                    m_protein.nama_resep AS nama_protein,
                    m_sayur.nama_resep AS nama_sayur,
                    m_buah.nama_resep AS nama_buah,
                    m_susu.nama_resep AS nama_susu,
                    COALESCE(mgh.energi, 0) AS energi,
                    COALESCE(mgh.protein, 0) AS protein_gizi,
                    COALESCE(mgh.lemak, 0) AS lemak,
                    COALESCE(mgh.karbohidrat, 0) AS karbohidrat_gizi,
                    COALESCE(mgh.serat, 0) AS serat,
                    COALESCE(mgh.natrium, 0) AS natrium,
                    SUM(rk.jumlah_penerima_total) AS total_penerima,
                    SUM(rk.jumlah_penerima_a) AS total_penerima_a,
                    SUM(rk.jumlah_penerima_b) AS total_penerima_b
                FROM tb_menu
                JOIN tb_resep AS m_karbo ON tb_menu.karbohidrat = m_karbo.id
                JOIN tb_resep AS m_protein ON tb_menu.protein = m_protein.id
                JOIN tb_resep AS m_sayur ON tb_menu.sayur = m_sayur.id
                JOIN tb_resep AS m_buah ON tb_menu.buah = m_buah.id
                JOIN tb_resep AS m_susu ON tb_menu.susu = m_susu.id
                LEFT JOIN tb_menu_gizi_harian AS mgh ON tb_menu.id = mgh.id_menu
                LEFT JOIN rincian_sekolah AS rk ON tb_menu.id = rk.id_menu_harian
                WHERE tb_menu.status_pengajuan != 'rejected'
                    AND tb_menu.tanggal_kirim BETWEEN ? AND ?
                GROUP BY
                    tb_menu.id,
                    tb_menu.tanggal_kirim,
                    m_karbo.nama_resep,
                    m_protein.nama_resep,
                    m_sayur.nama_resep,
                    m_buah.nama_resep,
                    m_susu.nama_resep,
                    mgh.energi,
                    mgh.protein,
                    mgh.lemak,
                    mgh.karbohidrat,
                    mgh.serat,
                    mgh.natrium
                ORDER BY tb_menu.created_at ASC
                ",
                [$tanggalAwal, $tanggalAkhir]
            ))->map(function ($row) {
                $row->tanggal_kirim = Carbon::parse($row->tanggal_kirim);
                return $row;
            });

            return Excel::download(
                new LaporanRekapMenuSimpleExport($data, $tanggalAwal, $tanggalAkhir, DataDapur::first()),
                'laporan_rekap_menu_' . $tanggalAwal . '_sd_' . $tanggalAkhir . '.xlsx'
            );
        }

        public function counterPaxIndex(Request $request)
        {
            $header = 'Counter Pax';
            $tanggal = $request->get('tanggal', Carbon::today()->format('Y-m-d'));

            $data = $this->getCounterPaxData($tanggal);

            $totals = [
                'a' => (int) $data->sum('jumlah_penerima_a'),
                'b' => (int) $data->sum('jumlah_penerima_b'),
                'total' => (int) $data->sum('jumlah_penerima_total'),
            ];

            return view('office.laporan.counter_pax_index', compact('header', 'tanggal', 'data', 'totals'));
        }

        public function counterPaxExportExcel(Request $request)
        {
            $request->validate([
                'tanggal' => 'required|date',
            ]);

            $tanggal = Carbon::parse($request->tanggal)->format('Y-m-d');
            $data = $this->getCounterPaxData($tanggal);
            $dapur = DataDapur::first();

            $totals = [
                'a' => (int) $data->sum('jumlah_penerima_a'),
                'b' => (int) $data->sum('jumlah_penerima_b'),
                'total' => (int) $data->sum('jumlah_penerima_total'),
            ];

            return Excel::download(
                new CounterPaxExport($tanggal, $data, $totals, $dapur),
                'counter_pax_' . $tanggal . '.xlsx'
            );
        }

        public function counterPaxExportPdf(Request $request)
        {
            $request->validate([
                'tanggal' => 'required|date',
            ]);

            $tanggal = Carbon::parse($request->tanggal)->format('Y-m-d');
            $data = $this->getCounterPaxData($tanggal);
            $dapur = DataDapur::first();

            $totals = [
                'a' => (int) $data->sum('jumlah_penerima_a'),
                'b' => (int) $data->sum('jumlah_penerima_b'),
                'total' => (int) $data->sum('jumlah_penerima_total'),
            ];

            $pdf = Pdf::loadView('office.PO.template_counter_pax', [
                'tanggal' => $tanggal,
                'data' => $data,
                'totals' => $totals,
                'dapur' => $dapur,
            ])->setPaper('a4', 'portrait');

            return $pdf->download('counter_pax_' . $tanggal . '.pdf');
        }

        public function v_laporan_harian()
        {
            $header = "Laporan Harian";

            return view('akutansi.laporan_harian.index', compact('header'));
        }

        public function ajax_laporan_harian(Request $request)  
        {
            // Ambil tanggal dari request
            $tanggal = Carbon::parse($request->tanggal)->format('Y-m-d');
            //return $tanggal;
            $id_gaji = TbMasterBahan::where('bahan', 'Gaji Karyawan')->first();
            $bantuan_gaji_karyawan = KasKecilTransaksi::whereDate('tanggal', $tanggal)
            ->where('master_bahan_id', $id_gaji->id)->sum('jumlah');
             

            $id_infra = TbMasterBahan::where('bahan', 'Bantuan Peralatan Dan Infra')->first();
            $bantuan_infra = KasKecilTransaksi::whereDate('tanggal', $tanggal)
            ->where('master_bahan_id', $id_infra->id)->sum('jumlah');
            
            $bantuan_operasional_dengan_po = DB::table('tb_kas_kecil_transaksi as tkk')
            ->join('tb_po as po', 'tkk.nomor_po', '=', 'po.nomor_po')
            ->join('tb_po_bahan as pb', 'pb.id_po', '=', 'po.id')
            ->where('pb.id_rincian_bahan', 0)
            ->whereDate('tkk.tanggal', $tanggal) // filter tanggal hari ini
            ->select('tkk.*', 'pb.jumlah_po')
            ->sum('jumlah_po');

            $bantuan_operasional_non_po_jumlah =  DB::table('tb_kas_kecil_transaksi as tkk')
            ->whereDate('tkk.tanggal', $tanggal) // filter tanggal
            ->whereNull('tkk.nomor_po')          // nomor_po harus null
            ->whereNotIn('tkk.master_bahan_id', [$id_gaji->id, $id_infra->id]) // selain id gaji & infra
            ->select('tkk.*')
            ->sum('jumlah');

            $list_bahan = DB::table('tb_kas_kecil_transaksi as tkk')
                ->whereDate('tkk.tanggal', $tanggal) // filter tanggal
                ->whereNull('tkk.nomor_po')          // nomor_po harus null
                ->whereNotIn('tkk.master_bahan_id', [$id_gaji->id, $id_infra->id]) // selain id gaji & infra
                ->select('tkk.*')
                ->get();

            $diskripsi = $list_bahan->map(function ($item) {
                return "{$item->deskripsi}";
            })->implode(', ');

            $bantuan_operasional_non_po =  DB::table('tb_kas_kecil_transaksi as tkk')
            ->whereDate('tkk.tanggal', $tanggal) // filter tanggal
            ->whereNull('tkk.nomor_po')          // nomor_po harus null
            ->whereNotIn('tkk.master_bahan_id', [$id_gaji->id, $id_infra->id]) // selain id gaji & infra
            ->select('tkk.*')
            ->first();

            $bantuan_bahan_baku= DB::table('tb_kas_kecil_transaksi as tkk')
            ->join('tb_po as po', 'tkk.nomor_po', '=', 'po.nomor_po')
            ->join('tb_po_bahan as pb', 'pb.id_po', '=', 'po.id')
            ->where('pb.id_rincian_bahan', '<>', 0) // selain 0
            ->whereDate('tkk.tanggal', $tanggal) // filter tanggal hari ini
            ->select('tkk.*','pb.jumlah_po')
            ->sum('jumlah_po');

            

            $menu = "Laporan Harian";
            return view('akutansi.laporan_harian.laporan_harian', compact(
                'menu',
                'tanggal',
                'bantuan_bahan_baku',
                'bantuan_operasional_non_po_jumlah',
                'bantuan_operasional_non_po',
                'diskripsi',
                'bantuan_operasional_dengan_po',
                'bantuan_infra',
                'bantuan_gaji_karyawan'

            ));
        }



    

}
