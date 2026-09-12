<?php

namespace App\Http\Controllers\office;

use App\Http\Controllers\Controller;
use App\Models\DataDapur;
use Illuminate\Http\Request;
use App\Models\TbMasterBahan;
use App\Models\KasKecilTransaksi;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf;


class KasKecilTransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $header     = "Data Pengeluaran Selain PO";
        $bahan      = TbMasterBahan::where('jenis',7)->get();
        if (request()->ajax()) {
            //$users = User::query();

            $kas = DB::table('tb_kas_kecil_transaksi as tkk')
                ->leftJoin('tb_master_bahan as mb', 'tkk.master_bahan_id', '=', 'mb.id')
                ->leftJoin('tb_satuan as ts', 'mb.satuan_bahan', '=', 'ts.id')
                ->where('tkk.status',1)
                ->select(
                    'tkk.*',
                    'mb.bahan',
                    'ts.satuan'
                )
                
                ->orderBy('tkk.tanggal', 'desc')
                ->get();


            return DataTables::of($kas)
                ->addIndexColumn() // Menambah index
                ->addColumn('action', function ($row) {
                    $action = '<button class="btn btn-sm btn-warning btn-edit-box" 
                        data-id="' . $row->id . '" 
                        data-idbahan="' . $row->master_bahan_id . '"
                        data-tanggal="' . $row->tanggal . '"
                        data-jenis="' . $row->jenis_transaksi . '"
                        data-deskripsi="' . $row->deskripsi . '"
                        data-jumlah="' . $row->jumlah . '"
                        data-nama="' . $row->nama_karyawan . '"
                        data-nomor="' . $row->nomor_transaksi . '"  
                        
                        >
                        Edit
                    </button>
                     <button class="btn btn-sm btn-danger btn-delete-box" data-id="' . $row->id . '">
                        Hapus
                    </button>';
                    return $action;
                })
                ->addColumn('jumlah_pembayaran', function ($row) {
                   
                    return number_format($row->jumlah,0,0);
                })
                ->addColumn('tanggal_transaksi', function ($row) {

                    return Carbon::parse($row->tanggal)->translatedFormat('l, j F Y');
                })
                
                ->rawColumns(['action', 'jumlah_pembayaran', 'tanggal_transaksi'])
                ->make(true);
        }
        return view('office/kas_kecil.index', compact('header', 'bahan'));
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
        $request->validate([
            'master_bahan_id' => 'required|exists:tb_master_bahan,id',
            'tanggal' => 'required',
            //'nomor_transaksi' => 'required',
            'jumlah' => 'required',
            'deskripsi' => 'required',

        ]);
        $nomor_transaksi = $request->nomor_transaksi ? $request->nomor_transaksi : '-';

        $dapur = DataDapur::first();
        KasKecilTransaksi::create([
            'tanggal' => $request->tanggal,
            'jenis_transaksi' => 'keluar',
            'master_bahan_id' => $request->master_bahan_id,
            'deskripsi' => $request->deskripsi,
            'jumlah' => $request->jumlah,
            'nama_karyawan' => $dapur->ahli_akuntan,
            'nomor_transaksi' => $request->nomor_transaksi,
            'tanggal' => $request->tanggal,
            'status' => 1,
            'id_parent' => 0
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data Pengeluaran berhasil ditambahkan.'
        ]);
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
        $request->validate([
           // 'master_bahan_id' => 'required|exists:tb_master_bahan,id',
            'tanggal' => 'required',
            'nomor_transaksi' => 'required',
            'jumlah' => 'required',
            'deskripsi' => 'required',
            //'nama_karyawan' => 'required',

        ]);

        $kas = KasKecilTransaksi::findOrFail($id);
        
        
        $dapur = DataDapur::first();
        KasKecilTransaksi::create([
            'tanggal' => $request->tanggal,
            'jenis_transaksi' => 'keluar',
            'master_bahan_id' => $kas->master_bahan_id,
            'deskripsi' => $request->deskripsi,
            'jumlah' => $request->jumlah,
            'nama_karyawan' => $dapur->ahli_akuntan,
            'nomor_transaksi' => $kas->nomor_transaksi,
            'tanggal' => $request->tanggal,
            'status' => 1,
            'nomor_po'=> $kas->nomor_po,
            'id_parent' => $id
        ]);
        $kas->status = 0;
        $kas->save();
        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diperbarui.'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        $kas = KasKecilTransaksi::findOrFail($id);
        $kas->status = 0;
        $kas->deskripsi = $request->deskripsi;

        $kas->save();

        if (!$kas) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan.'
            ], 404);
        }

       

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil dihapus.'
        ]);
    }

    public function pdf_laporan($bulan, $tahun)
    {
        $dapur = DataDapur::first();
        //$pdf = Pdf::loadView('office/mastermenu.template_pengajuan_menu');
        $transaksi = DB::table('tb_kas_kecil_transaksi')
            ->join('tb_master_bahan', 'tb_kas_kecil_transaksi.master_bahan_id', '=', 'tb_master_bahan.id')
            ->select(
                'tb_kas_kecil_transaksi.id',
                'tb_kas_kecil_transaksi.tanggal',
                'tb_kas_kecil_transaksi.jumlah',
                'tb_master_bahan.bahan',
                'tb_kas_kecil_transaksi.status'
            )
            ->whereYear('tb_kas_kecil_transaksi.tanggal', $tahun)
            ->whereMonth('tb_kas_kecil_transaksi.tanggal', $bulan)

            ->where('tb_kas_kecil_transaksi.status',1)
            ->orderBy('tb_kas_kecil_transaksi.tanggal', 'asc')
            ->get();
        // Kirim data ke view PDF
        $pdf = Pdf::loadView('office/kas_kecil.template_laporan', compact(
            'dapur',
            'transaksi'
            
        ));
        return $pdf->download('Formulir_Pengajuan_Menu_Harian .pdf');
    }
}
