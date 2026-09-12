<?php

namespace App\Http\Controllers\Office;


use App\Http\Controllers\Controller;
use App\Models\BoxBahanBaku;
use App\Models\Buffer;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
//import return type View
use Illuminate\View\View;
//import return type redirectResponse
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Models\TbPo;
use App\Models\TbPoBahan;
use App\Models\Supplier;
use App\Models\TbKontrak;
use App\Models\Menu;
use App\Models\rincian_menu_harian;
use App\Models\TbMasterBahan;
use App\Models\TbSatuan;
use App\Models\DataDapur;
use App\Models\MenuBahan;
use App\Models\Resep;
use App\Models\rincian_sekolah;
use App\Models\SpesifikasiBahan;
use App\Models\tb_karyawan;
use App\Models\TbBantuBahanPo;
use App\Models\TbRincianKontrak;
use App\Models\TbRincianMenuTemp;
use Svg\Tag\Rect;
use ZipArchive;

class PoController extends Controller
{
    //
    public function index()
    {
        // Judul halaman
        $header = "Dashboard PO";

        // Jika request via AJAX (untuk DataTables)
        if (request()->ajax()) {
            // Ambil data dari tb_po join dengan tb_kontrak dan tb_supplier
            $resep = TbPo::join('tb_kontrak', 'tb_po.id_kontrak', '=', 'tb_kontrak.id')
                ->join('tb_supplier', 'tb_kontrak.id_supplier', '=', 'tb_supplier.id')
                ->select([
                    'tb_kontrak.*',
                    'tb_supplier.nama_supplier',
                    'tb_po.nomor_po',
                    'tb_po.id as id_po',
                    'tb_po.status_po',
                    'tb_po.tanggal_po',
                    'tb_po.tanggal_approve',
                    'tb_po.manual',
                ]);

            // Filter: nomor PO
            if (request()->filled('search_nomor_po')) {
                $resep->where('tb_po.nomor_po', 'like', '%' . request('search_nomor_po') . '%');
            }

            // Filter: rentang tanggal PO
            if (request()->filled('tanggal_awal')) {
                $resep->whereDate('tb_po.tanggal_po', '>=', request('tanggal_awal'));
            }

            if (request()->filled('tanggal_akhir')) {
                $resep->whereDate('tb_po.tanggal_po', '<=', request('tanggal_akhir'));
            }

            $resep = $resep->orderBy('tb_po.id', 'desc')   // Urutkan terbaru
                ->limit(60)                     // Ambil 60 data terakhir
                ->get();

            // Return data ke DataTables
            return DataTables::of($resep)
                ->addIndexColumn() // Tambahkan nomor urut otomatis
                ->addColumn('tanggal_po_dibuat', function ($row) {
                    // Format tanggal PO ke bahasa Indonesia
                    \Carbon\Carbon::setLocale('id');
                    setlocale(LC_TIME, 'id_ID.utf8', 'Indonesian', 'id_ID');

                    return Carbon::parse($row->tanggal_po)->translatedFormat('l, d F Y');
                })
                ->addColumn('tanggal_acc', function ($row) {
                    // Format tanggal approve (jika ada), kalau kosong tampilkan "-"
                    if (!empty($row->tanggal_approve)) {
                        setlocale(LC_TIME, 'id_ID.utf8', 'Indonesian', 'id_ID');
                        return Carbon::parse($row->tanggal_approve)->translatedFormat('l, d F Y');
                    }
                    return '-';
                })
		->addColumn('keterangan_menu', function ($row) {
                    // Format tanggal approve (jika ada), kalau kosong tampilkan "-"
                    $data =  DB::table('tb_po_bahan')->where('id_po', $row->id_po)->first();
                    \Carbon\Carbon::setLocale('id');
                    if($data)
                    {
                    if ($data->id_rincian_bahan == 0) {
                        return 'non Bahan Pangan tanggal ' . \Carbon\Carbon::parse($row->tanggal_po)->translatedFormat('l, d F Y');
                    } else {
                         $menu = Menu::join('rincian_menu_harian', 'tb_menu.id', 'rincian_menu_harian.id_menu_harian')
                            ->where('rincian_menu_harian.id', $data->id_rincian_bahan)->select('tb_menu.*')->first();
                        $data_bahan = MenuBahan::where('bahan_id', $data->id_bahan)->first();
                            return $menu->menu ?? 'Menu Terhapus';                    }
                    }else{
                        return '-';
                    }
                    
                   
                })
                ->addColumn('action', function ($row) {
                    $printCenterUrl = route('mastermenu.print-center', [
                        'id_po' => $row->id_po,
                    ]);

                    // Aksi tombol tergantung level user & status PO
                    if (
                        auth()->user()->level == "kepala_dapur"
                        || auth()->user()->level == "backoffice"
                        || auth()->user()->level == "ahli_akuntan"
                    ) {

                        // Jika PO belum ACC dan belum CLOSE
                        if ($row->status_po != 'acc' && $row->status_po != 'close') {

                            // Jika PO manual
                            if ($row->manual == 1) {
                                return '
                                <button class="btn btn-success btn-sm update-po" data-id="' . $row->id_po . '">Update status</button>
                                <button class="btn btn-warning btn-sm update-close" data-id="' . $row->id_po . '">Update close</button>
                                <a href="' . route('pengajuan_po.edit_bahan_baku', $row->id_po) . '" class="btn btn-info btn-sm">Edit</a>
                                <form action="' . route('pengajuan_po.destroy', $row->id_po) . '" method="POST" style="display:inline;">
                                    ' . method_field('DELETE') . csrf_field() . '
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                                <a href="' . route('pdf_pengajuan_po_manual', $row->id_po) . '" class="btn btn-info btn-sm">PDF1</a>
                                <a href="' . route('docx_pengajuan_po_manual', $row->id_po) . '" class="btn btn-info btn-sm" hidden>DOCX</a>
                                <a href="' . route('pdf_pengajuan_po_tanpa_harga', $row->id_po) . '" class="btn btn-info btn-sm">PDF2</a>
                                <a href="' . $printCenterUrl . '" class="btn btn-dark btn-sm">Pusat Cetak</a>
                            ';
                            }
                            // Jika PO otomatis
                            else {
                                return '
                                <button class="btn btn-success btn-sm update-po" data-id="' . $row->id_po . '">Update status</button>
                                <button class="btn btn-warning btn-sm update-close" data-id="' . $row->id_po . '">Update close</button>
                                <a href="' . route('pengajuan_po.edit_bahan_baku', $row->id_po) . '" class="btn btn-info btn-sm">Edit</a>
                                <form action="' . route('pengajuan_po.destroy', $row->id_po) . '" method="POST" style="display:inline;">
                                    ' . method_field('DELETE') . csrf_field() . '
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                                <a href="' . route('pdf_pengajuan_po', $row->id_po) . '" class="btn btn-info btn-sm">PDF1</a>
                                <a href="' . route('pdf_pengajuan_po_tanpa_harga', $row->id_po) . '" class="btn btn-info btn-sm">PDF2</a>
                                <a href="' . $printCenterUrl . '" class="btn btn-dark btn-sm">Pusat Cetak</a>
                            ';
                            }
                        }
                        // Jika PO sudah ACC atau CLOSE
                        else {
                            if ($row->manual == 1) {
                                return '
                                <button class="btn btn-success btn-sm update-po" data-id="' . $row->id_po . '">Update status</button>
                                <button class="btn btn-warning btn-sm update-close" data-id="' . $row->id_po . '">Update close</button>
                                <a href="' . route('pengajuan_po.edit_bahan_baku', $row->id_po) . '" class="btn btn-info btn-sm">Edit</a>
                                <form action="' . route('pengajuan_po.destroy', $row->id_po) . '" method="POST" style="display:inline;">
                                    ' . method_field('DELETE') . csrf_field() . '
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                                <a href="' . route('pdf_pengajuan_po_manual', $row->id_po) . '" class="btn btn-info btn-sm">PDF1</a>
                                <a href="' . route('docx_pengajuan_po_manual', $row->id_po) . '" class="btn btn-info btn-sm" hidden>DOCX</a>
                                <a href="' . route('pdf_pengajuan_po_tanpa_harga', $row->id_po) . '" class="btn btn-info btn-sm">PDF2</a>
                                <a href="' . $printCenterUrl . '" class="btn btn-dark btn-sm">Pusat Cetak</a>
                            ';
                            } else {
                                return '
                                <button class="btn btn-success btn-sm update-po" data-id="' . $row->id_po . '">Update status</button>
                                <button class="btn btn-warning btn-sm update-close" data-id="' . $row->id_po . '">Update close</button>
                                <a href="' . route('pengajuan_po.edit_bahan_baku', $row->id_po) . '" class="btn btn-info btn-sm">Edit</a>
                                <form action="' . route('pengajuan_po.destroy', $row->id_po) . '" method="POST" style="display:inline;">
                                    ' . method_field('DELETE') . csrf_field() . '
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                                <a href="' . route('pdf_pengajuan_po', $row->id_po) . '" class="btn btn-info btn-sm">PDF1</a>
                                <a href="' . route('pdf_pengajuan_po_tanpa_harga', $row->id_po) . '" class="btn btn-info btn-sm">PDF2</a>
                                <a href="' . $printCenterUrl . '" class="btn btn-dark btn-sm">Pusat Cetak</a>
                            ';
                            }
                        }
                    }
                    // Jika user bukan kepala_dapur / backoffice / ahli_akuntan
                    else {
                        if ($row->manual == 1) {
                            return '
                            <form hidden  action="' . route('pengajuan_po.destroy', $row->id_po) . '" method="POST" style="display:inline;">
                                ' . method_field('DELETE') . csrf_field() . '
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                            <a href="' . route('pdf_pengajuan_po_manual', $row->id_po) . '" class="btn btn-info btn-sm">PDF</a>
                            <a href="' . route('docx_pengajuan_po_manual', $row->id_po) . '" class="btn btn-info btn-sm" hidden>DOCX</a>
                            <a href="' . $printCenterUrl . '" class="btn btn-dark btn-sm">Pusat Cetak</a>
                        ';
                        } else {
                            return '
                            <form hidden  action="' . route('pengajuan_po.destroy', $row->id_po) . '" method="POST" style="display:inline;">
                                ' . method_field('DELETE') . csrf_field() . '
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                            <a href="' . route('pdf_pengajuan_po', $row->id_po) . '" class="btn btn-info btn-sm">PDF</a>
                            <a href="' . $printCenterUrl . '" class="btn btn-dark btn-sm">Pusat Cetak</a>
                        ';
                        }
                    }
                })
                ->rawColumns(['action', 'tanggal_po_dibuat', 'keterangan_menu']) // Kolom yang berisi HTML
                ->make(true);
        }

        // Jika bukan request AJAX → tampilkan view
        return view('office/PO.index', compact('header'));
    }

    /**
     * API endpoint untuk menerima draft pengajuan PO sederhana.
     * Saat ini hanya melakukan validasi dan logging untuk audit,
     * pemrosesan penuh tetap dilakukan via dashboard internal.
     */
    public function api_pengajuan_po(Request $request)
    {
        $payload = $request->validate([
            'id_kontrak' => ['required', 'exists:tb_kontrak,id'],
            'tanggal_po' => ['required', 'date'],
            'nomor_po' => ['nullable', 'string', 'max:50'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.id_bahan' => ['required', 'exists:tb_master_bahan,id'],
            'items.*.jumlah_po' => ['required', 'numeric', 'min:0.01'],
        ]);

        $nomorPo = $payload['nomor_po'] ?? ('API-' . strtoupper(Str::random(10)));

        Log::info('API pengajuan PO diterima', [
            'nomor_po' => $nomorPo,
            'id_kontrak' => $payload['id_kontrak'],
            'item_count' => count($payload['items']),
            'user' => optional(auth('api')->user())->id,
        ]);

        return response()->json([
            'success' => true,
            'nomor_po' => $nomorPo,
            'message' => 'Permintaan PO diterima. Silakan lanjutkan proses di dashboard.',
        ], 202);
    }



    public function create()
    {
        // Variabel default untuk form
        $id = '';
        $header = 'Buat Pengajuan PO';

        // Ambil data karyawan
        $karyawan = tb_karyawan::all();

        // Ambil data dapur (fallback ke default jika belum ada)
        $datadapur = DataDapur::first() ?? (object) ['nomor_dapur' => '000'];

        // Hitung jumlah PO yang sudah ada untuk menentukan nomor urut
        $jumlah_kontrak = TbPo::count();

        // Tambahkan leading zero (format 3 digit)
        if ($jumlah_kontrak < 9) {
            $jumlah_kontrak = '00' . ($jumlah_kontrak + 1);
        } elseif ($jumlah_kontrak < 99) {
            $jumlah_kontrak = '0' . ($jumlah_kontrak + 1);
        } else {
            $jumlah_kontrak = ($jumlah_kontrak + 1);
        }

        // Buat nomor PO dengan format: PO-{kodeDapur}-{YYYYMM}{urut}
        $bulan_PO = Carbon::now()->format('Ym');
        $nomor_PO = 'PO-' . $datadapur->nomor_dapur . '-' . $bulan_PO . $jumlah_kontrak;

        // Ambil daftar supplier
        $Supplier = Supplier::select('id', 'nama_supplier')->get();

        // Ambil daftar kontrak beserta supplier terkait
        $kontrak = TbKontrak::join('tb_supplier', 'tb_kontrak.id_supplier', '=', 'tb_supplier.id')
            ->select('tb_kontrak.id', 'tb_supplier.nama_supplier')
            ->get();

        // Kirim data ke view
        return view('office/PO.create', compact(
            'header',
            'karyawan',
            'nomor_PO',
            'kontrak',
            'id'
        ));
    }


    public function create_po($id)
    {
        // Judul halaman
        $header = 'Buat Pengajuan PO';

        // Ambil semua data karyawan
        $karyawan = tb_karyawan::all();

        // Hitung jumlah PO yang sudah ada → dipakai untuk nomor urut
        $jumlah_kontrak = TbPo::count();

        // Format nomor urut 3 digit
        if ($jumlah_kontrak < 9) {
            $jumlah_kontrak = '00' . ($jumlah_kontrak + 1);
        } elseif ($jumlah_kontrak < 99) {
            $jumlah_kontrak = '0' . ($jumlah_kontrak + 1);
        } else {
            $jumlah_kontrak = ($jumlah_kontrak + 1);
        }

        // Ambil data dapur
        $dapur = DataDapur::first();

        // Format bulan: YYYYMM
        $bulan_PO = Carbon::now()->format('Ym');

        // Buat nomor PO
        // Contoh: PO-Y01-202508007
        $nomor_PO = 'PO-Y0' . $dapur->nomor_dapur . '-' . $bulan_PO . $jumlah_kontrak;

        // Ambil daftar supplier
        $Supplier = Supplier::select('id', 'nama_supplier')->get();

        // Ambil kontrak supplier berdasarkan menu harian yang dipilih ($id)
        $kontrak = Supplier::join('tb_kontrak', 'tb_supplier.id', '=', 'tb_kontrak.id_supplier')
            ->join('tb_rincian_kontrak', 'tb_kontrak.id', '=', 'tb_rincian_kontrak.id_kontrak')
            ->join('rincian_menu_harian', 'tb_rincian_kontrak.id_bahan', '=', 'rincian_menu_harian.id_bahan')
            ->join('tb_menu', 'rincian_menu_harian.id_menu_harian', '=', 'tb_menu.id')
            ->where('tb_menu.id', $id) // hanya kontrak yang terkait menu tertentu
            ->whereDate('tb_kontrak.akhir_kontrak', '>=', now()->toDateString()) // kontrak masih aktif
            ->select('tb_kontrak.id', 'tb_supplier.nama_supplier', 'tb_kontrak.id_supplier')
            ->distinct()
            ->get();

        // Kirim data ke view
        return view('office/PO.create', compact(
            'header',
            'karyawan',
            'nomor_PO',
            'kontrak',
            'id'
        ));
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
            'nomor_po'                     => 'required|min:1',
            'supplier'                     => 'required',
            'tanggal_pengajuan'            => 'required',
            // 'id_karyawan'                  => 'required',
        ]);
        $cek = TbPo::where('nomor_po', $request->nomor_po)->count();
        if ($cek > 0) {
            return redirect()->route('pengajuan_po.create')->with(['error' => 'Data Sudah Pernah Masuk!']);
        }
        $id = $request->id_menu;
        TbPo::create([
            'nomor_po'                  => $request->nomor_po,
            'id_kontrak'                => $request->supplier,
            'tanggal_po'                => $request->tanggal_pengajuan,
            'tanggal_pengajuan'         => $request->tanggal_pengajuan,
            'status_po'                 => 'draft',
            'id_karyawan'               => 0,
        ]);
        $po = TbPo::where('nomor_po', $request->nomor_po)->first();
        $menu = Menu::find($request->id_menu);
        $rincian_bahan = rincian_menu_harian::where('id_menu_harian', $request->id_menu)->get();
        
