<?php

namespace App\Http\Controllers\office;

use App\Http\Controllers\Controller;
use App\Traits\ApiPaginates;

use App\Models\Menu;
use App\Models\Resep;
use App\Models\rincian_menu_harian;
use App\Models\MenuBahan;
use App\Models\rincian_sekolah;
use App\Models\DataSekolah;
use App\Models\DataDapur;
use App\Models\TbPo;
use App\Models\TingkatanSekolah;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\LaporanRekapMenuExport;
use App\Models\BuahRumusPerhitunganBuah;
use App\Models\KomponenSehat;
use App\Models\PaketMenu;
use App\Models\PerhitunganBumbu;
use App\Models\RumusPerhitunganProtein;
use App\Models\RumusPerhitunganSayur;
use App\Models\SpesifikasiBahan;
use App\Models\tb_karyawan;
use App\Models\TbMasterBahan;
use App\Models\TbBantuBahanPo;
use App\Models\TbSatuan;
use App\Models\TbGramasiMenu;
use App\Models\TbRincianKontrak;
use App\Models\TbRumusPerhitunganKarbo;
use App\Models\MenuGiziHarian;
use App\Models\ResepRealisasiAkg;
use App\Services\AutoMenuGiziService;
use App\Services\DatabaseMenuGiziService;
use App\Services\MenuNutrisiMasterBahanNutrisiService;
use App\Services\MenuNutrisiCalculationService;
use App\Exports\LaporanGiziHarianExport;
use Illuminate\Http\Request;


