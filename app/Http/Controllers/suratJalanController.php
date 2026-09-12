<?php

namespace App\Http\Controllers;

use PDF;
use DataTables;
use Svg\Tag\Rect;
use App\Models\Menu;
use App\Models\User;
use App\Models\DataDapur;
use App\Models\suratJalan;
use Illuminate\Http\Request;
use App\Models\suratJalanItem;
use Illuminate\Support\Carbon;
use App\Models\rincian_sekolah;
use App\Models\KasKecilTransaksi;
use App\Models\tbOmprengTransaksi;
use Illuminate\Support\Facades\DB;
use League\CommonMark\Reference\Reference;

class suratJalanController extends Controller
{
    //
    public function v_suratJalan()
    {
        $header = "Surat Jalan";
        return view('office.suratJalan.suratJalan', compact('header'));
        //return view('suratJalan.v_suratJalan', compact('header'));
    }

    public function v_formSuratJalan(Request $request)
    {
        $header = "Form Surat Jalan";

        if(isset($request->id)){
            $surat_jalan = suratJalan::find($request->id);
            $menu = Menu::find($surat_jalan->id_menu_harian);
            return view('office/suratJalan.formSuratJalan', compact('header', 'surat_jalan', 'menu'));
        }
        //$referensi = "DLV".date('His').date('dmy').substr(microtime(FALSE), 2, 3);
        //$menu = Menu::where('tanggal_kirim', date('Y-m-d'))->first();
        $referensi = "DLV" . Carbon::now('Asia/Jakarta')->format('His')
            . Carbon::now('Asia/Jakarta')->format('dmy')
            . substr(microtime(false), 2, 3);

        $menu = Menu::where('tanggal_kirim', Carbon::now('Asia/Jakarta')->toDateString())->first();   
        return view('office/suratJalan.formSuratJalan', compact('header', 'referensi', 'menu'));
    }

    public function v_formSuratJalanB(Request $request)
    {
        $header = "Form Surat Jalan";

        if (isset($request->id)) {
            $surat_jalan = suratJalan::find($request->id);
            $menu = Menu::find($surat_jalan->id_menu_harian);
            return view('office/suratJalan.formSuratJalan', compact('header', 'surat_jalan', 'menu'));
        }
        //$referensi = "DLV".date('His').date('dmy').substr(microtime(FALSE), 2, 3);
        //$menu = Menu::where('tanggal_kirim', date('Y-m-d'))->first();
        $referensi = "DLV" . Carbon::now('Asia/Jakarta')->format('His')
            . Carbon::now('Asia/Jakarta')->format('dmy')
            . substr(microtime(false), 2, 3);

        $menu = Menu::where('tanggal_kirim', Carbon::now('Asia/Jakarta')->toDateString())
            ->orderByDesc('id') // urutkan dari yang terbaru
            ->first();
        return view('office/suratJalan.formSuratJalan', compact('header', 'referensi', 'menu'));
    }


    private function hitungOmprengSiap($referensi, $menu_id)
    {
        $packing_ompreng_a = tbOmprengTransaksi::where('tb_menu_id', $menu_id)
        ->where('porsi', 'a')
        ->count();
        $packing_ompreng_b = tbOmprengTransaksi::where('tb_menu_id', $menu_id)
        ->where('porsi', 'b')
        ->count();
        $packing_ompreng = $total_ompreng_a + $total_ompreng_b;

        $siap_ompreng = suratJalanItem::where('surat_jalan_referensi', $referensi)
            ->where('status', 0)
            ->get();
        
        return [
            'jumlah_ompreng' => $jumlah_ompreng,
            'total_ompreng' => $total_ompreng
        ];
    }

