<?php

namespace App\Http\Controllers;

use App\Models\DataDapur;
use App\Models\Menu;
use App\Models\Resep;
use App\Models\TbPoBahan;
use App\Models\TbSatuan;
use Illuminate\Http\Request;
//use PDF;
use App\Models\warehouseTransaksi;
use Illuminate\Support\Carbon;
use DataTables;
use Psy\Command\EditCommand;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;


use App\Exports\FormPenerimaanExport;
use App\Exports\FormPenerimaanNonPanganExport;
use App\Exports\FormPengeluaranNonPanganExport;
use App\Exports\LaporanHasilMasakExport;
use App\Exports\FormStokOpnamNonPanganExport;
use App\Exports\ChecklistUjiOrganoleptikExport;

class checklistKerjaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    //
    public function v_checklistGudang(Request $request)
    {
        $header = "Checklist Kerja Gudang";
        return view('office.checklist_kerja.checklistGudang', compact('header'));
    }

    public function pdf_checklistKerjaGudangKeluar(Request $request)
    {
        $header = "Checklist Kerja";
        $jenis = [
            1 => 'Karbohidrat',
            2 => 'Lauk',
            3 => 'Sayur',
            4 => 'Buah',
            5 => 'Tambahan',
            6 => 'bumbu',
        ];
        $tanggal = $request->tanggal;
        $dataByJenis = [];
        $dapur = DataDapur::firstOrDefault();
        foreach ($jenis as $key => $label) {
            $dataByJenis[$label] = warehouseTransaksi::with('tbPenerimaan.tbPoBahan.tbMasterBahan')
                ->whereHas('tbPenerimaan.tbPoBahan.tbMasterBahan', function ($query) use ($key) {
                    $query->where('jenis', $key);
                })
                ->whereDate('tanggal_akan_keluar', $request->tanggal)
                ->get();
        }
        $pdf = PDF::loadview('office.checklist_kerja.pdf.pdfChecklistKerjaGudangKeluar', compact('header', 'jenis', 'dataByJenis', 'dapur', 'tanggal'))->setPaper('A4','portrait');
        
        return $pdf->stream('PO '.'.pdf');
    }

    public function dt_checklistGudangKeluar(Request $request)
    {
        
        $tanggal = Carbon::parse($request->tanggal)->format('Y-m-d');
        $table = warehouseTransaksi::with('tbPenerimaan.tbPoBahan.tbMasterBahan')
                ->whereDate('tanggal_akan_keluar', $tanggal)
                ->get();
                //return $table;
        return datatables()->of($table)
            ->addIndexColumn()
            ->editColumn('jenis', function ($row){
                $jenis = [
                    1 => 'Karbohidrat',
                    2 => 'Lauk',
                    3 => 'Sayur',
                    4 => 'Buah',
                    5 => 'Tambahan',
                    6 => 'bumbu',
                ];
                if($row->tbPenerimaan == null){
                    return '-';
                }
                return $jenis[$row->tbPenerimaan->tbPoBahan->tbMasterBahan->jenis]??'-';
            })
            ->editColumn('status', function ($row) {
                if($row->status == 1){
                    return '<span class="badge badge-success">Keluar</span>';
                }
               
                else{
                    return '<span class="badge badge-info">Di gudang</span>';
                }
            })
            ->EditColumn('tanggal_akan_keluar', function ($row) {
                return Carbon::parse($row->tanggal_akan_keluar)->format('D MM YYYY');
            })
            ->addColumn('satuan', function ($row) {
                return $row->satuan->satuan;
            })
            
            ->rawColumns(['status'])
            ->make(true);
       
    }

    public function v_checklistPenerimaan(Request $request)
    {
        $header = "Checklist Penerimaan Barang";
        return view('office.checklist_kerja.checklistPenerimaan', compact('header'));
    }

    public function pdf_checklistKerjaPenerimaan(Request $request)
    {
        $tanggal = Carbon::parse($request->tanggal)->format('Y-m-d');
        $table = TbPoBahan::with(['tbMasterBahan.satuanBahan', 'tbPo.tbKontrak.supplier'])
            ->whereDate('tanggal_kedatangan', $tanggal)
            ->get();
	$table = DB::table('tb_po_bahan as pob')
            ->join('tb_po as po', 'pob.id_po', '=', 'po.id')
            ->join('tb_master_bahan as mb', 'pob.id_bahan', '=', 'mb.id')
            ->join('tb_satuan as s', 'pob.satuan', '=', 's.id')
            ->select(
                DB::raw("DATE_FORMAT(pob.tanggal_kedatangan, '%H:%i') as jam_menit"),
                'mb.bahan',
                'po.nomor_po',
                'pob.jumlah_bahan',
                's.satuan',
                'pob.jumlah_box'
            )
            ->whereDate('pob.tanggal_kedatangan', $tanggal)
            ->orderBy('pob.tanggal_kedatangan')
            ->get();
        $dapur = DataDapur::find(1);
        //return $table;
        $pdf = PDF::loadview('office.checklist_kerja.pdf.pdfChecklistKerjaPenerimaan', compact('table', 'tanggal', 'dapur'))->setPaper('A4','landscape');
        return $pdf->stream('PO '.'.pdf');
        //return $table;
    }

    public function pdf_checklistKerjaPenerimaan_2(Request $request) // rev
    {
        $tanggal = Carbon::parse($request->tanggal)->format('Y-m-d');
        $table = TbPoBahan::with(['tbMasterBahan.satuanBahan', 'tbPo.tbKontrak.supplier'])
            ->whereDate('tanggal_kedatangan', $tanggal)
            ->get();
        $table = DB::table('tb_po_bahan as pob')
            ->join('tb_po as po', 'pob.id_po', '=', 'po.id')
            ->join('tb_master_bahan as mb', 'pob.id_bahan', '=', 'mb.id')
            ->join('tb_satuan as s', 'pob.satuan', '=', 's.id')
            ->select(
                DB::raw("DATE_FORMAT(pob.tanggal_kedatangan, '%H:%i') as jam_menit"),
                'mb.bahan',
                'po.nomor_po',
                'pob.jumlah_bahan',
                's.satuan',
                'pob.jumlah_box'
            )
            ->whereDate('pob.tanggal_kedatangan', $tanggal)
            ->where('po.status_po', 'acc')
            ->orderBy('pob.tanggal_kedatangan')
            ->get();
            $table_beras = DB::table('tb_po_bahan as pob')
            ->join('tb_po as po', 'pob.id_po', '=', 'po.id')
            ->join('tb_master_bahan as mb', 'pob.id_bahan', '=', 'mb.id')
            ->join('tb_satuan as s', 'pob.satuan', '=', 's.id')
            ->select(
                DB::raw("DATE_FORMAT(pob.tanggal_kedatangan, '%H:%i') as jam_menit"),
                'mb.bahan',
                'po.nomor_po',
                'pob.jumlah_bahan',
                's.satuan',
                'pob.jumlah_box',
                'mb.jenis'
            )
            ->whereDate('pob.tanggal_kedatangan', $tanggal)
            ->whereIn('po.status_po', ['acc', 'close', 'bayar'])
            ->where('mb.jenis',1)
            ->orderBy('pob.tanggal_kedatangan')
            ->get();

        $table_lauk = DB::table('tb_po_bahan as pob')
            ->join('tb_po as po', 'pob.id_po', '=', 'po.id')
            ->join('tb_master_bahan as mb', 'pob.id_bahan', '=', 'mb.id')
            ->join('tb_satuan as s', 'pob.satuan', '=', 's.id')
            ->select(
                DB::raw("DATE_FORMAT(pob.tanggal_kedatangan, '%H:%i') as jam_menit"),
                'mb.bahan',
                'po.nomor_po',
                'pob.jumlah_bahan',
                's.satuan',
                'pob.jumlah_box',
                'mb.jenis'
            )
            ->whereDate('pob.tanggal_kedatangan', $tanggal)
            ->whereIn('po.status_po', ['acc', 'close', 'bayar'])
            ->where('mb.jenis', 2)
            //->orderBy('pob.tanggal_kedatangan')
            ->orderBy('mb.bahan', 'asc')
            ->get();
        $table_sayur = DB::table('tb_po_bahan as pob')
            ->join('tb_po as po', 'pob.id_po', '=', 'po.id')
            ->join('tb_master_bahan as mb', 'pob.id_bahan', '=', 'mb.id')
            ->join('tb_satuan as s', 'pob.satuan', '=', 's.id')
            ->select(
                DB::raw("DATE_FORMAT(pob.tanggal_kedatangan, '%H:%i') as jam_menit"),
                'mb.bahan',
                'po.nomor_po',
                'pob.jumlah_bahan',
                's.satuan',
                'pob.jumlah_box',
                'mb.jenis'
            )
            ->whereDate('pob.tanggal_kedatangan', $tanggal)
            ->whereIn('po.status_po', ['acc', 'close', 'bayar'])
            ->where('mb.jenis', 3)
            // ->orderBy('pob.tanggal_kedatangan')
            ->orderBy('mb.bahan', 'asc')
            ->get();

        $table_buah = DB::table('tb_po_bahan as pob')
            ->join('tb_po as po', 'pob.id_po', '=', 'po.id')
            ->join('tb_master_bahan as mb', 'pob.id_bahan', '=', 'mb.id')
            ->join('tb_satuan as s', 'pob.satuan', '=', 's.id')
            ->select(
                DB::raw("DATE_FORMAT(pob.tanggal_kedatangan, '%H:%i') as jam_menit"),
                'mb.bahan',
                'po.nomor_po',
                'pob.jumlah_bahan',
                's.satuan',
                'pob.jumlah_box',
                'mb.jenis'
            )
            ->whereDate('pob.tanggal_kedatangan', $tanggal)
            ->whereIn('po.status_po', ['acc', 'close', 'bayar'])
            ->where('mb.jenis', 4)
            //->orderBy('pob.tanggal_kedatangan')
            ->orderBy('mb.bahan', 'asc')
            ->get();

        $table_pendamping = DB::table('tb_po_bahan as pob')
            ->join('tb_po as po', 'pob.id_po', '=', 'po.id')
            ->join('tb_master_bahan as mb', 'pob.id_bahan', '=', 'mb.id')
            ->join('tb_satuan as s', 'pob.satuan', '=', 's.id')
            ->select(
                DB::raw("DATE_FORMAT(pob.tanggal_kedatangan, '%H:%i') as jam_menit"),
                'mb.bahan',
                'po.nomor_po',
                'pob.jumlah_bahan',
                's.satuan',
                'pob.jumlah_box',
                'mb.jenis'
            )
            ->whereDate('pob.tanggal_kedatangan', $tanggal)
            ->where('mb.jenis', 5)
            ->whereIn('po.status_po', ['acc', 'close', 'bayar'])
            //->orderBy('pob.tanggal_kedatangan')
            ->orderBy('mb.bahan', 'asc')
            ->get();

        $table_bumbu = DB::table('tb_po_bahan as pob')
            ->join('tb_po as po', 'pob.id_po', '=', 'po.id')
            ->join('tb_master_bahan as mb', 'pob.id_bahan', '=', 'mb.id')
            ->join('tb_satuan as s', 'pob.satuan', '=', 's.id')
            ->select(
                DB::raw("DATE_FORMAT(pob.tanggal_kedatangan, '%H:%i') as jam_menit"),
                'mb.bahan',
                'po.nomor_po',
                'pob.jumlah_bahan',
                's.satuan',
                'pob.jumlah_box',
                'mb.jenis',
                'po.status_po'
            )
            ->whereDate('pob.tanggal_kedatangan', $tanggal)
            ->whereIn('po.status_po', ['acc', 'close', 'bayar'])
            ->where('mb.jenis', 6)
            //->orderBy('pob.tanggal_kedatangan')
            ->orderBy('mb.bahan', 'asc')
            ->get();


        $dapur = DataDapur::find(1);
        //return $table;
        $pdf = PDF::loadview('office.checklist_kerja.pdf.pdfChecklistKerjaPenerimaan_2', compact(
            'table', 'tanggal', 'dapur',
            'table_beras',
            'table_lauk',
            'table_sayur',
            'table_buah',
            'table_pendamping',
            'table_bumbu',
            ))->setPaper('A4', 'landscape');
        return $pdf->stream('PO ' . '.pdf');
        //return $table;
    }

    public function dt_checklistPenerimaan(Request $request)
    {
        $tanggal = Carbon::parse($request->tanggal)->format('Y-m-d');
        $table = TbPoBahan::with(['tbMasterBahan.satuanBahan', 'tbPo.tbKontrak.supplier'])
        ->join('tb_po', 'tb_po_bahan.id_po','tb_po.id')
        ->where('tb_po.status_po','acc')    
        ->whereDate('tanggal_kedatangan', $tanggal)

            ->get();
        $table = DB::table('tb_po_bahan as po_bahan')
            ->join('tb_master_bahan as master_bahan', 'po_bahan.id_bahan', '=', 'master_bahan.id')
            ->join('tb_po as po', 'po_bahan.id_po', '=', 'po.id')
            ->join('tb_satuan as satuan', 'po_bahan.satuan', '=', 'satuan.id')
            ->select(
                'po_bahan.tanggal_kedatangan',
                'master_bahan.bahan',
                'po.nomor_po',
                DB::raw("'Koperasi Seribu Impian Bersama' as supplier"),
                'po_bahan.jumlah_bahan',
                'satuan.satuan'
            )
            ->whereDate('po_bahan.tanggal_kedatangan', $tanggal)
            ->whereIn('po.status_po', ['acc', 'close', 'bayar'])
            ->get();
        return datatables()->of($table)
            ->addIndexColumn()
            
            
            ->addColumn('satuan', function ($row) {
                
                return $satuan->satuan ?? '-';
                
                //return $row->tbMasterBahan->satuanBahan->satuan;
            })
            ->addColumn('supplier', function ($row) {
                return 'Koperasi Seribu Impian Bersama';
            })
            ->addColumn('nomor_po', function ($row) {
                return $row->nomor_po;
            })
            ->addColumn('nama_bahan', function ($row) {
                return $row->bahan;
            })
            ->editColumn('jumlah_bahan', function ($row) {
                return number_format($row->jumlah_bahan, 0, ',', '.');
            })
            ->editColumn('tanggal_kedatangan', function ($row) {
                return Carbon::parse($row->tanggal_kedatangan)->format('H:m');
            })
            ->make(true);
    }

    public function v_checklistMenu(Request $request)
    {
        $header = "Checklist Menu";
        return view('office.checklist_kerja.checklistMenu', compact('header'));
    }

    public function v_checklistHarian(Request $request)
    {
        $header = "Checklist Harian";
        return view('office.checklist_kerja.checklistHarian', compact('header'));
    }

    public function excel_checklistKerjaPenerimaan(Request $request)
    {
        $start = Carbon::parse($request->tanggal)->format('Y-m-d');

        $data = DB::table('rincian_menu_harian')
            ->join('tb_master_bahan', 'rincian_menu_harian.id_bahan', '=', 'tb_master_bahan.id')
            ->join('tb_satuan', 'rincian_menu_harian.id_satuan', '=', 'tb_satuan.id')
            ->join('tb_menu', 'rincian_menu_harian.id_menu_harian', '=', 'tb_menu.id')
            ->where('tb_menu.tanggal_kirim', $start)
            ->select(
                'rincian_menu_harian.id_bahan',
                'tb_master_bahan.bahan',
                'tb_satuan.satuan',
                DB::raw('SUM(rincian_menu_harian.jumlah) as total_jumlah'),
                DB::raw('MIN(rincian_menu_harian.keterangan) as keterangan')
            )
            ->groupBy(
                'rincian_menu_harian.id_bahan',
                'tb_master_bahan.bahan',
                'tb_satuan.satuan'
            )
            ->orderBy('tb_master_bahan.bahan')
            ->get();
        $count = DB::table('rincian_menu_harian')
            ->join('tb_master_bahan', 'rincian_menu_harian.id_bahan', '=', 'tb_master_bahan.id')
            ->join('tb_satuan', 'rincian_menu_harian.id_satuan', '=', 'tb_satuan.id')
            ->join('tb_menu', 'rincian_menu_harian.id_menu_harian', '=', 'tb_menu.id')
            ->where('tb_menu.tanggal_kirim', $start)
            ->select(
                'rincian_menu_harian.id_bahan',
                'tb_master_bahan.bahan',
                'tb_satuan.satuan',
                DB::raw('SUM(rincian_menu_harian.jumlah) as total_jumlah'),
                DB::raw('MIN(rincian_menu_harian.keterangan) as keterangan')
            )
            ->groupBy(
                'rincian_menu_harian.id_bahan',
                'tb_master_bahan.bahan',
                'tb_satuan.satuan'
            )
            ->orderBy('tb_master_bahan.bahan')
            ->count();

        





        return Excel::download(
            new FormPenerimaanExport($data, $start,$count),
            'Form Cheklist Penerimaan ' . $start . '.xlsx'
        );
    }

    public function excel_checklistKerja_Penerimaan_non_pangan(Request $request)
    {
        $start = Carbon::parse($request->tanggal)->format('Y-m-d');

        $data = DB::table('rincian_menu_harian')
            ->join('tb_master_bahan', 'rincian_menu_harian.id_bahan', '=', 'tb_master_bahan.id')
            ->join('tb_satuan', 'rincian_menu_harian.id_satuan', '=', 'tb_satuan.id')
            ->join('tb_menu', 'rincian_menu_harian.id_menu_harian', '=', 'tb_menu.id')
            ->where('tb_menu.tanggal_kirim', $start)
            ->select(
                'rincian_menu_harian.id_bahan',
                'tb_master_bahan.bahan',
                'tb_satuan.satuan',
                DB::raw('SUM(rincian_menu_harian.jumlah) as total_jumlah'),
                DB::raw('MIN(rincian_menu_harian.keterangan) as keterangan')
            )
            ->groupBy(
                'rincian_menu_harian.id_bahan',
                'tb_master_bahan.bahan',
                'tb_satuan.satuan'
            )
            ->orderBy('tb_master_bahan.bahan')
            ->get();
        $count = DB::table('rincian_menu_harian')
            ->join('tb_master_bahan', 'rincian_menu_harian.id_bahan', '=', 'tb_master_bahan.id')
            ->join('tb_satuan', 'rincian_menu_harian.id_satuan', '=', 'tb_satuan.id')
            ->join('tb_menu', 'rincian_menu_harian.id_menu_harian', '=', 'tb_menu.id')
            ->where('tb_menu.tanggal_kirim', $start)
            ->select(
                'rincian_menu_harian.id_bahan',
                'tb_master_bahan.bahan',
                'tb_satuan.satuan',
                DB::raw('SUM(rincian_menu_harian.jumlah) as total_jumlah'),
                DB::raw('MIN(rincian_menu_harian.keterangan) as keterangan')
            )
            ->groupBy(
                'rincian_menu_harian.id_bahan',
                'tb_master_bahan.bahan',
                'tb_satuan.satuan'
            )
            ->orderBy('tb_master_bahan.bahan')
            ->count();







        return Excel::download(
            new FormPenerimaanNonPanganExport($data, $start, $count),
            'Form Cheklist Penerimaan ' . $start . '.xlsx'
        );
    }

    public function excel_form_Pengeluaran_non_pangan(Request $request)
    {
        $start = Carbon::parse($request->tanggal)->format('Y-m-d');

        $data = DB::table('rincian_menu_harian')
            ->join('tb_master_bahan', 'rincian_menu_harian.id_bahan', '=', 'tb_master_bahan.id')
            ->join('tb_satuan', 'rincian_menu_harian.id_satuan', '=', 'tb_satuan.id')
            ->join('tb_menu', 'rincian_menu_harian.id_menu_harian', '=', 'tb_menu.id')
            ->where('tb_menu.tanggal_kirim', $start)
            ->select(
                'rincian_menu_harian.id_bahan',
                'tb_master_bahan.bahan',
                'tb_satuan.satuan',
                DB::raw('SUM(rincian_menu_harian.jumlah) as total_jumlah'),
                DB::raw('MIN(rincian_menu_harian.keterangan) as keterangan')
            )
            ->groupBy(
                'rincian_menu_harian.id_bahan',
                'tb_master_bahan.bahan',
                'tb_satuan.satuan'
            )
            ->orderBy('tb_master_bahan.bahan')
            ->get();
        $count = DB::table('rincian_menu_harian')
            ->join('tb_master_bahan', 'rincian_menu_harian.id_bahan', '=', 'tb_master_bahan.id')
            ->join('tb_satuan', 'rincian_menu_harian.id_satuan', '=', 'tb_satuan.id')
            ->join('tb_menu', 'rincian_menu_harian.id_menu_harian', '=', 'tb_menu.id')
            ->where('tb_menu.tanggal_kirim', $start)
            ->select(
                'rincian_menu_harian.id_bahan',
                'tb_master_bahan.bahan',
                'tb_satuan.satuan',
                DB::raw('SUM(rincian_menu_harian.jumlah) as total_jumlah'),
                DB::raw('MIN(rincian_menu_harian.keterangan) as keterangan')
            )
            ->groupBy(
                'rincian_menu_harian.id_bahan',
                'tb_master_bahan.bahan',
                'tb_satuan.satuan'
            )
            ->orderBy('tb_master_bahan.bahan')
            ->count();






        return Excel::download(
            new FormPengeluaranNonPanganExport($data, $start, $count),
            'Form Pengeluaran Non Pangan.xlsx'
        );
    }

    public function excel_hasil_matang(Request $request)
    {
        $start = Carbon::parse($request->tanggal)->format('Y-m-d');

        $data = DB::table('rincian_menu_harian')
            ->join('tb_master_bahan', 'rincian_menu_harian.id_bahan', '=', 'tb_master_bahan.id')
            ->join('tb_satuan', 'rincian_menu_harian.id_satuan', '=', 'tb_satuan.id')
            ->join('tb_menu', 'rincian_menu_harian.id_menu_harian', '=', 'tb_menu.id')
            ->where('tb_menu.tanggal_kirim', $start)
            ->select(
                'rincian_menu_harian.id_bahan',
                'tb_master_bahan.bahan',
                'tb_satuan.satuan',
                DB::raw('SUM(rincian_menu_harian.jumlah) as total_jumlah'),
                DB::raw('MIN(rincian_menu_harian.keterangan) as keterangan')
            )
            ->groupBy(
                'rincian_menu_harian.id_bahan',
                'tb_master_bahan.bahan',
                'tb_satuan.satuan'
            )
            ->orderBy('tb_master_bahan.bahan')
            ->get();
        $count = DB::table('rincian_menu_harian')
            ->join('tb_master_bahan', 'rincian_menu_harian.id_bahan', '=', 'tb_master_bahan.id')
            ->join('tb_satuan', 'rincian_menu_harian.id_satuan', '=', 'tb_satuan.id')
            ->join('tb_menu', 'rincian_menu_harian.id_menu_harian', '=', 'tb_menu.id')
            ->where('tb_menu.tanggal_kirim', $start)
            ->select(
                'rincian_menu_harian.id_bahan',
                'tb_master_bahan.bahan',
                'tb_satuan.satuan',
                DB::raw('SUM(rincian_menu_harian.jumlah) as total_jumlah'),
                DB::raw('MIN(rincian_menu_harian.keterangan) as keterangan')
            )
            ->groupBy(
                'rincian_menu_harian.id_bahan',
                'tb_master_bahan.bahan',
                'tb_satuan.satuan'
            )
            ->orderBy('tb_master_bahan.bahan')
            ->count();
    
        return Excel::download(
            new LaporanHasilMasakExport($data, $start, $count),
            'Hasil Matang ' . $start . '.xlsx'
        );
    }

    public function excel_Stok_Opnam_Non_Pangan(Request $request)
    {
        $start = Carbon::parse($request->tanggal)->format('Y-m-d');

        $data = DB::table('rincian_menu_harian')->get();
        $count = DB::table('rincian_menu_harian')->count();

        return Excel::download(
            new FormStokOpnamNonPanganExport($data, $start, $count),
            'Form Stok Opnam Kosong .xlsx'
        );
    }

    public function excel_form_Penerimaan_1(Request $request)
    {
        $tanggal = $request->input('tanggal', date('Y-m-d'));
        
        return Excel::download(
            new \App\Exports\FormPenerimaan1Export($tanggal),
            'Form_Penerimaan_' . date('d-m-Y', strtotime($tanggal)) . '.xlsx'
        );
    }

    
    public function excel_penerimaan_all(Request $request)
    {
        $tanggal = $request->input('tanggal', date('Y-m-d'));
        
        return Excel::download(
            new \App\Exports\PenerimaanMultiSheetExport($tanggal),
            'Penerimaan_All_' . date('d-m-Y', strtotime($tanggal)) . '.xlsx'
        );
    }

    public function excel_penerimaan_checklist_1_1(Request $request)
    {
        $tanggal = $request->input('tanggal', date('Y-m-d'));
        
        return Excel::download(
            new \App\Exports\PenerimaanChecklist1Export($tanggal),
            'Checklist_Penerimaan_Karbo_' . date('d-m-Y', strtotime($tanggal)) . '.xlsx'
        );
    }
    
    public function excel_penerimaan_checklist_2_1(Request $request)
    {
        $tanggal = $request->input('tanggal', date('Y-m-d'));
        $jam = $request->input('jam_pelayanan'); // 'semua' or specific HH:MM

        return Excel::download(
            new \App\Exports\PenerimaanChecklist2Export($tanggal, $jam),
            'Checklist_Penerimaan_2_' . date('d-m-Y', strtotime($tanggal)) . '.xlsx'
        );
    }

    public function excel_checklist_uji_organoleptik($idmenu)
    {
        $menu = DB::table('tb_menu')->where('id', $idmenu)->first();

        if (!$menu) {
            return redirect()->back()->with('error', 'Menu tidak ditemukan');
        }

        $tanggal = $menu->tanggal_kirim ? Carbon::parse($menu->tanggal_kirim)->format('d-m-Y') : now()->format('d-m-Y');

        return Excel::download(
            new ChecklistUjiOrganoleptikExport($idmenu),
            'Checklist_Uji_Organoleptik_' . $tanggal . '_Menu_' . $idmenu . '.xlsx'
        );
    }

    public function pdf_checklist_uji_organoleptik($idmenu)
    {
        $menu = Menu::with([
            'resepKarbohidrat',
            'resepProtein',
            'resepSayur',
            'resepBuah',
            'resepSusu',
        ])->find($idmenu);

        if (!$menu) {
            return redirect()->back()->with('error', 'Menu tidak ditemukan');
        }

        $dapur = DataDapur::first();
        $realisasiPax = (int) DB::table('rincian_sekolah')
            ->where('id_menu_harian', $idmenu)
            ->sum('jumlah_penerima_total');

        $items = [];

        $components = [
            'Karbohidrat' => $menu->resepKarbohidrat,
            'Lauk' => $menu->resepProtein,
            'Sayur' => $menu->resepSayur,
            'Buah' => $menu->resepBuah,
            'Suplemen' => $menu->resepSusu,
        ];

        foreach ($components as $label => $resep) {
            if ($resep) {
                $items[] = [
                    'label' => $label,
                    'nama_makanan' => $resep->nama_resep,
                ];
            }
        }

        $pdf = Pdf::loadView('office.checklist_kerja.pdf.pdfChecklistKerjaOrganoleptik', [
            'dapur' => $dapur,
            'menu' => $menu,
            'realisasiPax' => $realisasiPax,
            'items' => $items,
        ])->setPaper('A4', 'portrait');

        $tanggal = $menu->tanggal_kirim ? Carbon::parse($menu->tanggal_kirim)->format('d-m-Y') : now()->format('d-m-Y');

        return $pdf->stream('Checklist_Uji_Organoleptik_' . $tanggal . '_Menu_' . $idmenu . '.pdf');
    }
}   
