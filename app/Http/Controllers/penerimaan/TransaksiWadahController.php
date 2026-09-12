<?php


namespace App\Http\Controllers\penerimaan;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;


use App\Models\TransaksiWadah;
use App\Models\TbPenerimaan;
use App\Models\TbPoBahan;
use App\Models\TbBantuBahanPo;
use App\Models\TbSatuan;
use App\Models\TbWadah;
use App\Models\TbMasterBahan;
use App\Models\Menu;
use App\Models\rincian_sekolah;

class TransaksiWadahController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    public function __invoke()
    {
        $header = "Transaksi Kontainer";



        // Jika sudah lewat jam 13:00, gunakan tanggal besok
        //$tanggalKirim = $now->hour >= 19 ? $now->copy()->addDay()->toDateString() : $now->toDateString();
       

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
        if ($tanggalKirim->isSaturday() || $tanggalKirim->isSunday()) {
            // Ubah ke hari Senin
            $tanggalKirim->next(Carbon::MONDAY);
            $keterangan_menu = 'hari Senin';
        }

        $tanggalKirimFormatted = $tanggalKirim->translatedFormat('l, j F Y');
        $menuHarian = DB::table('rincian_menu_harian as r')
            ->join('tb_menu as m', 'r.id_menu_harian', '=', 'm.id')
            ->join('tb_resep as resep', 'r.id_resep', '=', 'resep.id')
            ->join('tb_master_bahan as bahan', 'r.id_bahan', '=', 'bahan.id')
            ->join('tb_satuan as satuan', 'bahan.satuan_bahan', '=', 'satuan.id')
            ->select(
                'r.id as id_rincian',
                'resep.nama_resep as resep',
                'bahan.bahan as bahan',
                'satuan.satuan as satuan',
                'r.jumlah'
            )
            ->whereDate('m.tanggal_kirim', $tanggalKirim)
            ->where('m.status_pengajuan', '!=', 'rejected') // Hindari data yang reject
            ->get(); // tampilkan 10 per halaman
        $menus = Menu::join('tb_resep as karbohidrat_bahan', 'tb_menu.karbohidrat', '=', 'karbohidrat_bahan.id')
            ->join('tb_resep as protein_bahan', 'tb_menu.protein', '=', 'protein_bahan.id')
            ->join('tb_resep as sayur_bahan', 'tb_menu.sayur', '=', 'sayur_bahan.id')
            ->join('tb_resep as buah_bahan', 'tb_menu.buah', '=', 'buah_bahan.id')
            ->join('tb_resep as susu_bahan', 'tb_menu.susu', '=', 'susu_bahan.id')
            ->whereDate('tb_menu.tanggal_kirim', $tanggalKirim)
            ->where('tb_menu.status_pengajuan', '!=', 'rejected') // Hindari data yang reject
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
        //$total = rincian_sekolah::where('id_menu_harian',$menus->id_menu)->sum('jumlah_penerima_total')??0;
	$total = 0;
	if ($menus) {
    		$total = rincian_sekolah::where('id_menu_harian', $menus->id_menu)->sum('jumlah_penerima_total') ?? 0;
	}
        if (request()->ajax()) {
            // Mengambil data wadah yang dipakai
            $wadah = TbWadah::where('status', 1)->get();

            return DataTables::of($wadah)
                ->addIndexColumn()
                ->addColumn('posisi_wadah', function ($row) {
                    $data_wadah = TransaksiWadah::where('qr_code_wadah', $row->qr_code)->first();

                    if (!$data_wadah) {
                        return 'Data tidak ditemukan';
                    }

                    if ($data_wadah->status == 0) {
                        return 'Diluar Gudang';
                    }

                    return match ($data_wadah->lokasi) {
                        0 => 'Gudang Kering',
                        1 => 'Gudang Chiller',
                        default => 'Gudang Freezer',
                    };
                })
                ->addColumn('isi_wadah', function ($row) {
                    // Ambil semua transaksi wadah yang sesuai dengan QR Code
                    $transaksi_wadah = TransaksiWadah::where('qr_code_wadah', $row->qr_code)->get();
                    $bahan_list = [];

                    foreach ($transaksi_wadah as $transaksi) {
                        $penerimaan = TbPenerimaan::find($transaksi->id_penerimaan);

                        if ($penerimaan) {
                            $po_bahan = TbPoBahan::find($penerimaan->id_barang_po);

                            if ($po_bahan) {
                                $bahan = TbMasterBahan::find($po_bahan->id_bahan);

                                if ($bahan) {
                                    $bahan_list[] = $bahan->bahan;
                                }
                            }
                        }
                    }

                    return implode(', ', $bahan_list);
                })
                ->addColumn('action', function ($row) {
                    return '<a href="' . route('detail_wadah', $row->qr_code) . '" class="edit btn btn-primary btn-sm">Detail Isi</a>';
                })
                ->rawColumns(['action', 'posisi_wadah'])
                ->make(true);
        }
        
        return view('penerimaan/transaksi-wadah.index_rev', compact('header', 'menuHarian','total','menus', 'keterangan_menu', 'tanggalKirimFormatted'));
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

    public function detail_wadah($id)
    {
        // Mengecek apakah ada data di tabel TbWadah dengan qr_code yang diberikan
        $cek_data = TbWadah::where('qr_code', $id)->count();

        if ($cek_data == 0) { // Jika tidak ditemukan
            return redirect()->route('transaksi_wadah.index') // Arahkan kembali ke halaman transaksi wadah
                ->with('error', 'Data Tidak Ditemukan'); // Kirim pesan error ke session
        } else {
            // Mengecek apakah status wadah masih belum digunakan (status = 0)
            $cek_data_wadah_dipakai = TbWadah::where('qr_code', $id)->where('status', 0)->count();

            if ($cek_data_wadah_dipakai == 1) { // Jika wadah masih belum digunakan
                return redirect()->route('transaksi_wadah.index') // Arahkan kembali ke halaman transaksi wadah
                    ->with('error', 'Status wadah masih belum digunakan'); // Kirim pesan error ke session
            }
        }

        // Jika ditemukan
        $wadah          = TbWadah::where('qr_code', $id)->first();
        $data_wadah     = TransaksiWadah::join('tb_wadah','tb_transaksi_wadah.qr_code_wadah','tb_wadah.qr_code')
                            ->whereIn('tb_transaksi_wadah.status', [0,1])
                            ->where('qr_code', $id)
                            ->where('tb_wadah.status',1)
                            ->first();
        $tanggal_digunakan = TransaksiWadah::join('tb_wadah','tb_transaksi_wadah.qr_code_wadah','tb_wadah.qr_code')
                            ->join('tb_penerimaan', 'tb_transaksi_wadah.id_penerimaan','tb_penerimaan.id')
                            ->join('tb_po_bahan', 'tb_penerimaan.id_barang_po','tb_po_bahan.id')
                            
                            ->whereIn('tb_transaksi_wadah.status', [0,1])
                            ->where('qr_code', $id)
                            ->where('tb_wadah.status',1)
                            ->select('tb_po_bahan.tanggal_digunakan')
                            ->first();
        $count_null_berat = DB::table('tb_transaksi_wadah')
            ->join('tb_wadah', 'tb_transaksi_wadah.qr_code_wadah', '=', 'tb_wadah.qr_code')
            ->join('tb_penerimaan', 'tb_transaksi_wadah.id_penerimaan', '=', 'tb_penerimaan.id')
            ->join('tb_po_bahan', 'tb_penerimaan.id_barang_po', '=', 'tb_po_bahan.id')
            ->join('tb_master_bahan', 'tb_po_bahan.id_bahan', '=', 'tb_master_bahan.id')
            ->whereIn('tb_transaksi_wadah.status', [0, 1]) // Hanya mengambil transaksi dengan status 0 atau 1
            ->where('tb_wadah.qr_code', $id) // Sesuai dengan QR Code wadah
            ->whereNull('tb_transaksi_wadah.jumlah_berat_sesudah') // Filter data yang belum memiliki berat sesudah
            ->count();

        $header = "transaksi wadah";
        if (request()->ajax()) {
            $transaksi_wadah = DB::table('tb_transaksi_wadah')
                ->join('tb_wadah', 'tb_transaksi_wadah.qr_code_wadah', 'tb_wadah.qr_code')
                ->join('tb_penerimaan', 'tb_transaksi_wadah.id_penerimaan', '=', 'tb_penerimaan.id')
                ->join('tb_po_bahan', 'tb_penerimaan.id_barang_po', '=', 'tb_po_bahan.id')
                ->join('tb_master_bahan', 'tb_po_bahan.id_bahan', '=', 'tb_master_bahan.id')
                ->whereIn('tb_transaksi_wadah.status', [0, 1]) // mengambil data wadah yang dipakai
                ->where('tb_wadah.qr_code', $id)
                ->select(
                    'tb_transaksi_wadah.id',
                    'tb_master_bahan.bahan',
                    'tb_transaksi_wadah.jumlah_berat_sebelum',
                    'tb_transaksi_wadah.jumlah_berat_sesudah',
                    DB::raw('(tb_transaksi_wadah.jumlah_berat_sebelum - tb_transaksi_wadah.jumlah_berat_sesudah) AS selisih_berat'),
                    'tb_transaksi_wadah.tanggal_dan_waktu_masuk',
                    'tb_transaksi_wadah.tanggal_dan_waktu_sesudah',
                    DB::raw('TIMEDIFF(tb_transaksi_wadah.tanggal_dan_waktu_sesudah, tb_transaksi_wadah.tanggal_dan_waktu_masuk) AS selisih_waktu')
                );
            

            return DataTables::of($transaksi_wadah)
                ->addIndexColumn()

                ->addColumn('action', function ($row) {
                    if (
                        empty($row->jumlah_berat_sebelum) ||
                        empty($row->jumlah_berat_sesudah) ||
                        empty($row->tanggal_dan_waktu_masuk) ||
                        empty($row->tanggal_dan_waktu_sesudah)
                    ) {
                        // Jika ada yang kosong, tampilkan tombol input timbangan
                        return '<button class="btn btn-sm btn-primary openModalBtn" 
                            data-id="' . $row->id . '">
                        Input Timbangan
                    </button>';
                    } else {
                        // Jika semua terisi, tampilkan simbol '-'
                        return '-';
                    }
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('penerimaan/transaksi-wadah.rincian_wadah', compact('header', 'id','count_null_berat', 'data_wadah','wadah', 'tanggal_digunakan'));
    }

    public function simpan_transaksi_wadah_sesudah(Request $request)
    {
        try {
            // Validasi Input
            $request->validate([
                'jumlah_berat_sesudah'  => 'required|numeric',
                'lokasi'                => 'required',
            ]);

            // Cek apakah wadah terdaftar

            $data = TransaksiWadah::where('id', $request->id)->first();
            // Simpan Data ke Database
            TransaksiWadah::where('id', $request->id)
                ->update([
                    'jumlah_berat_sesudah'      => $request->jumlah_berat_sesudah,
                    'tanggal_dan_waktu_masuk' => $data->tanggal_dan_waktu_masuk,
                    'tanggal_dan_waktu_sesudah' =>  Carbon::now('Asia/Jakarta'), // WIB Menggunakan timestamp saat ini
                    'lokasi'                    => $request->lokasi,
                    
                ]);



            // update di transaksi wadah 
            

            return response()->json(['success' => true, 'message' => 'Data berhasil disimpan']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
        //return redirect()->route('master_wadah.index')->with('success', 'Wadah berhasil ditambahkan!');
    }

    public function simpan_gudang($id)
    {
        // Hitung jumlah transaksi wadah yang masih memiliki 'jumlah_berat_sesudah' NULL
        $count_null_berat = DB::table('tb_transaksi_wadah')
            ->join('tb_wadah', 'tb_transaksi_wadah.qr_code_wadah', '=', 'tb_wadah.qr_code')
            ->join('tb_penerimaan', 'tb_transaksi_wadah.id_penerimaan', '=', 'tb_penerimaan.id')
            ->join('tb_po_bahan', 'tb_penerimaan.id_barang_po', '=', 'tb_po_bahan.id')
            ->join('tb_master_bahan', 'tb_po_bahan.id_bahan', '=', 'tb_master_bahan.id')
            ->whereIn('tb_transaksi_wadah.status', [0, 1]) // Hanya mengambil transaksi dengan status 0 atau 1
            ->where('tb_wadah.qr_code', $id) // Sesuai dengan QR Code wadah
            ->whereNull('tb_transaksi_wadah.jumlah_berat_sesudah') // Filter data yang belum memiliki berat sesudah
            ->count();

        // Jika masih ada bahan yang belum ditimbang, kembalikan error
        if ($count_null_berat > 0) {
            return redirect()->route('detail_wadah', $id)
                ->with('error', 'bahan yang belum ditimbang');
        }

        // Jika semua data sudah terisi, update status menjadi 1 (masuk gudang)
        DB::table('tb_transaksi_wadah')
            ->join('tb_wadah', 'tb_transaksi_wadah.qr_code_wadah', '=', 'tb_wadah.qr_code')
            ->join('tb_penerimaan', 'tb_transaksi_wadah.id_penerimaan', '=', 'tb_penerimaan.id')
            ->join('tb_po_bahan', 'tb_penerimaan.id_barang_po', '=', 'tb_po_bahan.id')
            ->join('tb_master_bahan', 'tb_po_bahan.id_bahan', '=', 'tb_master_bahan.id')
            ->whereIn('tb_transaksi_wadah.status', [0, 1])
            ->where('tb_wadah.qr_code', $id)
            ->update([
                'tb_transaksi_wadah.status' => 1
            ]);

        // Redirect ke halaman transaksi wadah dengan pesan sukses
        return redirect()->route('transaksi_wadah.index')
            ->with('success', 'Bahan berhasil masuk gudang');
    }
}
