<?php


namespace App\Http\Controllers\kitchen;

use App\Http\Controllers\Controller;
use App\Models\Gramasi;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
//import return type View
use Illuminate\View\View;
//import return type redirectResponse
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\HasilMasak;
use App\Models\HistoriMenu;
use App\Models\Menu;
use App\Models\rincian_menu_harian;
use App\Models\rincian_sekolah;
use App\Models\SisaBahanBakuMasak;
use App\Models\tbOmprengTransaksi;


use App\Models\DataDapur;
use Barryvdh\DomPDF\Facade\Pdf;

//excel 
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\exportExcelRekapHasilMasak;


class HasilMasakController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $header = "Hasil Masak";

        // Daftar paket menu untuk hari ini (untuk dropdown)
        $today = Carbon::now('Asia/Jakarta')->toDateString();
        $menuOptions = Menu::where('tanggal_kirim', $today)
            ->select('id', 'menu', 'tanggal_kirim')
            ->orderBy('id', 'asc')
            ->get();

        // Ambil pilihan menu_id dari query (jika ada), else default ke menu pertama hari ini
        $selectedMenuId = request('menu_id') ?? ($menuOptions->first()->id ?? null);

        // Detail paket menu terpilih (join untuk nama resep)
        $menus = null;
        if ($selectedMenuId) {
            $menus = Menu::join('tb_resep as karbohidrat_bahan', 'tb_menu.karbohidrat', '=', 'karbohidrat_bahan.id')
                ->join('tb_resep as protein_bahan', 'tb_menu.protein', '=', 'protein_bahan.id')
                ->join('tb_resep as sayur_bahan', 'tb_menu.sayur', '=', 'sayur_bahan.id')
                ->join('tb_resep as buah_bahan', 'tb_menu.buah', '=', 'buah_bahan.id')
                ->join('tb_resep as susu_bahan', 'tb_menu.susu', '=', 'susu_bahan.id')
                ->where('tb_menu.id', $selectedMenuId)
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
        }

        if ($menus) {
            $histori_masak_a = HistoriMenu::where('id_menu', $menus->id_menu)->where('kode', 'A')->first();
            $histori_masak_b = HistoriMenu::where('id_menu', $menus->id_menu)->where('kode', 'B')->first();
            $waktu_mulai = $histori_masak_b->waktu_mulai_masak ?? \Carbon\Carbon::now('Asia/Jakarta')->format('Y-m-d\T05:30');
        } else {
            $histori_masak_a = null;
            $histori_masak_b = null;
            $waktu_mulai =  \Carbon\Carbon::now('Asia/Jakarta')->format('Y-m-d\T05:30');
        }

        $satuanPerKomponen = [
            '1' => 'gram/ml/pcs',
            '2' => 'gram/ml/pcs',
            '3' => 'gram/ml/pcs',
            '4' => 'gram/ml/pcs',
            '5' => 'gram/ml/pcs',
        ];

        if ($menus) {
            $getSatuanKomponen = function ($resepId) {
                return DB::table('tb_menu_bahan as mb')
                    ->join('tb_satuan as s', 'mb.id_satuan', '=', 's.id')
                    ->where('mb.status_bahan_baku', 1)
                    ->where('mb.menu_id', $resepId)
                    ->value('s.satuan') ?? 'gram/ml/pcs';
            };

            $satuanPerKomponen['1'] = $getSatuanKomponen($menus->karbohidrat);
            $satuanPerKomponen['2'] = $getSatuanKomponen($menus->protein);
            $satuanPerKomponen['3'] = $getSatuanKomponen($menus->sayur);
            $satuanPerKomponen['4'] = 'buah'; // buah hardcoded sesuai tampilan tabel
            $satuanPerKomponen['5'] = $getSatuanKomponen($menus->susu);
        }

        if (request()->ajax()) {
            // id_menu aktif untuk DataTables (berdasarkan pilihan dropdown / default)
            $activeMenuId = request('menu_id') ?? ($menus->id_menu ?? null);

            $HasilMasak = DB::table('tb_hasil_masak')
                ->where('id_menu', $activeMenuId)
                ->select(
                    'id',
                    'id_komponen_sehat',
                    'jumlah',
                    DB::raw('created_at as waktu_jadi')
                )
                ->get();


            return DataTables::of($HasilMasak)
                ->addIndexColumn() // Menambah index
                ->addColumn('jumlah_jadi', function ($row) use($menus) {
                    if($row->id_komponen_sehat == 4)
                    {
                        return $row->jumlah . ' buah';
                    }else if($row->id_komponen_sehat == 1)
                    {
                        $satuan = DB::table('tb_menu_bahan as mb')
                        ->join('tb_satuan as s', 'mb.id_satuan', '=', 's.id')
                        ->select('s.satuan')
                        ->where('mb.status_bahan_baku', 1)
                        ->where('mb.menu_id', $menus->karbohidrat)
                        ->first();
                        return $row->jumlah . ' ' . ($satuan ? $satuan->satuan : 'gram');
                    }else if($row->id_komponen_sehat == 2)
                    {
                        $satuan = DB::table('tb_menu_bahan as mb')
                        ->join('tb_satuan as s', 'mb.id_satuan', '=', 's.id')
                        ->select('s.satuan')
                        ->where('mb.status_bahan_baku', 1)
                        ->where('mb.menu_id', $menus->protein)
                        ->first();
                        return $row->jumlah . ' ' . ($satuan ? $satuan->satuan : 'gram');
                    }else if($row->id_komponen_sehat == 3)
                    {
                        $satuan = DB::table('tb_menu_bahan as mb')
                        ->join('tb_satuan as s', 'mb.id_satuan', '=', 's.id')
                        ->select('s.satuan')
                        ->where('mb.status_bahan_baku', 1)
                        ->where('mb.menu_id', $menus->sayur)
                        ->first();
                        return $row->jumlah . ' ' . ($satuan ? $satuan->satuan : 'gram');
                    }else if($row->id_komponen_sehat == 5)
                    {
                        $satuan = DB::table('tb_menu_bahan as mb')
                        ->join('tb_satuan as s', 'mb.id_satuan', '=', 's.id')
                        ->select('s.satuan')
                        ->where('mb.status_bahan_baku', 1)
                        ->where('mb.menu_id', $menus->susu)
                        ->first();
                        return $row->jumlah . ' ' . ($satuan ? $satuan->satuan : 'gram');
                    }else{
                        return $row->jumlah . ' Gram';
                    }
                    
                })
                ->addColumn('nama_resep_input', function ($row) use($menus) {
                    if($row->id_komponen_sehat == 1 )
                    {
                        return $menus->nama_karbohidrat;
                    }else if($row->id_komponen_sehat == 2 )
                    {
                        
                        return $menus->nama_protein;
                    }else if($row->id_komponen_sehat == 3 )
                    {
                        return $menus->nama_sayur;
                    }else if($row->id_komponen_sehat == 4 )
                    {
                        return $menus->nama_buah;
                    }else if($row->id_komponen_sehat == 5 )
                    {
                        return $menus->nama_susu;
                    }else{
                        return '-';
                    }
                    
                })
                

                ->addColumn('action', function ($row) {
                    return '<button type="button" class="btn btn-warning btn-sm edit-btn" data-id="' . $row->id . '">Edit</button>
                            <a href="' . route('hasil-masak.delete', $row->id) . '" class="btn btn-danger btn-sm delete-link">Hapus</a>';
                })
                ->rawColumns(['action', 'jumlah_jadi'])
                ->make(true);
        }
        return view('kitchen/hasilmasak.index', compact('header','menus', 'histori_masak_a', 'histori_masak_b', 'waktu_mulai', 'menuOptions', 'selectedMenuId', 'satuanPerKomponen'));
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
            'id_menu' => 'required',
            'resep_id' => 'required',
            'berat' => 'required|numeric|min:1',
        ]);
        $now = Carbon::now('Asia/Jakarta')->toDateTimeString(); 
        HasilMasak::create([
            'id_menu' => $request->id_menu,
            'id_komponen_sehat' => $request->resep_id,
            'jumlah' => $request->berat,
            'status' => 'jadi',
            'waktu_matang' => $request->created_at,
        ]);

        HistoriMenu::where('id_menu', $request->id_menu)->update([
            'waktu_mulai_masak' => $request->waktu_mulai_masak,
        ]);
        

        $jumlah_karbo_jadi = HasilMasak::where('id_menu', $request->id_menu)->where('id_komponen_sehat', 1)->sum('jumlah');
        $jumlah_protein_jadi = HasilMasak::where('id_menu', $request->id_menu)->where('id_komponen_sehat', 2)->sum('jumlah');
        $jumlah_sayur_jadi = HasilMasak::where('id_menu', $request->id_menu)->where('id_komponen_sehat', 3)->sum('jumlah');
        $jumlah_buah_jadi = HasilMasak::where('id_menu', $request->id_menu)->where('id_komponen_sehat', 4)->sum('jumlah');
        $jumlah_susu_jadi = HasilMasak::where('id_menu', $request->id_menu)->where('id_komponen_sehat', 5)->sum('jumlah');


        $cek_a = HistoriMenu::where('id_menu', $request->id_menu)->where('kode', 'A')->first();
        $cek_b = HistoriMenu::where('id_menu', $request->id_menu)->where('kode', 'B')->first();
        $gramasi_a = Gramasi::where('kode', 'A')->first();
        $gramasi_b = Gramasi::where('kode', 'A')->first();

        $kebutuhan_karbo_a = $gramasi_a->karbohidrat * $cek_a->total_porsi;
        $selisih_karbo = $jumlah_karbo_jadi - $kebutuhan_karbo_a;
        if($selisih_karbo < 0 )
        {
            
            $cek_a->update([
                'hasil_menu_karbohidrat'    => $jumlah_karbo_jadi,
                'hasil_porsi_karbohidrat'   => $jumlah_karbo_jadi / $gramasi_a->karbohidrat
            ]);
        }else{
            $cek_a->update([
                'hasil_menu_karbohidrat'    => $kebutuhan_karbo_a,
                'hasil_porsi_karbohidrat'   => $kebutuhan_karbo_a /  $gramasi_a->karbohidrat
            ]);
            $selisih_b = $selisih_karbo /  $gramasi_b->karbohidrat;
            if($selisih_b < 0 )
            {
                $cek_b->update([
                    'hasil_menu_karbohidrat'    => $selisih_karbo,
                    'hasil_porsi_karbohidrat'   => 0
                ]);
            }else{
                $cek_b->update([
                    'hasil_menu_karbohidrat'    => $selisih_karbo,
                    'hasil_porsi_karbohidrat'   => $selisih_karbo /  $gramasi_b->karbohidrat
                ]);
            }
           
        }

        $kebutuhan_protein_a = $gramasi_a->protein * $cek_a->total_porsi;
        $selisih_protein = $jumlah_protein_jadi - $kebutuhan_protein_a;
        if ($selisih_protein < 0) {

            $cek_a->update([
                'hasil_menu_protein'    => $jumlah_protein_jadi,
                'hasil_porsi_protein'   => $jumlah_protein_jadi / $gramasi_a->protein
            ]);
        } else {
            $cek_a->update([
                'hasil_menu_protein'    => $kebutuhan_protein_a,
                'hasil_porsi_protein'   => $kebutuhan_protein_a /  $gramasi_a->protein
            ]);
            $selisih_b = $selisih_protein /  $gramasi_b->protein;
            if ($selisih_b < 0) {
                $cek_b->update([
                    'hasil_menu_protein'    => $selisih_protein,
                    'hasil_porsi_protein'   => 0
                ]);
            } else {
                $cek_b->update([
                    'hasil_menu_protein'    => $selisih_protein,
                    'hasil_porsi_protein'   => $selisih_protein /  $gramasi_b->protein
                ]);
            }
            
        }

        $kebutuhan_sayur_a = $gramasi_a->sayur * $cek_a->total_porsi;
        $selisih_sayur = $jumlah_sayur_jadi - $kebutuhan_sayur_a;
        if ($selisih_protein < 0) {

            $cek_a->update([
                'hasil_menu_sayur'    => $jumlah_sayur_jadi,
                'hasil_porsi_sayur'   => $jumlah_sayur_jadi / $gramasi_a->sayur
            ]);
        } else {
            $cek_a->update([
                'hasil_menu_sayur'    => $kebutuhan_sayur_a,
                'hasil_porsi_sayur'   => $kebutuhan_sayur_a /   $gramasi_a->sayur
            ]);

            $selisih_b = $selisih_sayur /  $gramasi_b->sayur;
            if ($selisih_b < 0) {
                $cek_b->update([
                    'hasil_menu_sayur'    => $selisih_sayur,
                    'hasil_porsi_sayur'   => 0
                ]);
            } else {
                $cek_b->update([
                    'hasil_menu_sayur'    => $selisih_sayur,
                    'hasil_porsi_sayur'   => $selisih_sayur /  $gramasi_b->sayur
                ]);
            }

            
        }

        $kebutuhan_buah_a = $gramasi_a->buah * $cek_a->total_porsi;
        $selisih_buah = $jumlah_buah_jadi - $kebutuhan_buah_a;
        if ($selisih_buah < 0) {

            $cek_a->update([
                'hasil_menu_buah'    => $jumlah_buah_jadi,
                'hasil_porsi_buah'   => $jumlah_buah_jadi / $gramasi_a->buah
            ]);
        } else {
            $cek_a->update([
                'hasil_menu_buah'    => $kebutuhan_buah_a,
                'hasil_porsi_buah'   => $kebutuhan_buah_a /   $gramasi_a->buah
            ]);

            $selisih_b = $selisih_buah /  $gramasi_b->buah;
            if ($selisih_b < 0) {
                $cek_b->update([
                    'hasil_menu_buah'    => $selisih_buah,
                    'hasil_porsi_buah'   => 0
                ]);
            } else {
                $cek_b->update([
                    'hasil_menu_buah'    => $selisih_buah,
                    'hasil_porsi_buah'   => $selisih_buah /  $gramasi_b->buah
                ]);
            }

        }

        $kebutuhan_susu_a = $gramasi_a->susu * $cek_a->total_porsi;
        $selisih_susu = $jumlah_susu_jadi - $kebutuhan_susu_a;
        if ($selisih_susu < 0) {

            $cek_a->update([
                'hasil_menu_susu'    => $jumlah_susu_jadi,
                'hasil_porsi_susu'   => $jumlah_susu_jadi / $gramasi_a->susu
            ]);
        } else {
            $cek_a->update([
                'hasil_menu_susu'    => $kebutuhan_susu_a,
                'hasil_porsi_susu'   => $kebutuhan_susu_a / $gramasi_a->susu
            ]);

            $selisih_b = $selisih_susu /  $gramasi_b->susu;
            if ($selisih_b < 0) {
                $cek_b->update([
                    'hasil_menu_susu'    => $selisih_susu,
                    'hasil_porsi_buah'   => 0
                ]);
            } else {
                $cek_b->update([
                    'hasil_menu_susu'    => $selisih_susu,
                    'hasil_porsi_susu'   => $selisih_susu /  $gramasi_b->susu
                ]);
            }

            
        }

            /*if($cek_a->status == 'kurang')
        {
            $gramasi = Gramasi::where('kode', 'A')->first();
            if ($request->resep_id == 1) {
                
                $cek_a->update([
                    'hasil_menu_karbohidrat'    => $cek_a->hasil_menu_karbohidrat + $request->berat,
                    'hasil_porsi_karbohidrat'   => ($cek_a->hasil_menu_karbohidrat  + $request->berat )/ $gramasi->karbohidrat
                ]);
               
            } else if ($request->resep_id == 2) {
                $cek_a->update([
                    'hasil_menu_protein' => $cek_a->hasil_menu_protein + $request->berat,
                    'hasil_porsi_protein'   =>  ($cek_a->hasil_menu_protein + $request->berat) / $gramasi->protein
                ]);
                
            } else if ($request->resep_id == 3) {
                $cek_a->update([
                    'hasil_menu_sayur' => $cek_a->hasil_menu_sayur + $request->berat,
                    'hasil_porsi_sayur'   =>  ($cek_a->hasil_menu_sayur+ $request->berat) / $gramasi->sayur
                ]);
                
            } else if ($request->resep_id == 4) {
                $cek_a->update([
                    'hasil_menu_buah' => $cek_a->hasil_menu_buah + $request->berat,
                    'hasil_porsi_buah'   => ($cek_a->hasil_menu_buah+ $request->berat)  / $gramasi->buah
                ]);
               
            } else if ($request->resep_id == 5) {
                $cek_a->update([
                    'hasil_menu_susu' => $cek_a->hasil_menu_susu + $request->berat,
                    'hasil_porsi_susu'   =>  ( $cek_a->hasil_menu_susu + $request->berat) / $gramasi->susu
                ]);
              
            }
        }else{
            $gramasi = Gramasi::where('kode', 'B')->first();
            if ($request->resep_id == 1) {

                $cek_b->update([
                    'hasil_menu_karbohidrat'    => $cek_b->hasil_menu_karbohidrat + $request->berat,
                    'hasil_porsi_karbohidrat'   => ($cek_b->hasil_menu_karbohidrat  + $request->berat) / $gramasi->karbohidrat
                ]);
            } else if ($request->resep_id == 2) {
                $cek_b->update([
                    'hasil_menu_protein' => $cek_b->hasil_menu_protein + $request->berat,
                    'hasil_porsi_protein'   => ($cek_b->hasil_menu_protein + $request->berat) / $gramasi->protein
                ]);
            } else if ($request->resep_id == 3) {
                $cek_b->update([
                    'hasil_menu_sayur' => $cek_b->hasil_menu_sayur + $request->berat,
                    'hasil_porsi_sayur'   => ($cek_b->hasil_menu_sayur + $request->berat) / $gramasi->sayur
                ]);
            } else if ($request->resep_id == 4) {
                $cek_b->update([
                    'hasil_menu_buah' => $cek_b->hasil_menu_buah + $request->berat,
                    'hasil_porsi_buah'   => ($cek_b->hasil_menu_buah + $request->berat)  / $gramasi->buah
                ]);
            } else if ($request->resep_id == 5) {
                $cek_b->update([
                    'hasil_menu_susu' => $cek_b->hasil_menu_susu + $request->berat,
                    'hasil_porsi_susu'   => ($cek_b->hasil_menu_susu + $request->berat) / $gramasi->susu
                ]);
            }
        }*/




            $cek_a = HistoriMenu::where('id_menu', $request->id_menu)
            ->where('kode', 'A')
            ->first();
        // Cek apakah masing-masing hasil porsi cukup atau kurang
        $status_porsi = [
            'karbohidrat' => $cek_a->hasil_porsi_karbohidrat < $cek_a->total_porsi ? 'kurang' : 'cukup',
            'protein' => $cek_a->hasil_porsi_protein < $cek_a->total_porsi ? 'kurang' : 'cukup',
            'sayur' => $cek_a->hasil_porsi_sayur < $cek_a->total_porsi ? 'kurang' : 'cukup',
            'buah' => $cek_a->hasil_porsi_buah < $cek_a->total_porsi ? 'kurang' : 'cukup',
            'susu' => $cek_a->hasil_porsi_susu < $cek_a->total_porsi ? 'kurang' : 'cukup',
        ];
        // Jika ada salah satu yang "kurang", maka status tetap "kurang", jika semua "cukup" maka update jadi "cukup"
        $new_status = in_array('kurang', $status_porsi) ? 'kurang' : 'cukup';

        // Update status di database
        $cek_a->update(['status' => $new_status]);


        $cek_b = HistoriMenu::where('id_menu', $request->id_menu)
            ->where('kode', 'B')
            ->first();
        // Cek apakah masing-masing hasil porsi cukup atau kurang
        $status_porsi = [
            'karbohidrat' => $cek_b->hasil_porsi_karbohidrat < $cek_b->total_porsi ? 'kurang' : 'cukup',
            'protein' => $cek_b->hasil_porsi_protein < $cek_b->total_porsi ? 'kurang' : 'cukup',
            'sayur' => $cek_b->hasil_porsi_sayur < $cek_b->total_porsi ? 'kurang' : 'cukup',
            'buah' => $cek_b->hasil_porsi_buah < $cek_b->total_porsi ? 'kurang' : 'cukup',
            'susu' => $cek_b->hasil_porsi_susu < $cek_b->total_porsi ? 'kurang' : 'cukup',
        ];
        // Jika ada salah satu yang "kurang", maka status tetap "kurang", jika semua "cukup" maka update jadi "cukup"
        $new_status = in_array('kurang', $status_porsi) ? 'kurang' : 'cukup';

        // Update status di database
        $cek_b->update(['status' => $new_status]);
      
        
        
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Data Masuk']);
        }

        return redirect()->back()->with('success', 'Data Masuk');
       
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
     * Get data for editing
     */
    public function getEdit($id)
    {
        $hasilMasak = HasilMasak::find($id);
        if (!$hasilMasak) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }
        return response()->json($hasilMasak);
    }

    /**
     * Update the specified resource
     */
    public function update(Request $request, $id)
    {
        $hasilMasak = HasilMasak::find($id);
        if (!$hasilMasak) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        $request->validate([
            'jumlah' => 'required|numeric|min:1',
        ]);

        $hasilMasak->update([
            'jumlah' => $request->jumlah,
        ]);

        return response()->json(['success' => true, 'message' => 'Data berhasil diperbarui']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $hasilMasak = HasilMasak::find($id);
        if (!$hasilMasak) {
            if (request()->ajax()) {
                return response()->json(['error' => 'Data tidak ditemukan'], 404);
            }
            return redirect()->back()->with('error', 'Data tidak ada');
        }

        $hasilMasak->delete();
        
        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Data berhasil dihapus']);
        }
        return redirect()->back()->with('success', 'Data Dihapus');
    }

    public function excel_hasil_masak($id)
    {
        $menu = Menu::find($id);
        $tanggal_kirim = Carbon::parse($menu->tanggal_kirim)->format('d-m-Y');;
        return Excel::download(new exportExcelRekapHasilMasak($id), 'Rekap Hasil Masak Tanggal ' . $tanggal_kirim . '.xlsx');
    }

    public function laporan_hasil_masak(Request $request)
    {
        $tanggal = Carbon::parse($request->tanggal)->format('Y-m-d');
        $menu = Menu::where('tanggal_kirim', $request->tanggal)->first(); // Urutkan dari yang terbaru;
        $tanggal_kirim = Carbon::parse($request->tanggal)->format('d-m-Y');;
        return Excel::download(new exportExcelRekapHasilMasak($menu->id), 'Rekap Hasil Masak Tanggal ' . $tanggal_kirim . '.xlsx');
    }

    public function excel_hasil_masak_tanggal(Request $request)
    {
        $tanggal    = Carbon::parse($request->tanggal)->format('Y-m-d') ?? date('Y-m-d');
        $menu = Menu::where('tanggal_kirim', $tanggal)->first();
        $tanggal_kirim = Carbon::parse($menu->tanggal_kirim)->format('d-m-Y');;
        return Excel::download(new exportExcelRekapHasilMasak($menu->id), 'Rekap Hasil Masak Tanggal ' . $tanggal_kirim . '.xlsx');
    }

    public function laporanMasakharian(Request $request)
    {
        $today = Carbon::now('Asia/Jakarta')->toDateString();
        try {
            $tanggalLaporan = $request->filled('tanggal')
                ? Carbon::parse($request->tanggal)->toDateString()
                : $today;
        } catch (\Exception $e) {
            $tanggalLaporan = $today;
        }

        $menuList = Menu::join('tb_resep as k', 'tb_menu.karbohidrat', '=', 'k.id')
            ->join('tb_resep as p', 'tb_menu.protein', '=', 'p.id')
            ->join('tb_resep as s', 'tb_menu.sayur', '=', 's.id')
            ->join('tb_resep as b', 'tb_menu.buah', '=', 'b.id')
            ->join('tb_resep as su', 'tb_menu.susu', '=', 'su.id')
            ->where('tb_menu.tanggal_kirim', $tanggalLaporan)
            ->select(
                'tb_menu.id as id_menu',
                'tb_menu.menu',
                'tb_menu.karbohidrat',
                'k.nama_resep as nama_karbohidrat',
                'tb_menu.protein',
                'p.nama_resep as nama_protein',
                'tb_menu.sayur',
                's.nama_resep as nama_sayur',
                'tb_menu.buah',
                'b.nama_resep as nama_buah',
                'tb_menu.susu',
                'su.nama_resep as nama_susu'
            )
            ->orderBy('tb_menu.id')
            ->get();

        $getSatuanResep = function ($resepId) {
            return DB::table('tb_menu_bahan as mb')
                ->join('tb_satuan as s', 'mb.id_satuan', '=', 's.id')
                ->where('mb.status_bahan_baku', 1)
                ->where('mb.menu_id', $resepId)
                ->value('s.satuan') ?? 'gram';
        };

        $komponenLabels = [
            1 => 'Karbohidrat',
            2 => 'Protein / Lauk',
            3 => 'Sayur',
            4 => 'Buah',
            5 => 'Susu / Pendamping',
        ];

        $sisaByMenu = SisaBahanBakuMasak::whereDate('tanggal', $tanggalLaporan)
            ->get()
            ->keyBy('id_menu');

        $dataPerMenu = [];
        foreach ($menuList as $menu) {
            $jumlahPorsi = DB::table('rincian_sekolah')
                ->where('id_menu_harian', $menu->id_menu)
                ->sum('jumlah_penerima_total');

            $satuan = [
                1 => $getSatuanResep($menu->karbohidrat),
                2 => $getSatuanResep($menu->protein),
                3 => $getSatuanResep($menu->sayur),
                4 => 'buah',
                5 => $getSatuanResep($menu->susu),
            ];

            $namaKomponen = [
                1 => $menu->nama_karbohidrat,
                2 => $menu->nama_protein,
                3 => $menu->nama_sayur,
                4 => $menu->nama_buah,
                5 => $menu->nama_susu,
            ];

            $historiB = HistoriMenu::where('id_menu', $menu->id_menu)->where('kode', 'B')->first();
            $jamMulai = $historiB->waktu_mulai_masak ?? null;

            $sisa = $sisaByMenu->get($menu->id_menu);

            $hasilMasak = DB::table('tb_hasil_masak')
                ->where('id_menu', $menu->id_menu)
                ->orderBy('id_komponen_sehat')
                ->orderBy('created_at')
                ->get();

            $perKomponen = [];
            foreach ($komponenLabels as $k => $label) {
                $entries = $hasilMasak->where('id_komponen_sehat', $k)->values();
                $perKomponen[$k] = [
                    'label'   => $label,
                    'nama'    => $namaKomponen[$k],
                    'satuan'  => $satuan[$k],
                    'entries' => $entries,
                    'total'   => $entries->sum('jumlah'),
                ];
            }

            $dataPerMenu[] = [
                'menu'        => $menu,
                'jamMulai'    => $jamMulai,
                'jumlahPorsi' => (int) $jumlahPorsi,
                'perKomponen' => $perKomponen,
                'sisaBahan'   => [
                    'karbo' => [
                        'qty' => (float) ($sisa->sisa_karbo ?? 0),
                        'satuan' => $satuan[1],
                    ],
                    'protein' => [
                        'qty' => (float) ($sisa->sisa_protein ?? 0),
                        'satuan' => $satuan[2],
                    ],
                    'sayur' => [
                        'qty' => (float) ($sisa->sisa_sayur ?? 0),
                        'satuan' => $satuan[3],
                    ],
                    'buah' => [
                        'qty' => (float) ($sisa->sisa_buah ?? 0),
                        'satuan' => $satuan[4],
                    ],
                    'susu' => [
                        'qty' => (float) ($sisa->sisa_susu ?? 0),
                        'satuan' => $satuan[5],
                    ],
                    'keterangan' => $sisa->keterangan ?? '-',
                ],
            ];
        }

        $dapur   = DataDapur::first();
        $tanggal = $tanggalLaporan;

        $pdf = Pdf::loadView('kitchen.hasilmasak.laporan_masak_harian', compact('dataPerMenu', 'dapur', 'tanggal'));
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('Laporan Masak ' . $tanggalLaporan . '.pdf');
    }

    public function v_menu_laporan_masak_harian(Request $request)
    {
        $header = "Laporan Masak Harian";
        $today = Carbon::now('Asia/Jakarta')->toDateString();

        try {
            $tanggalInput = $request->filled('tanggal')
                ? Carbon::parse($request->tanggal)->toDateString()
                : $today;
        } catch (\Exception $e) {
            $tanggalInput = $today;
        }

        $reportUrl = route('laporan-masak-harian', ['tanggal' => $tanggalInput]);

        $menuList = Menu::join('tb_resep as k', 'tb_menu.karbohidrat', '=', 'k.id')
            ->join('tb_resep as p', 'tb_menu.protein', '=', 'p.id')
            ->join('tb_resep as s', 'tb_menu.sayur', '=', 's.id')
            ->join('tb_resep as b', 'tb_menu.buah', '=', 'b.id')
            ->join('tb_resep as su', 'tb_menu.susu', '=', 'su.id')
            ->where('tb_menu.tanggal_kirim', $tanggalInput)
            ->select(
                'tb_menu.id as id_menu',
                'tb_menu.menu',
                'tb_menu.karbohidrat',
                'tb_menu.protein',
                'tb_menu.sayur',
                'tb_menu.buah',
                'tb_menu.susu',
                'k.nama_resep as nama_karbohidrat',
                'p.nama_resep as nama_protein',
                's.nama_resep as nama_sayur',
                'b.nama_resep as nama_buah',
                'su.nama_resep as nama_susu'
            )
            ->orderBy('tb_menu.id')
            ->get();

        $getSatuanResep = function ($resepId, $fallback = 'gram') {
            if (!$resepId) {
                return $fallback;
            }

            return DB::table('tb_menu_bahan as mb')
                ->join('tb_satuan as s', 'mb.id_satuan', '=', 's.id')
                ->where('mb.status_bahan_baku', 1)
                ->where('mb.menu_id', $resepId)
                ->value('s.satuan') ?? $fallback;
        };

        $tableRows = [];
        foreach ($menuList as $menu) {
            $jumlahPorsi = DB::table('rincian_sekolah')
                ->where('id_menu_harian', $menu->id_menu)
                ->sum('jumlah_penerima_total');

            $historiB = HistoriMenu::where('id_menu', $menu->id_menu)
                ->where('kode', 'B')
                ->first();

            $jamMulaiMasak = $historiB && $historiB->waktu_mulai_masak
                ? Carbon::parse($historiB->waktu_mulai_masak)->format('H:i')
                : '-';

            $totalPerKomponen = DB::table('tb_hasil_masak')
                ->select('id_komponen_sehat', DB::raw('SUM(jumlah) as total_jumlah'))
                ->where('id_menu', $menu->id_menu)
                ->groupBy('id_komponen_sehat')
                ->pluck('total_jumlah', 'id_komponen_sehat');

            $tableRows[] = [
                'menu' => $menu->menu,
                'jam_mulai' => $jamMulaiMasak,
                'jumlah_porsi' => (int) $jumlahPorsi,
                'karbo' => [
                    'nama' => $menu->nama_karbohidrat,
                    'qty' => (float) ($totalPerKomponen[1] ?? 0),
                    'satuan' => $getSatuanResep($menu->karbohidrat, 'gram'),
                ],
                'protein' => [
                    'nama' => $menu->nama_protein,
                    'qty' => (float) ($totalPerKomponen[2] ?? 0),
                    'satuan' => $getSatuanResep($menu->protein, 'gram'),
                ],
                'sayur' => [
                    'nama' => $menu->nama_sayur,
                    'qty' => (float) ($totalPerKomponen[3] ?? 0),
                    'satuan' => $getSatuanResep($menu->sayur, 'gram'),
                ],
                'buah' => [
                    'nama' => $menu->nama_buah,
                    'qty' => (float) ($totalPerKomponen[4] ?? 0),
                    'satuan' => 'buah',
                ],
                'susu' => [
                    'nama' => $menu->nama_susu,
                    'qty' => (float) ($totalPerKomponen[5] ?? 0),
                    'satuan' => $getSatuanResep($menu->susu, 'pcs'),
                ],
            ];
        }

        $sisaBahanByMenu = SisaBahanBakuMasak::whereDate('tanggal', $tanggalInput)
            ->get()
            ->keyBy('id_menu');

        $sisaBahanRows = [];
        foreach ($menuList as $menu) {
            $existing = $sisaBahanByMenu->get($menu->id_menu);

            $sisaBahanRows[] = [
                'id_menu' => $menu->id_menu,
                'menu' => $menu->menu,
                'karbo' => [
                    'qty' => (float) ($existing->sisa_karbo ?? 0),
                    'satuan' => $getSatuanResep($menu->karbohidrat, 'gram'),
                ],
                'protein' => [
                    'qty' => (float) ($existing->sisa_protein ?? 0),
                    'satuan' => $getSatuanResep($menu->protein, 'gram'),
                ],
                'sayur' => [
                    'qty' => (float) ($existing->sisa_sayur ?? 0),
                    'satuan' => $getSatuanResep($menu->sayur, 'gram'),
                ],
                'buah' => [
                    'qty' => (float) ($existing->sisa_buah ?? 0),
                    'satuan' => 'buah',
                ],
                'susu' => [
                    'qty' => (float) ($existing->sisa_susu ?? 0),
                    'satuan' => $getSatuanResep($menu->susu, 'pcs'),
                ],
                'keterangan' => $existing->keterangan ?? '',
            ];
        }

        return view('kitchen.hasilmasak.menu_laporan_masak_harian', compact('header', 'tanggalInput', 'reportUrl', 'tableRows', 'sisaBahanRows'));
    }

    public function simpanSisaBahanBaku(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'id_menu' => 'required|exists:tb_menu,id',
            'sisa_karbo' => 'nullable|numeric|min:0',
            'sisa_protein' => 'nullable|numeric|min:0',
            'sisa_sayur' => 'nullable|numeric|min:0',
            'sisa_buah' => 'nullable|numeric|min:0',
            'sisa_susu' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string|max:1000',
        ]);

        $tanggal = Carbon::parse($request->tanggal)->toDateString();

        SisaBahanBakuMasak::updateOrCreate(
            [
                'tanggal' => $tanggal,
                'id_menu' => $request->id_menu,
            ],
            [
                'sisa_karbo' => $request->sisa_karbo ?? 0,
                'sisa_protein' => $request->sisa_protein ?? 0,
                'sisa_sayur' => $request->sisa_sayur ?? 0,
                'sisa_buah' => $request->sisa_buah ?? 0,
                'sisa_susu' => $request->sisa_susu ?? 0,
                'keterangan' => $request->keterangan,
            ]
        );

        return redirect()
            ->route('menu-laporan-masak-harian', ['tanggal' => $tanggal])
            ->with('success', 'Sisa bahan baku berhasil disimpan.');
    }

    public function v_laporan_hasil_masak(Request $request)
    {
        $header = "Laporan Hasil Masak";
        $tanggal = $tanggal ?? Carbon::now()->toDateString(); // Format: YYYY-MM-DD

        // Cek apakah format tanggal valid
        try {
            $tanggalValid = Carbon::parse($tanggal)->toDateString();
        } catch (\Exception $e) {
            return abort(404, "Format tanggal tidak valid");
        }

        // Contoh ambil data dari database atau olah data berdasarkan tanggal
        // $data = ModelLaporan::whereDate('created_at', $tanggalValid)->get();


        return view('kitchen.hasilmasak.laporan_hasil_masak', compact('header', 'tanggal'));
    }

    public function dt_global_hasil_masak(Request $request)
    {
        $tanggal    = Carbon::parse($request->tanggal)->format('Y-m-d') ?? date('Y-m-d');
        $menu = DB::table('tb_menu')
            ->leftJoin('tb_resep as karbo', 'tb_menu.karbohidrat', '=', 'karbo.id')
            ->leftJoin('tb_resep as protein', 'tb_menu.protein', '=', 'protein.id')
            ->leftJoin('tb_resep as sayur', 'tb_menu.sayur', '=', 'sayur.id')
            ->leftJoin('tb_resep as buah', 'tb_menu.buah', '=', 'buah.id')
            ->leftJoin('tb_resep as susu', 'tb_menu.susu', '=', 'susu.id')
            ->select(
                'tb_menu.id',
                'tb_menu.tanggal_kirim',
                'karbo.nama_resep as nama_karbohidrat',
                'protein.nama_resep as nama_protein',
                'sayur.nama_resep as nama_sayur',
                'buah.nama_resep as nama_buah',
                'susu.nama_resep as nama_susu'
            )
            ->where('tb_menu.tanggal_kirim', $tanggal)
            ->first();
        $menuid = Menu::where('tanggal_kirim', $tanggal)->first();
        $jumlah_hasil_karbo         = HistoriMenu::where('id_menu', $menuid->id)->sum('hasil_menu_karbohidrat');
        $jumlah_hasil_protein       = HistoriMenu::where('id_menu', $menuid->id)->sum('hasil_menu_protein');
        $jumlah_hasil_sayur         = HistoriMenu::where('id_menu', $menuid->id)->sum('hasil_menu_sayur');
        $jumlah_hasil_buah          = HistoriMenu::where('id_menu', $menuid->id)->sum('hasil_menu_buah');
        $jumlah_hasil_susu          = HistoriMenu::where('id_menu', $menuid->id)->sum('hasil_menu_susu');

        $waktu = HistoriMenu::where('id_menu', $menuid->id)->first();
        $waktuMulai = Carbon::parse($waktu->waktu_mulai_masak);
        
        $hasil_pack_karbo           = $jumlah_hasil_karbo / 150;
        $satuan_pack_karbo          = "gram";
        $hasil_pack_protein         = $jumlah_hasil_protein / 100;
        $satuan_pack_protein        = "gram";
        $hasil_pack_sayur           = $jumlah_hasil_sayur / 100;
        $satuan_pack_sayur          = "gram";
        $hasil_pack_buah            = $jumlah_hasil_buah / 1;
        $satuan_pack_buah           = "biji";
        if ($menu->nama_susu == "tempe ***") {
            $hasil_pack_susu = $jumlah_hasil_susu / 50;
            $satuan_pack_susu          = "gram";
        } else {
            $hasil_pack_susu = $jumlah_hasil_susu / 1;
            $satuan_pack_susu          = "pcs";
        }
        $data_gastronom = DB::table('tb_hasil_masak')
            ->selectRaw('
                COUNT(CASE WHEN id_komponen_sehat = 1 THEN 1 END) AS jumlah_gastronom_karbo,
                COUNT(CASE WHEN id_komponen_sehat = 2 THEN 1 END) AS jumlah_gastronom_protein,
                COUNT(CASE WHEN id_komponen_sehat = 3 THEN 1 END) AS jumlah_gastronom_sayur,
                COUNT(CASE WHEN id_komponen_sehat = 4 THEN 1 END) AS jumlah_gastronom_buah,
                COUNT(CASE WHEN id_komponen_sehat = 5 THEN 1 END) AS jumlah_gastronom_susu
            ')
            ->where('id_menu', $menu->id)
            ->first();
        $daftar_menu    = $menu->nama_karbohidrat . ' , ' . $menu->nama_sayur . ' , ' . $menu->nama_protein . ' , ' . $menu->nama_buah . ' , ' . $menu->nama_susu;
        $jumlah_porsi   = rincian_sekolah::where('id_menu_harian', $menu->id)->sum('jumlah_penerima_total');
        $tanggal_menu   = Carbon::parse($menu->tanggal_kirim)->translatedFormat('l, j F Y');
        $jumlah_ompreng = tbOmprengTransaksi::where('tb_menu_id', $menu->id)->count() ?? 0;
        $hasilTerakhir = DB::table('tb_hasil_masak')
            ->select('id_komponen_sehat', DB::raw('MAX(waktu_matang) as waktu_terakhir'))
            ->where('id_menu', $menuid->id)
            ->whereIn('id_komponen_sehat', [1, 2, 3, 4, 5])
            ->groupBy('id_komponen_sehat')
            ->get();
            
        $waktukarbo  = $hasilTerakhir->firstWhere('id_komponen_sehat', 1)->waktu_terakhir ?? null;
        $waktuprotein = $hasilTerakhir->firstWhere('id_komponen_sehat', 2)->waktu_terakhir ?? null;
        $waktusayur = $hasilTerakhir->firstWhere('id_komponen_sehat', 3)->waktu_terakhir ?? null;
        $waktubuah = $hasilTerakhir->firstWhere('id_komponen_sehat', 4)->waktu_terakhir ?? null;
        $waktususu = $hasilTerakhir->firstWhere('id_komponen_sehat', 5)->waktu_terakhir ?? null;

        if($waktukarbo)
        {
            $selisihMenit_karbo     = $waktuMulai->diffInMinutes($waktukarbo) ?? null;
        }else{
            $selisihMenit_karbo     = 0;
        }
        if ($waktuprotein) {
            $selisihMenit_protein     = $waktuMulai->diffInMinutes($waktuprotein) ?? null;
        } else {
            $selisihMenit_protein     = 0;
        }
        if ($waktuprotein) {
            $selisihMenit_protein     = $waktuMulai->diffInMinutes($waktuprotein) ?? null;
        } else {
            $selisihMenit_protein     = 0;
        }
        if ($waktusayur) {
            $selisihMenit_sayur     = $waktuMulai->diffInMinutes($waktusayur) ?? null;
        } else {
            $selisihMenit_sayur     = 0;
        }
        if ($waktubuah) {
            $selisihMenit_buah     = $waktuMulai->diffInMinutes($waktubuah) ?? null;
        } else {
            $selisihMenit_buah     = 0;
        }
        if ($waktususu) {
            $selisihMenit_susu     = $waktuMulai->diffInMinutes($waktususu) ?? null;
        } else {
            $selisihMenit_susu     = 0;
        }

       
        $laporanItems = [
            'Menu',
            'PO',
            'Penerimaan',
            'Gudang',
            'Hasil Masak',
            'Surat Jalan'
        ];
        if (!$menu) {
            $data = [
                ['Karbohidrat', ''],
                ['Protein', ''],
                ['Sayur', ''],
                ['Buah', ''],
                ['Suplemen', ''],

            ];
        } else {
            if(ceil($hasil_pack_karbo) > $jumlah_ompreng)
            {
                $keterangan_pack_karbo = 'lebih ' . number_format(abs(ceil($hasil_pack_karbo) - $jumlah_ompreng), 0 , ',','.');
                $keterangan_pack_protein = 'lebih ' .number_format(abs(ceil($hasil_pack_protein) - $jumlah_ompreng), 0 , ',','.') ;
                $keterangan_pack_sayur = 'lebih ' . number_format(abs(ceil($hasil_pack_sayur) - $jumlah_ompreng), 0 , ',','.');
                $keterangan_pack_buah = 'lebih ' . number_format(abs(ceil($hasil_pack_buah) - $jumlah_ompreng), 0 , ',','.');
                $keterangan_pack_penunjang = 'lebih ' . number_format(abs(ceil($hasil_pack_susu) - $jumlah_ompreng), 0 , ',','.');
            }else{
                $keterangan_pack_karbo = 'Kurang ' . number_format(abs(ceil($hasil_pack_karbo) - $jumlah_ompreng), 0 , ',','.');
                $keterangan_pack_protein = 'Kurang ' . number_format(abs(ceil($hasil_pack_protein) - $jumlah_ompreng), 0 , ',','.');
                $keterangan_pack_sayur = 'Kurang ' . number_format(abs(ceil($hasil_pack_sayur) - $jumlah_ompreng), 0 , ',','.');
                $keterangan_pack_buah = 'Kurang ' .number_format(abs(ceil($hasil_pack_buah) - $jumlah_ompreng), 0 , ',','.');
                $keterangan_pack_penunjang = 'Kurang ' . number_format(abs(ceil($hasil_pack_susu) - $jumlah_ompreng), 0 , ',','.');
            }
            $data = [
                [$menu->nama_karbohidrat, number_format(ceil($hasil_pack_karbo), 0 , ',','.') ,'pack', number_format( $jumlah_hasil_karbo , 0 , ',','.'), $satuan_pack_karbo, $data_gastronom->jumlah_gastronom_karbo, '180 gram / pack | '. $selisihMenit_karbo .' menit', $jumlah_ompreng,'pack', ' '. $keterangan_pack_karbo . ' pack'],
                [$menu->nama_protein, number_format(ceil($hasil_pack_protein), 0 , ',','.'), 'pack', number_format($jumlah_hasil_protein, 0 , ',','.'), $satuan_pack_protein, $data_gastronom->jumlah_gastronom_protein, '100 gram / pack | ' . $selisihMenit_protein . ' menit', $jumlah_ompreng, 'pack', ' ' . $keterangan_pack_protein . ' pack'],
                [$menu->nama_sayur, number_format(ceil($hasil_pack_sayur), 0 , ',','.'), 'pack', number_format($jumlah_hasil_sayur , 0 , ',','.'), $satuan_pack_sayur, $data_gastronom->jumlah_gastronom_sayur, '100 gram / pack | ' . $selisihMenit_sayur . ' menit', $jumlah_ompreng, 'pack', ' ' . $keterangan_pack_sayur . ' pack'],
                [$menu->nama_buah, number_format(ceil($hasil_pack_buah), 0 , ',','.'), 'pack', number_format($jumlah_hasil_buah, 0 , ',','.'), $satuan_pack_buah, $data_gastronom->jumlah_gastronom_buah, '100 gram / pack | ' .  $selisihMenit_buah . ' menit', $jumlah_ompreng, 'pack', ' ' . $keterangan_pack_buah.' pack'],
                [$menu->nama_susu, number_format(ceil($hasil_pack_susu), 0 , ',','.'), 'pack', number_format($jumlah_hasil_susu, 0 , ',','.'), $satuan_pack_susu, $data_gastronom->jumlah_gastronom_susu, '50 gram | 1 pack | ' .  $selisihMenit_buah . ' menit', $jumlah_ompreng, 'pack', ' ' . $keterangan_pack_penunjang . ' pack'],
              

            ];
        }






        return response()->json(['data' => $data]);
    }

    
}