    public function dt_dataRincianSekolah(Request $request)
    {
        try {
            //return $request;
            $referensi = $request->referensi;
            $menu_harian = $request->menu_id;
            
            // Get all menus for today dynamically
            $today = Carbon::today()->toDateString();
            $menus_today = Menu::whereDate('tanggal_kirim', $today)
                ->orderBy('id')
                ->pluck('id')
                ->toArray();
            
            if (empty($menus_today)) {
                return DataTables::of(collect([]))
                ->addIndexColumn()
                ->addColumn('nama_sekolah', function () {
                    return 'Tidak ada menu hari ini';
                })
                ->addColumn('alamat_sekolah', function () {
                    return '';
                })
                ->addColumn('sisa_jumlah_penerima_a', function () {
                    return '';
                })
                ->addColumn('sisa_jumlah_penerima_b', function () {
                    return '';
                })
                ->addColumn('action', function () {
                    return '';
                })
                ->make(true);
            }

            $table = DB::table('rincian_sekolah')
                ->leftJoin('tb_data_sekolah', 'rincian_sekolah.id_sekolah', '=', 'tb_data_sekolah.id')
                ->join('tb_menu', 'rincian_sekolah.id_menu_harian', '=', 'tb_menu.id')
                ->leftJoin('surat_jalan_item', 'rincian_sekolah.id', '=', 'surat_jalan_item.rincian_sekolah_id')
                ->select(
                    DB::raw('MIN(rincian_sekolah.id) as id'), // ambil id pertama (terkecil)
                    'tb_data_sekolah.nama_sekolah',
                    'tb_data_sekolah.alamat_sekolah',
                    'tb_data_sekolah.jumlah_a',
                    'tb_data_sekolah.jumlah_b',
                    
                    DB::raw('SUM(rincian_sekolah.jumlah_penerima_a - COALESCE(
                CASE 
                    WHEN surat_jalan_item.surat_jalan_referensi = "'.$referensi.'" OR surat_jalan_item.status = 1 
                    THEN surat_jalan_item.jumlah_a ELSE 0 END, 0)) AS sisa_jumlah_penerima_a'),
                    DB::raw('SUM(rincian_sekolah.jumlah_penerima_b - COALESCE(
                CASE 
                    WHEN surat_jalan_item.surat_jalan_referensi = "'.$referensi.'" OR surat_jalan_item.status = 1 
                    THEN surat_jalan_item.jumlah_b ELSE 0 END, 0)) AS sisa_jumlah_penerima_b')
                )
                ->whereIn('rincian_sekolah.id_menu_harian', $menus_today)
                ->groupBy(
                    'tb_data_sekolah.nama_sekolah',
                    'tb_data_sekolah.alamat_sekolah',
                'tb_data_sekolah.jumlah_a',
                'tb_data_sekolah.jumlah_b',
                )
                ->havingRaw('sisa_jumlah_penerima_a > 0 OR sisa_jumlah_penerima_b > 0')
                ->get();