use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
//import return type View
use Illuminate\View\View;
//import return type redirectResponse
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class TbMasterMenuController extends Controller
{
    use ApiPaginates;
    //
    public function printCenter(Request $request): View
    {
        $header = 'Pusat Cetak Dokumen';
        $today = Carbon::today()->toDateString();
        $tanggalAcuan = $request->get('tanggal', $today);
        $tanggalAwal = $request->get('tanggal_awal', $tanggalAcuan);
        $tanggalAkhir = $request->get('tanggal_akhir', $tanggalAcuan);
        $idPo = $request->get('id_po');
        $idMenu = $request->get('id_menu');
        $idSuratJalan = $request->get('id_surat_jalan');
        $checklistPoId = $idPo;

        if (!$checklistPoId) {
            $checklistPoId = DB::table('tb_po_bahan as po_bahan')
                ->join('tb_po as po', 'po_bahan.id_po', '=', 'po.id')
                ->whereDate('po_bahan.tanggal_kedatangan', $tanggalAcuan)
                ->whereIn('po.status_po', ['acc', 'bayar'])
                ->orderBy('po_bahan.tanggal_kedatangan', 'asc')
                ->orderBy('po_bahan.id', 'asc')
                ->value('po_bahan.id_po');
        }

        $menuOptions = Menu::select('id', 'menu', 'tanggal_kirim')
            ->orderByDesc('tanggal_kirim')
            ->orderByDesc('id')
            ->limit(200)
            ->get();

        $menuOptionsJson = $menuOptions->map(function ($menuOption) {
            return [
                'id' => (string) $menuOption->id,
                'label' => $menuOption->tanggal_kirim . ' | ' . $menuOption->menu . ' | ID ' . $menuOption->id,
                'tanggal_kirim' => $menuOption->tanggal_kirim,
            ];
        })->values();

        return view('office.mastermenu.print_center', compact(
            'header',
            'today',
            'tanggalAcuan',
            'tanggalAwal',
            'tanggalAkhir',
            'idPo',
            'idMenu',
            'idSuratJalan',
            'checklistPoId',
            'menuOptions',
            'menuOptionsJson'
        ));
    }

    public function getChecklistPoId(Request $request)
    {
        $tanggal = $request->get('tanggal');
        
        if (!$tanggal) {
            return response()->json(['poId' => null, 'message' => 'Tanggal tidak diberikan'], 400);
        }

        $poId = DB::table('tb_po_bahan as po_bahan')
            ->join('tb_po as po', 'po_bahan.id_po', '=', 'po.id')
            ->whereDate('po_bahan.tanggal_kedatangan', $tanggal)
            ->whereIn('po.status_po', ['acc', 'bayar'])
            ->orderBy('po_bahan.tanggal_kedatangan', 'asc')
            ->orderBy('po_bahan.id', 'asc')
            ->value('po_bahan.id_po');

        return response()->json(['poId' => $poId]);
    }

    public function index()
    {
        $header = "Pengajuan Menu";
        if (request()->ajax()) {
            //$users = User::query();
            $Menu = Menu::select(

                'tb_menu.*',

            )
                //->whereDate('tb_menu.tanggal_kirim', '>=', Carbon::today()) // Filter berdasarkan tanggal
                ->where('tb_menu.status_pengajuan', '!=', 'rejected'); // Hindari data yang reject
            
            // Filter berdasarkan menu name + recipe names (karbohidrat, protein, sayur, buah, susu)
            if (request()->has('search_menu') && !empty(request()->get('search_menu'))) {
                $searchMenu = request()->get('search_menu');
                $Menu = $Menu->where(function($q) use ($searchMenu) {
                    $q->where('tb_menu.menu', 'like', '%' . $searchMenu . '%')
                      ->orWhereIn('tb_menu.karbohidrat', function($q2) use ($searchMenu) {
                          $q2->select('id')->from('tb_resep')->where('nama_resep', 'like', '%' . $searchMenu . '%');
                      })
                      ->orWhereIn('tb_menu.protein', function($q2) use ($searchMenu) {
                          $q2->select('id')->from('tb_resep')->where('nama_resep', 'like', '%' . $searchMenu . '%');
                      })
                      ->orWhereIn('tb_menu.sayur', function($q2) use ($searchMenu) {
                          $q2->select('id')->from('tb_resep')->where('nama_resep', 'like', '%' . $searchMenu . '%');
                      })
                      ->orWhereIn('tb_menu.buah', function($q2) use ($searchMenu) {
                          $q2->select('id')->from('tb_resep')->where('nama_resep', 'like', '%' . $searchMenu . '%');
                      })
                      ->orWhereIn('tb_menu.susu', function($q2) use ($searchMenu) {
                          $q2->select('id')->from('tb_resep')->where('nama_resep', 'like', '%' . $searchMenu . '%');
                      });
                });
            }
            
            // Filter berdasarkan date range
            if (request()->has('tanggal_awal') && !empty(request()->get('tanggal_awal'))) {
                $tanggalAwal = request()->get('tanggal_awal');
                $Menu = $Menu->whereDate('tb_menu.tanggal_kirim', '>=', $tanggalAwal);
            }
            
            if (request()->has('tanggal_akhir') && !empty(request()->get('tanggal_akhir'))) {
                $tanggalAkhir = request()->get('tanggal_akhir');
                $Menu = $Menu->whereDate('tb_menu.tanggal_kirim', '<=', $tanggalAkhir);
            }
            
            $Menu = $Menu->latest()
                ->limit(60) // Ambil 60 data terakhir
                ->get();

            //$Menu = Menu::all();
            return DataTables::of($Menu)
                ->addIndexColumn() // Menambah index
                ->addColumn('nama_karbohidrat', function ($row) {
                    $resep = Resep::find($row->karbohidrat);
                    if ($resep) {
                        return $resep->nama_resep;
                    } else {
                        return '--';
                    }
                })
                ->addColumn('nama_protein', function ($row) {
                    $resep = Resep::find($row->protein);
                    if ($resep) {
                        return $resep->nama_resep;
                    } else {
                        return '--';
                    }
                })
                ->addColumn('nama_sayur', function ($row) {
                    $resep = Resep::find($row->sayur);
                    if ($resep) {
                        return $resep->nama_resep;
                    } else {
                        return '--';
                    }
                })
                ->addColumn('nama_susu', function ($row) {
                    $resep = Resep::find($row->susu);
                    if ($resep) {
                        return $resep->nama_resep;
                    } else {
                        return '--';
                    }
                })
                ->addColumn('nama_buah', function ($row) {
                    $resep = Resep::find($row->buah);
                    if ($resep) {
                        return $resep->nama_resep;
                    } else {
                        return '--';
                    }
                })
                ->addColumn('jumlah_porsi', function ($row) {
                    $jumlah = rincian_sekolah::where('id_menu_harian',$row->id)->sum('jumlah_penerima_total');
                    return $jumlah.' porsi';
                })
                ->addColumn('tanggal_masak', function ($row) {

                //return Carbon::parse($row->tanggal_kirim)->translatedFormat('l, d F Y') . ' | '. $row->status_pengajuan;
                return $row->menu . ' | ' . $row->status_pengajuan;
                })
                ->addColumn('action', function ($row) {
                $printCenterUrl = route('mastermenu.print-center', [
                    'id_menu' => $row['id'],
                    'tanggal' => $row->tanggal_kirim,
                    'tanggal_awal' => $row->tanggal_kirim,
                    'tanggal_akhir' => $row->tanggal_kirim,
                ]);

                if ($row->tanggal_kirim < '2025-08-19') {
                    $status = 'yes';
                    if (auth()->check() && in_array(auth()->user()->level, ['backoffice'])) {
                        $btn = '<a href="' . route('mastermenu.edit', $row['id']) . '" class="edit btn btn-primary btn-sm " id="btn-edit-post" data-id="' . $row['id'] . '">Edit</a>
                            <a href="' . route('mastermenu.delete', $row['id']) . '" class="edit btn btn-danger btn-sm delete-button" id="btn-delete-post " data-id="' . $row['id'] . '" >Delete</a>
                            <a href="' . route('rincian_bahan', $row['id']) . '" class="edit btn btn-success btn-sm " id="btn-delete-post " data-id="' . $row['id'] . '" >Detail Bahan</a>
                            <a href="' . route('export.detail_pengajuan_menu.excel', $row->id) . '" class="btn btn-success btn-sm" target="_blank"> <i class="fa fa-file-excel"></i>excel</a>                         
                            <a href="' . route('pdf_pengajuan_menu_3', $row['id']) . '" class="edit btn btn-secondary btn-sm " id="btn-download-post">Pengajuan</a>
                            <a href="' . route('cetak.rekap.ceklist', $row['id']) . '" class="edit btn btn-secondary btn-sm " id="btn-download-post">Checklist masak</a>
                            <a href="' . route('cetak.rekap.ceklist_hasil_masak', $row['id']) . '" class="edit btn btn-secondary btn-sm " id="btn-download-post">Checklist Hasil Masak</a>
                            <a href="' . $printCenterUrl . '" class="edit btn btn-dark btn-sm" id="btn-print-center">Pusat Cetak</a>
                            ';
                    } else {
                        $btn = '<a href="' . route('rincian_bahan', $row['id']) . '" class="edit btn btn-success btn-sm " id="btn-delete-post " data-id="' . $row['id'] . '" >Detail Bahan</a>
                            
                            <a href="' . route('export.detail_pengajuan_menu.excel', $row->id) . '" class="btn btn-success btn-sm" target="_blank"> <i class="fa fa-file-excel"></i>excel</a>   
                            <a href="' . route('pdf_pengajuan_menu_3', $row['id']) . '" class="edit btn btn-secondary btn-sm " id="btn-download-post">Pengajuan</a>
                            <button class="btn btn-success btn-sm acc-button" data-id="' . $row['id'] . '">ACC</button>
                            <a href="' . route('cetak.rekap.ceklist', $row['id']) . '" class="edit btn btn-secondary btn-sm " id="btn-download-post">Checklist masak</a>
                            <a href="' . route('cetak.rekap.ceklist_hasil_masak', $row['id']) . '" class="edit btn btn-secondary btn-sm " id="btn-download-post">Checklist Hasil Masak</a>
                            <a href="' . $printCenterUrl . '" class="edit btn btn-dark btn-sm" id="btn-print-center">Pusat Cetak</a>
                            ';
                    }
                } else {
                    $status = 'no';
                    if (auth()->check() && in_array(auth()->user()->level, ['backoffice'])) {
                        $btn = '<a href="' . route('mastermenu.edit', $row['id']) . '" class="edit btn btn-primary btn-sm " id="btn-edit-post" data-id="' . $row['id'] . '">Edit</a>
                            <a href="' . route('mastermenu.delete', $row['id']) . '" class="edit btn btn-danger btn-sm delete-button" id="btn-delete-post " data-id="' . $row['id'] . '" >Delete</a>
                            <a href="' . route('rincian_bahan', $row['id']) . '" class="edit btn btn-success btn-sm " id="btn-delete-post " data-id="' . $row['id'] . '" >Detail Bahan</a>
                            <a href="' . route('export.detail_pengajuan_menu.excel', $row->id) . '" class="btn btn-success btn-sm" target="_blank"> <i class="fa fa-file-excel"></i>excel</a>                         
                           
                            <a href="' . route('pdf_pengajuan_menu_3', $row['id']) . '" class="edit btn btn-secondary btn-sm " id="btn-download-post">Pengajuan</a>
                            <a href="' . route('cetak.rekap.ceklist', $row['id']) . '" class="edit btn btn-secondary btn-sm " id="btn-download-post">Checklist masak</a>
                            <a href="' . route('cetak.rekap.ceklist_hasil_masak', $row['id']) . '" class="edit btn btn-secondary btn-sm " id="btn-download-post">Checklist Hasil Masak</a>
                            <a href="' . $printCenterUrl . '" class="edit btn btn-dark btn-sm" id="btn-print-center">Pusat Cetak</a>
                            ';
                    } else {
                        $btn = '<a href="' . route('rincian_bahan', $row['id']) . '" class="edit btn btn-success btn-sm " id="btn-delete-post " data-id="' . $row['id'] . '" >Detail Bahan</a>
                            <a href="' . route('pdf_pengajuan_menu', $row['id']) . '" class="edit btn btn-secondary btn-sm " id="btn-download-post">Download</a>
                            <a href="' . route('pdf_pengajuan_menu_3', $row['id']) . '" class="edit btn btn-secondary btn-sm " id="btn-download-post">Pengajuan</a>
                            <button class="btn btn-success btn-sm acc-button" data-id="' . $row['id'] . '">ACC</button>
                            <a href="' . route('cetak.rekap.ceklist', $row['id']) . '" class="edit btn btn-secondary btn-sm " id="btn-download-post">Checklist masak</a>
                            <a href="' . route('cetak.rekap.ceklist_hasil_masak', $row['id']) . '" class="edit btn btn-secondary btn-sm " id="btn-download-post">Checklist Hasil Masak</a>
                            <a href="' . $printCenterUrl . '" class="edit btn btn-dark btn-sm" id="btn-print-center">Pusat Cetak</a>
                            ';
                    }
                }  
                
                    
                    return $btn;
                })
                ->rawColumns(['action', 'jumlah_porsi'])
                ->make(true);
        }
        return view('office/mastermenu.index', compact('header'));
    }


    public function edit(string $id): View
    {
        $header = "Edit Menu";
        //get product by ID
        $Menu = Menu::findOrFail($id);
        $karyawan = tb_karyawan::all();
        $gramasi_menu = TbGramasiMenu::where('id_menu',$id)->get();
        $karbohidrat = Resep::where('id_komponen_sehat', 1)->get();
        $protein = Resep::where('id_komponen_sehat', 2)->get();
        $sayur = Resep::where('id_komponen_sehat', 3)->get();
        $buah = Resep::where('id_komponen_sehat', 4)->get();
        $susu = Resep::where('id_komponen_sehat', 5)->get();
        

        //render view with product
        return view('office/mastermenu.edit', compact('Menu', 'karyawan','karbohidrat', 'protein', 'sayur','buah','susu', 'header'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        //validate form
        $request->validate([
            'menu'         => 'required|min:1',
            'nama_pengaju' => 'required',
            'tanggal_pengajuan' => 'required|min:1',
            'tanggal_kirim' => 'required|min:1',
            

        ]);

        //get product by ID
        $menu = Menu::findOrFail($id);



        //update product without image
        $menu->update([
            'menu'                      => $request->menu,
           
            'nama_pengaju'              => $request->nama_pengaju,
            'tanggal_pengajuan'         => $request->tanggal_pengajuan,
            'tanggal_kirim'             => $request->tanggal_kirim,
            
        ]);
        
        //redirect to index
        return redirect()->route('mastermenu.index')->with(['success' => 'Data Berhasil Diinput']);
    }

    public function create(): View|RedirectResponse
    {
        $jumlah_sekolah = DataSekolah::where('jumlah_siswa', '>', 0)->count();

        if ($jumlah_sekolah == 0) {
            return redirect()->route('mastermenu.index')->with(['error' => 'mohon isi data sekolah terlebih dahulu']);
        }
        $header = "Tambah menu";
        $karyawan = tb_karyawan::all();
        $gramasi_menu = TbGramasiMenu::all();
        $Menu = Menu::all();
        //$karbohidrat = Resep::where('id_komponen_sehat', 1)->get();
        $karbohidrat = $resep = DB::table('tb_resep as r')
            //->join('tb_menu_bahan as mb', 'mb.menu_id', '=', 'r.id')
            ->select('r.id', 'r.nama_resep')
            ->where('id_komponen_sehat', 1)
            ->groupBy('r.id', 'r.nama_resep')
            ->get();

        $protein = $resep = DB::table('tb_resep as r')
           // ->join('tb_menu_bahan as mb', 'mb.menu_id', '=', 'r.id')
            ->select('r.id', 'r.nama_resep')
            ->where('id_komponen_sehat', 2)
            ->groupBy('r.id', 'r.nama_resep')
            ->get();
        $sayur = $resep = DB::table('tb_resep as r')
           // ->join('tb_menu_bahan as mb', 'mb.menu_id', '=', 'r.id')
            ->select('r.id', 'r.nama_resep')
            ->where('id_komponen_sehat', 3)
            ->groupBy('r.id', 'r.nama_resep')
            ->get();
        $buah = $resep = DB::table('tb_resep as r')
           // ->join('tb_menu_bahan as mb', 'mb.menu_id', '=', 'r.id')
            ->select('r.id', 'r.nama_resep')
            ->where('id_komponen_sehat', 4)
            ->groupBy('r.id', 'r.nama_resep')
            ->get();
        $susu = $resep = DB::table('tb_resep as r')
            ->join('tb_menu_bahan as mb', 'mb.menu_id', '=', 'r.id')
            ->select('r.id', 'r.nama_resep')
            ->where('id_komponen_sehat', 5)
            ->groupBy('r.id', 'r.nama_resep')
            ->get();

        //render view with product
        return view('office/mastermenu.create', compact('Menu', 'karyawan', 'karbohidrat', 'protein', 'sayur', 'buah', 'susu', 'header'));
        
    }

    public function store(Request $request): RedirectResponse
    {
        //validate form
        $request->validate([
            'menu'         => 'required|min:1',
            'nama_pengaju' => 'required|min:1',
            'tanggal_pengajuan' => 'required|min:1',
            'tanggal_kirim' => 'required|min:1',
            //gramasi input A 
            'gramasi_karbo_a' => 'required',
            'porsi_tray_a' => 'required',
            'kg_tray_a' => 'required',
            'gramasi_lauk_a' => 'required',
            'gramasi_sayur_utama_a' => 'required',
            'gramasi_sayur_kedua_a' => 'required',
            'gramasi_buah_a' => 'required',
            'gramasi_suplemen_a' => 'required',
            //gramasi input A 
            'gramasi_karbo_b' => 'required',
            'porsi_tray_b' => 'required',
            'kg_tray_b' => 'required',
            'gramasi_lauk_b' => 'required',
            'gramasi_sayur_utama_b' => 'required',
            'gramasi_sayur_kedua_b' => 'required',
            'gramasi_buah_b' => 'required',
            'gramasi_suplemen_b' => 'required'

        ]);

        $golongan = $request->golongan;
        Carbon::setLocale('id');
        $cek_menu = Menu::where('tanggal_kirim' ,$request->tanggal_kirim)->count();
        if ($cek_menu >= 10) {
            return redirect()->route('mastermenu.create')->with(['error' => 'Data Menu Pernah Dibuat']);
        }
        $hariKirim = Carbon::parse($request->tanggal_kirim)->translatedFormat('l');
        //create product
        Menu::create([
            'menu'              => $request->menu,
           // 'karbohidrat'       => $request->karbohidrat,
           // 'protein'           => $request->protein,
           // 'sayur'             => $request->sayur,
           // 'buah'              => $request->buah,
           // 'susu'              => $request->susu,
            'nama_pengaju'      => $request->nama_pengaju,
            'tanggal_pengajuan' => $request->tanggal_pengajuan,
            'tanggal_kirim'     => $request->tanggal_kirim,
            'nomor_pengajuan'   => '',
            'status'            => 'draft',
            'hari_kirim'        => $hariKirim,
            'golongan'           => $golongan

        ]);
        $data_terakhir = Menu::latest()->first();
        TbGramasiMenu::create([
            'id_menu' => $data_terakhir->id,
            'gramasi_karbo_a' => $request->gramasi_karbo_a,
            'porsi_tray_a' => $request->porsi_tray_a,
            'kg_tray_a' => $request->kg_tray_a,
            'gramasi_lauk_a' => $request->gramasi_lauk_a,
            'gramasi_sayur_utama_a' => $request->gramasi_sayur_utama_a,
            'gramasi_sayur_kedua_a' => $request->gramasi_sayur_kedua_a,
            'gramasi_buah_a' => $request->gramasi_buah_a,
            'gramasi_suplemen_a' => $request->gramasi_suplemen_a,
            'gramasi_karbo_b' => $request->gramasi_karbo_b,
            'porsi_tray_b' => $request->porsi_tray_b,
            'kg_tray_b' => $request->kg_tray_b,
            'gramasi_lauk_b' => $request->gramasi_lauk_b,
            'gramasi_sayur_utama_b' => $request->gramasi_sayur_utama_b,
            'gramasi_sayur_kedua_b' => $request->gramasi_sayur_kedua_b,
            'gramasi_buah_b' => $request->gramasi_buah_b,
            'gramasi_suplemen_b' => $request->gramasi_suplemen_b
            

        ]);

        // Ambil nama hari dari tanggal_kirim (contoh: "sabtu")
        $hariKirim = strtolower(Carbon::parse($request->tanggal_kirim)->translatedFormat('l'));

        
        // Ambil data sekolah yang hanya aktif di hari_kirim
        $hariKirim = $hariKirim; // gunakan hari kirim yang sudah diambil
        
        if($golongan == 'umum')
        {
            $sekolah = DataSekolah::join('sekolah_aktif', 'tb_data_sekolah.id', '=', 'sekolah_aktif.id_tb_data_sekolah')
            ->where("sekolah_aktif.$hariKirim", 1) // Hanya sekolah yang aktif pada hari kirim
            ->select(
                'tb_data_sekolah.*',
                
            )
            ->get();

        }elseif($golongan == 'pax_a')
        {
            $sekolah = DataSekolah::join('sekolah_aktif', 'tb_data_sekolah.id', '=', 'sekolah_aktif.id_tb_data_sekolah')
            ->where("sekolah_aktif.$hariKirim", 1) // Hanya sekolah yang aktif pada hari kirim
            ->whereIn('tb_data_sekolah.jenjang_sekolah', [
                'KB/Sederajat',
                'TK/Sederajat',
                'SD/Sederajat'
            ])
             ->select(
                'tb_data_sekolah.id',
                'tb_data_sekolah.nama_sekolah',
                'tb_data_sekolah.status_aktif',
                'tb_data_sekolah.jenjang_sekolah',
                'tb_data_sekolah.jumlah_siswa',
                'tb_data_sekolah.jumlah_a',
                'tb_data_sekolah.hari_sekolah',
                DB::raw('0 as jumlah_b') // override jadi 0
            )
            ->get();     
        }elseif($golongan == 'pax_b')
        {
                $sekolah = DataSekolah::join('sekolah_aktif', 'tb_data_sekolah.id', '=', 'sekolah_aktif.id_tb_data_sekolah')
                ->where("sekolah_aktif.$hariKirim", 1) // Hanya sekolah yang aktif pada hari kirim
                ->whereIn('tb_data_sekolah.jenjang_sekolah', [
                    'KB/Sederajat',
                    'TK/Sederajat',
                    'SD/Sederajat',
                    'SMP/Sederajat',
                    'SMA/Sederajat',
                    'SMK/Sederajat'
                ])
                ->select(
                    'tb_data_sekolah.id',
                    'tb_data_sekolah.nama_sekolah',
                    'tb_data_sekolah.status_aktif',
                    'tb_data_sekolah.jenjang_sekolah',
                    'tb_data_sekolah.jumlah_siswa',
                    'tb_data_sekolah.jumlah_b',
                    'tb_data_sekolah.hari_sekolah',
                    DB::raw('0 as jumlah_a') // override jadi 0
                )
                ->get(); 
        }elseif($golongan == 'busui_bumil')
        {
            $sekolah = DataSekolah::join('sekolah_aktif', 'tb_data_sekolah.id', '=', 'sekolah_aktif.id_tb_data_sekolah')
            ->where("sekolah_aktif.$hariKirim", 1) // Hanya sekolah yang aktif pada hari kirim
            ->whereIn('tb_data_sekolah.jenjang_sekolah', [
                
                'B3',
                'Bumil/Busui'
            ])
             ->select(
                
                'tb_data_sekolah.id',
                'tb_data_sekolah.nama_sekolah',
                'tb_data_sekolah.status_aktif',
                'tb_data_sekolah.jenjang_sekolah',
                'tb_data_sekolah.jumlah_siswa',
                'tb_data_sekolah.jumlah_b',
                'tb_data_sekolah.hari_sekolah',
                DB::raw('0 as jumlah_a') // override jadi 0
            )
            ->get(); 
        }elseif($golongan == 'balita' || $golongan == 'baduta')
        {
            $sekolah = DataSekolah::join('sekolah_aktif', 'tb_data_sekolah.id', '=', 'sekolah_aktif.id_tb_data_sekolah')
            ->where("sekolah_aktif.$hariKirim", 1) // Hanya sekolah yang aktif pada hari kirim
            ->whereIn('tb_data_sekolah.jenjang_sekolah', [
                'B3',
                'Balita/Baduta'
            ])
             ->select(
                'tb_data_sekolah.id',
                'tb_data_sekolah.nama_sekolah',
                'tb_data_sekolah.status_aktif',
                'tb_data_sekolah.jenjang_sekolah',
                'tb_data_sekolah.jumlah_siswa',
                'tb_data_sekolah.hari_sekolah',
                'tb_data_sekolah.jumlah_a',
                DB::raw('0 as jumlah_b') // override jadi 0
            )
            ->get(); 
        }   
        
        foreach ($sekolah as $data) {
            $jumlah_siswa = $data->jumlah_a+$data->jumlah_b;
            rincian_sekolah::create([
                'id_menu_harian' => $data_terakhir->id,
                'id_sekolah' => $data->id,
                'jumlah_penerima_total' => $jumlah_siswa,
                'jumlah_penerima_a' => $data->jumlah_a ?? 0,//tambahan jika null maka 0
                'jumlah_penerima_b' => $data->jumlah_b ?? 0,//tambahan jika null maka 0
                'status' => 0
            ]);
        }
        
        //redirect to index
        //return redirect()->route('mastermenu.index')->with(['success' => 'Data Berhasil Disimpan!']);
        return redirect()->route('rincian_sekolah_harian', $data_terakhir->id)->with(['success' => 'Data Berhasil Dinput']);
    }

    public function destroy($id)
    {
        //get product by ID
        DB::table('rincian_menu_harian')->where('id_menu_harian', $id)->delete();
        DB::table('rincian_sekolah')->where('id_menu_harian', $id)->delete();
        DB::table('tb_gramasi_menu')->where('id_menu', $id)->delete();
        DB::table('tb_histori_menu')->where('id_menu', $id)->delete();

        // Hapus data dari tb_menu
        DB::table('tb_menu')->where('id', $id)->delete();

        //redirect to index
        return redirect()->route('mastermenu.index')->with(['success' => 'Data Berhasil Dihapus!']);
    }

        public function index_rincian_sekolah_harian($idmenu)
        {
            $header = "Rincian Sekolah Penerima";
            $data_menu_harian = Menu::where('id', $idmenu)->first();
            $dapur = DataDapur::firstOrDefault();
            $jumlah = rincian_sekolah::where('id_menu_harian', $idmenu)->sum('jumlah_penerima_total');

        if (request()->ajax()) {
                //$users = User::query();
                $sekolah = rincian_sekolah::where('id_menu_harian', $idmenu)->join('tb_data_sekolah as d_sekolah', 'rincian_sekolah.id_sekolah', '=', 'd_sekolah.id')
                    ->select('rincian_sekolah.*', 'd_sekolah.nama_sekolah as sekolah', 'd_sekolah.jenjang_sekolah')
                    ->get();

                //$Menu = Menu::all();
                return DataTables::of($sekolah)
                    ->addIndexColumn() // Menambah index
                    ->addColumn('nama_dan_tingkat_sekolah', function ($row){
                        return $row->sekolah." (". $row->jenjang_sekolah.")";
                        
                    })
                    ->addColumn('input_jumlah', function ($row) {
                        return $row->jumlah_penerima_total . ' anak';
                    })
                    ->addColumn('input_jumlah_a', function ($row) {
                        return '<input type="number" class="form-control jumlaha" data-id="' . $row->id . '" value="' . $row->jumlah_penerima_a . '"> anak';
                    })
                    ->addColumn('input_jumlah_b', function ($row) {
                        return '<input type="number" class="form-control jumlahb" data-id="' . $row->id . '" value="' . $row->jumlah_penerima_b . '"> anak';
                    })
                    ->addColumn('action', function ($row) {
              
                    return '<button class="btn btn-success btn-sm update-jumlah" data-id="' . $row->id . '">Update</button>';
                    })
                    ->rawColumns(['action', 'input_jumlah', 'input_jumlah_b', 'input_jumlah_a', 'nama_dan_tingkat_sekolah'])
                    ->make(true);
            }
            return view('office/mastermenu.index_rincian_sekolah', compact('header', 'data_menu_harian','dapur', 'jumlah', 'idmenu'));
        }

        public function updateJumlahSekolah(Request $request)
        {
            try {
                $request->validate([
                    'id' => 'required|exists:rincian_sekolah,id',
                    'jumlaha' => 'required|numeric|min:0',
                    'jumlahb' => 'required|numeric|min:0',
                ]);
                
                $total = $request->jumlaha + $request->jumlahb;
                $rincian = rincian_sekolah::find($request->id);
                $rincian->jumlah_penerima_a = $request->jumlaha;
                $rincian->jumlah_penerima_b = $request->jumlahb;
                $rincian->jumlah_penerima_total = $total;
                $rincian->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Jumlah penerima sekolah berhasil diperbarui.',
                    'data' => $rincian,
                    'meta' => [
                        'timestamp' => now()->toIso8601String(),
                        'code' => 200,
                    ],
                ], 200);
            } catch (\Illuminate\Validation\ValidationException $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi data gagal.',
                    'errors' => $e->errors(),
                    'meta' => [
                        'timestamp' => now()->toIso8601String(),
                        'code' => 422,
                    ],
                ], 422);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui jumlah penerima sekolah.',
                    'errors' => ['exception' => $e->getMessage()],
                    'meta' => [
                        'timestamp' => now()->toIso8601String(),
                        'code' => 500,
                    ],
                ], 500);
            }
        }
        
        

        public function updateRincianSekolah($idmenu) {
                $sekolah = rincian_sekolah::where('id_menu_harian',$idmenu)->get();
                $menu = Menu::findOrFail($idmenu);
                $bulan_pengajuan = date('Ym', strtotime($menu->tanggal_pengajuan)); // Format YYYY-MM untuk filter
                $datadapur = DataDapur::first();
                $count = Menu::whereMonth('tanggal_pengajuan', date('m', strtotime($menu->tanggal_pengajuan)))
                ->whereYear('tanggal_pengajuan', date('Y', strtotime($menu->tanggal_pengajuan)))
                ->count();
                $jumlah_a = rincian_sekolah::where('id_menu_harian', $idmenu)->sum('jumlah_penerima_a');
                if($jumlah_a > 0 )
                {
                    $nomor_pengajuan = 'PMH-' . $datadapur->nomor_dapur . '-' . $bulan_pengajuan . '' . str_pad($count, 3, '0', STR_PAD_LEFT).'A';
                
                }else{
                    $nomor_pengajuan = 'PMH-' . $datadapur->nomor_dapur . '-' . $bulan_pengajuan . '' . str_pad($count, 3, '0', STR_PAD_LEFT).'B';
                
                }
                
                $menu->update([
                    'nomor_pengajuan' => $nomor_pengajuan,
                ]);
                $data_hasil_sekolah = DB::table('rincian_sekolah')
                ->selectRaw('
                    SUM(jumlah_penerima_total) AS total_penerima, 
                    SUM(jumlah_penerima_a) AS total_penerima_a, 
                    SUM(jumlah_penerima_b) AS total_penerima_b
                ')
                ->where('id_menu_harian', $idmenu)
                ->first(); // Mengambil satu baris hasil query

                // input histori menu a
                $cek_histori_a =  DB::table('tb_histori_menu')->where('id_menu', $idmenu)
                                  ->where('kode','A')->count();
                if($cek_histori_a < 1 )
                {
                    DB::table('tb_histori_menu')->insert([
                        'id_menu' => $idmenu,
                        'kode' => 'A',
                        'total_porsi' => $data_hasil_sekolah->total_penerima_a,
                    ]);
                }

                // input histori menu b
                $cek_histori_b =  DB::table('tb_histori_menu')->where('id_menu', $idmenu)
                    ->where('kode', 'B')->count();
                if ($cek_histori_a < 1) {
                    DB::table('tb_histori_menu')->insert([
                        'id_menu' => $idmenu,
                        'kode' => 'B',
                        'total_porsi' => $data_hasil_sekolah->total_penerima_b,
                    ]);
                }

                


            return redirect('/rincian_bahan/' . $idmenu)->with('success', 'Data Berhasil dimasukkan!');
        }


        public function rincian_bahan($idmenu)
        {
            $header = "Rincian Bahan";
            $data_menu_harian = Menu::where('id', $idmenu)->first();
            $dapur = DataDapur::first();
            $satuan = TbSatuan::all();
            
            $jumlah_kontrak = TbPo::count();
            if ($jumlah_kontrak < 9) {
                $nomor_kontrak_suffix = '00' . ($jumlah_kontrak + 1);
            } elseif ($jumlah_kontrak < 99) {
                $nomor_kontrak_suffix = '0' . ($jumlah_kontrak + 1);
            } else {
                $nomor_kontrak_suffix = ($jumlah_kontrak + 1);
            }
            $bulan_PO = Carbon::now()->format('Ym');
            $nomor_PO = 'PO-Y0' . ($dapur->nomor_dapur ?? '0') . '-' . $bulan_PO . $nomor_kontrak_suffix;
            
            $jumlah = rincian_sekolah::where('id_menu_harian', $idmenu)->sum('jumlah_penerima_total');
            $jumlah_a = rincian_sekolah::where('id_menu_harian', $idmenu)->sum('jumlah_penerima_a');
            $jumlah_b = rincian_sekolah::where('id_menu_harian', $idmenu)->sum('jumlah_penerima_b');
            
            $bumbu = TbMasterBahan::join('tb_satuan','tb_master_bahan.satuan_bahan','tb_satuan.id')->where('jenis', '!=', 7)
            ->select('tb_master_bahan.*','tb_satuan.satuan')->get();
            
            $karbohidrat = DB::table('tb_resep as r')
                //->join('tb_menu_bahan as mb', 'mb.menu_id', '=', 'r.id')
                ->select('r.id', 'r.nama_resep')
                ->where('id_komponen_sehat', 1)
                ->groupBy('r.id', 'r.nama_resep')
                ->get();

            $protein = DB::table('tb_resep as r')
               // ->join('tb_menu_bahan as mb', 'mb.menu_id', '=', 'r.id')
                ->select('r.id', 'r.nama_resep')
                ->where('id_komponen_sehat', 2)
                ->groupBy('r.id', 'r.nama_resep')
                ->get();
            $sayur =  DB::table('tb_resep as r')
                //->join('tb_menu_bahan as mb', 'mb.menu_id', '=', 'r.id')
                ->select('r.id', 'r.nama_resep')
                ->where('id_komponen_sehat', 3)
                ->groupBy('r.id', 'r.nama_resep')
                ->get();
            $buah =  DB::table('tb_resep as r')
                //->join('tb_menu_bahan as mb', 'mb.menu_id', '=', 'r.id')
                ->select('r.id', 'r.nama_resep')
                ->where('id_komponen_sehat', 4)
                ->groupBy('r.id', 'r.nama_resep')
                ->get();
            $suplemen = DB::table('tb_resep as r')
                ->select('r.id', 'r.nama_resep')
                ->where('id_komponen_sehat', 5)
                ->groupBy('r.id', 'r.nama_resep')
                ->get();

            // data karbo    
            $detail_karbo = 0;
            if($data_menu_harian->status_pengajuan == 'pending')    
            {
                $table_karbo = DB::table('tb_rincian_menu_temp')
                ->join('tb_master_bahan', 'tb_rincian_menu_temp.id_bahan', '=', 'tb_master_bahan.id')
                ->select(
                    'tb_master_bahan.bahan',
                    'tb_rincian_menu_temp.jumlah',
                    'tb_rincian_menu_temp.harga',
                    'tb_rincian_menu_temp.total_harga'
                )
                ->where('tb_rincian_menu_temp.id_menu', $idmenu)
                ->get();
                $detail_karbo = DB::table('tb_rumus_perhitungan_karbo')->where('id_menu', $idmenu)->first();
            }else{
                $table_karbo = DB::table('rincian_menu_harian')
                ->join('tb_master_bahan', 'rincian_menu_harian.id_bahan', '=', 'tb_master_bahan.id')
                ->select(
                    'tb_master_bahan.bahan',
                    'rincian_menu_harian.jumlah',
                    'rincian_menu_harian.harga',
                    'rincian_menu_harian.total_harga'
                )
                ->where('rincian_menu_harian.id_menu_harian', $idmenu)
                ->get();
                 $detail_karbo = DB::table('tb_rumus_perhitungan_karbo')->where('id_menu', $idmenu)->first();
            }
            $golongan = 'A';
            if ($jumlah_a != 0) {
                $golongan = 'A';
            } else {
                $golongan = 'B';
            }

            $paketmenu = DB::table('tb_paket_menu')
                ->leftJoin('tb_resep as karbo', 'tb_paket_menu.resep_karbo', '=', 'karbo.id')
                ->leftJoin('tb_resep as protein', 'tb_paket_menu.resep_protein', '=', 'protein.id')
                ->leftJoin('tb_resep as sayur', 'tb_paket_menu.resep_sayur', '=', 'sayur.id')
                ->leftJoin('tb_resep as buah', 'tb_paket_menu.resep_buah', '=', 'buah.id')
                ->leftJoin('tb_resep as suplemen', 'tb_paket_menu.resep_suplemen', '=', 'suplemen.id')
                ->where('tb_paket_menu.paket', $golongan)
                ->select(
                    'tb_paket_menu.id',
                    'karbo.nama_resep as nama_karbo',
                    'protein.nama_resep as nama_protein',
                    'sayur.nama_resep as nama_sayur',
                    'buah.nama_resep as nama_buah',
                    'suplemen.nama_resep as nama_suplemen'
                )
                ->get();
            
            // data sayur
            $sayur_1 = 0;
            $sayur_2 = 0;
            $sayur_3 = 0;
            $sayur_4 = 0;
            $rumus_sayur = DB::table('tb_rumus_perhitungan_sayur')
            ->where('id_menu', $idmenu)
            ->orderBy('id', 'desc')
            ->first();
            
            $rumus_sayur_semua = DB::table('tb_rumus_perhitungan_sayur')->get();            
            if(!$rumus_sayur )
            {

            } else if ($rumus_sayur->status = 1 ){


            // Ambil bahan dengan status_bahan_baku = 1 (misal: sayur porsi A)
            $sayur_1 = DB::table('tb_menu_bahan')
                
                ->join('tb_master_bahan', 'tb_menu_bahan.bahan_id', '=', 'tb_master_bahan.id')
                ->join('tb_box_bahan_baku', 'tb_master_bahan.id','=', 'tb_box_bahan_baku.id_bahan')
                ->select('tb_master_bahan.bahan', 'tb_menu_bahan.status_bahan_baku', 'tb_box_bahan_baku.penyusutan', 'tb_master_bahan.id')
                ->where('tb_menu_bahan.menu_id', $data_menu_harian->sayur)
                //->where('id_menu', $idmenu)
                ->where('tb_menu_bahan.status_bahan_baku', 1)
                ->first() ?? 0;

            // Ambil bahan dengan status_bahan_baku = 2 (misal: sayur porsi B)
            $sayur_2 = DB::table('tb_menu_bahan')
                ->join('tb_master_bahan', 'tb_menu_bahan.bahan_id', '=', 'tb_master_bahan.id')
                ->join('tb_box_bahan_baku', 'tb_master_bahan.id', '=', 'tb_box_bahan_baku.id_bahan')
                ->select('tb_master_bahan.bahan', 'tb_menu_bahan.status_bahan_baku', 'tb_box_bahan_baku.penyusutan', 'tb_master_bahan.id')
                ->where('tb_menu_bahan.menu_id', $data_menu_harian->sayur)
                //->where('id_menu', $idmenu)
                ->where('tb_menu_bahan.status_bahan_baku', 2)
                ->first() ?? 0;

            // Ambil bahan dengan status_bahan_baku = 4 (misal: sayur porsi C)
            $sayur_3 = DB::table('tb_menu_bahan')
                ->join('tb_master_bahan', 'tb_menu_bahan.bahan_id', '=', 'tb_master_bahan.id')
                ->join('tb_box_bahan_baku', 'tb_master_bahan.id', '=', 'tb_box_bahan_baku.id_bahan')
                ->select('tb_master_bahan.bahan', 'tb_menu_bahan.status_bahan_baku', 'tb_box_bahan_baku.penyusutan', 'tb_master_bahan.id')
                ->where('tb_menu_bahan.menu_id', $data_menu_harian->sayur)
               // ->where('id_menu', $idmenu)
                ->where('tb_menu_bahan.status_bahan_baku', 4)
                ->first() ?? 0;

            // Ambil bahan dengan status_bahan_baku = 5 (misal: sayur porsi D)
            $sayur_4 = DB::table('tb_menu_bahan')
                ->join('tb_master_bahan', 'tb_menu_bahan.bahan_id', '=', 'tb_master_bahan.id')
                ->join('tb_box_bahan_baku', 'tb_master_bahan.id', '=', 'tb_box_bahan_baku.id_bahan')
                ->select('tb_master_bahan.bahan', 'tb_menu_bahan.status_bahan_baku', 'tb_box_bahan_baku.penyusutan', 'tb_master_bahan.id')
                ->where('tb_menu_bahan.menu_id', $data_menu_harian->sayur)
                //->where('id_menu', $idmenu)
                ->where('tb_menu_bahan.status_bahan_baku', 5)
                ->first() ?? 0;

            }
            $rumus_sayur = DB::table('tb_rumus_perhitungan_sayur')
            ->where('id_menu', $idmenu)
                ->orderBy('id', 'desc')
                ->first();

            // data Protein
            $protein_1 = 0;
            $protein_2 = 0;
            $protein_3 = 0;
            $protein_4 = 0;
            $rumus_protein = DB::table('tb_rumus_perhitungan_protein')
            ->where('id_menu', $idmenu)
            ->orderBy('id', 'desc')
            ->first();
            
            $protein_sayur_semua = DB::table('tb_rumus_perhitungan_sayur')->where('id_menu', $idmenu)->get();            
            if(!$rumus_protein )
            {

            } else if ($rumus_protein->status = 1 ){


            // Ambil bahan dengan status_bahan_baku = 1 (misal: sayur porsi A)
            $protein_1 = DB::table('tb_menu_bahan')
                
                ->join('tb_master_bahan', 'tb_menu_bahan.bahan_id', '=', 'tb_master_bahan.id')
                ->join('tb_box_bahan_baku', 'tb_master_bahan.id','=', 'tb_box_bahan_baku.id_bahan')
                ->join('tb_satuan', 'tb_master_bahan.satuan_bahan', '=', 'tb_satuan.id')
                ->select('tb_master_bahan.bahan', 'tb_satuan.satuan', 'tb_menu_bahan.status_bahan_baku', 'tb_box_bahan_baku.penyusutan', 'tb_master_bahan.id')
                ->where('tb_menu_bahan.menu_id', $data_menu_harian->protein)
                //->where('id_menu', $idmenu)
                ->where('tb_menu_bahan.status_bahan_baku', 1)
                ->first() ?? 0;

            // Ambil bahan dengan status_bahan_baku = 2 (misal: sayur porsi B)
            $protein_2 =  DB::table('tb_menu_bahan')

                ->join('tb_master_bahan', 'tb_menu_bahan.bahan_id', '=', 'tb_master_bahan.id')
                ->join('tb_box_bahan_baku', 'tb_master_bahan.id', '=', 'tb_box_bahan_baku.id_bahan')
                ->join('tb_satuan', 'tb_master_bahan.satuan_bahan', '=', 'tb_satuan.id')
                ->select('tb_master_bahan.bahan', 'tb_satuan.satuan', 'tb_menu_bahan.status_bahan_baku', 'tb_box_bahan_baku.penyusutan', 'tb_master_bahan.id')
                ->where('tb_menu_bahan.menu_id', $data_menu_harian->protein)
                //->where('id_menu', $idmenu)
                ->where('tb_menu_bahan.status_bahan_baku', 2)
                ->first() ?? 0;

            // Ambil bahan dengan status_bahan_baku = 4 (misal: sayur porsi C)
            $protein_3 =  DB::table('tb_menu_bahan')

                ->join('tb_master_bahan', 'tb_menu_bahan.bahan_id', '=', 'tb_master_bahan.id')
                ->join('tb_box_bahan_baku', 'tb_master_bahan.id', '=', 'tb_box_bahan_baku.id_bahan')
                ->join('tb_satuan', 'tb_master_bahan.satuan_bahan', '=', 'tb_satuan.id')
                ->select('tb_master_bahan.bahan', 'tb_satuan.satuan', 'tb_menu_bahan.status_bahan_baku', 'tb_box_bahan_baku.penyusutan', 'tb_master_bahan.id')
                ->where('tb_menu_bahan.menu_id', $data_menu_harian->protein)
                //->where('id_menu', $idmenu)
                ->where('tb_menu_bahan.status_bahan_baku', 4)
                ->first() ?? 0;

            // Ambil bahan dengan status_bahan_baku = 5 (misal: sayur porsi D)
            $protein_4 =  DB::table('tb_menu_bahan')

                ->join('tb_master_bahan', 'tb_menu_bahan.bahan_id', '=', 'tb_master_bahan.id')
                ->join('tb_box_bahan_baku', 'tb_master_bahan.id', '=', 'tb_box_bahan_baku.id_bahan')
                ->join('tb_satuan', 'tb_master_bahan.satuan_bahan', '=', 'tb_satuan.id')
                ->select('tb_master_bahan.bahan', 'tb_satuan.satuan', 'tb_menu_bahan.status_bahan_baku', 'tb_box_bahan_baku.penyusutan', 'tb_master_bahan.id')
                ->where('tb_menu_bahan.menu_id', $data_menu_harian->protein)
                //->where('id_menu', $idmenu)
                ->where('tb_menu_bahan.status_bahan_baku', 5)
                ->first() ?? 0;
        }
            $rumus_protein = DB::table('tb_rumus_perhitungan_protein')
            ->where('id_menu', $idmenu)
                ->orderBy('id', 'desc')
                ->first();

            if (request()->ajax() && !request()->boolean('full_page')) {
                $isPending = $data_menu_harian->status_pengajuan === 'pending';

                if ($isPending) {
                    $rincian_menu_harian = DB::table('tb_rincian_menu_temp as r')
                        ->leftJoin('tb_resep as resep', 'r.id_resep', '=', 'resep.id')
                        ->leftJoin('tb_master_bahan as bahan', 'r.id_bahan', '=', 'bahan.id')
                        ->leftJoin('tb_satuan as satuan', 'r.id_satuan', '=', 'satuan.id')
                        ->where('r.id_menu', $idmenu)
                        ->select(
                            'r.id',
                            'r.id_menu as id_menu_harian',
                            'r.id_resep',
                            'resep.nama_resep',
                            'r.id_bahan',
                            'bahan.bahan as nama_bahan',
                            'r.jumlah',
                            'r.id_satuan',
                            'satuan.satuan as nama_satuan',
                            'r.harga',
                            'r.total_harga',
                            'r.jumlah_box',
                            'r.keterangan'
                        )
                        ->get();
                } else {
                    $rincian_menu_harian = rincian_menu_harian::where('id_menu_harian', $idmenu)->get();
                }

                return DataTables::of($rincian_menu_harian)
                    ->addIndexColumn() // Menambah index
                    ->addColumn('resep', function ($row) {
                        if ((int) ($row->bumbu ?? 0) === 1)
                        {
                            return 'Tambahan';
                        }else{
                            $resep = Resep::where('id', $row->id_resep)->first();
                            return $resep->nama_resep;
                        }
                        
                    })
                    ->addColumn('bahan', function ($row) {
                        $bahan = TbMasterBahan::where('id', $row->id_bahan)->first();
                        return $bahan ? $bahan->bahan : '-'; // Jika null, tampilkan '-'
                    })
                    ->addColumn('satuan', function ($row) {
                    $bahan = TbMasterBahan::where('id', $row->id_bahan)->first();
                    if ($bahan) {
                        $satuan = TbSatuan::where('id', $row->id_satuan)->first();
                        //return $satuan ? $satuan->satuan : '-'; // Jika null, tampilkan '-'
                        return $satuan->satuan; // Jika null, tampilkan '-'
                    }
                    return '-';
                    })
                    ->addColumn('jumlah_bahan', function ($row) {
                       // return '<input type="number" class="form-control jumlah" data-id="' . $row->id . '" value="' . $row->jumlah . '"> <button class="btn btn-success btn-sm update-jumlah" data-id="' . $row->id . '">Update</button> ';
                        return '<input type="number" class="form-control jumlah" data-id="' . $row->id . '" value="' . $row->jumlah . '"> <button class="btn btn-success btn-sm update-jumlah" data-id="' . $row->id . '">Update</button>';
                   
                        //return number_format($row->jumlah, 0, ',', '.'); // Format: 1.234,56
                    })
                    ->addColumn('action', function ($row) use ($isPending) {
                        if ($isPending) {
                            return '<a href="' . route('rincian_menu_temp.delete', $row->id) . '" class="edit btn btn-danger btn-sm delete-button" onclick="return confirm(\'Yakin hapus data temp ini?\')">Delete</a>';
                        }

                        return '<a href="' . route('Hapus_bahan_rincian', $row['id']) . '" class="edit btn btn-danger btn-sm delete-button" id="btn-delete-post " data-id="' . $row['id'] . '" onclick="return confirm("Yakin hapus?")" >Delete</a>';
                        //return number_format($row->jumlah, 0, ',', '.'); // Format: 1.234,56
                     })
                ->rawColumns(['resep', 'bahan','satuan','jumlah_bahan', 'action'])
                    ->make(true);
            }

            $extimasi_biaya = DB::table('tb_rincian_menu_temp')->where('id_menu', $idmenu)->sum('total_harga') ?? 0;
            $extimasi_biaya_karbo = DB::table('tb_rincian_menu_temp')->where('id_menu', $idmenu)->where('id_resep',$data_menu_harian->karbohidrat)->sum('total_harga') ?? 0;
            $extimasi_biaya_lauk = DB::table('tb_rincian_menu_temp')->where('id_menu', $idmenu)->where('id_resep', $data_menu_harian->protein)->sum('total_harga') ?? 0;
            $extimasi_biaya_sayur = DB::table('tb_rincian_menu_temp')->where('id_menu', $idmenu)->where('id_resep', $data_menu_harian->sayur)->sum('total_harga') ?? 0;
            $extimasi_biaya_buah = DB::table('tb_rincian_menu_temp')->where('id_menu', $idmenu)->where('id_resep', $data_menu_harian->buah)->sum('total_harga') ?? 0;
            $extimasi_biaya_suplemen = DB::table('tb_rincian_menu_temp')->where('id_menu', $idmenu)->where('id_resep', $data_menu_harian->susu)->sum('total_harga') ?? 0;
            $nama_karbo = Resep::find($data_menu_harian->karbohidrat)??'-';
            $nama_lauk = Resep::find($data_menu_harian->protein) ?? '-';
            $nama_sayur = Resep::find($data_menu_harian->sayur) ?? '-';
            $nama_buah = Resep::find($data_menu_harian->buah) ?? '-';
            $nama_suplemen = Resep::find($data_menu_harian->susu) ?? '-';

            // Hitung nutrisi otomatis dari tb_resep_realisasi_akg untuk tiap komponen resep
            $components = [
                'karbo' => $data_menu_harian->karbohidrat,
                'protein' => $data_menu_harian->protein,
                'sayur' => $data_menu_harian->sayur,
                'buah' => $data_menu_harian->buah,
                'suplemen' => $data_menu_harian->susu,
            ];

            $nutrisi_components = [];
            $total_nutrisi = [
                'energi_kcal' => 0,
                'protein_g' => 0,
                'lemak_g' => 0,
                'karbohidrat_g' => 0,
                'serat_g' => 0,
                'natrium_mg' => 0,
            ];

            foreach ($components as $key => $resepId) {
                if (empty($resepId)) {
                    $nut = (object) [
                        'energi_kcal' => 0,
                        'protein_g' => 0,
                        'lemak_g' => 0,
                        'karbohidrat_g' => 0,
                        'serat_g' => 0,
                        'natrium_mg' => 0,
                    ];
                } else {
                    $nut = ResepRealisasiAkg::where('id_resep', $resepId)
                        ->selectRaw('COALESCE(SUM(energi_kcal),0) as energi_kcal, COALESCE(SUM(protein_g),0) as protein_g, COALESCE(SUM(lemak_g),0) as lemak_g, COALESCE(SUM(karbohidrat_g),0) as karbohidrat_g, COALESCE(SUM(serat_g),0) as serat_g, COALESCE(SUM(natrium_mg),0) as natrium_mg')
                        ->first();
                }

                $nutrisi_components[$key] = $nut;

                // tambahkan ke total
                $total_nutrisi['energi_kcal'] += (float) ($nut->energi_kcal ?? 0);
                $total_nutrisi['protein_g'] += (float) ($nut->protein_g ?? 0);
                $total_nutrisi['lemak_g'] += (float) ($nut->lemak_g ?? 0);
                $total_nutrisi['karbohidrat_g'] += (float) ($nut->karbohidrat_g ?? 0);
                $total_nutrisi['serat_g'] += (float) ($nut->serat_g ?? 0);
                $total_nutrisi['natrium_mg'] += (float) ($nut->natrium_mg ?? 0);
            }
            $budget_harga = $jumlah_a * 8000 + $jumlah_b*10000;
            
            // Jangan overwrite saat refresh: hitung otomatis hanya jika data gizi belum ada.
            try {
                $menuGiziHarian = MenuGiziHarian::where('id_menu', $idmenu)->first();

                if (!$menuGiziHarian) {
                    $service = new MenuNutrisiCalculationService();
                    $service->calculateAndStoreMenuNutrition($idmenu, $jumlah);
                    $menuGiziHarian = MenuGiziHarian::where('id_menu', $idmenu)->first();
                }
            } catch (\Exception $e) {
                \Log::error('Gagal menghitung nutrisi menu: ' . $e->getMessage());
                // Fallback: tetap tampilkan data terakhir yang tersimpan di tb_menu_gizi_harian
                $menuGiziHarian = MenuGiziHarian::where('id_menu', $idmenu)->first();
            }
            
            return view('office/mastermenu.index_rincian_menu_rev', compact(
                'header',
                'budget_harga',
                'satuan',
                'extimasi_biaya',
                'nama_karbo',
                'nama_lauk',
                'nama_sayur',
                'nama_buah',
                'nama_suplemen',
                'extimasi_biaya_karbo',
                'extimasi_biaya_lauk',
                'extimasi_biaya_sayur',
                'extimasi_biaya_buah',
                'extimasi_biaya_suplemen',
                'bumbu',
                'data_menu_harian', 
                'dapur', 
                'jumlah', 
                'idmenu',
                'karbohidrat',
                'protein',
                'sayur',
                'buah',
                'suplemen',
                'paketmenu',
                'detail_karbo',
                'sayur_1','sayur_2','sayur_3','sayur_4',
                'rumus_sayur','rumus_sayur_semua',
                'protein_1','protein_2','protein_3','protein_4',
                'rumus_protein','protein_sayur_semua',
                'nomor_PO',
                'menuGiziHarian',
            ));
        }

        public function template()
        {
            return view('office/mastermenu.template_pengajuan_menu');
        }

        public function pdf_pengajuan_menu($idmenu)
        {
            $data_menu_harian = Menu::where('id', $idmenu)->first();
            $dapur = DataDapur::first();
            // Render file template.blade.php tanpa data
            //$pdf = Pdf::loadView('office/mastermenu.template_pengajuan_menu');
            $rincian_menu_harian = rincian_menu_harian::where('id_menu_harian', $idmenu)->get();
            $gramasi_menu = TbGramasiMenu::where('id_menu', $idmenu)->first(); 
            if(!$gramasi_menu)
            {
            $gramasi_menu = 0;
            }
            $data_sekolah = DB::select("
            SELECT 
                @rownum := @rownum + 1 AS nomor_urut,
                sekolah.nama_sekolah,
                r.jumlah_penerima_a,
                r.jumlah_penerima_b,
                r.jumlah_penerima_total
            FROM rincian_sekolah r
            JOIN tb_data_sekolah sekolah ON r.id_sekolah = sekolah.id
            CROSS JOIN (SELECT @rownum := 0) AS init
            WHERE r.id_menu_harian = ?
              AND r.jumlah_penerima_total > 0
            ", [$idmenu]);
            $total_a = DB::table('rincian_sekolah as r')
            ->join('tb_data_sekolah as sekolah', 'r.id_sekolah', '=', 'sekolah.id')
            ->select(
                DB::raw('@rownum := @rownum + 1 AS nomor_urut'),
                'sekolah.nama_sekolah',
                'r.jumlah_penerima_a',
                'r.jumlah_penerima_b',
                'r.jumlah_penerima_total'
            )
            ->crossJoin(DB::raw('(SELECT @rownum := 0) AS r')) // Inisial
            ->where('r.id_menu_harian', $idmenu)
            ->whereNotNull('r.jumlah_penerima_total')
            ->sum('r.jumlah_penerima_a');
            $total_b = DB::table('rincian_sekolah as r')
            ->join('tb_data_sekolah as sekolah', 'r.id_sekolah', '=', 'sekolah.id')
            ->select(
                DB::raw('@rownum := @rownum + 1 AS nomor_urut'),
                'sekolah.nama_sekolah',
                'r.jumlah_penerima_a',
                'r.jumlah_penerima_b',
                'r.jumlah_penerima_total'
            )
            ->crossJoin(DB::raw('(SELECT @rownum := 0) AS r')) // Inisial
            ->where('r.id_menu_harian', $idmenu)
            ->whereNotNull('r.jumlah_penerima_total')
            ->sum('r.jumlah_penerima_b');

            $tanggal_pengajuan = Carbon::parse($data_menu_harian->tanggal_pengajuan)->format('d-m-Y');
            $tanggal_kirim = Carbon::parse($data_menu_harian->tanggal_kirim)->format('d-m-Y');
            $bahan = TbMasterBahan::all();
            $totalPorsi = rincian_sekolah::where('id_menu_harian', $idmenu)->sum('jumlah_penerima_total');
            $karbohidrat = Resep::where('id',$data_menu_harian->karbohidrat)->first(); 
            $protein = Resep::where('id',$data_menu_harian->protein)->first();
            $sayur = Resep::where('id',$data_menu_harian->sayur)->first();
            $buah = Resep::where('id',$data_menu_harian->buah)->first();
            $susu = Resep::where('id',$data_menu_harian->susu)->first();
            $rumus_karbo = DB::table('tb_rumus_perhitungan_karbo')->where('id_menu', $idmenu)->first();
            $rumus_protein = DB::table('tb_rumus_perhitungan_protein')->where('id_menu', $idmenu)->first();
            $rumus_sayur = DB::table('tb_rumus_perhitungan_sayur')->where('id_menu', $idmenu)->first();
            $rumus_buah = DB::table('tb_rumus_perhitungan_buah')->where('id_menu', $idmenu)->first();
            $rumus_suplemen = DB::table('tb_rumus_perhitungan_suplemen')->where('id_menu', $idmenu)->first();
            $rincian_karbohidrat = DB::table('rincian_menu_harian as rmb')
            ->join('tb_menu_bahan as mbh', function ($join) {
                $join->on('rmb.id_resep', '=', 'mbh.menu_id')
                    ->on('rmb.id_bahan', '=', 'mbh.bahan_id');
            })
            ->join('tb_master_bahan as mb', 'rmb.id_bahan', '=', 'mb.id')
            ->join('tb_satuan as ts', 'rmb.id_satuan','=', 'ts.id')
            ->where('rmb.id_menu_harian', $idmenu)
            ->where('rmb.id_resep', $data_menu_harian->karbohidrat)
            ->select(
            DB::raw('@rownum := @rownum + 1 AS nomor_urut'),
                'mb.bahan',
                'mbh.jumlah as jumlah_menu_bahan',
                'rmb.jumlah as jumlah_rincian_menu_harian',
                'ts.satuan',
                'rmb.keterangan',
                'rmb.jumlah_box'
            )
            ->crossJoin(DB::raw('(SELECT @rownum := 0) AS r')) // Inisial
            ->get();

            $rincian_protein = DB::table('rincian_menu_harian as rmb')
            ->join('tb_menu_bahan as mbh', function ($join) {
                $join->on('rmb.id_resep', '=', 'mbh.menu_id')
                    ->on('rmb.id_bahan', '=', 'mbh.bahan_id');
            })
            ->join('tb_master_bahan as mb', 'rmb.id_bahan', '=', 'mb.id')
            ->join('tb_satuan as ts', 'rmb.id_satuan', '=', 'ts.id')
            ->where('rmb.id_menu_harian', $idmenu)
            ->where('rmb.id_resep', $data_menu_harian->protein)
            ->select(
            DB::raw('@rownum := @rownum + 1 AS nomor_urut'),
                'mb.bahan',
                'mbh.jumlah as jumlah_menu_bahan',
                'rmb.jumlah as jumlah_rincian_menu_harian',
                'ts.satuan',
                'rmb.keterangan',
                'rmb.jumlah_box'
            )
            ->crossJoin(DB::raw('(SELECT @rownum := 0) AS r')) // Inisial
            ->get();

            $rincian_sayur = DB::table('rincian_menu_harian as rmb')
            ->join('tb_menu_bahan as mbh', function ($join) {
                $join->on('rmb.id_resep', '=', 'mbh.menu_id')
                    ->on('rmb.id_bahan', '=', 'mbh.bahan_id');
            })
            ->join('tb_master_bahan as mb', 'rmb.id_bahan', '=', 'mb.id')
            ->join('tb_satuan as ts', 'rmb.id_satuan', '=', 'ts.id')
            ->where('rmb.id_menu_harian', $idmenu)
            ->where('rmb.id_resep', $data_menu_harian->sayur)
            ->select(
            DB::raw('@rownum := @rownum + 1 AS nomor_urut'),
                'mb.bahan',
                'mbh.jumlah as jumlah_menu_bahan',
                'rmb.jumlah as jumlah_rincian_menu_harian',
                'ts.satuan',
                'rmb.keterangan',
                'rmb.jumlah_box'
            )
            ->crossJoin(DB::raw('(SELECT @rownum := 0) AS r')) // Inisial
            ->get();

            $rincian_buah = DB::table('rincian_menu_harian as rmb')
            ->join('tb_menu_bahan as mbh', function ($join) {
                $join->on('rmb.id_resep', '=', 'mbh.menu_id')
                    ->on('rmb.id_bahan', '=', 'mbh.bahan_id');
            })
            ->join('tb_master_bahan as mb', 'rmb.id_bahan', '=', 'mb.id')
            ->join('tb_satuan as ts', 'rmb.id_satuan', '=', 'ts.id')
            ->where('rmb.id_menu_harian', $idmenu)
            ->where('rmb.id_resep', $data_menu_harian->buah)
            ->select(
            DB::raw('@rownum := @rownum + 1 AS nomor_urut'),
                'mb.bahan',
                'mbh.jumlah as jumlah_menu_bahan',
                'rmb.jumlah as jumlah_rincian_menu_harian',
                'ts.satuan',
                'rmb.keterangan',
                'rmb.jumlah_box'
            )
            ->crossJoin(DB::raw('(SELECT @rownum := 0) AS r')) // Inisial
            ->get();

            $rincian_susu = DB::table('rincian_menu_harian as rmb')
            ->join('tb_menu_bahan as mbh', function ($join) {
                $join->on('rmb.id_resep', '=', 'mbh.menu_id')
                    ->on('rmb.id_bahan', '=', 'mbh.bahan_id');
            })
            ->join('tb_master_bahan as mb', 'rmb.id_bahan', '=', 'mb.id')
            ->join('tb_satuan as ts', 'rmb.id_satuan', '=', 'ts.id')
            ->where('rmb.id_menu_harian', $idmenu)
            ->where('rmb.id_resep', $data_menu_harian->susu)
            ->select(
            DB::raw('@rownum := @rownum + 1 AS nomor_urut'),
                'mb.bahan',
                'mbh.jumlah as jumlah_menu_bahan',
                'rmb.jumlah as jumlah_rincian_menu_harian',
                'ts.satuan',
                'rmb.keterangan',
                'rmb.jumlah_box'
            )
            ->crossJoin(DB::raw('(SELECT @rownum := 0) AS r')) // Inisial
            ->get();

            $rincian_tambahan = DB::table('rincian_menu_harian as rmb')
            ->join('tb_master_bahan as mb', 'rmb.id_bahan', '=', 'mb.id')
            ->join('tb_satuan as ts', 'rmb.id_satuan', '=', 'ts.id')
            //->join('tb_satuan as ts', 'mb.satuan_bahan', '=', 'ts.id')
            ->where('rmb.id_menu_harian', $idmenu)

            ->where('rmb.bumbu', 1)
            ->whereIn('mb.jenis', [6,7])
            ->select(
                DB::raw('@rownum := @rownum + 1 AS nomor_urut'),
                'mb.bahan',
                DB::raw('"-" as jumlah_menu_bahan'),
                'rmb.jumlah as jumlah_rincian_menu_harian',
                'ts.satuan',
                'rmb.keterangan',
                'rmb.jumlah_box'
            )
            ->crossJoin(DB::raw('(SELECT @rownum := 0) AS r')) // Inisial
            ->get();

            // Unduh sebagai PDF dengan nama file "Formulir_Pengajuan_Menu_Harian.pdf"
            // Kirim data ke view PDF
            $pdf = Pdf::loadView('office/mastermenu.template_pengajuan_menu_3', compact(
                'data_menu_harian',
                'karbohidrat',
                'protein',
                'sayur',
                'buah',
                'susu',
                'rincian_tambahan',
                'rincian_karbohidrat',
                'rincian_protein',
                'rincian_sayur',
                'rincian_buah',
                'rincian_susu',
                'tanggal_pengajuan',
                'tanggal_kirim',
                'rincian_menu_harian',
                'dapur',
                'totalPorsi',
                'rumus_karbo',
                'rumus_protein',
                'rumus_sayur',
                'rumus_buah',
                'rumus_suplemen',
                'bahan',
                'data_sekolah',
                'total_b',
                'total_a',
                'gramasi_menu'
            ));
            return $pdf->download('Formulir_Pengajuan_Menu_Harian '.  $tanggal_kirim.'.pdf');
        }

    public function pdf_pengajuan_menu_3($idmenu)
    {
        $data_menu_harian = Menu::where('id', $idmenu)->first();
        $dapur = DataDapur::first();
        // Render file template.blade.php tanpa data
        //$pdf = Pdf::loadView('office/mastermenu.template_pengajuan_menu');
        $rincian_menu_harian = rincian_menu_harian::where('id_menu_harian', $idmenu)->get();
        $gramasi_menu = TbGramasiMenu::where('id_menu', $idmenu)->first();
        if (!$gramasi_menu) {
            $gramasi_menu = 0;
        }
        $data_sekolah = DB::select("
            SELECT 
                @rownum := @rownum + 1 AS nomor_urut,
                sekolah.nama_sekolah,
                r.jumlah_penerima_a,
                r.jumlah_penerima_b,
                r.jumlah_penerima_total
            FROM rincian_sekolah r
            JOIN tb_data_sekolah sekolah ON r.id_sekolah = sekolah.id
            CROSS JOIN (SELECT @rownum := 0) AS init
            WHERE r.id_menu_harian = ?
              AND r.jumlah_penerima_total > 0
            ", [$idmenu]);
        $total_a = DB::table('rincian_sekolah as r')
            ->join('tb_data_sekolah as sekolah', 'r.id_sekolah', '=', 'sekolah.id')
            ->select(
                DB::raw('@rownum := @rownum + 1 AS nomor_urut'),
                'sekolah.nama_sekolah',
                'r.jumlah_penerima_a',
                'r.jumlah_penerima_b',
                'r.jumlah_penerima_total'
            )
            ->crossJoin(DB::raw('(SELECT @rownum := 0) AS r')) // Inisial
            ->where('r.id_menu_harian', $idmenu)
            ->whereNotNull('r.jumlah_penerima_total')
            ->sum('r.jumlah_penerima_a');
        $total_b = DB::table('rincian_sekolah as r')
            ->join('tb_data_sekolah as sekolah', 'r.id_sekolah', '=', 'sekolah.id')
            ->select(
                DB::raw('@rownum := @rownum + 1 AS nomor_urut'),
                'sekolah.nama_sekolah',
                'r.jumlah_penerima_a',
                'r.jumlah_penerima_b',
                'r.jumlah_penerima_total'
            )
            ->crossJoin(DB::raw('(SELECT @rownum := 0) AS r')) // Inisial
            ->where('r.id_menu_harian', $idmenu)
            ->whereNotNull('r.jumlah_penerima_total')
            ->sum('r.jumlah_penerima_b');

        $tanggal_pengajuan = Carbon::parse($data_menu_harian->tanggal_pengajuan)->format('d-m-Y');
        $tanggal_kirim = Carbon::parse($data_menu_harian->tanggal_kirim)->format('d-m-Y');
        $bahan = TbMasterBahan::all();
        $totalPorsi = rincian_sekolah::where('id_menu_harian', $idmenu)->sum('jumlah_penerima_total');
        $karbohidrat = Resep::where('id', $data_menu_harian->karbohidrat)->first();
        $protein = Resep::where('id', $data_menu_harian->protein)->first();
        $sayur = Resep::where('id', $data_menu_harian->sayur)->first();
        $buah = Resep::where('id', $data_menu_harian->buah)->first();
        $susu = Resep::where('id', $data_menu_harian->susu)->first();
        $rumus_karbo = DB::table('tb_rumus_perhitungan_karbo')->where('id_menu', $idmenu)->first();
        $rumus_protein = DB::table('tb_rumus_perhitungan_protein')->where('id_menu', $idmenu)->first();
        $rumus_sayur = DB::table('tb_rumus_perhitungan_sayur')->where('id_menu', $idmenu)->first();
        $rumus_buah = DB::table('tb_rumus_perhitungan_buah')->where('id_menu', $idmenu)->first();
        $rumus_suplemen = DB::table('tb_rumus_perhitungan_suplemen')->where('id_menu', $idmenu)->first();
        $rincian_karbohidrat = DB::table('rincian_menu_harian as rmb')
            ->join('tb_menu_bahan as mbh', function ($join) {
                $join->on('rmb.id_resep', '=', 'mbh.menu_id')
                    ->on('rmb.id_bahan', '=', 'mbh.bahan_id');
            })
            ->join('tb_master_bahan as mb', 'rmb.id_bahan', '=', 'mb.id')
            ->join('tb_satuan as ts', 'rmb.id_satuan', '=', 'ts.id')
            ->where('rmb.id_menu_harian', $idmenu)
            ->where('rmb.id_resep', $data_menu_harian->karbohidrat)
            ->select(
                DB::raw('@rownum := @rownum + 1 AS nomor_urut'),
                'mb.bahan',
                'mbh.jumlah as jumlah_menu_bahan',
                'rmb.jumlah as jumlah_rincian_menu_harian',
                'ts.satuan',
                'rmb.keterangan',
                'rmb.jumlah_box'
            )
            ->crossJoin(DB::raw('(SELECT @rownum := 0) AS r')) // Inisial
            ->get();

        $rincian_protein = DB::table('rincian_menu_harian as rmb')
            ->join('tb_menu_bahan as mbh', function ($join) {
                $join->on('rmb.id_resep', '=', 'mbh.menu_id')
                    ->on('rmb.id_bahan', '=', 'mbh.bahan_id');
            })
            ->join('tb_master_bahan as mb', 'rmb.id_bahan', '=', 'mb.id')
            ->join('tb_satuan as ts', 'rmb.id_satuan', '=', 'ts.id')
            ->where('rmb.id_menu_harian', $idmenu)
            ->where('rmb.id_resep', $data_menu_harian->protein)
            ->select(
                DB::raw('@rownum := @rownum + 1 AS nomor_urut'),
                'mb.bahan',
                'mbh.jumlah as jumlah_menu_bahan',
                'rmb.jumlah as jumlah_rincian_menu_harian',
                'ts.satuan',
                'rmb.keterangan',
                'rmb.jumlah_box'
            )
            ->crossJoin(DB::raw('(SELECT @rownum := 0) AS r')) // Inisial
            ->get();

        $rincian_sayur = DB::table('rincian_menu_harian as rmb')
            ->join('tb_menu_bahan as mbh', function ($join) {
                $join->on('rmb.id_resep', '=', 'mbh.menu_id')
                    ->on('rmb.id_bahan', '=', 'mbh.bahan_id');
            })
            ->join('tb_master_bahan as mb', 'rmb.id_bahan', '=', 'mb.id')
            ->join('tb_satuan as ts', 'rmb.id_satuan', '=', 'ts.id')
            ->where('rmb.id_menu_harian', $idmenu)
            ->where('rmb.id_resep', $data_menu_harian->sayur)
            ->select(
                DB::raw('@rownum := @rownum + 1 AS nomor_urut'),
                'mb.bahan',
                'mbh.jumlah as jumlah_menu_bahan',
                'rmb.jumlah as jumlah_rincian_menu_harian',
                'ts.satuan',
                'rmb.keterangan',
                'rmb.jumlah_box'
            )
            ->crossJoin(DB::raw('(SELECT @rownum := 0) AS r')) // Inisial
            ->get();

        $rincian_buah = DB::table('rincian_menu_harian as rmb')
            ->join('tb_menu_bahan as mbh', function ($join) {
                $join->on('rmb.id_resep', '=', 'mbh.menu_id')
                    ->on('rmb.id_bahan', '=', 'mbh.bahan_id');
            })
            ->join('tb_master_bahan as mb', 'rmb.id_bahan', '=', 'mb.id')
            ->join('tb_satuan as ts', 'rmb.id_satuan', '=', 'ts.id')
            ->where('rmb.id_menu_harian', $idmenu)
            ->where('rmb.id_resep', $data_menu_harian->buah)
            ->select(
                DB::raw('@rownum := @rownum + 1 AS nomor_urut'),
                'mb.bahan',
                'mbh.jumlah as jumlah_menu_bahan',
                'rmb.jumlah as jumlah_rincian_menu_harian',
                'ts.satuan',
                'rmb.keterangan',
                'rmb.jumlah_box'
            )
            ->crossJoin(DB::raw('(SELECT @rownum := 0) AS r')) // Inisial
            ->get();

        $rincian_susu = DB::table('rincian_menu_harian as rmb')
            ->join('tb_menu_bahan as mbh', function ($join) {
                $join->on('rmb.id_resep', '=', 'mbh.menu_id')
                    ->on('rmb.id_bahan', '=', 'mbh.bahan_id');
            })
            ->join('tb_master_bahan as mb', 'rmb.id_bahan', '=', 'mb.id')
            ->join('tb_satuan as ts', 'rmb.id_satuan', '=', 'ts.id')
            ->where('rmb.id_menu_harian', $idmenu)
            ->where('rmb.id_resep', $data_menu_harian->susu)
            ->select(
                DB::raw('@rownum := @rownum + 1 AS nomor_urut'),
                'mb.bahan',
                'mbh.jumlah as jumlah_menu_bahan',
                'rmb.jumlah as jumlah_rincian_menu_harian',
                'ts.satuan',
                'rmb.keterangan',
                'rmb.jumlah_box'
            )
            ->crossJoin(DB::raw('(SELECT @rownum := 0) AS r')) // Inisial
            ->get();

        $rincian_tambahan = DB::table('rincian_menu_harian as rmb')
            ->join('tb_master_bahan as mb', 'rmb.id_bahan', '=', 'mb.id')
            ->join('tb_satuan as ts', 'rmb.id_satuan', '=', 'ts.id')
            //->join('tb_satuan as ts', 'mb.satuan_bahan', '=', 'ts.id')
            ->where('rmb.id_menu_harian', $idmenu)

            ->where('rmb.bumbu', 1)
            ->whereIn('mb.jenis', [6, 7])
            ->select(
                DB::raw('@rownum := @rownum + 1 AS nomor_urut'),
                'mb.bahan',
                DB::raw('"-" as jumlah_menu_bahan'),
                'rmb.jumlah as jumlah_rincian_menu_harian',
                'ts.satuan',
                'rmb.keterangan',
                'rmb.jumlah_box'
            )
            ->crossJoin(DB::raw('(SELECT @rownum := 0) AS r')) // Inisial
            ->get();
        
        $data_beras = DB::table('rincian_menu_harian as rmb')
            ->join('tb_master_bahan as mb', 'rmb.id_bahan', '=', 'mb.id')
            ->join('tb_satuan as ts', 'rmb.id_satuan', '=', 'ts.id')
            //->join('tb_satuan as ts', 'mb.satuan_bahan', '=', 'ts.id')
            ->where('rmb.id_menu_harian', $idmenu)

            ->where('mb.jenis', 1)
            ->select(
                DB::raw('@rownum := @rownum + 1 AS nomor_urut'),
                'mb.bahan',
                DB::raw('"-" as jumlah_menu_bahan'),
                'rmb.jumlah as jumlah_rincian_menu_harian',
                'ts.satuan',
                'rmb.keterangan',
                'rmb.jumlah_box'
            )
            ->crossJoin(DB::raw('(SELECT @rownum := 0) AS r')) // Inisial
            ->get();

        $data_lauk = DB::table('rincian_menu_harian as rmb')
            ->join('tb_master_bahan as mb', 'rmb.id_bahan', '=', 'mb.id')
            ->join('tb_satuan as ts', 'rmb.id_satuan', '=', 'ts.id')
            //->join('tb_satuan as ts', 'mb.satuan_bahan', '=', 'ts.id')
            ->where('rmb.id_menu_harian', $idmenu)

            ->where('mb.jenis', 2)
            ->select(
                DB::raw('@rownum := @rownum + 1 AS nomor_urut'),
                'mb.bahan',
                DB::raw('"-" as jumlah_menu_bahan'),
                'rmb.jumlah as jumlah_rincian_menu_harian',
                'ts.satuan',
                'rmb.keterangan',
                'rmb.jumlah_box'
            )
            ->crossJoin(DB::raw('(SELECT @rownum := 0) AS r')) // Inisial
            ->get();

        $data_sayur = DB::table('rincian_menu_harian as rmb')
            ->join('tb_master_bahan as mb', 'rmb.id_bahan', '=', 'mb.id')
            ->join('tb_satuan as ts', 'rmb.id_satuan', '=', 'ts.id')
            //->join('tb_satuan as ts', 'mb.satuan_bahan', '=', 'ts.id')
            ->where('rmb.id_menu_harian', $idmenu)

            ->where('mb.jenis', 3)
            ->select(
                DB::raw('@rownum := @rownum + 1 AS nomor_urut'),
                'mb.bahan',
                DB::raw('"-" as jumlah_menu_bahan'),
                'rmb.jumlah as jumlah_rincian_menu_harian',
                'ts.satuan',
                'rmb.keterangan',
                'rmb.jumlah_box'
            )
            ->crossJoin(DB::raw('(SELECT @rownum := 0) AS r')) // Inisial
            ->get();

        $data_buah = DB::table('rincian_menu_harian as rmb')
            ->join('tb_master_bahan as mb', 'rmb.id_bahan', '=', 'mb.id')
            ->join('tb_satuan as ts', 'rmb.id_satuan', '=', 'ts.id')
            //->join('tb_satuan as ts', 'mb.satuan_bahan', '=', 'ts.id')
            ->where('rmb.id_menu_harian', $idmenu)

            ->where('mb.jenis', 4)
            ->select(
                DB::raw('@rownum := @rownum + 1 AS nomor_urut'),
                'mb.bahan',
                DB::raw('"-" as jumlah_menu_bahan'),
                'rmb.jumlah as jumlah_rincian_menu_harian',
                'ts.satuan',
                'rmb.keterangan',
                'rmb.jumlah_box'
            )
            ->crossJoin(DB::raw('(SELECT @rownum := 0) AS r')) // Inisial
            ->get();

        $data_suplemen = DB::table('rincian_menu_harian as rmb')
            ->join('tb_master_bahan as mb', 'rmb.id_bahan', '=', 'mb.id')
            ->join('tb_satuan as ts', 'rmb.id_satuan', '=', 'ts.id')
            //->join('tb_satuan as ts', 'mb.satuan_bahan', '=', 'ts.id')
            ->where('rmb.id_menu_harian', $idmenu)

            ->where('mb.jenis', 5)
            ->select(
                DB::raw('@rownum := @rownum + 1 AS nomor_urut'),
                'mb.bahan',
                DB::raw('"-" as jumlah_menu_bahan'),
                'rmb.jumlah as jumlah_rincian_menu_harian',
                'ts.satuan',
                'rmb.keterangan',
                'rmb.jumlah_box'
            )
            ->crossJoin(DB::raw('(SELECT @rownum := 0) AS r')) // Inisial
            ->get();

        $data_bumbu = DB::table('rincian_menu_harian as rmb')
            ->join('tb_master_bahan as mb', 'rmb.id_bahan', '=', 'mb.id')
            ->join('tb_satuan as ts', 'rmb.id_satuan', '=', 'ts.id')
            ->join('tb_resep as tr', 'rmb.id_resep', '=', 'tr.id')

            //->join('tb_satuan as ts', 'mb.satuan_bahan', '=', 'ts.id')
            ->where('rmb.id_menu_harian', $idmenu)

            ->where('mb.jenis', 6)
            ->select(
                DB::raw('@rownum := @rownum + 1 AS nomor_urut'),
                'mb.bahan',
                DB::raw('"-" as jumlah_menu_bahan'),
                'rmb.jumlah as jumlah_rincian_menu_harian',
                'ts.satuan',
                'rmb.keterangan',
                'rmb.jumlah_box',
                'tr.nama_resep as resep'
            )
            ->crossJoin(DB::raw('(SELECT @rownum := 0) AS r')) // Inisial
            ->get();

        // Unduh sebagai PDF dengan nama file "Formulir_Pengajuan_Menu_Harian.pdf"
        // Kirim data ke view PDF
        $pdf = Pdf::loadView('office/mastermenu.template_pengajuan_menu_2', compact(
            'data_menu_harian',
            'karbohidrat',
            'protein',
            'sayur',
            'buah',
            'susu',
            'rincian_tambahan',
            'rincian_karbohidrat',
            'rincian_protein',
            'rincian_sayur',
            'rincian_buah',
            'rincian_susu',
            'tanggal_pengajuan',
            'tanggal_kirim',
            'rincian_menu_harian',
            'dapur',
            'totalPorsi',
            'rumus_karbo',
            'rumus_protein',
            'rumus_sayur',
            'rumus_buah',
            'rumus_suplemen',
            'bahan',
            'data_sekolah',
            'total_b',
            'total_a',
            'gramasi_menu',
            'data_bumbu',
            'data_beras',
            'data_lauk',
            'data_sayur',
            'data_buah',
            'data_suplemen'
        ));
        return $pdf->download('Formulir_Pengajuan_Menu_Harian ' .  $tanggal_kirim . '.pdf');
    }

     public function tambahan_rincian_menu_harian(Request $request)
     {
        $request->validate([
            'bahan_id' => 'required',
            'id_resep' => 'required',
            'id_satuan' => 'required',
            'jumlah_box' => 'required',
            'harga' => 'required',
            'jumlah' => 'required',
        ]);
        $spesifikasi_bahan = SpesifikasiBahan::where('id_bahan', $request->bahan_id)->first();
        $keterangan = '-';
        if($spesifikasi_bahan)
        {
            $keterangan = $spesifikasi_bahan->spesifikasi;
        }
        $kontrak = TbRincianKontrak::where('id_bahan', $request->bahan_id)->where('status',1)->first();
        $menu = Menu::findOrFail($request->id_menu);
        $totalHarga = (int) $request->harga * (int) $request->jumlah;

        if ($menu->status_pengajuan === 'pending') {
            DB::table('tb_rincian_menu_temp')->insert([
                'id_menu'       => $request->id_menu,
                'id_resep'      => $request->id_resep,
                'id_bahan'      => $request->bahan_id,
                'jumlah'        => $request->jumlah,
                'bumbu'         => 0,
                'harga'         => $request->harga,
                'total_harga'   => $totalHarga,
                'jumlah_box'    => $request->jumlah_box,
                'keterangan'    => $keterangan,
                'id_kontrak'    => $kontrak->id ?? 0,
                'id_satuan'     => $request->id_satuan,
            ]);
        } else {
            rincian_menu_harian::create([
                'id_menu_harian'    => $request->id_menu,
                'id_resep'          => $request->id_resep,
                'id_bahan'          => $request->bahan_id,
                'jumlah'            => $request->jumlah,
                'bumbu'             => 0,
                'harga'             => $request->harga,
                'total_harga'       => $totalHarga,
                'jumlah_box'        => $request->jumlah_box,
                'keterangan'        => $keterangan,
                'id_kontrak'        => $kontrak->id ?? 0,
                'id_satuan'         => $request->id_satuan,
            ]);
        }
        $cek_detail_resep = MenuBahan::where('menu_id', $request->id_resep)->where('bahan_id', $request->bahan_id)->first();
        if(!$cek_detail_resep)
        {
            MenuBahan::create([
                'menu_id' => $request->id_resep,
                'bahan_id' => $request->bahan_id,
                'jumlah' => $request->jumlah,
                'id_satuan' => $request->id_satuan,
                'status_bahan_baku' => 3,
                
            ]);
        }
        $satuan = TbMasterBahan::where('id', $request->bahan_id)->first();
        TbBantuBahanPo::create([
            'id_menu_harian'    => $request->id_menu,
            'id_resep'          => $request->id_resep,
            'id_bahan'          => $request->bahan_id,
            'jumlah'            => $request->jumlah ,
            'bumbu'             => 0,
            'satuan'            => $satuan->satuan_bahan,
            'jumlah_bahan'      => $request->jumlah ,

        ]);
        return response()->json(['success' => true, 'message' => 'Data berhasil ditambahkan']);

     }

     public function update_jumlah_rincian_bahan(Request $request)
     {

        $request->validate([
            'id' => 'required',
            'jumlah' => 'required',

        ]);

        $rincian = rincian_menu_harian::find($request->id);
        $rincian->jumlah = $request->jumlah;
        $rincian->save();
        $TbBantuBahanPo = TbBantuBahanPo::find($request->id);
        $TbBantuBahanPo->jumlah = $request->jumlah;
        $TbBantuBahanPo->jumlah_bahan = $request->jumlah;
        $TbBantuBahanPo->save();


        return response()->json(['message' => 'Jumlah Sekolah berhasil diperbarui!']);
        //return response()->json(['message' => 'Jumlah data berhasil diperbarui']);
    
     }

     public function update_jumlah_masak(Request $request)
     {
        $request->validate([
            'id_menu'  => 'required|integer',
            'tipe'     => 'required|in:protein,sayur',
            'jumlah_masak' => 'required|integer|min:0',
        ]);

        $table = $request->tipe === 'protein'
            ? 'tb_rumus_perhitungan_protein'
            : 'tb_rumus_perhitungan_sayur';

        DB::table($table)
            ->where('id_menu', $request->id_menu)
            ->update(['jumlah_masak' => $request->jumlah_masak]);

        return response()->json(['success' => true, 'message' => 'Jumlah Masak berhasil diperbarui!']);
     }

    public function cancel($id)
    {
        $menu = Menu::findOrFail($id);
        return response()->json($menu);
    }
    public function cancelConfirm(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);
        $menu->status_pengajuan = 'rejected';
        $menu->save();

        return response()->json([
            'success' => true,
            'redirect' => route('mastermenu.create')
        ]);
    }

    public function acc($id, Request $request)
    {
        $request->validate([
            'nama_acc' => 'required|string|max:255',
            'tanggal_acc' => 'required|date',
        ]);

        try {
            $menu = Menu::findOrFail($id);
            $menu->status_pengajuan = 'approved';
            $menu->nama_acc = $request->nama_acc;
            $menu->tanggal_acc = $request->tanggal_acc;
            $menu->save();

            return response()->json(['success' => true, 'message' => 'Menu berhasil di-ACC.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan ACC.', 'error' => $e->getMessage()], 500);
        }
    }
    
    public function hapus_bahan_rincian($id)
    {
        $data = rincian_menu_harian::find($id);
        rincian_menu_harian::find($id)->delete();
        return redirect()
            ->route('rincian_bahan', ['idmenu' => $data->id_menu_harian])
            ->with('success', 'Suplemen berhasil dihapus.');
    }

    public function ajaxTotalPax($id_menu)
    {
        try {
            $total  = (int) rincian_sekolah::where('id_menu_harian', $id_menu)->sum('jumlah_penerima_total');
            $jumlahA = (int) rincian_sekolah::where('id_menu_harian', $id_menu)->sum('jumlah_penerima_a');
            $jumlahB = (int) rincian_sekolah::where('id_menu_harian', $id_menu)->sum('jumlah_penerima_b');
            $budget = $jumlahA * 8000 + $jumlahB * 10000;
            
            return response()->json([
                'success' => true,
                'message' => 'Total pax dan budget berhasil diambil.',
                'data' => [
                    'total' => $total,
                    'jumlah_a' => $jumlahA,
                    'jumlah_b' => $jumlahB,
                    'budget' => $budget,
                ],
                'meta' => [
                    'timestamp' => now()->toIso8601String(),
                    'code' => 200,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data total pax.',
                'errors' => ['exception' => $e->getMessage()],
                'meta' => [
                    'timestamp' => now()->toIso8601String(),
                    'code' => 500,
                ],
            ], 500);
        }
    }

    public function ajaxSekolahAktifList(Request $request)
    {
        try {
            $data = DataSekolah::where('status_aktif', 1)
                ->select('id', 'nama_sekolah', 'jenjang_sekolah', 'jumlah_a', 'jumlah_b')
                ->orderBy('nama_sekolah')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Data sekolah aktif berhasil diambil.',
                'data' => $data,
                'pagination' => [
                    'total' => $data->count(),
                    'per_page' => $data->count(),
                    'current_page' => 1,
                    'last_page' => 1,
                ],
                'meta' => [
                    'timestamp' => now()->toIso8601String(),
                    'code' => 200,
                    'count' => $data->count(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data sekolah aktif.',
                'errors' => ['exception' => $e->getMessage()],
                'meta' => [
                    'timestamp' => now()->toIso8601String(),
                    'code' => 500,
                ],
            ], 500);
        }
    }

    public function tambahSekolahManual(Request $request)
    {
        try {
            $validated = $request->validate([
                'id_menu'    => 'required|exists:tb_menu,id',
                'id_sekolah' => 'required|exists:tb_data_sekolah,id',
                'jumlah_a'   => 'required|numeric|min:0',
                'jumlah_b'   => 'required|numeric|min:0',
            ]);

            $exists = rincian_sekolah::where('id_menu_harian', $request->id_menu)
                ->where('id_sekolah', $request->id_sekolah)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success'  => false,
                    'message' => 'Sekolah ini sudah terdaftar dalam menu tersebut.',
                    'errors' => ['id_sekolah' => ['Sekolah sudah terdaftar']],
                    'meta' => [
                        'timestamp' => now()->toIso8601String(),
                        'code' => 422,
                    ],
                ], 422);
            }

            $total = (int)$request->jumlah_a + (int)$request->jumlah_b;

            $rincian = rincian_sekolah::create([
                'id_menu_harian'        => $request->id_menu,
                'id_sekolah'            => $request->id_sekolah,
                'jumlah_penerima_a'     => $request->jumlah_a,
                'jumlah_penerima_b'     => $request->jumlah_b,
                'jumlah_penerima_total' => $total,
                'status'                => 0,
            ]);

            return response()->json([
                'success'  => true,
                'message' => 'Sekolah berhasil ditambahkan ke menu.',
                'data' => $rincian,
                'meta' => [
                    'timestamp' => now()->toIso8601String(),
                    'code' => 201,
                ],
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi data gagal.',
                'errors' => $e->errors(),
                'meta' => [
                    'timestamp' => now()->toIso8601String(),
                    'code' => 422,
                ],
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan sekolah ke menu.',
                'errors' => ['exception' => $e->getMessage()],
                'meta' => [
                    'timestamp' => now()->toIso8601String(),
                    'code' => 500,
                ],
            ], 500);
        }
    }

    public function apiRekapSekolah($id_menu, Request $request)
    {
        try {
            $query = DB::table('rincian_sekolah as r')
                ->join('tb_data_sekolah as sekolah', 'r.id_sekolah', '=', 'sekolah.id')
                ->select(
                    'r.id as id_rincian',
                    'r.id_menu_harian',
                    'r.id_sekolah',
                    'sekolah.nama_sekolah',
                    'sekolah.jenjang_sekolah',
                    'r.jumlah_penerima_a',
                    'r.jumlah_penerima_b',
                    'r.jumlah_penerima_total'
                )
                ->where('r.id_menu_harian', $id_menu);

            $result = $this->paginateApiQuery(
                $query,
                $request,
                ['sekolah.nama_sekolah', 'sekolah.jenjang_sekolah'],  // searchable
                ['sekolah.nama_sekolah', 'sekolah.jenjang_sekolah', 'r.jumlah_penerima_total'],  // sortable
                20  // default per_page
            );

            return response()->json([
                'success' => true,
                'message' => 'Data rekap sekolah berhasil diambil.',
                'data' => $result['data'],
                'pagination' => $result['pagination'],
                'meta' => [
                    'timestamp' => now()->toIso8601String(),
                    'code' => 200,
                    'count' => $result['pagination']['total'],
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data rekap sekolah.',
                'errors' => ['exception' => $e->getMessage()],
                'meta' => [
                    'timestamp' => now()->toIso8601String(),
                    'code' => 500,
                ],
            ], 500);
        }
    }

    public function apiRincianBahan($id_menu, Request $request)
    {
        try {
            $menu = Menu::findOrFail($id_menu);

            if ($menu->status_pengajuan === 'pending') {
                $query = DB::table('tb_rincian_menu_temp as r')
                    ->leftJoin('tb_resep as resep', 'r.id_resep', '=', 'resep.id')
                    ->leftJoin('tb_master_bahan as bahan', 'r.id_bahan', '=', 'bahan.id')
                    ->leftJoin('tb_satuan as satuan', 'r.id_satuan', '=', 'satuan.id')
                    ->select(
                        'r.id',
                        'r.id_menu as id_menu_harian',
                        'r.id_resep',
                        'resep.nama_resep',
                        'r.id_bahan',
                        'bahan.bahan as nama_bahan',
                        'r.jumlah',
                        'r.id_satuan',
                        'satuan.satuan as nama_satuan',
                        'r.harga',
                        'r.total_harga'
                    )
                    ->where('r.id_menu', $id_menu);
            } else {
                $query = DB::table('rincian_menu_harian as r')
                    ->leftJoin('tb_resep as resep', 'r.id_resep', '=', 'resep.id')
                    ->leftJoin('tb_master_bahan as bahan', 'r.id_bahan', '=', 'bahan.id')
                    ->leftJoin('tb_satuan as satuan', 'r.id_satuan', '=', 'satuan.id')
                    ->select(
                        'r.id',
                        'r.id_menu_harian',
                        'r.id_resep',
                        'resep.nama_resep',
                        'r.id_bahan',
                        'bahan.bahan as nama_bahan',
                        'r.jumlah',
                        'r.id_satuan',
                        'satuan.satuan as nama_satuan',
                        'r.harga',
                        'r.total_harga'
                    )
                    ->where('r.id_menu_harian', $id_menu);
            }

            $result = $this->paginateApiQuery(
                $query,
                $request,
                ['resep.nama_resep', 'bahan.bahan'],  // searchable
                ['resep.nama_resep', 'bahan.bahan', 'r.harga', 'r.total_harga'],  // sortable
                50  // default per_page
            );

            $totalHarga = DB::table(
                $menu->status_pengajuan === 'pending' ? 'tb_rincian_menu_temp' : 'rincian_menu_harian'
            )
                ->where($menu->status_pengajuan === 'pending' ? 'id_menu' : 'id_menu_harian', $id_menu)
                ->sum('total_harga');

            return response()->json([
                'success' => true,
                'message' => 'Data rincian bahan berhasil diambil.',
                'data' => [
                    'menu' => [
                        'id' => $menu->id,
                        'status_pengajuan' => $menu->status_pengajuan,
                    ],
                    'items' => $result['data'],
                    'summary' => [
                        'total_items' => $result['pagination']['total'],
                        'total_harga' => (float) $totalHarga,
                    ],
                ],
                'pagination' => $result['pagination'],
                'meta' => [
                    'timestamp' => now()->toIso8601String(),
                    'code' => 200,
                ],
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Menu tidak ditemukan.',
                'errors' => ['id_menu' => ['Menu dengan ID tersebut tidak ada']],
                'meta' => [
                    'timestamp' => now()->toIso8601String(),
                    'code' => 404,
                ],
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data rincian bahan.',
                'errors' => ['exception' => $e->getMessage()],
                'meta' => [
                    'timestamp' => now()->toIso8601String(),
                    'code' => 500,
                ],
            ], 500);
        }
    }

    public function dt_rekap_sekolah($id_menu)
    {
        // Ambil data Menu berdasarkan id_menu
        $data = Menu::where('id', $id_menu)->first();

        // Query rincian bahan resep karbohidrat
        if ($data->status_pengajuan == 'pending') {
            $table =  DB::table('rincian_sekolah as r')
                ->join('tb_data_sekolah as sekolah', 'r.id_sekolah', '=', 'sekolah.id')
                ->select(
                    DB::raw('@rownum := @rownum + 1 AS nomor_urut'),
                    'sekolah.nama_sekolah',
                    'sekolah.jenjang_sekolah',
                    'r.id as id_rincian',
                    'r.jumlah_penerima_a',
                    'r.jumlah_penerima_b',
                    'r.jumlah_penerima_total'
                )
                ->crossJoin(DB::raw('(SELECT @rownum := 0) AS init')) // Untuk nomor urut
                ->where('r.id_menu_harian', $id_menu)
                ->get();
        } else {
            $table = DB::table('rincian_sekolah as r')
                ->join('tb_data_sekolah as sekolah', 'r.id_sekolah', '=', 'sekolah.id')
                ->select(
                    DB::raw('@rownum := @rownum + 1 AS nomor_urut'),
                    'sekolah.nama_sekolah',
                    'sekolah.jenjang_sekolah',
                    'r.id AS id_rincian',
                    'r.jumlah_penerima_a',
                    'r.jumlah_penerima_b',
                    'r.jumlah_penerima_total'
                )
                ->crossJoin(DB::raw('(SELECT @rownum := 0) AS init')) // Untuk nomor urut
                ->where('r.id_menu_harian', $id_menu)
                ->get();
        }


        // Return data untuk DataTables
        return DataTables::of($table)
            ->addIndexColumn()
            ->addColumn('input_sekolah_a', function ($row) {
                return '<input type="number" class="form-control jumlah_a" data-id="' . $row->id_rincian . '" value="' . $row->jumlah_penerima_a . '"> <button class="btn btn-success btn-sm update-jumlah-sekolaha" data-id="' . $row->id_rincian . '">Update</button>';

                //return number_format($row->jumlah, 0, ',', '.'); // Format: 1.234,56
            })
            ->addColumn('input_sekolah_b', function ($row) {
                return '<input type="number" class="form-control jumlah_b" data-id="' . $row->id_rincian . '" value="' . $row->jumlah_penerima_b . '"> <button class="btn btn-success btn-sm update-jumlah-sekolahb" data-id="' . $row->id_rincian . '">Update</button>';
            })
            ->addColumn('action', function ($row) {
                return '<button type="button" class="btn btn-danger btn-sm btn-hapus-sekolah" data-id="' . $row->id_rincian . '" data-nama="' . htmlspecialchars($row->nama_sekolah) . '"><i class="fas fa-trash"></i> Hapus</button>';
            })
            ->rawColumns(['input_sekolah_a', 'input_sekolah_b', 'action'])
            ->make(true);
    }

    public function hapusRincianSekolah(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|exists:rincian_sekolah,id',
            ]);

            rincian_sekolah::findOrFail($request->id)->delete();

            return response()->json([
                'success'  => true,
                'message' => 'Data sekolah berhasil dihapus dari menu.',
                'data' => null,
                'meta' => [
                    'timestamp' => now()->toIso8601String(),
                    'code' => 204,
                ],
            ], 204);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi data gagal.',
                'errors' => $e->errors(),
                'meta' => [
                    'timestamp' => now()->toIso8601String(),
                    'code' => 422,
                ],
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data sekolah.',
                'errors' => ['exception' => $e->getMessage()],
                'meta' => [
                    'timestamp' => now()->toIso8601String(),
                    'code' => 500,
                ],
            ], 500);
        }
    }


    public function Lap_rekap_menu_export(Request $request)
    {
        $start = $request->tanggal_awal;
        $end = $request->tanggal_akhir ?: $start;

        if (empty($start)) {
            return back()->with('error', 'Tanggal awal wajib diisi.');
        }

        $menus = Menu::whereBetween('tanggal_kirim', [$start, $end])
            ->orderBy('tanggal_kirim')
            ->orderBy('id')
            ->get();

        $buildBahanUtama = function ($menuId, $resepId) {
            $utama = MenuBahan::where('menu_id', $resepId)
                ->where('status_bahan_baku', 1)
                ->first();

            if (!$utama) {
                return null;
            }

            return rincian_menu_harian::where('id_menu_harian', $menuId)
                ->join('tb_resep', 'rincian_menu_harian.id_resep', 'tb_resep.id')
                ->join('tb_master_bahan', 'rincian_menu_harian.id_bahan', 'tb_master_bahan.id')
                ->join('tb_satuan', 'rincian_menu_harian.id_satuan', 'tb_satuan.id')
                ->where('rincian_menu_harian.id_resep', $utama->menu_id)
                ->where('rincian_menu_harian.id_bahan', $utama->bahan_id)
                ->select('tb_master_bahan.bahan', 'rincian_menu_harian.*', 'tb_satuan.satuan', 'tb_resep.nama_resep')
                ->first();
        };

        $menuReports = [];

        foreach ($menus as $menu) {
            $totalA = (int) rincian_sekolah::where('id_menu_harian', $menu->id)->sum('jumlah_penerima_a');
            $totalB = (int) rincian_sekolah::where('id_menu_harian', $menu->id)->sum('jumlah_penerima_b');
            $totalPorsi = (int) rincian_sekolah::where('id_menu_harian', $menu->id)->sum('jumlah_penerima_total');

            $golongan = '-';
            if ($totalA > 0 && $totalB <= 0) {
                $golongan = 'A';
            } elseif ($totalB > 0 && $totalA <= 0) {
                $golongan = 'B';
            } elseif ($totalA > 0 && $totalB > 0) {
                $golongan = 'A/B';
            }

            $karbo = $buildBahanUtama($menu->id, $menu->karbohidrat);
            $lauk = $buildBahanUtama($menu->id, $menu->protein);
            $buah = $buildBahanUtama($menu->id, $menu->buah);
            $suplemen = $buildBahanUtama($menu->id, $menu->susu);

            $gramKarbo = TbRumusPerhitunganKarbo::where('id_menu', $menu->id)->first();
            $gramProtein = RumusPerhitunganProtein::where('id_menu', $menu->id)->first();
            $gramSayur = RumusPerhitunganSayur::where('id_menu', $menu->id)->first();
            $gramBuah = BuahRumusPerhitunganBuah::where('id_menu', $menu->id)->first();
            $gramSuplemen = DB::table('tb_rumus_perhitungan_suplemen')->where('id_menu', $menu->id)->first();

            $sayurBahanIds = MenuBahan::where('menu_id', $menu->sayur)
                ->where('status_bahan_baku', '!=', 3)
                ->pluck('bahan_id');

            $bahanSayur = collect();
            if ($sayurBahanIds->isNotEmpty()) {
                $bahanSayur = rincian_menu_harian::where('id_menu_harian', $menu->id)
                    ->join('tb_resep', 'rincian_menu_harian.id_resep', 'tb_resep.id')
                    ->join('tb_master_bahan', 'rincian_menu_harian.id_bahan', 'tb_master_bahan.id')
                    ->join('tb_satuan', 'rincian_menu_harian.id_satuan', 'tb_satuan.id')
                    ->where('rincian_menu_harian.id_resep', $menu->sayur)
                    ->whereIn('rincian_menu_harian.id_bahan', $sayurBahanIds->toArray())
                    ->select('tb_master_bahan.bahan', 'rincian_menu_harian.*', 'tb_satuan.satuan', 'tb_resep.nama_resep')
                    ->get();
            }

            $excludedIds = array_values(array_filter([
                optional($karbo)->id_bahan,
                optional($lauk)->id_bahan,
                optional($buah)->id_bahan,
                optional($suplemen)->id_bahan,
            ]));

            $queryBumbu = rincian_menu_harian::where('rincian_menu_harian.id_menu_harian', $menu->id)
                ->join('tb_resep', 'rincian_menu_harian.id_resep', 'tb_resep.id')
                ->join('tb_master_bahan', 'rincian_menu_harian.id_bahan', 'tb_master_bahan.id')
                ->join('tb_satuan', 'rincian_menu_harian.id_satuan', 'tb_satuan.id')
                ->select('tb_master_bahan.bahan', 'rincian_menu_harian.*', 'tb_satuan.satuan', 'tb_resep.nama_resep');

            if (!empty($excludedIds)) {
                $queryBumbu->whereNotIn('rincian_menu_harian.id_bahan', $excludedIds);
            }

            if ($sayurBahanIds->isNotEmpty()) {
                $queryBumbu->whereNotIn('rincian_menu_harian.id_bahan', $sayurBahanIds->toArray());
            }

            $bumbu = $queryBumbu->get();

            $menuReports[] = [
                'menu' => $menu,
                'golongan' => $golongan,
                'jumlah_a' => $totalA,
                'jumlah_b' => $totalB,
                'total_porsi' => $totalPorsi,
                'karbo' => $karbo,
                'lauk' => $lauk,
                'buah' => $buah,
                'suplemen' => $suplemen,
                'bahan_sayur' => $bahanSayur,
                'gram_karbo' => $gramKarbo,
                'gram_protein' => $gramProtein,
                'gram_sayur' => $gramSayur,
                'gram_buah' => $gramBuah,
                'gram_suplemen' => $gramSuplemen,
                'bumbu' => $bumbu,
            ];
        }

        $menuReportsByDate = collect($menuReports)->groupBy(function ($report) {
            return Carbon::parse($report['menu']->tanggal_kirim)->toDateString();
        });

        $bumbuTotalByDate = DB::table('rincian_menu_harian')
            ->join('tb_master_bahan', 'rincian_menu_harian.id_bahan', '=', 'tb_master_bahan.id')
            ->join('tb_satuan', 'rincian_menu_harian.id_satuan', '=', 'tb_satuan.id')
            ->join('tb_menu', 'rincian_menu_harian.id_menu_harian', '=', 'tb_menu.id')
            ->whereBetween('tb_menu.tanggal_kirim', [$start, $end])
            ->select(
                'tb_menu.tanggal_kirim',
                'rincian_menu_harian.id_bahan',
                'tb_master_bahan.bahan',
                'tb_satuan.satuan',
                DB::raw('SUM(rincian_menu_harian.jumlah) as total_jumlah'),
                DB::raw('AVG(rincian_menu_harian.harga) as harga'),
                DB::raw('SUM(rincian_menu_harian.total_harga) as total_harga'),
                DB::raw('MIN(rincian_menu_harian.keterangan) as keterangan')
            )
            ->groupBy(
                'tb_menu.tanggal_kirim',
                'rincian_menu_harian.id_bahan',
                'tb_master_bahan.bahan',
                'tb_satuan.satuan'
            )
            ->orderBy('tb_menu.tanggal_kirim')
            ->orderBy('tb_master_bahan.bahan')
            ->get()
            ->groupBy(function ($row) {
                return Carbon::parse($row->tanggal_kirim)->toDateString();
            });

        return Excel::download(
            new LaporanRekapMenuExport($start, $end, $menuReportsByDate, $bumbuTotalByDate),
            'Rekap_menu_' . $start . '_sampai_' . $end . '.xlsx'
        );
    }

    public function Lap_rekap_menu_export2($idmenu,Request $request)
    {
        
    }

    public function getMenuGizi($idmenu)
    {
        $gizi = MenuGiziHarian::where('id_menu', $idmenu)->first();
        if (!$gizi) {
            $gizi = (object)[
                'id' => null,
                'id_menu' => $idmenu,
                'energi' => 0,
                'protein' => 0,
                'lemak' => 0,
                'karbohidrat' => 0,
                'serat' => 0,
                'natrium' => 0,
            ];
        }
        return response()->json($gizi);
    }

    public function storeMenuGizi(Request $request)
    {
        $validated = $request->validate([
            'id_menu' => 'required|exists:tb_menu,id',
            'energi' => 'required|numeric|min:0',
            'protein' => 'required|numeric|min:0',
            'lemak' => 'required|numeric|min:0',
            'karbohidrat' => 'required|numeric|min:0',
            'serat' => 'required|numeric|min:0',
            'natrium' => 'required|numeric|min:0',
        ]);

        $gizi = MenuGiziHarian::updateOrCreate(
            ['id_menu' => $validated['id_menu']],
            [
                'energi' => $validated['energi'],
                'protein' => $validated['protein'],
                'lemak' => $validated['lemak'],
                'karbohidrat' => $validated['karbohidrat'],
                'serat' => $validated['serat'],
                'natrium' => $validated['natrium'],
            ]
        );

        return response()->json([
            'success' => true,
            'message' => $gizi->wasRecentlyCreated
                ? 'Data nutrisi berhasil ditambahkan'
                : 'Data nutrisi berhasil diperbarui',
            'data' => $gizi,
        ]);
    }

    public function calculateMenuGizi(Request $request, AutoMenuGiziService $autoMenuGiziService, DatabaseMenuGiziService $databaseMenuGiziService, MenuNutrisiMasterBahanNutrisiService $menuNutrisiMasterBahanNutrisiService, $idmenu)
    {
        try {
            $mode = strtolower((string) $request->input('mode', 'ai'));

            if ($mode === 'database') {
                $result = $menuNutrisiMasterBahanNutrisiService->calculateAndStore((int) $idmenu);
            } else {
                $result = $autoMenuGiziService->calculateOrReuseAndStore((int) $idmenu);
            }

            $gizi = $result['model'];

            $message = $result['source'] === 'master_bahan_nutrisi'
                ? 'Data nutrisi berhasil dihitung otomatis dari master bahan nutrisi.'
                : ($result['source'] === 'database'
                    ? 'Data nutrisi berhasil dihitung otomatis dari database.'
                    : 'Data nutrisi berhasil dihitung dengan AI.');

            if ($result['source'] === 'database_reuse') {
                $message = 'Data nutrisi menggunakan data menu sebelumnya yang komponennya sama.';
            }

            if ($result['source'] === 'ai' && !empty($result['ai_reason'])) {
                $message .= ' ' . $result['ai_reason'];
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $gizi,
                'missing_ingredients' => $result['missing_ingredients'],
                'source' => $result['source'],
                'ai_reason' => $result['ai_reason'] ?? null,
            ]);
        } catch (\RuntimeException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        } catch (\Throwable $exception) {
            $mode = strtolower((string) $request->input('mode', 'ai'));

            return response()->json([
                'success' => false,
                'message' => $mode === 'database'
                    ? 'Gagal menghitung data nutrisi dari database.'
                    : 'Gagal menghitung data nutrisi dengan AI.',
            ], 500);
        }
    }

    public function updateMenuGizi(Request $request, $id)
    {
        $validated = $request->validate([
            'id_menu' => 'required|exists:tb_menu,id',
            'energi' => 'required|numeric|min:0',
            'protein' => 'required|numeric|min:0',
            'lemak' => 'required|numeric|min:0',
            'karbohidrat' => 'required|numeric|min:0',
            'serat' => 'required|numeric|min:0',
            'natrium' => 'required|numeric|min:0',
        ]);

        $gizi = MenuGiziHarian::find($id);

        if ($gizi) {
            $gizi->update([
                'id_menu' => $validated['id_menu'],
                'energi' => $validated['energi'],
                'protein' => $validated['protein'],
                'lemak' => $validated['lemak'],
                'karbohidrat' => $validated['karbohidrat'],
                'serat' => $validated['serat'],
                'natrium' => $validated['natrium'],
            ]);
        } else {
            $gizi = MenuGiziHarian::updateOrCreate(
                ['id_menu' => $validated['id_menu']],
                [
                    'energi' => $validated['energi'],
                    'protein' => $validated['protein'],
                    'lemak' => $validated['lemak'],
                    'karbohidrat' => $validated['karbohidrat'],
                    'serat' => $validated['serat'],
                    'natrium' => $validated['natrium'],
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Data nutrisi berhasil diperbarui',
            'data' => $gizi,
        ]);
    }

    public function deleteMenuGizi($id)
    {
        $gizi = MenuGiziHarian::findOrFail($id);
        $gizi->delete();
        return response()->json(['success' => true, 'message' => 'Data nutrisi berhasil dihapus']);
    }

    public function exportGiziHarian($idmenu)
    {
        $menu = Menu::find($idmenu);
        if (!$menu) {
            return redirect()->back()->with('error', 'Menu tidak ditemukan');
        }

        $tanggal = \Carbon\Carbon::parse($menu->tanggal_kirim)->format('d-m-Y');
        return Excel::download(
            new LaporanGiziHarianExport($idmenu),
            'Laporan_Gizi_' . $tanggal . '.xlsx'
        );
    }
}