        return redirect()->route('rincian_pengajuan_po', ['id' => $po->id, 'id_menu' => $id, 'id_kontrak' => $po->id_kontrak]);
    }

    public function auto_generate_po(Request $request)
    {
        $request->validate([
            'nomor_po'          => 'required|min:1',
            'id_menu'           => 'required|integer|exists:tb_menu,id',
            'id_kontrak'        => 'required|integer',
            'tanggal_pengajuan' => 'required|date',
            'tanggal_po'        => 'required|date',
            'tanggal_kedatangan' => 'nullable|date',
        ]);

        $po = TbPo::where('nomor_po', $request->nomor_po)->first();
        $isUpdate = (bool) $po;

        if ($po) {
            $nextRevisi = ((int) ($po->revisi ?? 0)) + 1;
            $po->update([
                'id_kontrak'        => $request->id_kontrak,
                'tanggal_po'        => $request->tanggal_po,
                'tanggal_pengajuan' => $request->tanggal_pengajuan,
                'revisi'            => $nextRevisi,
            ]);
        } else {
            $po = TbPo::create([
                'nomor_po'          => $request->nomor_po,
                'id_kontrak'        => $request->id_kontrak,
                'tanggal_po'        => $request->tanggal_po,
                'tanggal_pengajuan' => $request->tanggal_pengajuan,
                'status_po'         => 'draft',
                'id_karyawan'       => 0,
                'revisi'            => 0,
            ]);
        }

        return redirect()->route('rincian_pengajuan_po', ['id' => $po->id, 'id_menu' => $request->id_menu, 'id_kontrak' => $po->id_kontrak])
            ->with(['success' => $isUpdate
                ? 'PO berhasil direvisi. Rev ' . ((int) ($po->revisi ?? 0)) . '. Rincian bahan silakan input dari tombol Pengiriman Otomatis.'
                : 'PO otomatis berhasil dibuat. Rincian bahan silakan input dari tombol Pengiriman Otomatis. Rev 0']);
    }

    public function store_po(Request $request)
    {
        $request->validate([
            'nomor_po'                     => 'required|min:1',
            'supplier'                     => 'required',
            'tanggal_pengajuan'            => 'required',
            //  'id_karyawan'                  => 'required',
        ]);
        $cek = TbPo::where('nomor_po', $request->nomor_po)->count();
        if ($cek > 0) {
            return redirect()->route('pengajuan_po.create')->with(['error' => 'Data Sudah Pernah Masuk!']);
        }
        $id = $request->id_menu;
        TbPo::create([
            'nomor_po'                  => $request->nomor_po,
            'id_kontrak'                => $request->supplier,
            'tanggal_po'                => $request->tanggal_pengajuan,
            'tanggal_pengajuan'         => $request->tanggal_pengajuan,
            'status_po'                 => 'draft',
            'id_karyawan'               => 0,
        ]);
        $po = TbPo::where('nomor_po', $request->nomor_po)->first();
        $menu = Menu::find($request->id_menu);
        $rincian_bahan = rincian_menu_harian::where('id_menu_harian', $request->id_menu)->get();
        /*foreach($rincian_bahan as $data)
        {
            TbPoBahan::create([
                'id_po'              => $po->id,
                'id_bahan'           => $data->id_bahan,
                'jumlah_bahan'       => $data->jumlah,
                'satuan'             => $data->id_satuan ?? '0', // Pastikan tidak null
                'jumlah_po'          => str_replace('.', '', $data->total_harga),
                //'jumlah_po'          => 0,
                'tanggal_kedatangan' => date('Y-m-d', strtotime($menu->tanggal_kirim. ' -1 days')), // Default 2 hari sebelum tanggal digunakan,
                'tanggal_digunakan'  => $menu->tanggal_kirim,
                'id_rincian_bahan'   => $data->id,
                'buffer'             => 0,
                'keterangan'         => $data->keterangan,
                'jumlah_box'         => $data->jumlah_box,
                //'id_rincian_kontrak' => $request->merek_bahan,
                'id_rincian_kontrak' => $data->id_kontrak,
                'id_kontrak' => 13
            ]);
            
            
        }*/
        return redirect()->route('rincian_pengajuan_po', ['id' => $po->id, 'id_menu' => $id, 'id_kontrak' => $po->id_kontrak]);
    }

    public function rincian_pengajuan_po($id, $id_menu, $id_kontrak)
    {
        $po         = TbPo::where('id', $id)->first();
        $menu       = Menu::where('id', $id_menu)->first();
        $header = 'Rincian Pengajuan PO ';
        $data_bahan_koperasi = DB::table('tb_rincian_kontrak as rk')
            ->join('tb_master_bahan as mb', 'mb.id', '=', 'rk.id_bahan')
            ->join('rincian_menu_harian as rmh', 'rmh.id_bahan', '=', 'mb.id')
            ->join('tb_satuan as satuan', 'satuan.id', '=', 'rk.satuan_bahan')
            ->where('rk.status', 1)
            ->where('rmh.id_menu_harian', $id_menu)
            ->select('rk.id', 'rk.id_bahan', 'mb.bahan', 'rk.merek_bahan', 'rk.harga_bahan', 'satuan.satuan')
            ->get();

        if (request()->ajax()) {
            

        $menu = menu::join('rincian_menu_harian', 'tb_menu.id', '=', 'rincian_menu_harian.id_menu_harian')
                //->join('tb_resep', 'rincian_menu_harian.id_resep', '=', 'tb_resep.id')
                ->join('tb_master_bahan', 'rincian_menu_harian.id_bahan', '=', 'tb_master_bahan.id')
                ->join('tb_satuan', 'rincian_menu_harian.id_satuan', '=', 'tb_satuan.id')
                ->join('tb_menu_bahan', function ($join) {
                    $join->on('tb_menu_bahan.menu_id', '=', 'rincian_menu_harian.id_resep')
                        ->on('tb_menu_bahan.bahan_id', '=', 'rincian_menu_harian.id_bahan');
                })
                ->where('tb_master_bahan.bahan', '!=', 'air')
                ->where('tb_menu.id', $id_menu)
                ->select([
                    'tb_menu.id',
                    'tb_menu.tanggal_kirim',
                    'rincian_menu_harian.id_resep',
                    //'tb_resep.nama_resep',
                    'tb_master_bahan.bahan',
                    'tb_master_bahan.id as idbahan',
                    'tb_menu_bahan.status_bahan_baku',
                    'rincian_menu_harian.id as id_rincian_menu',
                    'rincian_menu_harian.jumlah',
                    'tb_satuan.satuan',
                    'tb_menu_bahan.id as id_menu_bahan', // kalau mau ambil juga
                ])
                ->get();


            return DataTables::of($menu)
                ->addIndexColumn() // Menambah index

                ->addColumn('nama_resep', function ($row) {
                    $status_baku = '-';
                    if($row->status_bahan_baku == 3 )
                    {
                        $status_baku = 'bumbu';
                        
                    }else{
                        $status_baku = 'utama';
                    
                    }

                    setlocale(LC_TIME, 'id_ID.utf8', 'Indonesian', 'id_ID'); // Set bahasa Indonesia
                    if ($row->id_resep == 0) {
                        return 'tambahan (' . strftime('%A, %d-%m-%Y', strtotime($row->tanggal_kirim)) . ')';
                    } else {
                        $nama_resep = Resep::find($row->id_resep);
                        return $nama_resep->nama_resep . '  '. $status_baku.' (' . strftime('%A, %d-%m-%Y', strtotime($row->tanggal_kirim)) . ')';
                    }

                    // return $row->nama_resep . ' (' . strftime('%A, %d-%m-%Y', strtotime($row->tanggal_kirim)) . ')';
                })

                ->addColumn('nama_resep_dan_tanggal', function ($row) {
                    setlocale(LC_TIME, 'id_ID.utf8', 'Indonesian', 'id_ID'); // Set bahasa Indonesia
                    return $row->nama_resep . ' (' . strftime('%A, %d-%m-%Y', strtotime($row->tanggal_kirim)) . ')';
                })
                ->addColumn('bahan_dan_total', function ($row) {
                    //konversi gram - kg 
                    $total =  $row->jumlah;
                    $satuannya = $row->satuan;
                    if ($row->satuan == 'Gram') {
                        $total = $row->jumlah;
                        // $satuannya = 'Kg';
                    }
                    return $row->bahan . ' | ' .  number_format($total, 0, ',', '.') . ' ' . $satuannya;
                })
                ->addColumn('terpenuhi', function ($row) use ($id) {

                   
                    $satuannya = $row->satuan;
                    $data       = TbPoBahan::where('id_rincian_bahan', $row->id_rincian_menu)
                        ->sum('jumlah_bahan');
                    return number_format($data, 0, ',', '.') . ' ' . $satuannya ?? '-';
                })
                ->addColumn('input_jumlah', function ($row) {
                    return '<input type="number" class="form-control jumlah" " data-id="' . $row->id . '" value="' . $row->jumlah_penerima . '">';
                })
                ->addColumn('action', function ($row) use ($id, $po, $id_kontrak) {
                    $data       = TbPoBahan::where('id_po', $id)->where('id_bahan', $row->idbahan)
                        ->where('tanggal_digunakan', $row->tanggal_kirim)->where('id_rincian_bahan', $row->id_rincian_menu)->sum('jumlah_bahan');
                    $datapo      = TbPoBahan::where('id_po', $id)->where('id_bahan', $row->idbahan)
                        ->where('tanggal_digunakan', $row->tanggal_kirim)->where('id_rincian_bahan', $row->id_rincian_menu)->first();


                    $cek        = $row->jumlah - $data;
                    if ($cek == 0) {
                        return 'Rp. ' . number_format($datapo->jumlah_po ?? 0, 0, ',', '.');
                    } else {
                        $total = $cek;
                        $satuannya = $row->satuan;
                        $rincian_kontrak = TbRincianKontrak::join('tb_satuan', 'tb_rincian_kontrak.satuan_bahan', 'tb_satuan.id')
                            ->where('tb_rincian_kontrak.id_kontrak', $id_kontrak)
                            ->where('id_bahan', $row->idbahan)->select('tb_satuan.satuan')->first();
                        if ($row->satuan == 'Gram' && $rincian_kontrak->satuan == "Kg") {
                            $total = $cek / 1000;
                        } else if ($row->satuan == 'ml' && $rincian_kontrak->satuan == "liter") {
                            $total = $cek / 1000;
                        }

                        $total_bayar    =  $total;
                        //$total_bayar    = 0;
                        $cek_data = TbPoBahan::where('id_rincian_bahan', $row->id_rincian_menu)->first();
                        if (empty($cek_data)) {
                            $keterangan = SpesifikasiBahan::where('id_bahan', $row->idbahan)->first();
                            if ($keterangan) {
                                $keterangan = $keterangan->spesifikasi;
                            } else {
                                $keterangan = '-';
                            }
                            
                            $buffer         = Buffer::first();
                            $jumlah_buffer  = $cek * $buffer->buffer_po / 100;
                            $jumlah_box = 1;
                            $cek_box = BoxBahanBaku::where('id_bahan', $row->idbahan)->first();
                            if ($cek_box) {
                                $jumlah_box = ($cek + $jumlah_buffer) /  $cek_box->isi_per_box;
                                $jumlah_box = ceil($jumlah_box);
                            }
                            //=================================================================
                            $data_baru = rincian_menu_harian::find($row->id_rincian_menu)??0;
                            $total_bayar = $data_baru->total_harga ?? $total_bayar;
                            $cek        = $data_baru->jumlah ?? $cek ;
                            $cek_menu = Menu::find($row->id);
                            $jumlah_box = $data_baru->jumlah_box;
                            $kontrak = TbRincianKontrak::find($data_baru->id_kontrak);
                            //=================================================================
                            /*$btn = '<button class="btn btn-sm btn-primary openModalBtn" 
                            data-idbahan="' . $row->idbahan . '"
                            data-jumlahbahan="' . $cek . '"
                            data-satuan="' . $satuannya . '"
                            data-bahan="' . $row->bahan . '"
                            data-tanggal_digunakan="' . $row->tanggal_kirim . '"
                            data-tanggal_kirim="' . Carbon::parse($row->tanggal_kirim)
                            ->subDay()
                            ->setTime(11, 0)
                            ->format('Y-m-d\TH:i') . '"
                            data-jumlahpo="' . $total_bayar . '" 
                            data-rincian="' . $row->id_rincian_menu . '"
                            data-keterangan="' . $keterangan . '"
                            data-buffer="' . ceil($jumlah_buffer) . '"
                            data-box="' . $jumlah_box . '"
                            data-merek="' . $kontrak->merek_bahan . '"
                            data-idmerek="' . $kontrak->id . '"
                            data-harga="' . $kontrak->harga_bahan . '">
                            Input Pengiriman
                        </button>';*/
                        // return '<input type="checkbox" name="bahan[]" value="' . $row->id_rincian_menu . '">';
                        $btn = '<button class="btn btn-sm btn-primary openModalBtn" 
                            data-idbahan="' . $row->idbahan . '"
                            data-jumlahbahan="' . $cek . '"
                            data-satuan="' . $satuannya . '"
                            data-bahan="' . $row->bahan . '"
                            data-tanggal_digunakan="' . $row->tanggal_kirim . '"
                            data-tanggal_kirim="' . Carbon::parse($row->tanggal_kirim)
                            ->subDay()
                            ->setTime(11, 0)
                            ->format('Y-m-d\TH:i') . '"
                            data-jumlahpo="' . $total_bayar . '" 
                            data-rincian="' . $row->id_rincian_menu . '"
                            data-keterangan="' . $keterangan . '"
                            data-buffer="' . ceil($jumlah_buffer) . '"
                            data-box="' . $jumlah_box . '"
                            data-merek="' . $kontrak->merek_bahan . '"
                            data-idmerek="' . $kontrak->id . '"
                            data-harga="' . $kontrak->harga_bahan . '">
                            Input Pengiriman
                        </button>'; 
                           return $btn;
                        } else {
                            $total = TbPoBahan::where('id_rincian_bahan', $row->id_rincian_menu)->sum('jumlah_po');
                            $po_bahan_items = TbPoBahan::where('id_rincian_bahan', $row->id_rincian_menu)
                                ->where('id_po', $id)
                                ->get();
                            
                            $deleteBtn = '';
                            if ($po_bahan_items->count() > 0) {
                                $po_bahan_ids = $po_bahan_items->pluck('id')->implode(',');
                                $delete_url = url('/pengajuan_po/hapus_po_bahan/' . $po_bahan_ids);
                                $deleteBtn = '<a href="' . $delete_url . '" class="btn btn-sm btn-danger ms-2" 
                                    onclick="return confirm(\'Hapus pengiriman yang sudah diinput?\')">
                                    <i class="fas fa-trash"></i> Hapus
                                </a>';
                            }
                            
                            //return 'Rp. ' . number_format($total ?? 0, 0, ',', '.') . ',-';
                            return 'sudah PO ' . $deleteBtn;
                        }
                    }
                })
                ->rawColumns(['action', 'nama_resep_dan_tanggal', 'nama_resep', 'bahan_dan_total', 'terpenuhi'])
                ->make(true);
        }
        return view('office/PO.index_rincian_po', compact('header', 'po', 'id_menu', 'menu', 'data_bahan_koperasi'));
    }

    public function simpan_rincian_pengajuan_po(Request $request)
    {
        $po         = TbPo::where('id', $request->idpo)->first();
        if (empty($request->tanggal_kirim)) {
            return redirect()->route('rincian_pengajuan_po', ['id' => $po->id, 'id_menu' => $request->id_menu, 'id_kontrak' => $po->id_kontrak])
                ->with(['error' => 'Isi dulu data tanggal kirim']);
        } else if (empty($request->jumlahpo)) {
            return redirect()->route('rincian_pengajuan_po', ['id' => $po->id, 'id_menu' => $request->id_menu, 'id_kontrak' => $po->id_kontrak])
                ->with(['error' => 'Isi dulu data jumlah pembayaran po']);
        }

        $satuan     = TbSatuan::where('satuan', $request->satuan)->first();
        $bahan      = TbMasterBahan::where('id', $request->idbahan)->first();
        $cek        = TbPoBahan::where('id_po', $request->idpo)->where('id_bahan', $request->idbahan)
        
            ->where('tanggal_digunakan', $request->tanggal_digunakan)->count();
        $total = menu::join('rincian_menu_harian', 'tb_menu.id', 'rincian_menu_harian.id_menu_harian')
            ->join('tb_resep', 'rincian_menu_harian.id_resep', 'tb_resep.id')
            ->join('tb_master_bahan', 'rincian_menu_harian.id_bahan', 'tb_master_bahan.id')
            ->join('tb_satuan', 'tb_master_bahan.satuan_bahan', 'tb_satuan.id')
            ->join('tb_rincian_kontrak', 'rincian_menu_harian.id_bahan', 'tb_rincian_kontrak.id_bahan')
            ->join('tb_kontrak', 'tb_rincian_kontrak.id_kontrak', 'tb_kontrak.id')
            ->where('rincian_menu_harian.id_bahan', $request->idbahan)
            ->where('tb_menu.tanggal_kirim', $request->tanggal_digunakan)

            ->where('tb_menu.status_pengajuan', '!=', 'rejected') // Hindari data yang reject
            ->select([
                'tb_menu.id',
                'rincian_menu_harian.jumlah',
                'rincian_menu_harian.id as id_menu'
            ])->sum('jumlah');
        if ($cek == 1) {
            $data = TbPoBahan::where('id_po', $request->idpo)->where('id_bahan', $request->idbahan)
                ->where('tanggal_digunakan', $request->tanggal_digunakan)->first();
            $total = $total - $data->jumlah_bahan;

            if ($total < $request->jumlah) {
                return redirect()->route('rincian_pengajuan_po', ['id' => $po->id, 'id_menu' => $request->id_menu, 'id_kontrak' => $po->id_kontrak])->with(['error' => 'jumlah melebihi kuota']);
            }
        }
        $data_rincian_bahan_menu = rincian_menu_harian::find($request->idrincian);
        if ($bahan->bahan == 'Beras Medium' || $bahan->bahan == 'Beras Premium' || $bahan->bahan == 'Beras' || $bahan->bahan == 'beras') {
            $jumlah_total = $request->jumlah;
            $pack_utama = $jumlah_total / 72;
            $pack_utamas = $jumlah_total / 72;
            // Hitung pembagian pack awal
            $pack25 = floor($pack_utama) * 2;
            $pack5  = floor($pack_utama) * 4;
            $pack1  = floor($pack_utama) * 2;

            // Hitung sisa setelah diambil pack utama
            $sisa = $jumlah_total - floor($pack_utama) * 72;
            $pack25sisa = floor($sisa / 25);
            $sisa = $sisa - $pack25sisa * 25;
            $pack5sisa = floor($sisa / 5);
            $pack1sisa = $sisa % 5;
            $pack25 =  $pack25 + $pack25sisa;
            $pack5 =  $pack5 + $pack5sisa;
            $pack1 =  $pack1 + $pack1sisa;



            
            $paket25kg = $pack25;
            $paket5kg = $pack5;
            $paket1kg = $pack1;
            // jumlah yang 25 kg
            for ($i = 0; $i < $paket25kg; $i++) {
                TbPoBahan::create([
                    'id_po'              => $request->idpo,
                    'id_bahan'           => $request->idbahan,
                    'jumlah_bahan'       => 25,
                    'satuan'             => $data_rincian_bahan_menu->id_satuan ?? 'N/A', // Pastikan tidak null
                    'jumlah_po'          => $this->calculateJumlahPoBySatuan((float) ($data_rincian_bahan_menu->harga ?? 0), 25, $data_rincian_bahan_menu->id_satuan),
                    //'jumlah_po'          => 0,
                    'tanggal_kedatangan' => $request->tanggal_kirim ?? date('Y-m-d', strtotime($request->tanggal_digunakan . ' -1 days')), // Default 2 hari sebelum tanggal digunakan,
                    'tanggal_digunakan'  => $request->tanggal_digunakan,
                    'id_rincian_bahan'   => $data_rincian_bahan_menu->id,
                    'buffer'             => $request->buffer,
                    'keterangan'         => '-',
                    'jumlah_box'         => 1,
                    //'id_rincian_kontrak' => $request->merek_bahan,
                    'id_rincian_kontrak' => $request->idmerek,
                    'id_kontrak' => 13
                ]);

                
            }
            // jumlah yang 5 kg
            for ($i = 0; $i < $paket5kg; $i++) {
                TbPoBahan::create([
                    'id_po'              => $request->idpo,
                    'id_bahan'           => $request->idbahan,
                    'jumlah_bahan'       => 5,
                    'satuan'             => $data_rincian_bahan_menu->id_satuan ?? 'N/A', // Pastikan tidak null
                    'jumlah_po'          => $this->calculateJumlahPoBySatuan((float) ($data_rincian_bahan_menu->harga ?? 0), 5, $data_rincian_bahan_menu->id_satuan),
                    //'jumlah_po'          => 0,
                    'tanggal_kedatangan' => $request->tanggal_kirim ?? date('Y-m-d', strtotime($request->tanggal_digunakan . ' -1 days')), // Default 2 hari sebelum tanggal digunakan,
                    'tanggal_digunakan'  => $request->tanggal_digunakan,
                    'id_rincian_bahan'   => $data_rincian_bahan_menu->id,
                    'buffer'             => $request->buffer,
                    'keterangan'         => '-',
                    'jumlah_box'         => 1,
                    //'id_rincian_kontrak' => $request->merek_bahan,
                    'id_rincian_kontrak' => $request->idmerek,
                    'id_kontrak' => 13
                ]);
                
            }
            // jumlah yang 1 kg
            for ($i = 0; $i < $paket1kg; $i++) {
                TbPoBahan::create([
                    'id_po'              => $request->idpo,
                    'id_bahan'           => $request->idbahan,
                    'jumlah_bahan'       => 1,
                    'satuan'             => $data_rincian_bahan_menu->id_satuan ?? 'N/A', // Pastikan tidak null
                    'jumlah_po'          => $this->calculateJumlahPoBySatuan((float) ($data_rincian_bahan_menu->harga ?? 0), 1, $data_rincian_bahan_menu->id_satuan),
                    //'jumlah_po'          => 0,
                    'tanggal_kedatangan' => $request->tanggal_kirim ?? date('Y-m-d', strtotime($request->tanggal_digunakan . ' -1 days')), // Default 2 hari sebelum tanggal digunakan,
                    'tanggal_digunakan'  => $request->tanggal_digunakan,
                    'id_rincian_bahan'   => $data_rincian_bahan_menu->id,
                    'buffer'             => $request->buffer,
                    'keterangan'         => '-',
                    'jumlah_box'         => 1,
                    //'id_rincian_kontrak' => $request->merek_bahan,
                    'id_rincian_kontrak' => $request->idmerek,
                    'id_kontrak'        => 13
                ]);
                
            }
        } else {

            /*for ($i = 0; $i < $data_rincian_bahan_menu->jumlah_box; $i++) {
                TbPoBahan::create([
                    'id_po'              => $request->idpo,
                    'id_bahan'           => $request->idbahan,
                    'jumlah_bahan'       => $data_rincian_bahan_menu->jumlah / $data_rincian_bahan_menu->jumlah_box,
                    'satuan'             => $data_rincian_bahan_menu->id_satuan ?? 'N/A', // Pastikan tidak null
                    'jumlah_po'          => $data_rincian_bahan_menu->total_harga / $data_rincian_bahan_menu->jumlah_box,
                    //'jumlah_po'          => 0,
                    'tanggal_kedatangan' => $request->tanggal_kirim ?? date('Y-m-d', strtotime($request->tanggal_digunakan . ' -1 days')), // Default 2 hari sebelum tanggal digunakan,
                    'tanggal_digunakan'  => $request->tanggal_digunakan,
                    'id_rincian_bahan'   => $data_rincian_bahan_menu->id,
                    'buffer'             => $request->buffer,
                    'keterangan'         => $data_rincian_bahan_menu->keterangan,
                    'jumlah_box'         => 1,
                    //'id_rincian_kontrak' => $request->merek_bahan,
                    'id_rincian_kontrak' => $request->idmerek,
                    'id_kontrak' => 13
                ]);
                
            }*/
            TbPoBahan::create([
                'id_po'              => $request->idpo,
                'id_bahan'           => $request->idbahan,
                'jumlah_bahan'       => $data_rincian_bahan_menu->jumlah ,
                'satuan'             => $data_rincian_bahan_menu->id_satuan ?? 'N/A', // Pastikan tidak null
                'jumlah_po'          => $this->calculateJumlahPoBySatuan(
                    (float) ($data_rincian_bahan_menu->harga ?? 0),
                    (float) ($data_rincian_bahan_menu->jumlah ?? 0),
                    $data_rincian_bahan_menu->id_satuan
                ),
                //'jumlah_po'          => 0,
                'tanggal_kedatangan' => $request->tanggal_kirim ?? date('Y-m-d', strtotime($request->tanggal_digunakan . ' -1 days')), // Default 2 hari sebelum tanggal digunakan,
                'tanggal_digunakan'  => $request->tanggal_digunakan,
                'id_rincian_bahan'   => $data_rincian_bahan_menu->id,
                'buffer'             => $request->buffer,
                'keterangan'         => $data_rincian_bahan_menu->keterangan,
                'jumlah_box'         => $data_rincian_bahan_menu->jumlah_box,
                //'id_rincian_kontrak' => $request->merek_bahan,
                'id_rincian_kontrak' => $request->idmerek,
                'id_kontrak' => 13
            ]);
        }
        

        TbBantuBahanPo::where('id', $request->idrincian)->update([
            'id_po'              => $request->idpo,
            'jumlah_bahan'       => $request->jumlah,
            'jumlah_po'          => $request->jumlahpo,
            //'jumlah_po'          => 0,
            'tanggal_kedatangan' => $request->tanggal_kirim ?? date('Y-m-d', strtotime($request->tanggal_digunakan . ' -1 days')), // Default 2 hari sebelum tanggal digunakan,
            'tanggal_digunakan'  => $request->tanggal_digunakan,
            // 'buffer'             => $request->buffer,
            // 'keterangan'         => $request->keterangan,   
        ]);
        return back()->with('success', 'Data berhasil disimpan');
        //return redirect()->route('rincian_pengajuan_po', ['id' => $po->id, 'id_menu' => $request->id_menu, 'id_kontrak' => $po->id_kontrak]);
    }

    public function simpan_draft_pengajuan_po($id, $id_menu)
    {
        $po = TbPo::where('id', $id)->first();
        #update tb po
        TbPoBahan::where('id_po', $id)->update([
            'id_kontrak'        => $po->id_kontrak
        ]);
        TbBantuBahanPo::where('id_po', $id)->update([
            'id_kontrak'        => $po->id_kontrak
        ]);
        $po->update([
            'status_po'         => 'pengajuan'
        ]);
        return redirect()->route('rincian_menu_po', $id_menu)->with(['success' => 'data Berhasil Disimpan']);
    }

    public function destroy($id)
    {
        $po = TbPo::where('id', $id)->first();
        //TbPoBahan::where('id_po', $id)->delete();
	$po->status_po = 'cancel';
        $po->update();

        if (!$po) {
            return redirect()->back()->with('error', 'Data tidak ditemukan!');
        }


        return redirect()->back()->with('success', 'Data berhasil dihapus!');
    }

    public function pdf_pengajuan_po_lama($id)
    {
        $dapur      = DataDapur::first();
        $po         = TbPo::where('id', $id)->first();
        $kontrak    = TbKontrak::where('id', $po->id_kontrak)->first();
        $Supplier   = Supplier::where('id', $kontrak->id_supplier)->first();
        $rincian_po = DB::table('tb_po_bahan as po_bahan')
            ->join('tb_po as po', 'po_bahan.id_po', 'po.id')
            ->join('tb_master_bahan as bahan', 'po_bahan.id_bahan', 'bahan.id')
            //->join('tb_rincian_kontrak', 'bahan.id', 'tb_rincian_kontrak.id_bahan')
            ->join('tb_rincian_kontrak', 'po_bahan.id_rincian_kontrak', 'tb_rincian_kontrak.id')
            ->join('tb_kontrak', 'tb_rincian_kontrak.id_kontrak', 'tb_kontrak.id')
            ->join('tb_satuan', 'bahan.satuan_bahan', 'tb_satuan.id')
            ->join('tb_satuan as satuan2', 'tb_rincian_kontrak.satuan_bahan', 'satuan2.id')

            ->where('po.id', $id)
            ->where('tb_kontrak.id', $po->id_kontrak)
            ->where('tb_rincian_kontrak.status', 1)
            ->select(
                DB::raw('@rownum := @rownum + 1 AS nomor_urut'),
                'bahan.bahan',
                'tb_rincian_kontrak.harga_bahan',
                DB::raw("COALESCE(tb_rincian_kontrak.merek_bahan, '-') as merek_bahan"),
                'po_bahan.tanggal_kedatangan',
                'po_bahan.jumlah_bahan',
                'tb_satuan.satuan',
                'po_bahan.jumlah_po',
                'po_bahan.keterangan',
                'satuan2.satuan as satuan_kontrak',
                'po_bahan.jumlah_box'

            )
            ->crossJoin(DB::raw('(SELECT @rownum := 0) AS r')) // Inisial
            ->get();
        $rincian_po_cek = DB::table('tb_po_bahan as po_bahan')
            ->join('tb_po as po', 'po_bahan.id_po', 'po.id')
            ->join('tb_master_bahan as bahan', 'po_bahan.id_bahan', 'bahan.id')
            //->join('tb_rincian_kontrak', 'bahan.id', 'tb_rincian_kontrak.id_bahan')
            ->join('tb_rincian_kontrak', 'po_bahan.id_rincian_kontrak', 'tb_rincian_kontrak.id')
            ->join('tb_kontrak', 'tb_rincian_kontrak.id_kontrak', 'tb_kontrak.id')
            ->join('tb_satuan', 'bahan.satuan_bahan', 'tb_satuan.id')
            ->join('tb_satuan as satuan2', 'tb_rincian_kontrak.satuan_bahan', 'satuan2.id')

            ->where('po.id', $id)
            ->where('tb_kontrak.id', $po->id_kontrak)
            ->where('tb_rincian_kontrak.status', 1)
            ->select(
                DB::raw('@rownum := @rownum + 1 AS nomor_urut'),
                'bahan.bahan',
                'tb_rincian_kontrak.harga_bahan',
                DB::raw("COALESCE(tb_rincian_kontrak.merek_bahan, '-') as merek_bahan"),
                'po_bahan.tanggal_kedatangan',
                'po_bahan.jumlah_bahan',
                'tb_satuan.satuan',
                'po_bahan.jumlah_po',
                'po_bahan.keterangan',
                'satuan2.satuan as satuan_kontrak',
                'po_bahan.jumlah_box'

            )
            ->crossJoin(DB::raw('(SELECT @rownum := 0) AS r')) // Inisial
            ->count();

        if ($rincian_po_cek == 0) {
            $rincian_po = DB::table('tb_po_bahan as po_bahan')
                ->join('tb_po as po', 'po_bahan.id_po', 'po.id')
                ->join('tb_master_bahan as bahan', 'po_bahan.id_bahan', 'bahan.id')
                ->join('tb_rincian_kontrak', 'bahan.id', 'tb_rincian_kontrak.id_bahan')
                ->join('tb_kontrak', 'tb_rincian_kontrak.id_kontrak', 'tb_kontrak.id')
                ->join('tb_satuan', 'bahan.satuan_bahan', 'tb_satuan.id')
                ->join('tb_satuan as satuan2', 'tb_rincian_kontrak.satuan_bahan', 'satuan2.id')

                ->where('po.id', $id)
                ->where('tb_kontrak.id', $po->id_kontrak)
                ->select(
                    DB::raw('@rownum := @rownum + 1 AS nomor_urut'),
                    'bahan.bahan',
                    'tb_rincian_kontrak.harga_bahan',
                    'po_bahan.tanggal_kedatangan',
                    'po_bahan.jumlah_bahan',
                    'tb_satuan.satuan',
                    'po_bahan.jumlah_po',
                    'satuan2.satuan as satuan_kontrak'

                )
                ->crossJoin(DB::raw('(SELECT @rownum := 0) AS r')) // Inisial
                ->get();
        }



        $total = DB::table('tb_po_bahan as po_bahan')
            ->join('tb_po as po', 'po_bahan.id_po', 'po.id')
            ->join('tb_master_bahan as bahan', 'po_bahan.id_bahan', 'bahan.id')
            ->join('tb_rincian_kontrak', 'bahan.id', 'tb_rincian_kontrak.id_bahan')
            ->join('tb_kontrak', 'tb_rincian_kontrak.id_kontrak', 'tb_kontrak.id')
            ->join('tb_satuan', 'bahan.satuan_bahan', 'tb_satuan.id')
            ->where('po.id', $id)
            ->where('tb_kontrak.id', $po->id_kontrak)
            ->select(
                DB::raw('@rownum := @rownum + 1 AS nomor_urut'),
                'bahan.bahan',
                'tb_rincian_kontrak.harga_bahan',
                'po_bahan.tanggal_kedatangan',
                'po_bahan.jumlah_bahan',
                'tb_satuan.satuan',
                'po_bahan.jumlah_po'

            )
            ->crossJoin(DB::raw('(SELECT @rownum := 0) AS r')) // Inisial
            ->sum('po_bahan.jumlah_po');
        $totalberas = DB::table('tb_po_bahan as po_bahan')
            ->join('tb_po as po', 'po_bahan.id_po', 'po.id')
            ->join('tb_master_bahan as bahan', 'po_bahan.id_bahan', 'bahan.id')
            ->join('tb_rincian_kontrak', 'bahan.id', 'tb_rincian_kontrak.id_bahan')
            ->join('tb_kontrak', 'tb_rincian_kontrak.id_kontrak', 'tb_kontrak.id')
            ->join('tb_satuan', 'bahan.satuan_bahan', 'tb_satuan.id')
            ->where('po.id', $id)
            ->where('tb_kontrak.id', $po->id_kontrak)
            ->where('bahan.bahan', 'beras')
            ->select(
                DB::raw('@rownum := @rownum + 1 AS nomor_urut'),
                'bahan.bahan',
                'tb_rincian_kontrak.harga_bahan',
                'po_bahan.tanggal_kedatangan',
                'po_bahan.jumlah_bahan',
                'tb_satuan.satuan',
                'po_bahan.jumlah_po'

            )
            ->crossJoin(DB::raw('(SELECT @rownum := 0) AS r')) // Inisial
            ->sum('po_bahan.jumlah_bahan');
        $totalBerasKg = $totalberas / 1000 ?? 0;
        // Packing beras
        /*$pack25 = floor($totalBerasKg / 25);
        $sisa = $totalBerasKg % 25;

        $pack5 = floor($sisa / 5);
        $sisa = $sisa % 5;

        $pack1 = $sisa > 0 ? 1 : 0;
        */
        $pack_utama = $totalBerasKg / 36;
        $pack25 = floor($pack_utama) * 1;
        $pack5 = floor($pack_utama) * 2;
        $pack1 = floor($pack_utama) * 1;
        $sisa = $totalBerasKg - floor($pack_utama) * 36;
        $pack5 = floor($sisa / 5) + $pack5;
        $pack1 = ($sisa % 5) + $pack1;

        if ($rincian_po_cek == 0) {
            $pdf = Pdf::loadView('office/PO.template_pengajuan_po_lama', compact(
                'po',
                'dapur',
                'kontrak',
                'Supplier',
                'rincian_po',
                'total',
                'pack25',
                'pack5',
                'pack1',
                'totalBerasKg'
            ));

            $tanggal_pengajuan = $po->tanggal_pengajuan = Carbon::parse($po->tanggal_pengajuan)->format('d-m-Y');
            return $pdf->download('Formulir_Pengajuan_PO ' . $tanggal_pengajuan . '.pdf');
        } else {
            $pdf = Pdf::loadView('office/PO.template_pengajuan_po', compact(
                'po',
                'dapur',
                'kontrak',
                'Supplier',
                'rincian_po',
                'total',
                'pack25',
                'pack5',
                'pack1',
                'totalBerasKg'
            ));

            $tanggal_pengajuan = $po->tanggal_pengajuan = Carbon::parse($po->tanggal_pengajuan)->format('d-m-Y');
            return $pdf->download('Formulir_Pengajuan_PO ' . $tanggal_pengajuan . '.pdf');
        }
    }

    public function pdf_pengajuan_po($id)
    {
        $dapur      = DataDapur::first();
        $po         = TbPo::where('id', $id)->first();
        $kontrak    = TbKontrak::where('id', $po->id_kontrak)->first();
        $Supplier   = Supplier::where('id', $kontrak->id_supplier)->first();
        DB::statement(DB::raw('SET @rownum = 0'));
        $rincian_po = DB::table('tb_po_bahan as po_bahan')
            ->join('tb_po as po', 'po_bahan.id_po', '=', 'po.id')
            ->join('tb_master_bahan as bahan', 'po_bahan.id_bahan', '=', 'bahan.id')
            ->join('rincian_menu_harian', 'po_bahan.id_rincian_bahan', '=', 'rincian_menu_harian.id')
            ->join('tb_rincian_kontrak', 'rincian_menu_harian.id_kontrak', '=', 'tb_rincian_kontrak.id')
            ->join('tb_resep', 'rincian_menu_harian.id_resep', '=', 'tb_resep.id')
            ->join('tb_satuan', 'rincian_menu_harian.id_satuan', '=', 'tb_satuan.id')
            ->join('tb_satuan as satuan2', 'tb_rincian_kontrak.satuan_bahan', '=', 'satuan2.id')
            ->selectRaw('
            @rownum := @rownum + 1 AS nomor_urut,
            bahan.bahan,
            po_bahan.tanggal_kedatangan,
            po_bahan.jumlah_bahan,
            po_bahan.jumlah_po,
            po_bahan.keterangan,
            tb_resep.nama_resep,
            po_bahan.jumlah_box,
            rincian_menu_harian.harga as harga_bahan,
            COALESCE(tb_rincian_kontrak.merek_bahan, "-") AS merek_bahan,
            tb_satuan.satuan,
            satuan2.satuan AS satuan_kontrak
        ')
            ->where('po.id', $id)
->where('po_bahan.jumlah_bahan', '>', 0)
            //->where('tb_rincian_kontrak.status', 1)
            ->get();
        $rincian_po_cek = DB::table('tb_po_bahan as po_bahan')
            ->join('tb_po as po', 'po_bahan.id_po', 'po.id')
            ->join('tb_master_bahan as bahan', 'po_bahan.id_bahan', 'bahan.id')
            //->join('tb_rincian_kontrak', 'bahan.id', 'tb_rincian_kontrak.id_bahan')
            ->join('tb_rincian_kontrak', 'po_bahan.id_rincian_kontrak', 'tb_rincian_kontrak.id')
            ->join('tb_kontrak', 'tb_rincian_kontrak.id_kontrak', 'tb_kontrak.id')
            ->join('tb_satuan', 'bahan.satuan_bahan', 'tb_satuan.id')
            ->join('tb_satuan as satuan2', 'tb_rincian_kontrak.satuan_bahan', 'satuan2.id')

            ->where('po.id', $id)
            ->where('tb_kontrak.id', $po->id_kontrak)
            ->where('tb_rincian_kontrak.status', 1)
            ->select(
                DB::raw('@rownum := @rownum + 1 AS nomor_urut'),
                'bahan.bahan',
                'tb_rincian_kontrak.harga_bahan',
                DB::raw("COALESCE(tb_rincian_kontrak.merek_bahan, '-') as merek_bahan"),
                'po_bahan.tanggal_kedatangan',
                'po_bahan.jumlah_bahan',
                'tb_satuan.satuan',
                'po_bahan.jumlah_po',
                'po_bahan.keterangan',
                'satuan2.satuan as satuan_kontrak',
                'po_bahan.jumlah_box'

            )
            ->crossJoin(DB::raw('(SELECT @rownum := 0) AS r')) // Inisial
            ->count();

        if ($rincian_po_cek == 0) {
            $rincian_po = DB::table('tb_po_bahan as po_bahan')
                ->join('tb_po as po', 'po_bahan.id_po', '=', 'po.id')
                ->join('tb_master_bahan as bahan', 'po_bahan.id_bahan', '=', 'bahan.id')
                ->join('rincian_menu_harian', 'po_bahan.id_rincian_bahan', '=', 'rincian_menu_harian.id')
                ->join('tb_resep', 'rincian_menu_harian.id_resep', '=', 'tb_resep.id')
                ->join('tb_rincian_kontrak', 'rincian_menu_harian.id_kontrak', '=', 'tb_rincian_kontrak.id')
                ->join('tb_satuan', 'rincian_menu_harian.id_satuan', '=', 'tb_satuan.id')
                ->join('tb_satuan as satuan2', 'tb_rincian_kontrak.satuan_bahan', '=', 'satuan2.id')
                ->selectRaw('
            @rownum := @rownum + 1 AS nomor_urut,
            bahan.bahan,
            po_bahan.tanggal_kedatangan,
            po_bahan.jumlah_bahan,
            po_bahan.jumlah_po,
            po_bahan.keterangan,
            po_bahan.jumlah_box,
            tb_resep.nama_resep,
            rincian_menu_harian.harga as harga_bahan,
            COALESCE(tb_rincian_kontrak.merek_bahan, "-") AS merek_bahan,
            tb_satuan.satuan,
            satuan2.satuan AS satuan_kontrak
        ')
                ->where('po.id', $id)
               // ->where('tb_rincian_kontrak.status', 1)
                ->get();
        }
        if(!$rincian_po)
        {
            $rincian_po = DB::table('tb_po_bahan as po_bahan')
                ->join('tb_po as po', 'po_bahan.id_po', 'po.id')
                ->join('tb_master_bahan as bahan', 'po_bahan.id_bahan', 'bahan.id')
                ->join('tb_rincian_kontrak', 'bahan.id', 'tb_rincian_kontrak.id_bahan')
                ->join('tb_kontrak', 'tb_rincian_kontrak.id_kontrak', 'tb_kontrak.id')
                ->join('tb_satuan', 'bahan.satuan_bahan', 'tb_satuan.id')
                ->join('tb_satuan as satuan2', 'tb_rincian_kontrak.satuan_bahan', 'satuan2.id')

                ->where('po.id', $id)
                ->where('tb_kontrak.id', $po->id_kontrak)
                ->select(
                    DB::raw('@rownum := @rownum + 1 AS nomor_urut'),
                    'bahan.bahan',
                    'tb_rincian_kontrak.harga_bahan',
                    'po_bahan.tanggal_kedatangan',
                    'po_bahan.jumlah_bahan',
                    'tb_satuan.satuan',
                    'po_bahan.jumlah_po',
                    'satuan2.satuan as satuan_kontrak'

                )
                ->crossJoin(DB::raw('(SELECT @rownum := 0) AS r')) // Inisial
                ->get();
        }


        $total = DB::table('tb_po_bahan as po_bahan')
            ->join('tb_po as po', 'po_bahan.id_po', 'po.id')
            ->join('tb_master_bahan as bahan', 'po_bahan.id_bahan', 'bahan.id')
            ->join('tb_rincian_kontrak', 'bahan.id', 'tb_rincian_kontrak.id_bahan')
            ->join('tb_kontrak', 'tb_rincian_kontrak.id_kontrak', 'tb_kontrak.id')
            ->join('tb_satuan', 'bahan.satuan_bahan', 'tb_satuan.id')
            ->where('po.id', $id)
            ->where('tb_kontrak.id', $po->id_kontrak)
            ->select(
                DB::raw('@rownum := @rownum + 1 AS nomor_urut'),
                'bahan.bahan',
                'tb_rincian_kontrak.harga_bahan',
                'po_bahan.tanggal_kedatangan',
                'po_bahan.jumlah_bahan',
                'tb_satuan.satuan',
                'po_bahan.jumlah_po'

            )
            ->crossJoin(DB::raw('(SELECT @rownum := 0) AS r')) // Inisial
            ->sum('po_bahan.jumlah_po');
        $totalberas =
            DB::table('tb_po_bahan as po_bahan')
            ->join('tb_po as po', 'po_bahan.id_po', '=', 'po.id')
            ->join('tb_master_bahan as bahan', 'po_bahan.id_bahan', '=', 'bahan.id')
            ->where('po.id', $id)
            ->where('bahan.bahan', 'beras')
            ->sum('po_bahan.jumlah_bahan');
        $totalBerasKg = $totalberas  ?? 0;
        // Packing beras
        /*$pack25 = floor($totalBerasKg / 25);
        $sisa = $totalBerasKg % 25;

        $pack5 = floor($sisa / 5);
        $sisa = $sisa % 5;

        $pack1 = $sisa > 0 ? 1 : 0;
        */
        $pack_utama = $totalBerasKg / 36;
        $pack25 = floor($pack_utama) * 1;
        $pack5 = floor($pack_utama) * 2;
        $pack1 = floor($pack_utama) * 1;
        $sisa = $totalBerasKg - floor($pack_utama) * 36;
        $pack5 = floor($sisa / 5) + $pack5;
        $pack1 = ($sisa % 5) + $pack1;

        if ($rincian_po_cek == 0) {
            $pdf = Pdf::loadView('office/PO.template_pengajuan_po_lama', compact(
                'po',
                'dapur',
                'kontrak',
                'Supplier',
                'rincian_po',
                'total',
                'pack25',
                'pack5',
                'pack1',
                'totalBerasKg'
            ));

            $tanggal_pengajuan = $po->tanggal_pengajuan = Carbon::parse($po->tanggal_pengajuan)->format('d-m-Y');
            return $pdf->download('Formulir_Pengajuan_PO ' . $tanggal_pengajuan . '.pdf');
        } else {
            $sample = DB::table('tb_po_bahan as po_bahan')
                ->join('tb_po as po', 'po_bahan.id_po', '=', 'po.id')
                ->join('rincian_menu_harian', 'po_bahan.id_rincian_bahan', '=', 'rincian_menu_harian.id')
                ->select('po_bahan.*', 'po_bahan.id_po', 'rincian_menu_harian.id_menu_harian')
               
                ->where('po.id', $id)
                //->where('tb_rincian_kontrak.status', 1)
                ->first();
            $menu = DB::table('tb_menu')->join('rincian_menu_harian', 'tb_menu.id' ,'rincian_menu_harian.id_menu_harian')
            ->where('rincian_menu_harian.id', $sample->id_menu_harian)->select('tb_menu.tanggal_kirim')->first();
            $rincian = DB::table('tb_po_bahan as po_bahan')
        ->join('tb_po as po', 'po_bahan.id_po', '=', 'po.id')
        ->join('tb_master_bahan as bahan', 'po_bahan.id_bahan', '=', 'bahan.id')
        ->join('rincian_menu_harian', 'po_bahan.id_rincian_bahan', '=', 'rincian_menu_harian.id')
        ->join('tb_satuan', 'rincian_menu_harian.id_satuan', '=', 'tb_satuan.id')
        ->select(
            'po_bahan.id_bahan',
            'bahan.bahan',
            'tb_satuan.satuan',
            DB::raw('SUM(po_bahan.jumlah_bahan) as total_jumlah_bahan')
        )
        ->where('po.id', $id)
        ->groupBy('po_bahan.id_bahan', 'bahan.bahan', 'tb_satuan.satuan')
        ->get();

    // Gabungkan jadi 1 string: "Beras 10 kg, Gula 5 kg, ..."
    $nama_bahan = $rincian->map(function ($row) {
        return $row->bahan . ' ' . $row->total_jumlah_bahan . ' ' . $row->satuan;
    })->implode(', ');
            $jumlah_porsi = DB::table('rincian_sekolah')->where('id_menu_harian', $sample->id_menu_harian)
            ->sum('jumlah_penerima_total');
            $cek_bahan = TbMasterBahan::where('id', $sample->id_bahan)->first();
            if($cek_bahan->jenis == 1)
            {
                $keterangan_bahan = 'Beras';
            } else if ($cek_bahan->jenis == 2) {
                $keterangan_bahan = 'Lauk';
            } elseif ($cek_bahan->jenis == 3) {
                $keterangan_bahan = 'Sayur';
            } elseif ($cek_bahan->jenis == 4) {
                $keterangan_bahan = 'Buah';
            } elseif ($cek_bahan->jenis == 5) {
                $keterangan_bahan = 'Pendamping';
            } elseif ($cek_bahan->jenis == 6) {
                $keterangan_bahan = 'Bumbu';
            } else {
                $keterangan_bahan = '--';
            } 
            
            $pdf = Pdf::loadView('office/PO.template_pengajuan_po', compact(
                'po',

                'dapur',
                'kontrak',
                'Supplier',
                'rincian_po',
                'total',
                'menu',
                'pack25',
                'pack5',
                'pack1',
                'totalBerasKg',
                'keterangan_bahan',
                'sample',
                'jumlah_porsi',
		'nama_bahan'
            ));            $tanggal_pengajuan = $po->tanggal_pengajuan = Carbon::parse($po->tanggal_pengajuan)->format('d-m-Y');
            return $pdf->download('Formulir_Pengajuan_PO ' . $tanggal_pengajuan . '.pdf');
        }
    }

    public function pdf_pengajuan_po_manual($id)
    {
        $documentData = $this->getManualPoDocumentData((int) $id);
        extract($documentData);

        $pdf = Pdf::loadView('office/PO.template_pengajuan_po_manual', compact(
            'po',
            'dapur',
            'kontrak',
            'Supplier',
            'rincian_po',
            'total',
            'pack25',
            'pack5',
            'pack1',
            'totalBerasKg'
        ));

        $tanggal_pengajuan = $po->tanggal_pengajuan = Carbon::parse($po->tanggal_pengajuan)->format('d-m-Y');
        return $pdf->download('Formulir_Pengajuan_PO ' . $tanggal_pengajuan . '.pdf');
    }

    public function docx_pengajuan_po_manual($id)
    {
        $documentData = $this->getManualPoDocumentData((int) $id);
        extract($documentData);

        $directory = storage_path('app/temp');
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $tanggalPengajuan = Carbon::parse($po->tanggal_pengajuan)->format('d-m-Y');
        $filePath = $directory . DIRECTORY_SEPARATOR . 'Formulir_Pengajuan_PO_' . $po->id . '_' . time() . '.docx';

        $zip = new ZipArchive();
        if ($zip->open($filePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            abort(500, 'Gagal membuat file DOCX.');
        }

        $zip->addFromString('[Content_Types].xml', $this->buildDocxContentTypesXml());
        $zip->addFromString('_rels/.rels', $this->buildDocxRootRelsXml());
        $zip->addFromString('word/document.xml', $this->buildManualPoDocxDocumentXml($documentData));
        $zip->addFromString('word/_rels/document.xml.rels', $this->buildDocxDocumentRelsXml());
        $zip->addFromString('word/styles.xml', $this->buildDocxStylesXml());
        $logoPath = public_path('image/logo.png');
        if (file_exists($logoPath)) {
            $zip->addFile($logoPath, 'word/media/logo.png');
        }
        $zip->close();

        return response()->download($filePath, 'Formulir_Pengajuan_PO ' . $tanggalPengajuan . '.docx')->deleteFileAfterSend(true);
    }

    protected function getManualPoDocumentData(int $id): array
    {
        $dapur = DataDapur::first();
        $po = TbPo::where('id', $id)->firstOrFail();
        $kontrak = TbKontrak::where('id', $po->id_kontrak)->first();
        $Supplier = Supplier::where('id', $kontrak->id_supplier)->first();
        $rincian_po = DB::table('tb_po_bahan as po_bahan')
            ->join('tb_po as po', 'po_bahan.id_po', 'po.id')
            ->join('tb_master_bahan as bahan', 'po_bahan.id_bahan', 'bahan.id')
            ->leftJoin('tb_satuan', 'po_bahan.satuan', 'tb_satuan.id')
            ->where('po.id', $id)
            ->select(
                DB::raw('@rownum := @rownum + 1 AS nomor_urut'),
                'bahan.bahan',
                'po_bahan.tanggal_kedatangan',
                'po_bahan.jumlah_bahan',
                'tb_satuan.satuan',
                'po_bahan.jumlah_po'
            )
            ->crossJoin(DB::raw('(SELECT @rownum := 0) AS r'))
            ->get();

        $total = DB::table('tb_po_bahan as po_bahan')
            ->join('tb_po as po', 'po_bahan.id_po', 'po.id')
            ->where('po.id', $id)
            ->sum('po_bahan.jumlah_po');

        $totalberas = DB::table('tb_po_bahan as po_bahan')
            ->join('tb_po as po', 'po_bahan.id_po', 'po.id')
            ->join('tb_master_bahan as bahan', 'po_bahan.id_bahan', 'bahan.id')
            ->where('po.id', $id)
            ->where('bahan.bahan', 'beras')
            ->sum('po_bahan.jumlah_bahan');

        $totalBerasKg = ($totalberas ?? 0) / 1000;
        $pack25 = floor($totalBerasKg / 25);
        $sisa = $totalBerasKg % 25;
        $pack5 = floor($sisa / 5);
        $sisa = $sisa % 5;
        $pack1 = $sisa > 0 ? 1 : 0;

        return compact('po', 'dapur', 'kontrak', 'Supplier', 'rincian_po', 'total', 'pack25', 'pack5', 'pack1', 'totalBerasKg');
    }

    protected function buildDocxContentTypesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Default Extension="png" ContentType="image/png"/>'
            . '<Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>'
            . '<Override PartName="/word/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.styles+xml"/>'
            . '</Types>';
    }

    protected function buildDocxRootRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>'
            . '</Relationships>';
    }

    protected function buildDocxDocumentRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/image" Target="media/logo.png"/>'
            . '</Relationships>';
    }

    protected function buildDocxStylesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<w:styles xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">'
            . '<w:style w:type="paragraph" w:default="1" w:styleId="Normal"><w:name w:val="Normal"/></w:style>'
            . '</w:styles>';
    }

    protected function buildManualPoDocxDocumentXml(array $documentData): string
    {
        extract($documentData);

        $rows = '';
        foreach ($rincian_po as $row) {
            $hargaSatuan = (float) ($row->jumlah_bahan ?? 0) > 0 ? ((float) $row->jumlah_po / (float) $row->jumlah_bahan) : 0;
            $rows .= $this->buildDocxTableRow([
                ['text' => (string) $row->nomor_urut, 'align' => 'center'],
                ['text' => (string) $row->bahan],
                ['text' => number_format((float) $row->jumlah_bahan, 0, ',', '.'), 'align' => 'right'],
                ['text' => (string) ($row->satuan ?? ''), 'align' => 'right'],
                ['text' => number_format($hargaSatuan, 0, ',', '.'), 'align' => 'right'],
                ['text' => number_format((float) $row->jumlah_po, 0, ',', '.'), 'align' => 'right'],
            ]);
        }

        $rows .= $this->buildDocxTableRow([
            ['text' => 'Total', 'align' => 'center', 'gridSpan' => 5, 'bold' => true],
            ['text' => 'Rp.' . number_format((float) $total, 0, ',', '.'), 'align' => 'right', 'bold' => true],
        ]);

        $tanggalPo = strtolower(Carbon::parse($po->tanggal_po)->locale('id')->translatedFormat('l, d F Y'));
        $tanggalPengajuan = strtolower(Carbon::parse($po->tanggal_pengajuan)->locale('id')->translatedFormat('l, d F Y'));

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" xmlns:wp="http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing" xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" xmlns:pic="http://schemas.openxmlformats.org/drawingml/2006/picture">'
            . '<w:body>'
            . $this->buildDocxHeaderTable()
            . $this->buildDocxParagraph((string) ($dapur->nama_dapur ?? ''), 'center', true, 24)
            . $this->buildDocxParagraph(trim((string) (($dapur->kecamatan ?? '') . ' - ' . ($dapur->kota ?? ''))), 'center', false, 20)
            . $this->buildDocxParagraph('')
            . $this->buildDocxParagraph('NO.PO : ' . (string) ($po->nomor_po ?? ''), 'right', true)
            . $this->buildDocxParagraph('Tanggal Pemesanan : ' . $tanggalPo, 'right')
            . $this->buildDocxParagraph('Tanggal Pengiriman : ' . $tanggalPengajuan, 'right')
            . $this->buildDocxParagraph('')
            . '<w:tbl>'
            . $this->buildDocxTableProperties()
            . $this->buildDocxTableRow([
                ['text' => 'No', 'align' => 'center', 'bold' => true],
                ['text' => 'Nama Pesanan', 'align' => 'center', 'bold' => true],
                ['text' => 'Qty', 'align' => 'center', 'bold' => true],
                ['text' => 'Satuan', 'align' => 'center', 'bold' => true],
                ['text' => 'Harga Satuan', 'align' => 'center', 'bold' => true],
                ['text' => 'Harga Total', 'align' => 'center', 'bold' => true],
            ])
            . $rows
            . '</w:tbl>'
            . $this->buildDocxParagraph('')
                . $this->buildDocxSignatureTable((string) ($dapur->ahli_akuntan ?? ''), (string) ($dapur->kepala_dapur ?? ''))
            . '<w:sectPr><w:pgSz w:w="11906" w:h="16838"/><w:pgMar w:top="720" w:right="720" w:bottom="720" w:left="720" w:header="708" w:footer="708" w:gutter="0"/></w:sectPr>'
            . '</w:body></w:document>';
    }

            protected function buildDocxHeaderTable(): string
            {
            return '<w:tbl>'
                . $this->buildDocxBorderlessTableProperties()
                . '<w:tr>'
                . '<w:tc><w:tcPr><w:tcW w:w="2600" w:type="dxa"/></w:tcPr><w:p><w:r>' . $this->buildDocxImageDrawing('rId2') . '</w:r></w:p></w:tc>'
                . '<w:tc><w:tcPr><w:tcW w:w="7000" w:type="dxa"/></w:tcPr>'
                . '<w:p><w:pPr><w:jc w:val="right"/></w:pPr><w:r><w:rPr><w:b/><w:sz w:val="32"/></w:rPr><w:t>PURCHASE ORDER</w:t></w:r></w:p>'
                . '</w:tc>'
                . '</w:tr>'
                . '</w:tbl>';
            }

            protected function buildDocxSignatureTable(string $leftName, string $rightName): string
            {
            return '<w:tbl>'
                . $this->buildDocxBorderlessTableProperties()
                . '<w:tr>'
                . '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="dxa"/></w:tcPr><w:p><w:r><w:t>Ahli Akuntan</w:t></w:r></w:p></w:tc>'
                . '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="dxa"/></w:tcPr><w:p><w:pPr><w:jc w:val="right"/></w:pPr><w:r><w:t>Kepala Dapur</w:t></w:r></w:p></w:tc>'
                . '</w:tr>'
                . '<w:tr>'
                . '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="dxa"/></w:tcPr><w:p><w:r><w:t xml:space="preserve"> </w:t></w:r></w:p><w:p><w:r><w:t xml:space="preserve"> </w:t></w:r></w:p></w:tc>'
                . '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="dxa"/></w:tcPr><w:p><w:r><w:t xml:space="preserve"> </w:t></w:r></w:p><w:p><w:r><w:t xml:space="preserve"> </w:t></w:r></w:p></w:tc>'
                . '</w:tr>'
                . '<w:tr>'
                . '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="dxa"/></w:tcPr><w:p><w:r><w:t>' . $this->escapeDocxText($leftName) . '</w:t></w:r></w:p></w:tc>'
                . '<w:tc><w:tcPr><w:tcW w:w="5000" w:type="dxa"/></w:tcPr><w:p><w:pPr><w:jc w:val="right"/></w:pPr><w:r><w:t>' . $this->escapeDocxText($rightName) . '</w:t></w:r></w:p></w:tc>'
                . '</w:tr>'
                . '</w:tbl>';
            }

    protected function buildDocxParagraph(string $text, string $align = 'left', bool $bold = false, int $size = 22): string
    {
        $jc = in_array($align, ['center', 'right', 'left'], true) ? $align : 'left';
        $boldXml = $bold ? '<w:b/>' : '';

        return '<w:p>'
            . '<w:pPr><w:jc w:val="' . $jc . '"/></w:pPr>'
            . '<w:r><w:rPr>' . $boldXml . '<w:sz w:val="' . $size . '"/></w:rPr><w:t xml:space="preserve">' . $this->escapeDocxText($text) . '</w:t></w:r>'
            . '</w:p>';
    }

    protected function buildDocxTableProperties(): string
    {
        return '<w:tblPr>'
            . '<w:tblBorders>'
            . '<w:top w:val="single" w:sz="8" w:space="0" w:color="000000"/>'
            . '<w:left w:val="single" w:sz="8" w:space="0" w:color="000000"/>'
            . '<w:bottom w:val="single" w:sz="8" w:space="0" w:color="000000"/>'
            . '<w:right w:val="single" w:sz="8" w:space="0" w:color="000000"/>'
            . '<w:insideH w:val="single" w:sz="8" w:space="0" w:color="000000"/>'
            . '<w:insideV w:val="single" w:sz="8" w:space="0" w:color="000000"/>'
            . '</w:tblBorders>'
            . '</w:tblPr>';
    }

    protected function buildDocxBorderlessTableProperties(): string
    {
        return '<w:tblPr>'
            . '<w:tblBorders>'
            . '<w:top w:val="nil"/><w:left w:val="nil"/><w:bottom w:val="nil"/><w:right w:val="nil"/><w:insideH w:val="nil"/><w:insideV w:val="nil"/>'
            . '</w:tblBorders>'
            . '</w:tblPr>';
    }

    protected function buildDocxImageDrawing(string $relationshipId): string
    {
        return '<w:drawing>'
            . '<wp:inline distT="0" distB="0" distL="0" distR="0">'
            . '<wp:extent cx="914400" cy="914400"/>'
            . '<wp:docPr id="1" name="Logo"/>'
            . '<a:graphic xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main">'
            . '<a:graphicData uri="http://schemas.openxmlformats.org/drawingml/2006/picture">'
            . '<pic:pic xmlns:pic="http://schemas.openxmlformats.org/drawingml/2006/picture">'
            . '<pic:nvPicPr><pic:cNvPr id="0" name="logo.png"/><pic:cNvPicPr/></pic:nvPicPr>'
            . '<pic:blipFill><a:blip r:embed="' . $relationshipId . '"/><a:stretch><a:fillRect/></a:stretch></pic:blipFill>'
            . '<pic:spPr><a:xfrm><a:off x="0" y="0"/><a:ext cx="914400" cy="914400"/></a:xfrm><a:prstGeom prst="rect"><a:avLst/></a:prstGeom></pic:spPr>'
            . '</pic:pic>'
            . '</a:graphicData>'
            . '</a:graphic>'
            . '</wp:inline>'
            . '</w:drawing>';
    }

    protected function buildDocxTableRow(array $cells): string
    {
        $xml = '<w:tr>';

        foreach ($cells as $cell) {
            $align = $cell['align'] ?? 'left';
            $bold = !empty($cell['bold']) ? '<w:b/>' : '';
            $gridSpan = isset($cell['gridSpan']) ? '<w:gridSpan w:val="' . (int) $cell['gridSpan'] . '"/>' : '';

            $xml .= '<w:tc>'
                . '<w:tcPr>' . $gridSpan . '</w:tcPr>'
                . '<w:p>'
                . '<w:pPr><w:jc w:val="' . $align . '"/></w:pPr>'
                . '<w:r><w:rPr>' . $bold . '</w:rPr><w:t xml:space="preserve">' . $this->escapeDocxText((string) ($cell['text'] ?? '')) . '</w:t></w:r>'
                . '</w:p>'
                . '</w:tc>';
        }

        return $xml . '</w:tr>';
    }

    protected function escapeDocxText(string $text): string
    {
        return htmlspecialchars($text, ENT_XML1 | ENT_COMPAT, 'UTF-8');
    }

    public function cheklist_penerimaan_bgn(Request $request, $id)
    {
        $dapur = DataDapur::first();
        $po = TbPo::where('id', $id)->firstOrFail();
        $orientation = 'landscape';

        $tanggalChecklist = DB::table('tb_po_bahan')
            ->where('id_po', $id)
            ->orderBy('tanggal_kedatangan', 'asc')
            ->value('tanggal_kedatangan');

        if (!$tanggalChecklist) {
            abort(404, 'Tanggal kedatangan PO tidak ditemukan.');
        }

        $tanggalChecklist = Carbon::parse($tanggalChecklist)->toDateString();

        $rincian_po = DB::table('tb_po_bahan as po_bahan')
            ->join('tb_po as po', 'po_bahan.id_po', '=', 'po.id')
            ->join('tb_master_bahan as bahan', 'po_bahan.id_bahan', '=', 'bahan.id')
            ->leftJoin('tb_satuan', 'po_bahan.satuan', '=', 'tb_satuan.id')
            ->whereDate('po_bahan.tanggal_kedatangan', $tanggalChecklist)
            ->whereIn('po.status_po', ['acc', 'bayar'])
            ->select(
                'po.id as id_po',
                'po.nomor_po',
                'bahan.bahan as nama_pesanan',
                'po_bahan.jumlah_bahan as qty',
                'tb_satuan.satuan as satuan',
                'po_bahan.tanggal_kedatangan as waktu_kedatangan'
            )
            ->orderBy('po.nomor_po', 'asc')
            ->orderBy('bahan.bahan', 'asc')
            ->orderBy('po_bahan.id', 'asc')
            ->get();

        $pdf = Pdf::loadView('office.penerimaan.pdf.cheklist_penerimaan_bgn', compact(
            'po',
            'dapur',
            'rincian_po',
            'tanggalChecklist',
            'orientation'
        ))->setPaper('A4', $orientation);

        return $pdf->download('Checklist_Penerimaan_BGN_' . Carbon::parse($tanggalChecklist)->format('d-m-Y') . '_' . $orientation . '.pdf');
    }

    public function template()
    {
        return view('office/PO.template_pengajuan_po');
    }


    public function pilih_menu()
    {

        $header     = "List Menu Belum PO";
        if (request()->ajax()) {
            //$users = User::query();
            $Menu = Menu::join('tb_resep as m_karbo', 'tb_menu.karbohidrat', '=', 'm_karbo.id')
                ->join('tb_resep as m_protein', 'tb_menu.protein', '=', 'm_protein.id')
                ->join('tb_resep as m_sayur', 'tb_menu.sayur', '=', 'm_sayur.id')
                ->join('tb_resep as m_buah', 'tb_menu.buah', '=', 'm_buah.id')
                ->join('tb_resep as m_susu', 'tb_menu.susu', '=', 'm_susu.id')
                ->whereIn('status_pengajuan', ['pending', 'kirim po', 'approved'])
                ->select(

                    'tb_menu.*',
                    'm_karbo.nama_resep as nama_karbohidrat',
                    'm_protein.nama_resep as nama_protein',
                    'm_sayur.nama_resep as nama_sayur',
                    'm_buah.nama_resep as nama_buah',
                    'm_susu.nama_resep as nama_susu'
                )
                ->latest()
                ->get();

            //$Menu = Menu::all();
            return DataTables::of($Menu)
                ->addIndexColumn() // Menambah index

                ->addColumn('action', function ($row) {
                    if ($row->status_pengajuan == 'pending' || $row->status_pengajuan == 'approved') {
                        $btn = '<a href="' . route('rincian_menu_po', $row['id']) . '" class="edit btn btn-success btn-sm delete-button" id="btn-delete-post " data-id="' . $row['id'] . '" >Buat PO</a>';
                    } else if ($row->status_pengajuan == 'kirim po') {
                        $btn = '<a href="' . route('rincian_menu_po', $row['id']) . '" class="edit btn btn-success btn-sm delete-button" id="btn-delete-post " data-id="' . $row['id'] . '" >rincian PO</a>';
                    }
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('office/PO.index_menu', compact('header'));
    }

    private function ensureRincianKontrakId(?int $idKontrak, int $idBahan, float $hargaBahan, ?int $idSatuan = null): ?int
    {
        if (!$idKontrak) {
            return null;
        }

        $rincian = TbRincianKontrak::where('id_kontrak', $idKontrak)
            ->where('id_bahan', $idBahan)
            ->where('status', 1)
            ->orderByDesc('id')
            ->first();

        if ($rincian) {
            return $rincian->id;
        }

        $masterBahan = TbMasterBahan::find($idBahan);

        $newRincian = TbRincianKontrak::create([
            'id_kontrak'  => $idKontrak,
            'id_bahan'    => $idBahan,
            'merek_bahan' => $masterBahan->bahan ?? '-',
            'harga_bahan' => $hargaBahan,
            'jumlah_bahan'=> 1,
            'satuan_bahan'=> $idSatuan ?? 0,
            'status'      => 1,
            'kemasan'     => '-',
        ]);

        return $newRincian->id;
    }

    public function rincian_bahan_menu($id)
    {

        $menu       = Menu::where('id', $id)->first();
        setlocale(LC_TIME, 'id_ID.utf8', 'Indonesian');
        $tanggal = Carbon::parse($menu->tanggal_kirim)
    ->locale('id')
    ->translatedFormat('l, d F Y');
        $kontrak = TbKontrak::join('tb_supplier', 'tb_kontrak.id_supplier', 'tb_supplier.id')
            ->select('tb_kontrak.id', 'tb_supplier.nama_supplier')->get();
        $header     = "Rincian Menu " . $tanggal;
        $data_rincian_kontrak_supplier = DB::table('rincian_menu_harian as rmh')
            ->leftJoin('tb_rincian_kontrak as rk', function ($join) {
                $join->on('rk.id_bahan', '=', 'rmh.id_bahan')
                    ->where('rk.status', 1);
            })
            ->where('rmh.id_menu_harian', $id)
            ->select(
                'rmh.id',
                'rmh.id_menu_harian',
                'rmh.id_bahan',
                DB::raw("CASE WHEN rk.status = 1 THEN 'bahan ada' ELSE 'bahan kosong' END AS keterangan")
            )
            ->get();
        $totalKosong = DB::table('rincian_menu_harian as rmh')
            ->leftJoin('tb_rincian_kontrak as rk', function ($join) {
                $join->on('rk.id_bahan', '=', 'rmh.id_bahan')
                    ->where('rk.status', 1);
            })
            ->where('rmh.id_menu_harian', $id)
            ->whereNull('rk.id_bahan')
            ->count();

        if (request()->ajax()) {
            
            $data = DB::table('rincian_menu_harian as rmh')
                ->leftJoin('tb_rincian_kontrak as rk', function ($join) {
                    $join->on('rk.id_bahan', '=', 'rmh.id_bahan')
                        ->where('rk.status', 1);
                })
                ->join('tb_master_bahan as mb', 'rmh.id_bahan', '=', 'mb.id')
                ->where('rmh.id_menu_harian', $id)
                ->select(
                    'rmh.id',
                    'rmh.id_bahan',
                    'rmh.id_satuan',
                    'mb.bahan',
                    'mb.satuan_bahan',
                    'rmh.id_resep',
                    'rmh.id_menu_harian',
                    'rmh.jumlah',
                    DB::raw("CASE WHEN rk.status = 1 THEN 'bahan ada' ELSE 'bahan kosong' END AS keterangan")
                )
                //->groupBy('rmh.id_menu_harian')
                ->get();
            //$Menu = Menu::all();
            return DataTables::of($data)
                ->addIndexColumn() // Menambah index
                ->addColumn('nama_resep', function ($row) {

                    if ($row->id_resep == 0) {
                        return 'tambahan';
                    } else {
                        $nama_resep = Resep::find($row->id_resep);
                        return $nama_resep->nama_resep;
                    }
                })
                ->addColumn('total_berat', function ($row) {
                    // $cek_data = TbPoBahan::where('id_rincian_bahan')->first();
                    return number_format($row->jumlah, 0, ',', '.') . '';
                })
                ->addColumn('satuan', function ($row) {
                    $cek_data = TbPoBahan::where('id_rincian_bahan', $row->id)->first();
                    /*if ($cek_data) {
                       $satuan = TbSatuan::find($row->id_satuan);
                        return $satuan->satuan;
                    } else {
                        $satuan = TbSatuan::find($row->satuan_bahan);
                        return $satuan->satuan;
                    }*/
                    $satuan = TbSatuan::find($row->id_satuan);
                        return $satuan->satuan;
                })

                ->addColumn('total_po', function ($row) {
                    $cek_data = TbPoBahan::where('id_rincian_bahan', $row->id)->first();

                    if ($cek_data) {
                        $total = TbPoBahan::where('id_rincian_bahan', $row->id)->sum('jumlah_po');
                        return 'Rp. ' . number_format($total, 0, ',', '.');
                    } else {
                        return 'Rp. 0';
                    }
                })
                ->addColumn('action', function ($row) {
                    // Check if contract detail exists for this material
                    $rincian_kontrak = TbRincianKontrak::where('id_bahan', $row->id_bahan)
                        ->where('status', 1)
                        ->first();

                    if (!$rincian_kontrak) {
                        // No contract detail - show input button and delete button
                        $inputBtn = '<a href="' . route('rincian_menu_po.form_input_kontrak', $row->id) . '" class="btn btn-warning btn-sm"><i class="fas fa-plus"></i> Input Kontrak</a>';
                        $deleteBtn = '<a href="' . route('rincian_menu_po.hapus', $row->id) . '" class="btn btn-danger btn-sm" onclick="return confirm(\'Hapus item ini?\');" style="margin-left:5px;"><i class="fas fa-trash"></i></a>';
                        return $inputBtn . ' ' . $deleteBtn;
                    }

                    // Contract detail exists - show PO info
                    $cek_data = TbPoBahan::join('tb_po', 'tb_po_bahan.id_po', 'tb_po.id')
                        ->where('tb_po_bahan.id_rincian_bahan', $row->id)
                        ->select('tb_po.id_kontrak')
                        ->first();

                    if ($cek_data) {
                        $data =  TbKontrak::join('tb_supplier', 'tb_kontrak.id_supplier', 'tb_supplier.id')
                            ->where('tb_kontrak.id', $cek_data->id_kontrak)
                            ->select('tb_kontrak.id', 'tb_supplier.nama_supplier')->first();
                        $btn = !empty($data['id']) ?
                            $data->nama_supplier : 'belum PO';
                        return $btn;
                    } else {
                        return 'belum PO';
                    }
                })
                ->addColumn('no_po', function ($row) {
                    $cek_data = TbPoBahan::where('id_rincian_bahan', $row->id)->first();
                    $cek_po   = TbPo::find($cek_data->id_po ?? 0);
                    if ($cek_data) {
                        return $cek_po->nomor_po ?? '-';
                    } else {
                        return '-';
                    }
                })
                ->rawColumns(['action', 'total_po', 'satuan', 'nama_resep', 'no_po'])
                ->make(true);
        }
        return view('office/PO.rincian_menu', compact('header', 'kontrak', 'menu', 'totalKosong'));
    }

    public function input_supplier($id)
    {
        $header     = "Input Supplier ";
        $pobahan = TbBantuBahanPo::where('id', $id)->first();

        $kontrak = TbKontrak::join('tb_supplier', 'tb_kontrak.id_supplier', 'tb_supplier.id')
            ->where('id_bahan', $pobahan->id_bahan)->select('tb_kontrak.id', 'tb_supplier.nama_supplier')->get();


        return view('office/PO.create_supplier', compact('header', 'pobahan', 'kontrak'));
    }

    public function simpan_rincian_menu_po($id)
    {
        $po = TbPo::join('tb_bantu_bahan_po', 'tb_po.id', 'tb_bantu_bahan_po.id_po')
            ->where('id_menu_harian', $id)->get();
        $menu = Menu::where('id', $id)->first();
        foreach ($po as $data) {
            $data->update([
                'status_po'     => 'menunggu',
            ]);
        }
        $menu->update([
            'status_pengajuan'  => 'kirim po',
        ]);
        return redirect()->route('pilih_menu_po')->with(['success' => 'data diajukan']);
    }

    public function updatemanualpo(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $po = TbPo::find($request->id);
        $po->status_po = 'acc';
        $po->tanggal_approve = now()->format("Y-m-d");
        $po->save();

        return response()->json(['message' => $po->nomor_po . ' ACC !']);
    }

    public function updatemanualclose(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $po = TbPo::find($request->id);
        $po->status_po = 'close';
        //$po->tanggal_approve = now()->format("Y-m-d");
        $po->save();

        return response()->json(['message' => $po->nomor_po . ' Close !']);
    }

    //===============================================API==================================//

    public function edit_po(string $id): View
    {
        // Mendefinisikan header untuk halaman
        $header = "Edit PO";

        // Mengambil data dapur berdasarkan ID (ID yang diberikan adalah 1)
        $dapur  = DataDapur::findOrFail(1);

        // Mengambil data Purchase Order (PO) berdasarkan ID PO yang diterima sebagai parameter
        $po         = TbPo::findOrFail($id);
        $data_po    = TbPoBahan::where('id_po', $id)->first()??0;
        $karbohidrat         = '-';
        $protein             = '-';
        $sayur               = '-';
        $buah                = '-';
        $susu                = '-';
        $keterangan          = $data_po->keterangan;
 	$tanggal_menu = Carbon::parse($po->tanggal_pengajuan)
            ->locale('id')  // Menggunakan bahasa Indonesia untuk format tanggal
            ->isoFormat('dddd, D MMMM YYYY'); // Format tanggal: hari, tanggal bulan tahun (misal: Senin, 1 Januari 2025)

        if($data_po->id_rincian_bahan)
        {
            $sample_rincian_menu = rincian_menu_harian::find($data_po->id_rincian_bahan);
            $menu                = Menu::find($sample_rincian_menu->id_menu_harian);
            $karbohidrat         = Resep::find($menu->karbohidrat)->nama_resep;
            $protein             = Resep::find($menu->protein)->nama_resep;
            $sayur               = Resep::find($menu->sayur)->nama_resep;
            $buah                = Resep::find($menu->buah)->nama_resep;
            $susu                = Resep::find($menu->susu)->nama_resep;
	    $tanggal_menu = Carbon::parse($menu->tanggal_kirim)
            ->locale('id')  // Menggunakan bahasa Indonesia untuk format tanggal
            ->isoFormat('dddd, D MMMM YYYY'); // Format tanggal: hari, tanggal bulan tahun (misal: Senin, 1 Januari 2025)

        }
        
        // Mengatur tanggal pengajuan PO dengan format tanggal dalam bahasa Indonesia
        $tanggal_pengajuan = Carbon::parse($po->tanggal_pengajuan)
            ->locale('id')  // Menggunakan bahasa Indonesia untuk format tanggal
            ->isoFormat('dddd, D MMMM YYYY'); // Format tanggal: hari, tanggal bulan tahun (misal: Senin, 1 Januari 2025)
	

        // Mengambil data kontrak yang terkait dengan PO berdasarkan ID kontrak yang ada pada PO
        $kontrak    = TbKontrak::findOrFail($po->id_kontrak);

        // Mengambil data supplier berdasarkan ID supplier yang terdapat dalam data kontrak
        $supplier   = Supplier::findOrFail($kontrak->id_supplier);
        $is_po_bahan_pangan = ((int) $po->manual === 0);
        $id_menu_harian = null;
        $menu_tipe_options = [];

        if ($is_po_bahan_pangan) {
            $id_menu_harian = DB::table('tb_po_bahan')
                ->join('rincian_menu_harian', 'tb_po_bahan.id_rincian_bahan', '=', 'rincian_menu_harian.id')
                ->where('tb_po_bahan.id_po', $id)
                ->where('tb_po_bahan.id_rincian_bahan', '>', 0)
                ->value('rincian_menu_harian.id_menu_harian');

            if (!$id_menu_harian) {
                $id_menu_harian = Menu::whereDate('tanggal_kirim', $po->tanggal_pengajuan)
                    ->orderByDesc('id')
                    ->value('id');
            }

            if ($id_menu_harian) {
                $menuHeader = Menu::find($id_menu_harian);
                if ($menuHeader) {
                    $buildMenuOption = function ($key, $labelPrefix, $resepId) use ($id, $id_menu_harian, $po) {
                        $referensiTanggal = DB::table('tb_po_bahan')
                            ->join('rincian_menu_harian', 'tb_po_bahan.id_rincian_bahan', '=', 'rincian_menu_harian.id')
                            ->where('tb_po_bahan.id_po', $id)
                            ->where('rincian_menu_harian.id_menu_harian', $id_menu_harian)
                            ->where('rincian_menu_harian.id_resep', $resepId)
                            ->whereNotNull('tb_po_bahan.tanggal_kedatangan')
                            ->whereNotNull('tb_po_bahan.tanggal_digunakan')
                            ->select('tb_po_bahan.tanggal_kedatangan', 'tb_po_bahan.tanggal_digunakan')
                            ->orderByDesc('tb_po_bahan.id')
                            ->first();

                        $defaultTanggalDigunakan = Carbon::parse($po->tanggal_pengajuan)->format('Y-m-d');
                        $defaultTanggalKedatangan = Carbon::parse($po->tanggal_pengajuan)->subDay()->format('Y-m-d 08:00');

                        if ($referensiTanggal) {
                            $defaultTanggalDigunakan = Carbon::parse($referensiTanggal->tanggal_digunakan)->format('Y-m-d');
                            $defaultTanggalKedatangan = Carbon::parse($referensiTanggal->tanggal_kedatangan)->format('Y-m-d H:i');
                        }

                        return [
                            'key' => $key,
                            'label' => $labelPrefix . ' - ' . (optional(Resep::find($resepId))->nama_resep ?? '-'),
                            'resep_id' => $resepId,
                            'tanggal_kedatangan' => str_replace(' ', 'T', $defaultTanggalKedatangan),
                            'tanggal_digunakan' => $defaultTanggalDigunakan,
                        ];
                    };

                    $menu_tipe_options = [
                        $buildMenuOption('karbohidrat', 'Karbohidrat', $menuHeader->karbohidrat),
                        $buildMenuOption('protein', 'Protein', $menuHeader->protein),
                        $buildMenuOption('sayur', 'Sayur', $menuHeader->sayur),
                        $buildMenuOption('buah', 'Buah', $menuHeader->buah),
                        $buildMenuOption('susu', 'Susu', $menuHeader->susu),
                    ];
                }
            }
        }
        
        // Mengambil semua satuan untuk dropdown
        $satuan_list = TbSatuan::all();
        // Mengambil semua bahan untuk modal tambah data
        $bahan_list = TbMasterBahan::orderBy('bahan', 'asc')->get();

        // Merender tampilan 'edit_index_rincian_po' dan mengirimkan data yang telah diambil ke tampilan
        return view('office/PO.edit_index_rincian_po', compact(
            'dapur', 'po', 'tanggal_pengajuan', 'kontrak', 'supplier', 'header'
            ,'karbohidrat','protein','sayur','buah','susu','tanggal_menu',
            'keterangan', 'satuan_list', 'bahan_list', 'is_po_bahan_pangan', 'id_menu_harian', 'menu_tipe_options'
        ));
    }


    public function dt_bahan_edit_po(string $id)
    {
        // Mengambil data bahan berdasarkan ID PO
        $table = TbPoBahan::where('id_po', $id)
            ->get();

        // Menggunakan DataTables untuk menampilkan data dalam format JSON
        return DataTables::of($table)
            ->addIndexColumn() // Menambahkan kolom indeks untuk nomor urut

            // Mengedit kolom 'Bahan' untuk menampilkan nama bahan berdasarkan ID
            ->editColumn('Bahan_baku', function ($row) {
                // Mengambil data bahan dari tabel TbMasterBahan berdasarkan ID bahan
                Carbon::setLocale('id');
                $bahan = TbMasterBahan::findOrFail($row->id_bahan);
                $tanggalKedatangan = Carbon::parse($row->tanggal_kedatangan)->isoFormat('dddd, D MMMM YYYY HH:mm:ss');

                // Mengembalikan nama bahan, jika tidak ada maka menampilkan '-'
                return $bahan->bahan . ' | ' . $tanggalKedatangan ?? '-';
            })

            // Menambahkan kolom 'Jumlah_Bahan_baku' yang menampilkan jumlah bahan beserta satuannya
            ->addColumn('Jumlah_Bahan_baku', function ($row) {
                // Gunakan satuan yang tersimpan pada baris PO agar hasil edit langsung terlihat di tabel.
                $satuan = TbSatuan::find($row->satuan);
                
                // Menampilkan jumlah bahan dengan format 2 desimal dan menambahkan satuan bahan
                return number_format($row->jumlah_bahan, 2, ',', '.') . " " . ($satuan->satuan ?? '-');
            })

            // Menambahkan kolom 'Jumlah_PO_bahan_baku' yang menampilkan jumlah PO bahan baku dengan format mata uang
            ->addColumn('Jumlah_PO_bahan_baku', function ($row) {
                // Menampilkan jumlah PO dengan format mata uang 'Rp.' dan pemisah ribuan
                return "Rp. " . number_format($row->jumlah_po, 0, ',', '.');
            })
            // Menambahkan kolom 'action' untuk tombol edit
            ->addColumn('action', function ($row) {
                // Tombol Edit untuk membuka modal
                return '<a href="javascript:void(0)" class="btn btn-primary btn-sm mr-1" onclick="editModal(' . $row->id . ')">Edit</a>' .
                    '<a href="' . route('pengajuan_po.delete_data', $row->id) . '" class="btn btn-danger btn-sm btn-delete-bahan">Delete</a>';
            })
            ->rawColumns(['action', 'Jumlah_PO_bahan_baku', 'Jumlah_Bahan_baku', 'Bahan_baku'])

            // Menghasilkan data dalam format JSON untuk DataTables
            ->make(true);
    }

    // Endpoint untuk mengambil data bahan berdasarkan ID
    public function get_EditData($id)
    {
        $data = TbPoBahan::findOrFail($id);

        // Mengambil data bahan terkait
        $bahan = TbMasterBahan::findOrFail($data->id_bahan);
        $rincian_bahan = rincian_menu_harian::find($data->id_rincian_bahan);
        $satuan = TbSatuan::find($data->satuan);
        $satuan_list = TbSatuan::all();
        
        return response()->json([
            'id' => $data->id,
            'bahan' => $bahan->bahan,
            'jumlah_bahan' => $data->jumlah_bahan,
            'harga'     => $rincian_bahan->harga ?? 0,
            'jumlah_po' => $data->jumlah_po,
            'jumlah_box' => $data->jumlah_box ?? 1,
            'satuan' => $data->satuan,
            'satuan_nama' => $satuan->satuan ?? '-',
            'satuan_list' => $satuan_list,
            'tanggal_kedatangan' => $data->tanggal_kedatangan,
            'tanggal_digunakan' => $data->tanggal_digunakan,
            'keterangan' => $data->keterangan,
        ]);
    }

    public function update_Data_po(Request $request)
    {
        // Validasi input dari request
        $validated = $request->validate([
            'id' => 'required|integer|exists:tb_po_bahan,id',
            'bahan' => 'required|string|max:255',
            'jumlah_bahan' => 'required|numeric',
            'jumlah_po' => 'required|numeric',
            'satuan' => 'required|integer',
            'tanggal_kedatangan' => 'required|date',
            'tanggal_digunakan' => 'required|date',
        ]);



        // Temukan data yang akan diperbarui
        $data = TbPoBahan::findOrFail($request->id);

        // Update data - untuk beras/sayur harga satuan per kg, jadi langsung jumlah_bahan * harga_satuan
        $data->jumlah_bahan = $request->jumlah_bahan;
        $data->jumlah_po = $request->jumlah_bahan * $request->harga;
        $data->satuan = $request->satuan;
        $data->jumlah_box = $request->jumlah_box;
        $data->tanggal_kedatangan = $request->tanggal_kedatangan;
        $data->tanggal_digunakan = $request->tanggal_digunakan;
        $data->keterangan = $request->keterangan;        
        // Simpan perubahan
        try {
            $data->save();

        if ((int) $data->id_rincian_bahan > 0) {
        $detailMenu = rincian_menu_harian::find($data->id_rincian_bahan);
        if ($detailMenu) {
            $detailMenu->id_satuan = $request->satuan;
            $detailMenu->save();
        }
        }

	    if ($data->id_rincian_bahan == 0)
            {
                $data = TbPoBahan::findOrFail($request->id);

                // Perbarui data
                $data->jumlah_bahan = $request->jumlah_bahan;
                $data->jumlah_po = $request->jumlah_bahan * $request->harga;
                $data->jumlah_box = $request->jumlah_box;
                $data->tanggal_kedatangan = $request->tanggal_kedatangan;
                $data->tanggal_digunakan = $request->tanggal_digunakan;
                $data->keterangan = $request->keterangan;
                $data->save();
            }
            /*if($data->id_rincian_bahan != 0 )
            {
                $rincian_menu = rincian_menu_harian::find($data->id_rincian_bahan);

                $rincian_menu->update([
                    'jumlah'    => $data->jumlah_bahan,
                    'harga'    => $request->harga,
                    'total_harga'    => $data->jumlah_po,
                ]);
                $rincian_menu = TbRincianMenuTemp::where('id_menu',$rincian_menu->id_menu_harian)
                                ->where('id_resep', $rincian_menu->id_resep)
                                ->where('id_bahan', $rincian_menu->id_bahan)
                                ->update([
                                    'jumlah'    => $data->jumlah_bahan,
                                    'harga'    => $request->harga,
                                    'total_harga'    => $data->jumlah_po,
                                    'updated_at'    => \Carbon\Carbon::now(),
                                ]);
            }*/
            

            return response()->json(['message' => 'Data berhasil diperbarui']);
        } catch (\Exception $e) {
            // Jika ada kesalahan saat menyimpan data, tangkap dan tampilkan error
            return response()->json(['error' => 'Gagal memperbarui data', 'details' => $e->getMessage()], 500);
        }
    }

    public function store_Data_po(Request $request)
    {
        $validated = $request->validate([
            'id_po' => 'required|integer|exists:tb_po,id',
            'id_bahan' => 'required|integer|exists:tb_master_bahan,id',
            'jumlah_bahan' => 'required|numeric|min:0.01',
            'satuan' => 'required|integer|exists:tb_satuan,id',
            'harga' => 'required|numeric|min:0',
            'tanggal_kedatangan' => 'required|date',
            'tanggal_digunakan' => 'required|date',
            'jumlah_box' => 'nullable|numeric|min:1',
            'keterangan' => 'nullable|string|max:255',
            'id_menu_harian' => 'nullable|integer|exists:tb_menu,id',
            'menu_tipe' => 'nullable|string|in:karbohidrat,protein,sayur,buah,susu',
            'menu_resep_id' => 'nullable|integer|exists:tb_resep,id',
        ]);

        try {
            $po = TbPo::findOrFail($validated['id_po']);
            $idRincianBahan = 0;
            $jumlahTambah = (float) $validated['jumlah_bahan'];
            $hargaSatuan = (float) $validated['harga'];
            $totalTambah = $jumlahTambah * $hargaSatuan;
            $idRincianKontrak = $this->ensureRincianKontrakId(
                $po->id_kontrak,
                $validated['id_bahan'], 
                $hargaSatuan,
                $validated['satuan']
            );

            if ((int) $po->manual === 0 && !empty($validated['id_menu_harian']) && !empty($validated['menu_resep_id'])) {
                $rincianMenu = rincian_menu_harian::where('id_menu_harian', $validated['id_menu_harian'])
                    ->where('id_resep', $validated['menu_resep_id'])
                    ->where('id_bahan', $validated['id_bahan'])
                    ->first();

                if ($rincianMenu) {
                    $rincianMenu->jumlah = (float) $rincianMenu->jumlah + $jumlahTambah;
                    $rincianMenu->harga = $hargaSatuan;
                    $rincianMenu->total_harga = (float) $rincianMenu->total_harga + $totalTambah;
                    $rincianMenu->jumlah_box = $validated['jumlah_box'] ?? $rincianMenu->jumlah_box ?? 1;
                    $rincianMenu->id_kontrak = $idRincianKontrak;
                    $rincianMenu->id_satuan = $validated['satuan'];
                    $rincianMenu->keterangan = $validated['keterangan'] ?? ($validated['menu_tipe'] ?? $rincianMenu->keterangan);
                    $rincianMenu->save();

                    $idRincianBahan = $rincianMenu->id;
                } else {
                    $rincianMenu = rincian_menu_harian::create([
                        'id_menu_harian' => $validated['id_menu_harian'],
                        'id_resep' => $validated['menu_resep_id'],
                        'id_bahan' => $validated['id_bahan'],
                        'jumlah' => $jumlahTambah,
                        'bumbu' => 0,
                        'harga' => $hargaSatuan,
                        'total_harga' => $totalTambah,
                        'jumlah_box' => $validated['jumlah_box'] ?? 1,
                        'id_kontrak' => $idRincianKontrak,
                        'id_satuan' => $validated['satuan'],
                        'keterangan' => $validated['keterangan'] ?? ($validated['menu_tipe'] ?? null),
                    ]);

                    $idRincianBahan = $rincianMenu->id;
                }

                $tempRincian = TbRincianMenuTemp::where('id_menu', $validated['id_menu_harian'])
                    ->where('id_resep', $validated['menu_resep_id'])
                    ->where('id_bahan', $validated['id_bahan'])
                    ->first();

                if ($tempRincian) {
                    $tempRincian->jumlah = (float) $tempRincian->jumlah + $jumlahTambah;
                    $tempRincian->harga = $hargaSatuan;
                    $tempRincian->total_harga = (float) $tempRincian->total_harga + $totalTambah;
                    $tempRincian->jumlah_box = $validated['jumlah_box'] ?? $tempRincian->jumlah_box ?? 1;
                    $tempRincian->id_kontrak = $idRincianKontrak;
                    $tempRincian->id_satuan = $validated['satuan'];
                    $tempRincian->keterangan = $validated['keterangan'] ?? ($validated['menu_tipe'] ?? $tempRincian->keterangan);
                    $tempRincian->save();
                } else {
                    TbRincianMenuTemp::create([
                        'id_menu' => $validated['id_menu_harian'],
                        'id_resep' => $validated['menu_resep_id'],
                        'id_bahan' => $validated['id_bahan'],
                        'jumlah' => $jumlahTambah,
                        'bumbu' => 0,
                        'harga' => $hargaSatuan,
                        'total_harga' => $totalTambah,
                        'jumlah_box' => $validated['jumlah_box'] ?? 1,
                        'id_kontrak' => $idRincianKontrak,
                        'id_satuan' => $validated['satuan'],
                        'keterangan' => $validated['keterangan'] ?? ($validated['menu_tipe'] ?? null),
                    ]);
                }
            }

            TbPoBahan::create([
                'id_po' => $validated['id_po'],
                'id_bahan' => $validated['id_bahan'],
                'jumlah_bahan' => $validated['jumlah_bahan'],
                'satuan' => $validated['satuan'],
                'jumlah_po' => $totalTambah,
                'id_kontrak' => $po->id_kontrak,
                'id_rincian_bahan' => $idRincianBahan,
                'id_rincian_kontrak' => $idRincianKontrak,
                'tanggal_kedatangan' => $validated['tanggal_kedatangan'],
                'tanggal_digunakan' => $validated['tanggal_digunakan'],
                'jumlah_box' => $validated['jumlah_box'] ?? 1,
                'keterangan' => $validated['keterangan'] ?? ($validated['menu_tipe'] ?? null),
                'buffer' => 0,
            ]);

            return response()->json(['message' => 'Data bahan PO berhasil ditambahkan']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal menambahkan data bahan PO', 'details' => $e->getMessage()], 500);
        }
    }

    public function destroy_Data_po($id)
    {
        try {
            $data = TbPoBahan::findOrFail($id);
            $data->delete();

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['message' => 'Data bahan PO berhasil dihapus']);
            }

            return back()->with('success', 'Data bahan PO berhasil dihapus');

        } catch (\Exception $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['error' => 'Gagal menghapus data bahan PO', 'details' => $e->getMessage()], 500);
            }

            return back()->with('error', 'Gagal menghapus data bahan PO');

        }
    }

    public function pdf_pengajuan_po_tanpa_harga_lama($id)
    {

        $dapur      = DataDapur::firstOrDefault();
        $po         = TbPo::where('id', $id)->first();

        $kontrak    = TbKontrak::where('id', $po->id_kontrak)->first();
        $Supplier   = Supplier::where('id', $kontrak->id_supplier)->first();
        /* $rincian_po = DB::table('tb_menu')
            ->join('rincian_menu_harian', 'tb_menu.id', '=', 'rincian_menu_harian.id_menu_harian')
            ->join('tb_master_bahan', 'rincian_menu_harian.id_bahan', '=', 'tb_master_bahan.id')
            ->join('tb_satuan', 'tb_master_bahan.satuan_bahan', '=', 'tb_satuan.id')
            ->leftJoin('tb_spesifikasi_bahan', 'tb_spesifikasi_bahan.id', '=', 'tb_master_bahan.id')
            ->where('tb_menu.id', $id_menu)
            ->select([
                'tb_menu.id',
                'tb_menu.tanggal_kirim',
                'rincian_menu_harian.id_resep',
                'tb_master_bahan.bahan',
                'tb_master_bahan.id as idbahan',
                DB::raw("COALESCE(tb_spesifikasi_bahan.spesifikasi, '-') as spesifikasi"),
                'rincian_menu_harian.id as id_rincian_menu',
                'rincian_menu_harian.jumlah',
                'tb_satuan.satuan',
            ])
            ->get();
        */
        $sample_rincian_po = DB::table('tb_po_bahan')
            ->join('tb_master_bahan', 'tb_po_bahan.id_bahan', '=', 'tb_master_bahan.id')
            ->join('tb_satuan', 'tb_master_bahan.satuan_bahan', '=', 'tb_satuan.id')
            ->leftJoin('rincian_menu_harian', 'tb_po_bahan.id_rincian_bahan', '=', 'rincian_menu_harian.id')
            ->leftJoin('tb_resep', 'rincian_menu_harian.id_resep', '=', 'tb_resep.id')
            ->where('tb_po_bahan.id_po', $id)
            ->select([
                'rincian_menu_harian.id_menu_harian'
            ])
            ->orderByDesc('tb_po_bahan.id')->first();
        $jumlah_pack = rincian_sekolah::where('id_menu_harian', $sample_rincian_po->id_menu_harian)->sum('jumlah_penerima_total');
        $rincian_po = DB::table('tb_po_bahan')
            ->join('tb_master_bahan', 'tb_po_bahan.id_bahan', '=', 'tb_master_bahan.id')
            ->join('tb_satuan', 'tb_master_bahan.satuan_bahan', '=', 'tb_satuan.id')
            ->leftJoin('rincian_menu_harian', 'tb_po_bahan.id_rincian_bahan', '=', 'rincian_menu_harian.id')
            ->leftJoin('tb_resep', 'rincian_menu_harian.id_resep', '=', 'tb_resep.id')
            ->where('tb_po_bahan.id_po', $id)
            ->select([
                DB::raw('@rownum := @rownum + 1 AS nomor_urut'),
                'tb_po_bahan.id',
                'tb_po_bahan.id_po',
                'tb_master_bahan.bahan',
                'tb_po_bahan.jumlah_bahan',
                'tb_po_bahan.buffer',
                'tb_satuan.satuan',
                'tb_po_bahan.keterangan',
                'tb_po_bahan.tanggal_kedatangan',
                'tb_resep.nama_resep as resep',
                'rincian_menu_harian.id_menu_harian'
            ])
            ->get();
        $total = DB::table('tb_po_bahan as po_bahan')
            ->join('tb_po as po', 'po_bahan.id_po', 'po.id')
            ->join('tb_master_bahan as bahan', 'po_bahan.id_bahan', 'bahan.id')
            ->join('tb_rincian_kontrak', 'bahan.id', 'tb_rincian_kontrak.id_bahan')
            ->join('tb_kontrak', 'tb_rincian_kontrak.id_kontrak', 'tb_kontrak.id')
            ->join('tb_satuan', 'bahan.satuan_bahan', 'tb_satuan.id')

            ->where('tb_kontrak.id', $po->id_kontrak)
            ->select(
                DB::raw('@rownum := @rownum + 1 AS nomor_urut'),
                'bahan.bahan',
                'tb_rincian_kontrak.harga_bahan',
                'po_bahan.tanggal_kedatangan',
                'po_bahan.jumlah_bahan',
                'tb_satuan.satuan',
                'po_bahan.jumlah_po'

            )
            ->crossJoin(DB::raw('(SELECT @rownum := 0) AS r')) // Inisial
            ->sum('po_bahan.jumlah_po');

        $totalberas = DB::table('tb_po_bahan as po_bahan')
            ->join('tb_po as po', 'po_bahan.id_po', '=', 'po.id')
            ->join('tb_master_bahan as bahan', 'po_bahan.id_bahan', '=', 'bahan.id')
            ->join('tb_rincian_kontrak', 'bahan.id', '=', 'tb_rincian_kontrak.id_bahan')
            ->join('tb_kontrak', 'tb_rincian_kontrak.id_kontrak', '=', 'tb_kontrak.id')
            ->join('tb_satuan', 'bahan.satuan_bahan', '=', 'tb_satuan.id')
            ->where('po.id', $id)
            ->where('tb_kontrak.id', $po->id_kontrak)
            ->where('bahan.bahan', 'beras')
            ->select(DB::raw('(po_bahan.jumlah_bahan + po_bahan.buffer) as total_beras'))
            ->first('');
        //->sum('po_bahan.jumlah_bahan');
        $totalBerasKg = $totalberas->total_beras / 1000 ?? 0;
        // Packing beras
        /*$pack25 = floor($totalBerasKg / 25);
        $sisa = $totalBerasKg % 25;

        $pack5 = floor($sisa / 5);
        $sisa = $sisa % 5;

        $pack1 = $sisa > 0 ? 1 : 0;
        */
        //$pack25 = $id.' '. $po->id_kontrak .' '. $totalBerasKg;
        $pack_utama = $totalBerasKg / 36;
        $pack25 = floor($pack_utama) * 1;
        $pack5 = floor($pack_utama) * 2;
        $pack1 = floor($pack_utama) * 1;
        $sisa = $totalBerasKg - floor($pack_utama) * 36;
        $pack5 = floor($sisa / 5) + $pack5;
        $pack1 = ($sisa % 5) + $pack1;

        $pdf = Pdf::loadView('office/PO.template_pengajuan_po_2', compact(
            'po',
            'dapur',
            'kontrak',
            'Supplier',
            'rincian_po',
            'total',
            'pack25',
            'pack5',
            'pack1',
            'totalBerasKg',
            'jumlah_pack'
        ))->setPaper('a4', 'landscape');

        $tanggal_pengajuan = $po->tanggal_pengajuan = Carbon::parse($po->tanggal_pengajuan)->format('d-m-Y');
        return $pdf->download('Formulir_Pengajuan_PO ' . $tanggal_pengajuan . '.pdf');
    }

    public function pdf_pengajuan_po_tanpa_harga($id)
    {

        $dapur      = DataDapur::firstOrDefault();
        $po         = TbPo::where('id', $id)->first();

        $kontrak    = TbKontrak::where('id', $po->id_kontrak)->first();
        $Supplier   = Supplier::where('id', $kontrak->id_supplier)->first();
        /* $rincian_po = DB::table('tb_menu')
            ->join('rincian_menu_harian', 'tb_menu.id', '=', 'rincian_menu_harian.id_menu_harian')
            ->join('tb_master_bahan', 'rincian_menu_harian.id_bahan', '=', 'tb_master_bahan.id')
            ->join('tb_satuan', 'tb_master_bahan.satuan_bahan', '=', 'tb_satuan.id')
            ->leftJoin('tb_spesifikasi_bahan', 'tb_spesifikasi_bahan.id', '=', 'tb_master_bahan.id')
            ->where('tb_menu.id', $id_menu)
            ->select([
                'tb_menu.id',
                'tb_menu.tanggal_kirim',
                'rincian_menu_harian.id_resep',
                'tb_master_bahan.bahan',
                'tb_master_bahan.id as idbahan',
                DB::raw("COALESCE(tb_spesifikasi_bahan.spesifikasi, '-') as spesifikasi"),
                'rincian_menu_harian.id as id_rincian_menu',
                'rincian_menu_harian.jumlah',
                'tb_satuan.satuan',
            ])
            ->get();
        */
        $sample_rincian_po = DB::table('tb_po_bahan')
            ->join('tb_master_bahan', 'tb_po_bahan.id_bahan', '=', 'tb_master_bahan.id')
            ->join('tb_satuan', 'tb_master_bahan.satuan_bahan', '=', 'tb_satuan.id')
            ->leftJoin('rincian_menu_harian', 'tb_po_bahan.id_rincian_bahan', '=', 'rincian_menu_harian.id')
            ->leftJoin('tb_resep', 'rincian_menu_harian.id_resep', '=', 'tb_resep.id')
            ->where('tb_po_bahan.id_po', $id)
            ->select([
                'rincian_menu_harian.id_menu_harian'
            ])
            ->orderByDesc('tb_po_bahan.id')->first();
        $jumlah_pack = rincian_sekolah::where('id_menu_harian', $sample_rincian_po->id_menu_harian)->sum('jumlah_penerima_total');
        $jumlah_pack_a = rincian_sekolah::where('id_menu_harian', $sample_rincian_po->id_menu_harian)->sum('jumlah_penerima_a');
        $jumlah_pack_b = rincian_sekolah::where('id_menu_harian', $sample_rincian_po->id_menu_harian)->sum('jumlah_penerima_b');
        
        $rincian_po = DB::table('tb_po_bahan as po_bahan')
                        ->join('tb_po as po', 'po_bahan.id_po', '=', 'po.id')
                        ->join('tb_master_bahan as bahan', 'po_bahan.id_bahan', '=', 'bahan.id')
                        ->join('rincian_menu_harian', 'po_bahan.id_rincian_bahan', '=', 'rincian_menu_harian.id')
                        ->join('tb_resep', 'rincian_menu_harian.id_resep', '=', 'tb_resep.id')
                        ->join('tb_rincian_kontrak', 'rincian_menu_harian.id_kontrak', '=', 'tb_rincian_kontrak.id')
                        ->join('tb_satuan', 'rincian_menu_harian.id_satuan', '=', 'tb_satuan.id')
                        ->join('tb_satuan as satuan2', 'tb_rincian_kontrak.satuan_bahan', '=', 'satuan2.id')
                        
                        ->selectRaw('
                    @rownum := @rownum + 1 AS nomor_urut,
                    bahan.bahan,
                    po_bahan.tanggal_kedatangan,
                    po_bahan.jumlah_bahan,
                    po_bahan.jumlah_po,
                    po_bahan.keterangan,
                    po_bahan.jumlah_box,
                    tb_resep.nama_resep,
                    tb_resep.id as id_resep,
                    tb_rincian_kontrak.harga_bahan,
                    COALESCE(tb_rincian_kontrak.merek_bahan, "-") AS merek_bahan,
                    tb_satuan.satuan,
                    satuan2.satuan AS satuan_kontrak
                ')
                        ->where('po.id', $id)
                        //->where('tb_rincian_kontrak.status', 1)
                        ->get();
        /*$rincian_po = DB::table('tb_po_bahan')
            ->join('tb_master_bahan', 'tb_po_bahan.id_bahan', '=', 'tb_master_bahan.id')
            ->join('tb_satuan', 'tb_master_bahan.satuan_bahan', '=', 'tb_satuan.id')
            ->leftJoin('rincian_menu_harian', 'tb_po_bahan.id_rincian_bahan', '=', 'rincian_menu_harian.id')
            ->leftJoin('tb_resep', 'rincian_menu_harian.id_resep', '=', 'tb_resep.id')
            ->where('tb_po_bahan.id_po', $id)
            ->select([
                DB::raw('@rownum := @rownum + 1 AS nomor_urut'),
                'tb_po_bahan.id',
                'tb_po_bahan.id_po',
                'tb_master_bahan.bahan',
                'tb_po_bahan.jumlah_bahan',
                'tb_po_bahan.buffer',
                'tb_satuan.satuan',
                'tb_po_bahan.keterangan',
                'tb_po_bahan.tanggal_kedatangan',
                'tb_resep.nama_resep as resep',
                'rincian_menu_harian.id_menu_harian'
            ])
            ->get();*/
        $cheklist = DB::table('tb_po_bahan as po_bahan')
            ->join('tb_po as po', 'po_bahan.id_po', '=', 'po.id')
            ->join('tb_master_bahan as bahan', 'po_bahan.id_bahan', '=', 'bahan.id')
            ->join('rincian_menu_harian', 'po_bahan.id_rincian_bahan', '=', 'rincian_menu_harian.id')
            ->join('tb_resep', 'rincian_menu_harian.id_resep', '=', 'tb_resep.id')
            ->join('tb_rincian_kontrak', 'rincian_menu_harian.id_kontrak', '=', 'tb_rincian_kontrak.id')
            ->join('tb_satuan', 'rincian_menu_harian.id_satuan', '=', 'tb_satuan.id')
            ->join('tb_satuan as satuan2', 'tb_rincian_kontrak.satuan_bahan', '=', 'satuan2.id')

            ->selectRaw('
                    @rownum := @rownum + 1 AS nomor_urut,
                    bahan.bahan,
                    po_bahan.tanggal_kedatangan,
                    po_bahan.jumlah_bahan,
                    po_bahan.jumlah_po,
                    po_bahan.keterangan,
                    po_bahan.jumlah_box,
                    tb_resep.nama_resep,
                    tb_resep.id as id_resep,
                    tb_rincian_kontrak.harga_bahan,
                    COALESCE(tb_rincian_kontrak.merek_bahan, "-") AS merek_bahan,
                    tb_satuan.satuan,
                    satuan2.satuan AS satuan_kontrak
                ')
            ->where('po.id', $id)
            //->where('tb_rincian_kontrak.status', 1)
            ->count();

        if($cheklist < 1)
        {
            $rincian_po = DB::table('tb_po_bahan')
                ->join('tb_master_bahan', 'tb_po_bahan.id_bahan', '=', 'tb_master_bahan.id')
                ->join('tb_satuan', 'tb_master_bahan.satuan_bahan', '=', 'tb_satuan.id')
                ->leftJoin('rincian_menu_harian', 'tb_po_bahan.id_rincian_bahan', '=', 'rincian_menu_harian.id')
                ->leftJoin('tb_resep', 'rincian_menu_harian.id_resep', '=', 'tb_resep.id')
                ->where('tb_po_bahan.id_po', $id)
                ->select([
                    DB::raw('@rownum := @rownum + 1 AS nomor_urut'),
                    'tb_po_bahan.id',
                    'tb_po_bahan.id_po',
                    'tb_master_bahan.bahan',
                    'tb_po_bahan.jumlah_bahan',
                    'tb_po_bahan.buffer',
                    'tb_satuan.satuan',
                    'tb_po_bahan.keterangan',
                    'tb_po_bahan.tanggal_kedatangan',
                    'tb_resep.nama_resep as resep',
                    'rincian_menu_harian.id_menu_harian'
                ])
                ->get();
        }
        $total = DB::table('tb_po_bahan as po_bahan')
            ->join('tb_po as po', 'po_bahan.id_po', 'po.id')
            ->join('tb_master_bahan as bahan', 'po_bahan.id_bahan', 'bahan.id')
            ->join('tb_rincian_kontrak', 'bahan.id', 'tb_rincian_kontrak.id_bahan')
            ->join('tb_kontrak', 'tb_rincian_kontrak.id_kontrak', 'tb_kontrak.id')
            ->join('tb_satuan', 'bahan.satuan_bahan', 'tb_satuan.id')

            ->where('tb_kontrak.id', $po->id_kontrak)
            ->select(
                DB::raw('@rownum := @rownum + 1 AS nomor_urut'),
                'bahan.bahan',
                'tb_rincian_kontrak.harga_bahan',
                'po_bahan.tanggal_kedatangan',
                'po_bahan.jumlah_bahan',
                'tb_satuan.satuan',
                'po_bahan.jumlah_po'

            )
            ->crossJoin(DB::raw('(SELECT @rownum := 0) AS r')) // Inisial
            ->sum('po_bahan.jumlah_po');

        $totalberas =
            DB::table('tb_po_bahan as po_bahan')
            ->join('tb_po as po', 'po_bahan.id_po', '=', 'po.id')
            ->join('tb_master_bahan as bahan', 'po_bahan.id_bahan', '=', 'bahan.id')
            ->where('po.id', $id)
            ->where('bahan.bahan', 'beras')
            ->sum('po_bahan.jumlah_bahan');
        $totalBerasKg = $totalberas  ?? 0;
        // Packing beras
        /*$pack25 = floor($totalBerasKg / 25);
        $sisa = $totalBerasKg % 25;

        $pack5 = floor($sisa / 5);
        $sisa = $sisa % 5;

        $pack1 = $sisa > 0 ? 1 : 0;
        */
        //$pack25 = $id.' '. $po->id_kontrak .' '. $totalBerasKg;
        $pack_utama = $totalBerasKg / 36;
        $pack25 = floor($pack_utama) * 1;
        $pack5 = floor($pack_utama) * 2;
        $pack1 = floor($pack_utama) * 1;
        $sisa = $totalBerasKg - floor($pack_utama) * 36;
        $pack5 = floor($sisa / 5) + $pack5;
        $pack1 = ($sisa % 5) + $pack1;

        $menu = Menu::find($sample_rincian_po->id_menu_harian);
        
        // Mapping golongan ke label display dan quantity
        $golongan_mapping = [
            'pax_a' => ['label' => 'Pack A', 'quantity' => $jumlah_pack_a],
            'pax_b' => ['label' => 'Pack B', 'quantity' => $jumlah_pack_b],
            'balita' => ['label' => 'Balita', 'quantity' => 0], // Placeholder if needed
            'baduta' => ['label' => 'Baduta', 'quantity' => 0], // Placeholder if needed
            'busui_bumil' => ['label' => 'Ibu Hamil/Menyusui', 'quantity' => 0], // Placeholder if needed
            'umum' => ['label' => 'Umum', 'quantity' => 0], // Placeholder if needed
        ];
        
        // Get menu golongan and prepare breakdown
        $menu_golongan = $menu->golongan ?? 'umum';
        $pack_breakdown = [];
        
        if ($menu_golongan == 'pax_a') {
            $pack_breakdown[] = ['label' => 'Pack A', 'quantity' => $jumlah_pack_a];
        } elseif ($menu_golongan == 'pax_b') {
            $pack_breakdown[] = ['label' => 'Pack B', 'quantity' => $jumlah_pack_b];
        } else {
            // For mixed menus, show both A and B
            $pack_breakdown[] = ['label' => 'Pack A', 'quantity' => $jumlah_pack_a];
            $pack_breakdown[] = ['label' => 'Pack B', 'quantity' => $jumlah_pack_b];
        }
        
        $pdf = Pdf::loadView('office/PO.template_pengajuan_po_2', compact(
            'po',
            'dapur',
            'kontrak',
            'Supplier',
            'rincian_po',
            'total',
            'pack25',
            'pack5',
            'pack1',
            'totalBerasKg',
            'jumlah_pack',
            'jumlah_pack_a',
            'jumlah_pack_b',
            'pack_breakdown',
            'menu_golongan',
        ))->setPaper('a4', 'landscape');

        $tanggal_pengajuan = $po->tanggal_pengajuan = Carbon::parse($po->tanggal_pengajuan)->format('d-m-Y');
        return $pdf->download('Formulir_Pengajuan_PO ' . $tanggal_pengajuan . '.pdf');
    }

    public function form_input_kontrak($id)
    {
        $rincian_harian = rincian_menu_harian::find($id);
        if (!$rincian_harian) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        $bahan = TbMasterBahan::find($rincian_harian->id_bahan);
        $menu = Menu::find($rincian_harian->id_menu_harian);

        return view('office/PO.form_input_kontrak', compact('rincian_harian', 'bahan', 'menu'));
    }

    public function simpan_kontrak_rincian(Request $request, $id)
    {
        $request->validate([
            'harga_bahan' => 'required|numeric|min:0'
        ]);

        $rincian_harian = rincian_menu_harian::find($id);
        if (!$rincian_harian) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        $bahan = TbMasterBahan::find($rincian_harian->id_bahan);
        
        // Get latest contract or use default ID 13
        $kontrak_active = TbKontrak::orderByDesc('id')->first();
        $kontrak_id = $kontrak_active ? $kontrak_active->id : 13;

        // Create contract detail
        TbRincianKontrak::create([
            'id_kontrak'   => $kontrak_id,
            'id_bahan'     => $rincian_harian->id_bahan,
            'merek_bahan'  => $bahan->bahan ?? '-',
            'harga_bahan'  => (float) $request->harga_bahan,
            'jumlah_bahan' => 1,
            'satuan_bahan' => $bahan->satuan_bahan ?? 0,
            'status'       => 1,
            'kemasan'      => '-'
        ]);

        return redirect()->route('rincian_menu_po', $rincian_harian->id_menu_harian)
            ->with('success', 'Kontrak rincian berhasil ditambahkan');
    }

    public function hapus_rincian_menu($id)
    {
        $rincian_harian = rincian_menu_harian::find($id);
        if (!$rincian_harian) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        $id_menu_harian = $rincian_harian->id_menu_harian;
        $rincian_harian->delete();

        return redirect()->route('rincian_menu_po', $id_menu_harian)
            ->with('success', 'Item berhasil dihapus dari menu');
    }

    public function form_bulk_pengiriman($id, $id_menu, $id_kontrak)
    {
        $po = TbPo::find($id);
        $menu = Menu::find($id_menu);

        // Calculate default delivery date (H-1 from menu's tanggal_kirim)
        $default_tanggal_kirim = $menu && $menu->tanggal_kirim 
            ? \Carbon\Carbon::parse($menu->tanggal_kirim)->subDay()->format('Y-m-d')
            : date('Y-m-d');

        // Get menu items grouped by recipe type with order
        $recipe_order = [
            'Lauk Utama', // Protein
            'Pendamping', // Complementary
            'Buah', // Fruit
            'Sayur', // Vegetable
            'Karbohidrat', // Carbohydrate
        ];

        $menu_items = DB::table('rincian_menu_harian as rmh')
            ->join('tb_master_bahan as mb', 'rmh.id_bahan', '=', 'mb.id')
            ->join('tb_menu_bahan as tmb', function ($join) {
                $join->on('tmb.menu_id', '=', 'rmh.id_resep')
                    ->on('tmb.bahan_id', '=', 'rmh.id_bahan');
            })
            ->join('tb_resep as tr', 'rmh.id_resep', '=', 'tr.id')
            ->leftJoin('tb_satuan as ts', 'rmh.id_satuan', '=', 'ts.id')
            ->where('rmh.id_menu_harian', $id_menu)
            ->where('mb.bahan', '!=', 'air')
            ->select(
                'rmh.id as id_rincian',
                'rmh.id_bahan',
                'rmh.id_resep',
                'mb.bahan',
                'rmh.jumlah',
                'rmh.id_satuan',
                'ts.satuan',
                'rmh.harga',
                'rmh.total_harga',
                'rmh.keterangan',
                'tr.nama_resep',
                'tmb.status_bahan_baku'
            )
            ->get();

        // Group by resep nama and sort by recipe_order
        $grouped = $menu_items->groupBy('nama_resep');
        
        // Sort according to custom order
        $sorted = collect();
        foreach ($recipe_order as $resep_name) {
            if ($grouped->has($resep_name)) {
                $sorted[$resep_name] = $grouped[$resep_name];
            }
        }
        
        // Add remaining reseps not in the order
        foreach ($grouped as $resep_name => $items) {
            if (!$sorted->has($resep_name)) {
                $sorted[$resep_name] = $items;
            }
        }

        return view('office/PO.bulk_input_pengiriman', compact('po', 'menu', 'sorted', 'id_menu', 'id_kontrak', 'default_tanggal_kirim'));
    }

    public function simpan_bulk_pengiriman(Request $request)
    {
        $request->validate([
            'tanggal_kirim' => 'required|date',
            'jam_kirim' => 'required',
            'tanggal_digunakan' => 'required|date',
            'items' => 'required|array',
            'id_menu' => 'required',
            'id_po' => 'required'
        ]);

        $po = TbPo::find($request->id_po);
        if (!$po) {
            return redirect()->back()->with('error', 'PO tidak ditemukan');
        }

        // Combine date and time for delivery
        $tanggal_kirim = $request->tanggal_kirim . ' ' . $request->jam_kirim;
        $tanggal_digunakan = $request->tanggal_digunakan;

        $count_created = 0;
        $count_updated = 0;
        $count_failed = 0;

        // Get edited values arrays
        $edited_jumlah = $request->input('items_jumlah', []);
        $edited_harga = $request->input('items_harga', []);

        foreach ($request->items as $id_rincian) {
            $rincian_harian = rincian_menu_harian::find($id_rincian);
            
            if (!$rincian_harian) {
                $count_failed++;
                continue;
            }

            // Check if already has PO entry for this item and menu
            $existingRows = TbPoBahan::where('id_rincian_bahan', $id_rincian)
                ->where('id_po', $po->id)
                ->where('tanggal_digunakan', $tanggal_digunakan)
                ->orderBy('id', 'asc')
                ->get();

            // Get edited values or use original values
            $jumlah = isset($edited_jumlah[$id_rincian]) 
                ? floatval($edited_jumlah[$id_rincian]) 
                : ($rincian_harian->jumlah ?? 0);
            
            $harga = isset($edited_harga[$id_rincian]) 
                ? floatval($edited_harga[$id_rincian]) 
                : ($rincian_harian->harga ?? 0);

            // Get material data
            $bahan = TbMasterBahan::find($rincian_harian->id_bahan);

            $payload = [
                'id_bahan' => $rincian_harian->id_bahan,
                'jumlah_bahan' => $jumlah,
                'satuan' => $rincian_harian->id_satuan ?? 1,
                'jumlah_po' => (float) ($rincian_harian->total_harga ?? 0),
                'tanggal_kedatangan' => $tanggal_kirim,
                'tanggal_digunakan' => $tanggal_digunakan,
                'buffer' => 0,
                'keterangan' => $rincian_harian->keterangan,
                'jumlah_box' => 1,
                'id_rincian_kontrak' => null,
                'id_kontrak' => $po->id_kontrak ?? 13,
            ];

            if ($existingRows->isNotEmpty()) {
                $primaryRow = $existingRows->first();
                $primaryRow->update($payload);

                if ($existingRows->count() > 1) {
                    $duplicateIds = $existingRows->skip(1)->pluck('id')->all();
                    TbPoBahan::whereIn('id', $duplicateIds)->delete();
                }

                $count_updated++;
            } else {
                TbPoBahan::create(array_merge([
                    'id_po' => $po->id,
                    'id_rincian_bahan' => $id_rincian,
                ], $payload));

                $count_created++;
            }
        }

        return redirect()->route('rincian_pengajuan_po', [$po->id, $request->id_menu, $po->id_kontrak])
            ->with('success', "Input pengiriman berhasil: $count_created baru, $count_updated diupdate");
    }

    public function hapus_po_bahan($ids)
    {
        // Parse IDs (could be single or comma-separated)
        $id_list = explode(',', $ids);
        
        $count_deleted = 0;
        foreach ($id_list as $id) {
            $po_bahan = TbPoBahan::find((int)$id);
            
            if ($po_bahan) {
                // Get PO info for redirect
                $po_id = $po_bahan->id_po;
                $rincian_bahan = rincian_menu_harian::find($po_bahan->id_rincian_bahan);
                $id_menu = $rincian_bahan->id_menu_harian ?? 0;
                $po = TbPo::find($po_id);
                
                // Delete the record
                $po_bahan->delete();
                $count_deleted++;
            }
        }

        if (isset($po) && $po) {
            return redirect()->route('rincian_pengajuan_po', [$po->id, $id_menu, $po->id_kontrak])
                ->with('success', "Pengiriman berhasil dihapus: $count_deleted item");
        }

        return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data');
    }

    private function calculateJumlahPoBySatuan(float $harga, float $jumlah, $satuanRef = null): int
    {
        $satuanName = strtoupper(trim((string) $this->resolveSatuanName($satuanRef)));
        $quantityForPrice = $jumlah;

        if (in_array($satuanName, ['GRAM', 'GR'], true)) {
            $quantityForPrice = $jumlah / 1000;
        } elseif (in_array($satuanName, ['ML', 'MILLILITER'], true)) {
            $quantityForPrice = $jumlah / 1000;
        }

        return (int) round($harga * $quantityForPrice);
    }

    private function resolveSatuanName($satuanRef = null): string
    {
        if (is_numeric($satuanRef)) {
            return (string) optional(TbSatuan::find((int) $satuanRef))->satuan;
        }

        return (string) $satuanRef;
    }
}