            //return $table;
            return DataTables::of($table)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                $btn = '<button onclick="formTransaksiBarangMasuk(' . $row->id . ')" class="btn btn-info btn-sm " id="btn-edit-post" >Kirim</button>';
                return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        } catch (\Exception $e) {
            \Log::error('dt_dataRincianSekolah error: ' . $e->getMessage());
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    
    }

    public function dt_suratJalanItem(Request $request){
        $table = suratJalanItem::with('rincianSekolah.data_sekolah')
            ->where('surat_jalan_referensi', $request->referensi)
            ->get();
        //return $table;
            return DataTables::of($table)
            ->addIndexColumn()
            ->addColumn('nama_sekolah', function($row){
                return $row->rincianSekolah->data_sekolah->nama_sekolah;
            })
            ->addColumn('alamat_sekolah', function($row){
                return $row->rincianSekolah->data_sekolah->alamat_sekolah;
            })
            
            ->addColumn('action', function ($row) {
                
                $btn = '<button onClick="deleteSuratJalanItem(' . $row['id'] . ')" class="btn btn-danger btn-sm " id="btn-edit-post" data-id="' . $row['id'] . '">Hapus</button>';

                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function form_suratJalanItem(Request $request)
    {
        try {
            $id_rincian_sekolah = explode(',', $request->query('id_rincian_sekolah')); // Pisahkan ID dengan koma
            $referensi = $request->referensi;

            // Ambil data barang berdasarkan ID yang dipilih
            $sekolah_id = rincian_sekolah::whereIn('id', $id_rincian_sekolah)->first();
            
            if (!$sekolah_id) {
                return redirect()->back()->with('error', 'Data sekolah tidak ditemukan');
            }

            $menu = Menu::where('id', $sekolah_id->id_menu_harian)->first();
            $menu2 = Menu::whereDate('tanggal_kirim', now())
                ->orderBy('id', 'DESC')
                ->first();
            
            // Hanya tambahkan sekolah2 jika menu2 berbeda dan ada data
            if ($menu2 && $sekolah_id->id_menu_harian != $menu2->id) {
                $sekolah2 = rincian_sekolah::where('id_menu_harian', $menu2->id)
                    ->where('id_sekolah', $sekolah_id->id_sekolah)
                    ->first();
                    
                if ($sekolah2) {
                    array_push($id_rincian_sekolah, $sekolah2->id);
                }
            }

            // Query tanpa HAVING clause yang ketat, biarkan frontend handle filtering
            $sekolah = DB::table('rincian_sekolah')
                ->leftJoin('tb_data_sekolah', 'rincian_sekolah.id_sekolah', '=', 'tb_data_sekolah.id')
                ->leftJoin('surat_jalan_item', 'rincian_sekolah.id', '=', 'surat_jalan_item.rincian_sekolah_id')
                ->select(
                    DB::raw('MIN(rincian_sekolah.id) as id'),
                    'tb_data_sekolah.nama_sekolah',
                    'tb_data_sekolah.alamat_sekolah',
                    'tb_data_sekolah.jumlah_a',
                    'tb_data_sekolah.jumlah_b',
                    DB::raw("SUM(rincian_sekolah.jumlah_penerima_a - COALESCE(
                CASE WHEN surat_jalan_item.surat_jalan_referensi = '".$referensi."' OR surat_jalan_item.status = 1 
                     THEN surat_jalan_item.jumlah_a ELSE 0 END, 0)) AS sisa_jumlah_penerima_a"),
                    DB::raw("SUM(rincian_sekolah.jumlah_penerima_b - COALESCE(
                CASE WHEN surat_jalan_item.surat_jalan_referensi = '".$referensi."' OR surat_jalan_item.status = 1 
                     THEN surat_jalan_item.jumlah_b ELSE 0 END, 0)) AS sisa_jumlah_penerima_b")
                )
                ->whereIn('rincian_sekolah.id', $id_rincian_sekolah)
                ->groupBy(
                    'tb_data_sekolah.id',
                    'tb_data_sekolah.nama_sekolah',
                    'tb_data_sekolah.alamat_sekolah',
                    'tb_data_sekolah.jumlah_a',
                    'tb_data_sekolah.jumlah_b'
                )
                ->get();

            return view('office.suratJalan.form.fromSuratJalanItem', compact('sekolah'));
        } catch (\Exception $e) {
            \Log::error('form_suratJalanItem error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function ajax_simpanSuratJalanItem(Request $request)
    {
        //return $request;
        
        foreach ($request->items as $itemData) {
            $surat_jalan_item = suratJalanItem::where('surat_jalan_referensi', $request->referensi)
                ->where('rincian_sekolah_id', $itemData['rincian_sekolah_id'])
                ->first();
                if($surat_jalan_item){
                    suratJalanItem::updateOrCreate(
                        [
                            'rincian_sekolah_id' => $itemData['rincian_sekolah_id'],

                            'surat_jalan_referensi' => $request->referensi,
                        ],
                        [
                            'jumlah_a' =>$itemData['jumlah_penerima_a']+$surat_jalan_item->jumlah_a,
                            'jumlah_b' =>$itemData['jumlah_penerima_b']+$surat_jalan_item->jumlah_b,
                            'jumlah' => $itemData['jumlah_penerima_a']+ $surat_jalan_item->jumlah_a + $itemData['jumlah_penerima_b']+ $surat_jalan_item->jumlah_b
                        ]
                    );
                    return response()->json([
                        'status' => 'success',
                        'message' => 'data berhasil disimpan'
                    ]);
                }
                suratJalanItem::updateOrCreate(
                    [
                        'rincian_sekolah_id' => $itemData['rincian_sekolah_id'],

                        'surat_jalan_referensi' => $request->referensi,
                    ],
                    [
                        'jumlah_a' =>$itemData['jumlah_penerima_a'],
                        'jumlah_b' =>$itemData['jumlah_penerima_b'],
                        'jumlah' => $itemData['jumlah_penerima_a'] + $itemData['jumlah_penerima_b'],
                    ]
                );
            
            
        }

        

        

    }

    public function ajax_simpanSuratJalan(Request $request)
    {
        //return $request;
        $surat_jalan = suratJalan::where('referensi', $request->referensi)->first();
        if(isset($surat_jalan)){
            if($surat_jalan->status == 0){
                suratJalan::updateOrCreate(
                    [
                        'referensi' => $request->referensi,
                    ],
                    [
                        
                        'user_id' => auth()->user()->id,
                        'id_menu_harian' => $request->menu_id,
                        'driver' => $request->driver,
                        'driver_assistant' => $request->driver_assistant,
                        'plat_nomor' => $request->plat_nomor
                    ],
                );
                return response()->json([
                    'status' => 'success',
                    'message' => 'data berhasil disimpan'
                ]);
            }
            return response()->json([
                'status' => 'error',
                'message' => 'data tidak bisa disimpan karena sudah dipublikasi'
            ]);
            
        }
        suratJalan::create(
            [
                'referensi' => $request->referensi,
                'user_id' => auth()->user()->id,
                'id_menu_harian' => $request->menu_id,
                'driver' => $request->driver,
                'driver_assistant' => $request->driver_assistant,
                'plat_nomor' => $request->plat_nomor
            ],
        );
        return response()->json([
            'status' => 'success',
            'message' => 'data berhasil disimpan'
        ]);
        
    }

    public function ajax_pubSuratJalan(Request $request)
    {
        $surat_jalan = $this->ajax_simpanSuratJalan($request);
        if($surat_jalan){
            $rule = [
                'driver' => 'required',
                'plat_nomor' => 'required',
                
            ];
            $message = [
                'plat_nomor.required' => 'plat nomor kendaraan pengantar tidak boleh kosong',
                'driver.required' => 'nama petugas pengirim tidak boleh kosong',
            ];
            $this->validate($request, $rule, $message);
            //get kode surat
            //$pub_surat_jalan = suratJalan::where('referensi', $request->referensi)->first();
            //return $sj_warehouse->transaksi_warehouse_id;
            //no surat
            $no = suratJalan::whereNotNull('no')
                ->whereMonth('published_at', Carbon::now()->month)
                ->whereYear('published_at', Carbon::now()->year)
                ->max('no') + 1;
            $no_sj = $no.'/SJ/'.date('m').'/'.date('Y');
            $hasil = suratJalan::where('referensi', $request->referensi)
                ->update([
                    'status' => 1,
                    'no_surat_jalan' => $no_sj,
                    'no' => $no,
                    'published_at' => now()->setTimezone('Asia/Jakarta'),


                ]);
            $total_siswa_yang_dikirim = DB::table('rincian_sekolah as r')
                ->join('tb_menu as m', 'r.id_menu_harian', '=', 'm.id')
                ->whereDate('m.tanggal_kirim', now()->toDateString())
                ->sum('r.jumlah_penerima_total');
            $tanggal = Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY');
            $dapur = DataDapur::first();
            
            $total_biaya = $total_siswa_yang_dikirim * 2000;
            // Generate nomor_po dulu
            $nomor_po = 'b.infra/' . $dapur->nomor_dapur . '/' . now()->format('Ymd');

            // Cek apakah sudah ada data dengan nomor_po ini
            $cek_data = KasKecilTransaksi::where('nomor_po', $nomor_po)->where('status',1)->exists();

            if ($cek_data) {
                // Sudah ada data dengan nomor_po ini
            } else {
                // Belum ada, boleh create baru
                KasKecilTransaksi::create([
                    'tanggal'         => now()->toDateTimeString(),
                    'master_bahan_id' => 171,
                    'jenis_transaksi' => 'keluar',
                    'deskripsi'       => 'Biaya Infrastruktur dan Peralatan pada ' . $tanggal . " dengan total penerima " . $total_siswa_yang_dikirim,
                    'jumlah'          => $total_biaya,
                    'nama_karyawan'   => $dapur->ahli_akuntan,
                    'nomor_transaksi' => '-',
                    'status'          => 1,
                    'id_parent'       => 0,
                    'nomor_po'        => $nomor_po,
                ]);
            }




            $hasil_item = suratJalanItem::where('surat_jalan_referensi', $request->referensi)
                    ->update([
                        'status' => 1,

                    ]);
            //mendapatkan id untuk redirect
            $hasil = suratJalan::where('referensi', $request->referensi)->first();
            if($hasil && $hasil_item){
                return response()->json([
                    'status' => 'success',
                    'message' => 'data berhasil disimpan',
                    'id' => $hasil->id,
                ]);
            }
            return response()->json([
                'status' => 'error',
                'message' => 'terjadi kesalahan saat publikasi surat jalan',
            ]);    
            
        }
        
    }

    public function dt_suratJalan()
    {
        $table = suratJalan::with('menu')->orderBy('created_at', 'desc')->get();
        //return $table;
        return DataTables::of($table)
            ->addIndexColumn()
            ->addColumn('menu', function($row){
                if(isset($row->menu)){
                    return $row->menu->menu;
                }                                   
            })
            ->addColumn('rencana_kirim', function($row){
                if(isset($row->menu)){
                    return date('d-m-Y', strtotime($row->menu->tanggal_kirim));
                }
            })
            ->editColumn('published_at', function($row){
                return date('d-m-Y', strtotime($row->published_at));
            })
            ->editColumn('status', function($row){
                if($row->status == 0){
                    return 'draft';
                }
                else if($row->status == 1){
                    return 'published';
                }
            })
            
            ->addColumn('action', function ($row) {
                $printCenterUrl = route('mastermenu.print-center', [
                    'id_surat_jalan' => $row['id'],
                    'id_menu' => optional($row->menu)->id,
                    'tanggal' => optional($row->menu)->tanggal_kirim,
                    'tanggal_awal' => optional($row->menu)->tanggal_kirim,
                    'tanggal_akhir' => optional($row->menu)->tanggal_kirim,
                ]);

                $btn = '<a href="suratJalan/detailSuratJalan-' . $row['id'] . '" class="btn btn-primary btn-sm " >Detail</a>
                    <a href="' . $printCenterUrl . '" class="btn btn-dark btn-sm">Pusat Cetak</a>';

                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function v_detailSuratJalan(Request $request)
    {
        $header = "Detail Surat Jalan";
        if(isset($request->id)){
            $surat_jalan = suratJalan::find($request->id);
            return view('office.suratJalan.detailSuratJalan', compact('header', 'surat_jalan'));
        }
    }

    public function pdf_suratJalan(Request $request)
    {
        $dapur = DataDapur::find(1);
        $surat_jalan = suratJalan::find($request->id);
        //return $surat_jalan;
        $surat_jalan_item = suratJalanItem::with('rincianSekolah.data_sekolah')
            ->where('surat_jalan_referensi', $surat_jalan->referensi)
            ->get();
            //return $surat_jalan_item;
        $pdf = PDF::loadview('office.suratJalan.pdf.pdfSuratJalanA5', compact('surat_jalan', 'surat_jalan_item', 'dapur'))->setPaper('A5','landscape');
        return $pdf->stream('PO '.'.pdf');
        
    }

    public function ajax_deleteSuratJalanItem(Request $request)
    {
        $surat_jalan_item = suratJalanItem::find($request->id);
        $surat_jalan_item->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'data berhasil dihapus'
        ]);
    }

    public function pdf_suratJalanA4(Request $request)
    {
        $surat_jalan = suratJalan::find($request->id);
        //return $surat_jalan;
        $dapur = DataDapur::find(1);
        $surat_jalan_item = suratJalanItem::with('rincianSekolah.data_sekolah')
            ->where('surat_jalan_referensi', $surat_jalan->referensi)
            ->get();
            //return $surat_jalan_item;
        $pdf = PDF::loadview('office.suratJalan.pdf.pdfSuratJalanA4', compact('surat_jalan', 'surat_jalan_item', 'dapur'))->setPaper('A4','potrait');
        return $pdf->stream('PO '.'.pdf');
    }
}




   
