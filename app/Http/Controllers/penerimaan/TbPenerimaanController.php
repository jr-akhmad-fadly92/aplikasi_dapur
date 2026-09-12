<?php

namespace App\Http\Controllers\penerimaan;

use App\Http\Controllers\Controller;
use App\Models\BoxBahanBaku;
use App\Models\DataDapur;
use App\Models\PenerimaanDelete;
use App\Models\rincian_menu_harian;
use App\Models\rincian_sekolah;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Models\TbPenerimaan;
use App\Models\TbPoBahan;
use App\Models\TbBantuBahanPo;
use App\Models\TbPo;
use App\Models\TbSatuan;
use App\Models\TbWadah;
use App\Models\TransaksiWadah;


class TbPenerimaanController extends Controller
{
    private function canManagePenerimaanAdjustments(): bool
    {
        return auth()->check() && in_array(auth()->user()->level, ['backoffice', 'admin'], true);
    }

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Menampilkan daftar penerimaan.
     */
    public function index()
    {
        $header     = "Dashboard Bahan Masuk";
        $penerimaan = TbPenerimaan::all();
        $satuan     = TbSatuan::all();
        Carbon::setlocale('id');
        if (request()->ajax()) {
            $pobahan =  DB::table('tb_po_bahan')
                ->leftJoin('tb_penerimaan', 'tb_po_bahan.id', '=', 'tb_penerimaan.id_barang_po')
                ->leftJoin('tb_master_bahan', 'tb_po_bahan.id_bahan', '=', 'tb_master_bahan.id')
                ->leftJoin('tb_po', 'tb_po_bahan.id_po', '=', 'tb_po.id')
                ->leftJoin('tb_kontrak', 'tb_po.id_kontrak', '=', 'tb_kontrak.id')
                ->leftJoin('tb_supplier', 'tb_kontrak.id_supplier', '=', 'tb_supplier.id')
                ->join('tb_satuan', 'tb_po_bahan.satuan', '=', 'tb_satuan.id')
                // ->where('tb_po_bahan.tanggal_kedatangan', '>=', Carbon::now()->addDays(1)->toDateString())
                /*->whereBetween('tb_po_bahan.tanggal_kedatangan', [
    Carbon::today()->toDateTimeString(),
    Carbon::tomorrow()->toDateTimeString()
])*/
->whereBetween('tb_po_bahan.tanggal_digunakan', [
    Carbon::now()->toDateString(),        // hari ini
    Carbon::now()->addDay()->toDateString() // besok
])
/*->whereBetween('tb_po_bahan.tanggal_kedatangan', [
    Carbon::yesterday()->startOfDay()->toDateString(),
    Carbon::today()->endOfDay()->toDateString()
])*/

                ->whereIn('tb_po.status_po',['acc','bayar'])
                ->select(
                    'tb_po_bahan.id',
                    'tb_po_bahan.id_po',
                    'tb_satuan.satuan as nama_satuan',
                    'tb_supplier.nama_supplier',
                    'tb_master_bahan.id as idbahan',
                    'tb_master_bahan.bahan as nama_bahan',
                    'tb_po_bahan.id_rincian_bahan',
                    'tb_po_bahan.jumlah_bahan',
                    'tb_po_bahan.satuan',
                    'tb_po_bahan.tanggal_kedatangan',
                   
                    DB::raw('COALESCE(SUM(tb_penerimaan.jumlah_datang), 0) AS total_terima'),
                    DB::raw('(tb_po_bahan.jumlah_bahan - COALESCE(SUM(tb_penerimaan.jumlah_datang), 0)) AS sisa_jumlah'),
                    DB::raw('(tb_po_bahan.jumlah_bahan - COALESCE(SUM(tb_penerimaan.jumlah_datang), 0)) AS jumlah_yang_akan_datang'),
                    DB::raw("
                        CASE
                            WHEN COALESCE(SUM(tb_penerimaan.jumlah_datang), 0) = 0 THEN 'Belum datang'
                            WHEN COALESCE(SUM(tb_penerimaan.jumlah_datang), 0) < tb_po_bahan.jumlah_bahan THEN 'Barang sudah datang namun kurang'
                            ELSE ''
                        END AS keterangan
                    "),
                    DB::raw("
                        GROUP_CONCAT(
                            CASE
                                WHEN tb_penerimaan.status = 0 THEN CONCAT('Barang direject karena ', tb_penerimaan.keterangan)
                            END
                            SEPARATOR ', '
                        ) AS keterangan_reject
                    ")
                )
                ->groupBy(
                    'tb_po_bahan.id',
                    'tb_po_bahan.id_po',
                    'tb_satuan.satuan',
                    'tb_supplier.nama_supplier',
                    'tb_master_bahan.id',
                    'tb_master_bahan.bahan',
                    'tb_po_bahan.id_rincian_bahan',
                    'tb_po_bahan.jumlah_bahan',
                    'tb_po_bahan.satuan',
                    'tb_po_bahan.tanggal_kedatangan'
                )
               // ->having('sisa_jumlah', '>', 0)
                ->orderBy('tb_po_bahan.tanggal_kedatangan');

            return DataTables::of($pobahan)
                ->addIndexColumn()
                ->addColumn('nomor_po_datang', function ($row) {
                    $data_po = TbPo::where('id',$row->id_po)->first();
                    $data_bahan = TbPoBahan::find($row->id);
                    $ket = 'untuk Gol B';
                    if($data_bahan->id_rincian_bahan != 0 )
                    {
                        $data_menu = rincian_menu_harian::find($data_bahan->id_rincian_bahan);
                        $cek = rincian_sekolah::where('id_menu_harian',$data_menu->id_menu_harian)->sum('jumlah_penerima_a');
                        if($cek > 0)
                        {
                        $ket = 'untuk Gol B';
                        }else{
                        $ket = 'untuk Gol A';
                        }
                    }else{
                        $ket = 'untuk non Pangan';
                    }
                    return $data_po->nomor_po.' '. $ket ?? '-';
                })
                ->addColumn('jumlah_yang_sudah_datang', function ($row) {
                    $satuan = TbSatuan::find($row->satuan);
                    $nama_satuan = $satuan->satuan;
                    $total_diterima = 0;
                    if($satuan->satuan == 'kg' || $satuan->satuan == 'Kg')
                    {
                        $nama_satuan = 'gram';
                        $total_diterima = $row->jumlah_bahan*1000;
                    }else{
                    $nama_satuan = $nama_satuan = $satuan->satuan;
                    $total_diterima = $row->jumlah_bahan;
                    }

                    return  $row->total_terima ??0;
                })
                ->addColumn('keterangan', function ($row) {
                    
                    return $row->keterangan_reject ?? $row->keterangan;
                }) 
                ->addColumn('jumlah_dan_satuan_sudah_datang', function ($row) {
                    $satuan = TbSatuan::find($row->satuan);
                    $nama_satuan = $satuan->satuan;
                    $total_diterima = 0;
                    if($satuan->satuan == 'kg' || $satuan->satuan == 'Kg')
                    {
                        $nama_satuan = 'gram';
                        
                    }else if($satuan->satuan == 'liter' || $satuan->satuan == 'Liter')
                    {
                    $nama_satuan = 'ml';
                    }else{
                    $nama_satuan = $nama_satuan = $satuan->satuan;
                    }
                    return number_format($row->total_terima, 0, ',', '.') . ' ' . $nama_satuan;
                }) 
                ->addColumn('jumlah_dan_satuan', function ($row) {
                    $satuan = TbSatuan::find($row->satuan);
                    $sisa = $row->total_terima;
                    if($satuan->satuan == 'kg' || $satuan->satuan == 'Kg')
                    {
                        $sisa = $row->jumlah_bahan*1000 - $sisa;
                        $nama_satuan = 'gram';
                    }else if($satuan->satuan == 'liter' || $satuan->satuan == 'Liter')
                    {
                    $sisa = $row->jumlah_bahan - $sisa;
                        $nama_satuan = 'ml';
                    }else{
                    $sisa = $row->jumlah_bahan - $sisa;
                        $nama_satuan = $nama_satuan = $satuan->satuan;
                    }

                    if($sisa < 0)
                    {
                    return 'lebih '.number_format(abs($sisa), 0, ',', '.') . ' ' . $nama_satuan;

                    }else{

                    return number_format($sisa, 0, ',', '.') . ' ' . $nama_satuan;
                    }
                })

                ->addColumn('action', function ($row) {
                $satuan = TbSatuan::find($row->satuan);
                $jumlah_pack = 0 ;
                $sisa = $row->total_terima;
              
                if ($satuan->satuan == 'kg' || $satuan->satuan == 'Kg') {
                    $sisa = $row->jumlah_bahan * 1000  - $sisa;
                    $nama_satuan = 'gram';
                } else if ($satuan->satuan == 'liter' || $satuan->satuan == 'Liter') {
                    $sisa = $sisa * 1000;
                    $nama_satuan = 'ml';
                } else {
                    $sisa = $row->jumlah_bahan - $sisa;
                    $nama_satuan =  $satuan->satuan;
                }
                $cek_tipe_box = BoxBahanBaku::where('id_bahan', $row->idbahan)->first();
                
		    
                    $satuan = TbSatuan::find($row->satuan);
                    $nama_satuan = $satuan->satuan;
                    $total_diterima = 0;
                    if($satuan->satuan == 'kg' || $satuan->satuan == 'Kg')
                    {
                        $nama_satuan = 'gram';
                        
                    }else if($satuan->satuan == 'liter' || $satuan->satuan == 'Liter')
                    {
                    $nama_satuan = 'ml';
                    }else{
                    $nama_satuan = $satuan->satuan;
                    }

                if($cek_tipe_box)
                {
                    if($cek_tipe_box->isi_per_box < $sisa)
                    {
                        $jumlah_pack  = $cek_tipe_box->isi_per_box;
                    }else{
                        $jumlah_pack  = $sisa;
                    }
                    
			        $btn            = '<button class="btn btn-sm btn-primary openModalBtn" 
                    
                                            data-idbahan="' . $row->id . '"
                                            data-jumlahbahan="' .  number_format(abs($sisa), 0, ',', '.')  . ' ' . $nama_satuan . '"
                                            data-idrincian="' . $row->id_rincian_bahan . '"
                                            data-jumlahdatang=' . 0 . '
                                            
                                            >
                                            Input Penerimaan
                                        </button>';
                } else{
                    $btn            = '<button class="btn btn-sm btn-primary openModalBtn" 
                    
                                            data-idbahan="' . $row->id . '"
                                            data-jumlahbahan="' . number_format(abs($row->sisa_jumlah), 0, ',', '.')  . ' ' . $nama_satuan . '"
                                            data-idrincian="' . $row->id_rincian_bahan . '"
                                            data-jumlahdatang=' . 0 . '
                                            >
                                            Input Penerimaan
                                        </button>';
                }
                
                
                return $btn;
                })
                ->rawColumns(['action', 'status_wadah', 'keterangan'])
                ->make(true);
        }

      
        return view('penerimaan/penerimaan_bahan.index', compact('header','penerimaan', 'satuan'));
    }

    /**
     * Menampilkan form tambah penerimaan.
     */
    public function create(): View
    {
        return view('penerimaan.create');
    }

    /**
     * Menyimpan data penerimaan ke database.
     */
    public function store(Request $request)
    {
        /*$request->validate([
            'id_barang_po' => 'required|integer',
            'jumlah_datang' => 'required|integer',
            'jumlah_berat' => 'required|integer',
            'satuan_berat' => 'required|integer',
            'status' => 'required|in:0,1,2',
            'keterangan' => 'nullable|string',
            'id_qr_code' => 'required|integer',
            'nama_penerima' => 'required|string|max:100'
        ]);

        TbPenerimaan::create($request->all());
        */
        return redirect()->route('tb_penerimaan.index')->with('success', 'Data penerimaan berhasil ditambahkan!');
    }

    /**
     * Menampilkan detail penerimaan.
     */
    public function show($id)
    {
        $penerimaan = TbPenerimaan::findOrFail($id);
        return view('penerimaan.show', compact('penerimaan'));
    }

    /**
     * Menampilkan form edit penerimaan.
     */
    public function edit($id): View
    {
        $penerimaan = TbPenerimaan::findOrFail($id);
        return view('penerimaan.edit', compact('penerimaan'));
    }

    /**
     * Memperbarui data penerimaan.
     */
    public function update(Request $request, $id)
    {
        $penerimaan = TbPenerimaan::findOrFail($id);
        
        $request->validate([
            'id_barang_po' => 'required|integer',
            'jumlah_datang' => 'required|integer',
            'jumlah_berat' => 'required|integer',
            'satuan_berat' => 'required|integer',
            'status' => 'required|in:0,1,2',
            'keterangan' => 'nullable|string',
            'id_qr_code' => 'required|integer',
            'nama_penerima' => 'required|string|max:100'
        ]);

        $penerimaan->update($request->all());

        return redirect()->route('tb_penerimaan.index')->with('success', 'Data penerimaan berhasil diperbarui!');
    }

    /**
     * Menghapus data penerimaan.
     */
    public function destroy($id)
    {
        $penerimaan = TbPenerimaan::findOrFail($id);
        $penerimaan->delete();

        return redirect()->route('tb_penerimaan.index')->with('success', 'Data penerimaan berhasil dihapus!');
    }

    public function laporan_penerimaan(Request $request)
    {
        $header = 'Laporan Penerimaan';
        $tanggal = $request->get('tanggal', Carbon::today()->toDateString());
        $satuan = TbSatuan::all();

        $rencanaKedatangan = DB::table('tb_po_bahan as po_bahan')
            ->join('tb_po as po', 'po_bahan.id_po', '=', 'po.id')
            ->join('tb_master_bahan as bahan', 'po_bahan.id_bahan', '=', 'bahan.id')
            ->leftJoin('tb_satuan', 'po_bahan.satuan', '=', 'tb_satuan.id')
            ->leftJoin('tb_penerimaan as penerimaan', 'po_bahan.id', '=', 'penerimaan.id_barang_po')
            ->whereDate('po_bahan.tanggal_kedatangan', $tanggal)
            ->whereIn('po.status_po', ['acc', 'bayar'])
            ->select(
                'po_bahan.id',
                'po_bahan.id_po',
                'po_bahan.id_rincian_bahan',
                'po.nomor_po',
                'bahan.bahan as nama_bahan',
                'po_bahan.jumlah_bahan',
                'tb_satuan.satuan',
                'po_bahan.tanggal_kedatangan',
                DB::raw('COALESCE(SUM(CASE WHEN penerimaan.status IS NULL OR penerimaan.status <> 0 THEN penerimaan.jumlah_datang ELSE 0 END), 0) as total_datang')
            )
            ->groupBy(
                'po_bahan.id',
                'po_bahan.id_po',
                'po_bahan.id_rincian_bahan',
                'po.nomor_po',
                'bahan.bahan',
                'po_bahan.jumlah_bahan',
                'tb_satuan.satuan',
                'po_bahan.tanggal_kedatangan'
            )
            ->orderBy('po_bahan.tanggal_kedatangan', 'asc')
            ->get();

        $akanDatang = $rencanaKedatangan->filter(function ($item) {
            return (float) $item->total_datang <= 0;
        })->values();

        $sudahDatang = $rencanaKedatangan->filter(function ($item) {
            return (float) $item->total_datang > 0;
        })->values();

        $totalDatangPerHari = DB::table('tb_penerimaan as penerimaan')
            ->join('tb_po_bahan as po_bahan', 'penerimaan.id_barang_po', '=', 'po_bahan.id')
            ->join('tb_po as po', 'po_bahan.id_po', '=', 'po.id')
            ->join('tb_master_bahan as bahan', 'po_bahan.id_bahan', '=', 'bahan.id')
            ->leftJoin('tb_satuan', 'po_bahan.satuan', '=', 'tb_satuan.id')
            ->whereDate('penerimaan.created_at', $tanggal)
            ->whereIn('po.status_po', ['acc', 'bayar'])
            ->where('penerimaan.status', '<>', 0)
            ->select(
                'po_bahan.id_po',
                'po.nomor_po',
                'bahan.bahan as nama_bahan',
                'tb_satuan.satuan',
                DB::raw('SUM(penerimaan.jumlah_datang) as jumlah_datang_hari_ini')
            )
            ->groupBy('po_bahan.id_po', 'po.nomor_po', 'bahan.bahan', 'tb_satuan.satuan')
            ->orderBy('po.nomor_po', 'asc')
            ->get();

        $detailPenerimaanPerHari = DB::table('tb_penerimaan as penerimaan')
            ->join('tb_po_bahan as po_bahan', 'penerimaan.id_barang_po', '=', 'po_bahan.id')
            ->join('tb_po as po', 'po_bahan.id_po', '=', 'po.id')
            ->join('tb_master_bahan as bahan', 'po_bahan.id_bahan', '=', 'bahan.id')
            ->leftJoin('tb_satuan', 'po_bahan.satuan', '=', 'tb_satuan.id')
            ->whereDate('penerimaan.created_at', $tanggal)
            ->whereIn('po.status_po', ['acc', 'bayar'])
            ->where('penerimaan.status', '<>', 0)
            ->select(
                'penerimaan.id as id_penerimaan',
                'penerimaan.id_barang_po',
                'po_bahan.id_rincian_bahan',
                'po_bahan.jumlah_bahan',
                'po.nomor_po',
                'bahan.bahan as nama_bahan',
                'penerimaan.jumlah_datang',
                'tb_satuan.satuan',
                'penerimaan.qr_code_wadah',
                'penerimaan.status',
                'penerimaan.keterangan',
                'penerimaan.nama_penerima',
                'penerimaan.created_at',
                DB::raw('CASE WHEN EXISTS (SELECT 1 FROM warehouse_transaksi wt WHERE wt.id_penerimaan = penerimaan.id) THEN 1 ELSE 0 END as sudah_masuk_gudang')
            )
            ->orderBy('penerimaan.created_at', 'desc')
            ->get();

        $masukGudang = DB::table('warehouse_transaksi as wt')
            ->leftJoin('tb_penerimaan as penerimaan', 'wt.id_penerimaan', '=', 'penerimaan.id')
            ->leftJoin('tb_po_bahan as po_bahan', 'penerimaan.id_barang_po', '=', 'po_bahan.id')
            ->leftJoin('tb_po as po', 'po_bahan.id_po', '=', 'po.id')
            ->leftJoin('tb_satuan', 'wt.id_satuan', '=', 'tb_satuan.id')
            ->whereDate('wt.tanggal_masuk', '<=', $tanggal)
            ->where(function ($query) use ($tanggal) {
                $query->whereNull('wt.tanggal_keluar')
                    ->orWhereDate('wt.tanggal_keluar', '>', $tanggal);
            })
            ->select(
                'wt.id',
                'wt.kode_wadah',
                DB::raw('COALESCE(po.nomor_po, wt.no_po) as nomor_po'),
                'wt.nama_barang',
                'wt.jumlah',
                'wt.id_satuan',
                'tb_satuan.satuan',
                'wt.tanggal_masuk',
                'wt.tanggal_akan_keluar',
                'wt.tanggal_keluar',
                'wt.lokasi',
                'wt.keterangan'
            )
            ->orderBy('wt.nama_barang', 'asc')
            ->orderBy('wt.tanggal_masuk', 'asc')
            ->get();

        $masukGudang = $masukGudang
            ->groupBy(function ($item) {
                $namaBarang = trim((string) $item->nama_barang);
                return $namaBarang !== '' ? mb_strtolower($namaBarang) : 'row:' . $item->id;
            })
            ->flatMap(function ($items) {
                $groupSize = $items->count();
                $groupIds = $items->pluck('id')->implode(',');

                return $items->values()->map(function ($item, $index) use ($groupSize, $groupIds) {
                    $item->display_nama_barang = $index === 0 ? $item->nama_barang : '';
                    $item->group_size = $groupSize;
                    $item->group_item_ids = $index === 0 ? $groupIds : '';
                    $item->show_group_actions = $index === 0;

                    return $item;
                });
            })
            ->values();

        $totalDirencanakan = $rencanaKedatangan->count();
        $totalSudahDatang  = $sudahDatang->count();
        $persentaseDatang = $totalDirencanakan > 0
            ? min(100, round(($totalSudahDatang / $totalDirencanakan) * 100, 2))
            : 0;

        $poChecklistId = DB::table('tb_po_bahan as po_bahan')
            ->join('tb_po as po', 'po_bahan.id_po', '=', 'po.id')
            ->whereDate('po_bahan.tanggal_kedatangan', $tanggal)
            ->where('po.status_po', 'acc')
            ->orderBy('po_bahan.tanggal_kedatangan', 'asc')
            ->orderBy('po_bahan.id', 'asc')
            ->value('po_bahan.id_po');

        return view('penerimaan.laporan.index', compact(
            'header',
            'tanggal',
            'akanDatang',
            'sudahDatang',
            'totalDatangPerHari',
            'detailPenerimaanPerHari',
            'masukGudang',
            'totalDirencanakan',
            'totalSudahDatang',
            'persentaseDatang',
            'satuan',
            'poChecklistId'
        ));
    }

    public function koreksi_penerimaan_bahan(Request $request)
    {
        if (!$this->canManagePenerimaanAdjustments()) {
            return response()->json(['success' => false, 'message' => 'Hanya admin atau backoffice yang dapat melakukan koreksi'], 403);
        }

        $request->validate([
            'id_penerimaan' => 'required|integer|exists:tb_penerimaan,id',
            'jumlah_datang' => 'required|numeric|min:0',
            'qrcode_wadah' => 'required|string',
            'status' => 'required|in:0,1,2',
            'keterangan' => 'nullable|string',
            'nama_penerima' => 'nullable|string',
        ]);

        DB::table('tb_penerimaan')
            ->where('id', $request->id_penerimaan)
            ->update([
                'jumlah_datang' => $request->jumlah_datang,
                'qr_code_wadah' => $request->qrcode_wadah,
                'status' => $request->status,
                'keterangan' => $request->keterangan,
                'nama_penerima' => $request->nama_penerima,
                'updated_at' => Carbon::now('Asia/Jakarta'),
            ]);

        DB::table('warehouse_transaksi')
            ->where('id_penerimaan', $request->id_penerimaan)
            ->update([
                'kode_wadah' => $request->qrcode_wadah,
                'jumlah' => $request->jumlah_datang,
                'updated_at' => Carbon::now('Asia/Jakarta'),
            ]);

        return response()->json(['success' => true, 'message' => 'Data penerimaan berhasil dikoreksi']);
    }

    public function update_stok_gudang(Request $request)
    {
        if (!$this->canManagePenerimaanAdjustments()) {
            return response()->json(['success' => false, 'message' => 'Hanya admin atau backoffice yang dapat melakukan koreksi'], 403);
        }

        $request->validate([
            'id' => 'required|integer|exists:warehouse_transaksi,id',
            'jumlah' => 'required|numeric|min:0',
        ]);

        $warehouseItem = DB::table('warehouse_transaksi')
            ->where('id', $request->id)
            ->first();

        DB::table('warehouse_transaksi')
            ->where('id', $request->id)
            ->update([
                'jumlah' => $request->jumlah,
                'updated_at' => Carbon::now('Asia/Jakarta'),
            ]);

        if ($warehouseItem && !empty($warehouseItem->id_penerimaan)) {
            DB::table('tb_penerimaan')
                ->where('id', $warehouseItem->id_penerimaan)
                ->update([
                    'jumlah_datang' => $request->jumlah,
                    'updated_at' => Carbon::now('Asia/Jakarta'),
                ]);

            DB::table('tb_transaksi_wadah')
                ->where('id_penerimaan', $warehouseItem->id_penerimaan)
                ->update([
                    'jumlah_berat_sesudah' => $request->jumlah,
                    'updated_at' => Carbon::now('Asia/Jakarta'),
                ]);
        }

        return response()->json(['success' => true, 'message' => 'Stok barang berhasil diperbarui']);
    }

    public function update_satuan_gudang(Request $request)
    {
        if (!$this->canManagePenerimaanAdjustments()) {
            return response()->json(['success' => false, 'message' => 'Hanya admin atau backoffice yang dapat melakukan koreksi'], 403);
        }

        $request->validate([
            'item_ids' => 'required|string',
            'id_satuan' => 'required|integer|exists:tb_satuan,id',
        ]);

        $itemIds = collect(explode(',', $request->item_ids))
            ->map(function ($id) {
                return (int) trim($id);
            })
            ->filter(function ($id) {
                return $id > 0;
            })
            ->unique()
            ->values();

        if ($itemIds->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Data barang gudang tidak valid'], 422);
        }

        DB::table('warehouse_transaksi')
            ->whereIn('id', $itemIds->all())
            ->update([
                'id_satuan' => $request->id_satuan,
                'updated_at' => Carbon::now('Asia/Jakarta'),
            ]);

        $penerimaanIds = DB::table('warehouse_transaksi')
            ->whereIn('id', $itemIds->all())
            ->whereNotNull('id_penerimaan')
            ->pluck('id_penerimaan')
            ->filter(function ($id) {
                return !empty($id);
            })
            ->unique()
            ->values();

        if ($penerimaanIds->isNotEmpty()) {
            DB::table('tb_po_bahan')
                ->whereIn('id', function ($query) use ($penerimaanIds) {
                    $query->select('id_barang_po')
                        ->from('tb_penerimaan')
                        ->whereIn('id', $penerimaanIds->all());
                })
                ->update([
                    'satuan' => $request->id_satuan,
                    'updated_at' => Carbon::now('Asia/Jakarta'),
                ]);
        }

        return response()->json(['success' => true, 'message' => 'Satuan barang berhasil diperbarui']);
    }

    public function stok_opnam_pdf(Request $request)
    {
        $tanggal = $request->get('tanggal', Carbon::today()->toDateString());
        $dapur = DataDapur::first();

        $stokOpnam = DB::table('warehouse_transaksi as wt')
            ->leftJoin('tb_penerimaan as penerimaan', 'wt.id_penerimaan', '=', 'penerimaan.id')
            ->leftJoin('tb_po_bahan as po_bahan', 'penerimaan.id_barang_po', '=', 'po_bahan.id')
            ->leftJoin('tb_master_bahan as bahan', 'po_bahan.id_bahan', '=', 'bahan.id')
            ->selectRaw('COALESCE(bahan.bahan, wt.nama_barang) as nama_barang')
            ->selectRaw('SUM(wt.jumlah) as jumlah_awal')
            ->selectRaw('SUM(CASE WHEN wt.tanggal_keluar IS NOT NULL AND DATE(wt.tanggal_keluar) = ? THEN wt.jumlah ELSE 0 END) as jumlah_keluar', [$tanggal])
            ->groupBy(DB::raw('COALESCE(bahan.bahan, wt.nama_barang)'))
            ->whereDate('wt.tanggal_masuk', '<=', $tanggal)
            ->where(function ($query) use ($tanggal) {
                $query->whereNull('wt.tanggal_keluar')
                    ->orWhereDate('wt.tanggal_keluar', '>=', $tanggal);
            })
            ->orderBy('nama_barang', 'asc')
            ->get()
            ->map(function ($item, $index) {
                $item->nomor_urut = $index + 1;
                $item->jumlah_akhir = (float) $item->jumlah_awal - (float) $item->jumlah_keluar;
                return $item;
            });

        $pdf = Pdf::loadView('penerimaan.laporan.template_stok_opnam', compact('tanggal', 'dapur', 'stokOpnam'));
        return $pdf->download('Stok_Opnam_' . Carbon::parse($tanggal)->format('d-m-Y') . '.pdf');
    }
    
    public function simpan_penerimaan_bahan(Request $request)
    {
        try {
            // Validasi Input
            $request->validate([
                'jumlah_datang' => 'required|numeric',
                //'jumlah_berat' => 'required|numeric',
                //'satuan_berat' => 'required|string',
                'qrcode_wadah' => 'required|string',
                'status' => 'required|in:0,1,2',
                'keterangan' => 'nullable|string',
                'nama_penerima' => 'nullable|string',
            ]);

            // Cek apakah wadah terdaftar
            /*$wadah = TbWadah::where('qr_code', $request->qrcode_wadah)->first();
            if (!$wadah) {
                return response()->json(['success' => false, 'message' => 'Wadah tidak terdaftar'], 400);
            }else{
                
            }
            $wadah = TbPenerimaan::where('qr_code_wadah', $request->qrcode_wadah)
            ->whereIn('status', [ 3])->count();
            if ($wadah > 0 ) {
                return response()->json(['success' => false, 'message' => 'Wadah sedang dipakai'], 400);
            } else {
            }*/
            // Simpan Data ke Database
            
            $penerimaan = new TbPenerimaan();
            
            $penerimaan->id_barang_po = $request->idbahan;
            $penerimaan->jumlah_datang = $request->jumlah_datang;
            /*$penerimaan->jumlah_berat = $request->jumlah_berat;
            $penerimaan->satuan_berat = $request->satuan_berat;*/
            $penerimaan->jumlah_berat = 0;
            $penerimaan->satuan_berat = 1;
            $penerimaan->qr_code_wadah = $request->qrcode_wadah;
            $penerimaan->status = $request->status;
            $penerimaan->keterangan = $request->keterangan;
            $penerimaan->nama_penerima = $request->nama_penerima;
            
            $penerimaan->save();
            /*$wadah->update([
                'status'    =>1
            ]);
            */


            // update di transaksi wadah 
            $data_penerimaan = TbPenerimaan::leftJoin('tb_po_bahan', 'tb_penerimaan.id_barang_po', '=', 'tb_po_bahan.id')
                ->leftJoin('tb_po', 'tb_po_bahan.id_po', '=', 'tb_po.id')
                ->select(
                    'tb_penerimaan.id',
                    'tb_po.nomor_po'
                )
                ->orderBy('tb_penerimaan.id', 'desc')
                ->first();
            TbPenerimaan::where('id', $data_penerimaan->id)->update([
                'created_at'  => Carbon::now('Asia/Jakarta')
            ]);
            $nomor_po = 'PO-Y002-202508012';
            $nomor_po = $data_penerimaan->nomor_po;
            $count_pemesanan = DB::table('tb_po as po')
                ->join('tb_po_bahan as pb', 'po.id', '=', 'pb.id_po')
                ->select(
                    'po.nomor_po',
                    'pb.id'
                )
                ->where('po.nomor_po', $nomor_po)
                ->count();
            $count_datang = TbPenerimaan::Join('tb_po_bahan', 'tb_penerimaan.id_barang_po', '=', 'tb_po_bahan.id')
                ->Join('tb_po', 'tb_po_bahan.id_po', '=', 'tb_po.id')
                ->where('tb_po.nomor_po', $nomor_po)
                ->select(
                    'tb_penerimaan.id',
                    'tb_po.nomor_po'
                )
                ->orderBy('tb_penerimaan.id', 'desc')
                ->count();

            $jumlah_pemesanan = DB::table('tb_po as po')
                ->join('tb_po_bahan as pb', 'po.id', '=', 'pb.id_po')
                ->select(
                    'po.nomor_po',
                    'pb.id',
                    'pb.jumlah_bahan'
                )
                ->where('po.nomor_po', $nomor_po)
                ->first();
            $jumlah_datang = TbPenerimaan::Join('tb_po_bahan', 'tb_penerimaan.id_barang_po', '=', 'tb_po_bahan.id')
                ->Join('tb_po', 'tb_po_bahan.id_po', '=', 'tb_po.id')
                ->where('tb_po.nomor_po', $nomor_po)
                ->select(
                    'tb_penerimaan.id',
                    'tb_penerimaan.jumlah_datang',
                    'tb_po.nomor_po',
                )
                ->orderBy('tb_penerimaan.id', 'desc')
                ->sum('tb_penerimaan.jumlah_datang');

                /*if($count_datang >= $count_pemesanan )
                {
                    TbPo::where('tb_po.nomor_po', $data_penerimaan->nomor_po)->update([
                        'status_po' => 'bayar'
                    ]);
                }*/

                if($jumlah_datang >= $jumlah_pemesanan->jumlah_bahan )
                {
                    TbPo::where('tb_po.nomor_po', $data_penerimaan->nomor_po)->update([
                        'status_po' => 'bayar'
                    ]);
                }

                TransaksiWadah::create([
                'id_penerimaan'             => $data_penerimaan->id,
                'qr_code_wadah'             => $request->qrcode_wadah,
                'jumlah_berat_sebelum'      => $request->jumlah_datang,
                'tanggal_dan_waktu_masuk'   => $request->created_at,
                'status'                    => 0,

            ]);

            return response()->json(['success' => true, 'message' => 'Data berhasil disimpan']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
        //return redirect()->route('master_wadah.index')->with('success', 'Wadah berhasil ditambahkan!');
    }

    public function getSingle($index)
    {
        
        $start = Carbon::yesterday()->setTime(16, 0, 0); // H-1 jam 16.00
        $end = Carbon::today()->setTime(16, 0, 0);       // H jam 16.00

        $data = DB::table('tb_po_bahan as po_bahan')
            ->join('tb_master_bahan as bahan', 'po_bahan.id_bahan', '=', 'bahan.id')
            ->join('tb_po as po', 'po_bahan.id_po', '=', 'po.id')
            ->join('tb_satuan as satuan', 'po_bahan.satuan', '=', 'satuan.id')
            ->leftJoin('tb_penerimaan as penerimaan', 'penerimaan.id_barang_po', '=', 'po_bahan.id')
            //->leftJoin('tb_box_bahan_baku as box', 'box.id_bahan', '=', 'bahan.id')
            /*->whereBetween('tanggal_kedatangan', [
                \Carbon\Carbon::yesterday()->format('Y-m-d') . ' 18:00:00',
                \Carbon\Carbon::today()->format('Y-m-d') . ' 18:00:00'
            ])*/
            ->where('po.status_po','acc')
            ->select(
                'po.nomor_po',
                'po.id as id_po',
                'bahan.bahan',
                'bahan.id as id_bahan',
                'po_bahan.jumlah_bahan',
                'po_bahan.jumlah_box',
                'po_bahan.keterangan',
                'po_bahan.tanggal_kedatangan',
                'satuan.satuan',
                'penerimaan.jumlah_datang',
                DB::raw('(po_bahan.jumlah_bahan - IFNULL(penerimaan.jumlah_datang, 0)) as kekurangan'),
              //  'box.isi_per_box'
            )
            ->offset($index)
            ->limit(1)
            ->first();



        if (!$data) {
            return response()->json(['message' => 'Data not found'], 404);
        }
        $totalJumlahBahan = DB::table('tb_po_bahan as po_bahan')
            ->leftJoin('tb_penerimaan as penerimaan', 'penerimaan.id_barang_po', '=', 'po_bahan.id')
            ->where('po_bahan.id_bahan', $data->id_bahan)
            ->where('po_bahan.id_po', $data->id_po)
            ->sum('po_bahan.jumlah_bahan');
        $data->jumlah_bahan = rtrim(rtrim(number_format($totalJumlahBahan, 3, ',', '.'), '0'), ',');
        $jumlah_datang = DB::table('tb_po_bahan as po_bahan')
            ->leftJoin('tb_penerimaan as penerimaan', 'penerimaan.id_barang_po', '=', 'po_bahan.id')
            ->where('po_bahan.id_bahan', $data->id_bahan)
            ->where('po_bahan.id_po', $data->id_po)
            ->sum('penerimaan.jumlah_datang');
        if($data->satuan == 'Kg' )
        {

            $data->jumlah_datang =  ($jumlah_datang / 1000) . '';
            $data->kekurangan =  $totalJumlahBahan - ($jumlah_datang / 1000);
        }else{

            $data->jumlah_datang =  ($jumlah_datang / 1) . '';
            $data->kekurangan =  $totalJumlahBahan - ($jumlah_datang / 1);
        }
        $data->kekurangan = rtrim(rtrim(number_format($data->kekurangan, 3, ',', '.'), '0'), ',');
        $data_detail = DB::table('tb_po_bahan as po_bahan')
            ->leftJoin('tb_penerimaan as penerimaan', 'penerimaan.id_barang_po', '=', 'po_bahan.id')
            ->select(
                'po_bahan.id',
                'po_bahan.id_bahan',
                'po_bahan.id_po',
                'po_bahan.jumlah_bahan as isi_per_box',
                'po_bahan.jumlah_box as jumlah_box',
                DB::raw('IFNULL(SUM(penerimaan.jumlah_datang),0) as jumlah_datang')
            )
            ->where('po_bahan.id_bahan', $data->id_bahan)
            ->where('po_bahan.id_po', $data->id_po)
            ->groupBy(
                'po_bahan.id',
                'po_bahan.id_bahan',
                'po_bahan.id_po',
                'po_bahan.jumlah_bahan',
                'po_bahan.jumlah_box'
            )
            ->get();
        $data->jumlah_box  = DB::table('tb_po_bahan as po_bahan')
            ->leftJoin('tb_penerimaan as penerimaan', 'penerimaan.id_barang_po', '=', 'po_bahan.id')
            ->where('po_bahan.id_bahan', $data->id_bahan)
            ->where('po_bahan.id_po', $data->id_po)
            ->count();
        $boxes = [];

        foreach ($data_detail as $row) {
            $isiPerBox     = $row->isi_per_box;
            $jumlahBox     = $row->jumlah_box;
            $jumlahDatang  = $row->jumlah_datang;

            for ($i = 0; $i < $jumlahBox; $i++) {
                $status = ($jumlahDatang >= $isiPerBox) ? 'sudah datang' : 'belum datang';
                $boxes[] = [
                    'jumlah_box' => 1,
                    'isi_per_box' => rtrim(rtrim(number_format($isiPerBox, 3, ',', '.'), '0'), ','),
                    'status' => $status
                ];
                $jumlahDatang -= $isiPerBox;
            }
        }

        $data->detail_box = $boxes;




        return response()->json($data);
    }

    public function v_data_masuk()
    {
        $header     = "Dashboard Bahan Masuk";
        if (request()->ajax()) {
            $data = DB::table('tb_penerimaan')
                ->join('tb_po_bahan', 'tb_penerimaan.id_barang_po', '=', 'tb_po_bahan.id')
                ->join('tb_po', 'tb_po_bahan.id_po', 'tb_po.id')
                ->join('tb_master_bahan', 'tb_po_bahan.id_bahan', '=', 'tb_master_bahan.id')
                ->whereIn('tb_po.status_po', ['acc','bayar']) // hanya ambil yang status_po = 'acc'
                ->select(
                    'tb_po.nomor_po',
                    'tb_penerimaan.*',
                    'tb_master_bahan.bahan',
                    'tb_penerimaan.jumlah_datang'
                )->get();


            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('bahan_dan_id', function ($row) {
                    //return $row->nomor_po.'|| ' .$row->bahan;
		     return $row->nomor_po.'|| ' .$row->bahan .'||'. $row->qr_code_wadah;
                })
                ->addColumn('action', function ($row) {
                    return '<button class="btn btn-danger btn-sm delete" data-id="' . $row->id . '">Delete</button>';
                })
                ->addColumn('waktu_datang', function ($row) {
                    return \Carbon\Carbon::parse($row->created_at)->translatedFormat('l, H:i, d F Y');
                })
                ->addColumn('jumlah_yang_datang', function ($row) {
                    return number_format($row->jumlah_datang, 0, ',', '.');
                })


                ->rawColumns(['action', 'jumlah_yang_datang','waktu_datang'])
                ->make(true);
        }
        return view('penerimaan/penerimaan_bahan.index_data_masuk', compact('header'));
        //return view('penerimaan/penerimaan_bahan.index', compact('header'));
    }

    public function destroy_bahan($id)
    {
        $data   = DB::table('tb_penerimaan')->where('id', $id)->first();
        $dapur  = DataDapur::first();
        PenerimaanDelete::create([
            'id_barang_po'   => $data->id_barang_po,
            'jumlah_datang'  => $data->jumlah_datang,
            'jumlah_berat'   => $data->jumlah_berat,
            'satuan_berat'   => $data->satuan_berat, // contoh: 1 = kg
            'status'         => 4, // reject
            'keterangan'     => 'Salah Input',
            'qr_code_wadah'  => $data->qr_code_wadah,
            'nama_penerima'  => $data->nama_penerima .'|'. $dapur->ahli_gizi,
        ]);

        $data_penerimaan = TbPenerimaan::leftJoin('tb_po_bahan', 'tb_penerimaan.id_barang_po', '=', 'tb_po_bahan.id')
            ->leftJoin('tb_po', 'tb_po_bahan.id_po', '=', 'tb_po.id')
            ->where('tb_penerimaan.id', $data->id)
            ->select(
                'tb_penerimaan.id',
                'tb_po.nomor_po'
            )
            ->orderBy('tb_penerimaan.id', 'desc')
            ->first();
        TbPenerimaan::where('id', $data_penerimaan->id)->update([
            'created_at'  => Carbon::now('Asia/Jakarta')
        ]);
        TbPo::where('tb_po.nomor_po', $data_penerimaan->nomor_po)->update([
            'status_po' => 'acc'
        ]);
        
        DB::table('tb_transaksi_wadah')->where('id_penerimaan',$data->id)->delete();
        
        DB::table('tb_penerimaan')->where('id', $id)->delete();
        return response()->json(['status' => 'deleted']);
    }

    public function dt_sudah_diterima()
    { 
        $table =  DB::table('tb_penerimaan')
            ->join('tb_po_bahan', 'tb_penerimaan.id_barang_po', '=', 'tb_po_bahan.id')
            ->join('tb_master_bahan', 'tb_po_bahan.id_bahan', '=', 'tb_master_bahan.id')
            ->leftJoin('warehouse_transaksi', 'tb_penerimaan.id', '=', 'warehouse_transaksi.id_penerimaan')
            ->whereNull('warehouse_transaksi.id_penerimaan')
            ->where('tb_penerimaan.status', '!=', 3)    
            ->select(
                'tb_penerimaan.id',
                'tb_penerimaan.qr_code_wadah',
                'tb_master_bahan.bahan',
                'tb_penerimaan.created_at',
                'tb_penerimaan.jumlah_datang',
                'tb_penerimaan.satuan_berat',
                'tb_penerimaan.keterangan'
            )
            ->orderBy('tb_penerimaan.created_at', 'desc')
            ->get();
        //return $table;
        return DataTables::of($table)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                return '<button class="btn btn-danger btn-sm delete" data-id="' . $row->id . '">Delete</button>';
            })
            ->addColumn('jumlah_satuan', function ($row) {
                $satuan = TbSatuan::find($row->satuan_berat);
                if ($satuan) {
                    return number_format($row->jumlah_datang, 0, '.', ',') . ' ' . $satuan->satuan;
                } else {
                    return '-';
                }
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function dt_gudang()
    { 
        $table =  DB::table('warehouse_transaksi')
            ->whereNull('warehouse_transaksi.tanggal_keluar')
            ->select(
                'kode_wadah',
                'nama_barang',
                'tanggal_masuk',
                'tanggal_akan_keluar',
                'jumlah',
                'id_satuan'
            )
            ->orderBy('warehouse_transaksi.created_at', 'desc')
            ->get();
        //return $table;
        return DataTables::of($table)
            ->addIndexColumn()
            ->addColumn('jumlah_satuan', function ($row) {
                $satuan = TbSatuan::find($row->id_satuan);
                if($satuan)
                {
                    return number_format($row->jumlah,0,'.',',') .' '.$satuan->satuan;
                }else
                {
                    return '-';
                }
            })

            ->rawColumns(['jumlah_satuan'])


            ->make(true);
    }
}
