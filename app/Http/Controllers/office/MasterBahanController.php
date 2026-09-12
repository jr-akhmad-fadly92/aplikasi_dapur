<?php

namespace App\Http\Controllers\office;

use App\Exports\LaporanDataBahanExport;

use App\Http\Controllers\Controller;
use App\Models\SpesifikasiBahan;
use App\Models\TbMasterBahan;
use App\Models\TbSatuan;
use App\Models\DataDapur;
use App\Models\Golongan;
use App\Models\golonganBahan;
use App\Models\MasterBahanNutrisi;
use App\Models\TbBahanAkg;
use App\Models\TbRincianKontrak;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
//import return type View
use Illuminate\View\View;
//import return type redirectResponse
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Resep;
use App\Models\BoxBahanBaku;
use App\Services\BoxBahanBakuRuleService;

class MasterBahanController extends Controller
{
    public function index()
    {
        $header = "Data Bahan";
        if (request()->ajax()) {
        //$users = User::query();
            $query = TbMasterBahan::query();

            // Filter by jenis (type)
            if (request()->has('filter_jenis') && request('filter_jenis') != '') {
                $query->where('jenis', request('filter_jenis'));
            }

            // Filter by satuan (unit)
            if (request()->has('filter_satuan') && request('filter_satuan') != '') {
                $query->where('satuan_bahan', request('filter_satuan'));
            }

            $bahan = $query->get();
           
            
            return DataTables::of($bahan)
                ->addIndexColumn() // Menambah index
                ->addColumn('nama_satuan_bahan', function ($row) {
                    $data = TbSatuan::where('id', $row->satuan_bahan)->first();
                    if ($data) {
                        return $data->satuan;
                    } else {
                        return '-';
                    }
                    //return $spesifikasi;
                })

                ->addColumn('Spesifikasi_bahan', function ($row) {
                    $data_spesifikasi = SpesifikasiBahan::where('id_bahan', $row->id)->first();
                    if($data_spesifikasi)
                    {
                        $spesifikasi = $data_spesifikasi->spesifikasi;
                    }else{
                        $spesifikasi = "belum ada";
                    }
                    return $spesifikasi;
                })
                ->addColumn('action', function ($row) {
                    $data_spesifikasi = SpesifikasiBahan::where('id_bahan', $row->id)->first();
                    if($data_spesifikasi)
                    {
                        $spesifikasi = $data_spesifikasi->spesifikasi;
                    }else{
                        $spesifikasi = ".";
                    }
                          $btn = '<a hidden href="' . route('kandungan-gizi', $row['id']) . '" class="edit btn btn-success btn-sm " id="btn-edit-post" data-id="' . $row['id'] . '">Detail Gizi</a>
                              <a href="' . route('master_bahan.akg.index', $row['id']) . '" class="edit btn btn-secondary btn-sm" data-id="' . $row['id'] . '">AKG Bahan</a>
                           <a hidden href="' . route('kemasan-material', $row['id']) . '" class="edit btn btn-success btn-sm " id="btn-edit-post" data-id="' . $row['id'] . '">Detail Kemasan</a>
                           <a href="' . route('master_bahan.edit', $row['id']) . '" class="edit btn btn-primary btn-sm " id="btn-edit-post" data-id="' . $row['id'] . '">Edit</a>
                            <a hidden href="' . route('master_bahan.delete', $row->id) . '" 
                            class="edit btn btn-danger btn-sm delete-button" 
                            data-id="' . $row->id . '">Deletes</a>
                            <button class="btn btn-sm btn-warning openModalBtn" 
                                data-idbahan="' . $row->id . '"
                                data-keterangan="' . $spesifikasi . '">
                                Tambah Spesifikasi bahan
                            </button>';
                    
                    return $btn;
                })
                ->addColumn('jenis_bahan', function($row){
                    $nilai = '-';
                    if($row->jenis == 1){
                    $nilai = 'Karbo';
                    } else if ($row->jenis == 2) {
                    $nilai  ='Lauk';
                    } else if ($row->jenis == 3) {
                    $nilai = 'sayur';
                    } else if ($row->jenis == 4) {
                    $nilai = 'buah';
                    } else if ($row->jenis == 5) {
                    $nilai = 'suplemen';
                    } else if ($row->jenis == 6) {
                    $nilai = 'bumbu';
                    } else if ($row->jenis == 7) {
                        $nilai = 'Penunjang';
                    }else{
                    $nilai ='-';
                    }
                    return $nilai;
                }) // Menambah index
                ->rawColumns(['action', 'jenis_bahan'])
                ->make(true);
               
        }
        return view('office/masterbahan.index',compact('header'));
        
        

    }

    

    public function edit(string $id): View
    {
        $header = "Rubah Master Bahan";
        //get product by ID
        $bahan = TbMasterBahan::findOrFail($id);
        $satuan = TbSatuan::all();
        $golongan = Golongan::all();
        $golongan_bahan = golonganBahan::where('bahan_id', $id)->first();
        //render view with product
        return view('office/masterbahan.edit', compact('bahan','satuan','header', 'golongan', 'golongan_bahan'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        //validate form
        $request->validate([
            'bahan'         => 'required|min:3',
            'satuan_bahan'   => 'required|min:1',
           
        ]);

        //get product by ID
        $bahan = TbMasterBahan::findOrFail($id);

        $golongan_bahan = TbMasterBahan::findOrFail($id);

        //update product without image
        $bahan->update([
            'bahan'             => $request->bahan,
            'gramasi'           => 1,
            'satuan_gudang'     => 1,
            'satuan_bahan'      => $request->satuan_bahan,
            'jenis'      => $request->jenis


        ]);
        golonganBahan::where('bahan_id', $id)->update([
            'golongan_id'       => $request->golongan,
            'keterangan'        => '-'
        ]);
        /*$golongan_bahan = golonganBahan::where('bahan_id',$id)->first();

        $golongan_bahan->update([
            'golongan_id'             => $request->golongan,
            'keterangan'              => '-'


        ]);
        */
        //redirect to index
        return redirect()->route('master_bahan.index')->with(['success' => 'Data Berhasil Diubah!']);
    }

    public function create(): View
    {
        $header = "Tambah Master Bahan";
        $satuan = TbSatuan::all();
        $golongan = Golongan::all();
        return view('office/masterbahan.create', compact( 'satuan', 'golongan','header'));
    }

    public function store(Request $request): RedirectResponse
    {
        //validate form
        $request->validate([
            'bahan'         => 'required|min:3',
            //'gramasi'   => 'required|min:1',
            //'satuan_gudang'   => 'required|min:1',
            'satuan_bahan'   => 'required|min:1',
            'golongan'   => 'required|min:1',

        ]);
        $cek = TbMasterBahan::where('bahan',$request->bahan)->count();
        if($cek > 0 )
        {
            return redirect()->back()->with(['error' => 'Data Sudah Ada! '. $request->bahan]);
        }

        //create product
        TbMasterBahan::create([
            'bahan'             => $request->bahan,
            'gramasi'           => 1,
            'satuan_gudang'     => 1,
            'satuan_bahan'      => $request->satuan_bahan,
            'jenis'             => $request->jenis
        ]);
        $data_terakhir      = TbMasterBahan::latest()->first();
        $cek_kontrak        = TbRincianKontrak::where('id_bahan', $data_terakhir->id)->count();
        if($cek_kontrak == 0)
        {
            TbRincianKontrak::create([
                'id_kontrak'    => 13,
                'id_bahan'      => $data_terakhir->id,
                'harga_bahan'   => 1,
                'jumlah_bahan'  => 1,
                'satuan_bahan'  => $data_terakhir->satuan_bahan,
                'merek_bahan'   => $data_terakhir->bahan,
                'status'        => 1,
                'kemasan'       => 'Gram',
            ]);
        }
        golonganBahan::create([
            'golongan_id'       => $request->golongan,
            'bahan_id'          => $data_terakhir->id,
            'keterangan'        => '-'
        ]);

        $satuan_obj  = TbSatuan::where('id', $data_terakhir->satuan_bahan)->first();
        $satuan_nama = $satuan_obj ? $satuan_obj->satuan : '';

        $ruleService = new BoxBahanBakuRuleService();
        $ruleData = $ruleService->calculate(
            (string) $data_terakhir->bahan,
            (int) $data_terakhir->jenis,
            (string) $satuan_nama
        );

        BoxBahanBaku::create([
            'id_bahan'     => $data_terakhir->id,
            'isi_per_box'  => $ruleData['isi_per_box'],
            'penyusutan'   => $ruleData['penyusutan'],
            'hasil_matang' => $ruleData['hasil_matang'],
        ]);

        //redirect to index
        return redirect()->route('master_bahan.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }

    public function destroy($id)
    {
        //get product by ID
        $bahan = TbMasterBahan::findOrFail($id);


        //delete product
        $bahan->delete();

        //redirect to index
        return redirect()->route('master_bahan.index')->with(['success' => 'Data Berhasil Dihapus!']);
    }

    public function akgIndex(string $id): View
    {
        $bahan = TbMasterBahan::findOrFail($id);
        $header = 'AKG Bahan - ' . $bahan->bahan;

        $akgList = TbBahanAkg::with('masterBahanNutrisi')
            ->where('id_bahan', $id)
            ->orderByDesc('id')
            ->get();

        $masterBahanNutrisiList = MasterBahanNutrisi::query()
            ->orderBy('nama_bahan')
            ->get(['id', 'kode', 'nama_bahan', 'bdd', 'energi', 'protein', 'lemak', 'karbohidrat', 'serat', 'natrium']);

        return view('office.masterbahan.akg', compact('header', 'bahan', 'akgList', 'masterBahanNutrisiList'));
    }

    public function akgStore(Request $request, string $id): RedirectResponse
    {
        TbMasterBahan::findOrFail($id);

        $validated = $request->validate([
            'id_master_bahan_nutrisi' => 'required|integer',
            'bdd' => 'nullable|numeric|min:0',
            'energi' => 'nullable|numeric|min:0',
            'protein' => 'nullable|numeric|min:0',
            'lemak' => 'nullable|numeric|min:0',
            'karbohidrat' => 'nullable|numeric|min:0',
            'serat' => 'nullable|numeric|min:0',
            'natrium' => 'nullable|numeric|min:0',
        ]);

        TbBahanAkg::updateOrCreate(
            [
                'id_bahan' => (int) $id,
                'id_master_bahan_nutrisi' => (int) $validated['id_master_bahan_nutrisi'],
            ],
            [
                'bdd' => (float) ($validated['bdd'] ?? 0),
                'energi' => (float) ($validated['energi'] ?? 0),
                'protein' => (float) ($validated['protein'] ?? 0),
                'lemak' => (float) ($validated['lemak'] ?? 0),
                'karbohidrat' => (float) ($validated['karbohidrat'] ?? 0),
                'serat' => (float) ($validated['serat'] ?? 0),
                'natrium' => (float) ($validated['natrium'] ?? 0),
            ]
        );

        return redirect()->route('master_bahan.akg.index', $id)->with(['success' => 'Data AKG bahan berhasil disimpan!']);
    }

    public function akgUpdate(Request $request, string $id, string $akgId): RedirectResponse
    {
        TbMasterBahan::findOrFail($id);

        $validated = $request->validate([
            'bdd' => 'nullable|numeric|min:0',
            'energi' => 'nullable|numeric|min:0',
            'protein' => 'nullable|numeric|min:0',
            'lemak' => 'nullable|numeric|min:0',
            'karbohidrat' => 'nullable|numeric|min:0',
            'serat' => 'nullable|numeric|min:0',
            'natrium' => 'nullable|numeric|min:0',
        ]);

        $akg = TbBahanAkg::where('id', $akgId)
            ->where('id_bahan', $id)
            ->firstOrFail();

        $akg->update([
            'bdd' => (float) ($validated['bdd'] ?? 0),
            'energi' => (float) ($validated['energi'] ?? 0),
            'protein' => (float) ($validated['protein'] ?? 0),
            'lemak' => (float) ($validated['lemak'] ?? 0),
            'karbohidrat' => (float) ($validated['karbohidrat'] ?? 0),
            'serat' => (float) ($validated['serat'] ?? 0),
            'natrium' => (float) ($validated['natrium'] ?? 0),
        ]);

        return redirect()->route('master_bahan.akg.index', $id)->with(['success' => 'Data AKG bahan berhasil diubah!']);
    }

    public function akgDestroy(string $id, string $akgId): RedirectResponse
    {
        TbMasterBahan::findOrFail($id);

        TbBahanAkg::where('id', $akgId)
            ->where('id_bahan', $id)
            ->delete();

        return redirect()->route('master_bahan.akg.index', $id)->with(['success' => 'Data AKG bahan berhasil dihapus!']);
    }

    public function pdf_laporan_master_bahan()
    {
        
        $dapur = DataDapur::first();
        $jumlah_karbohidrat = TbMasterBahan::where('jenis', 1)->count();
        $jumlah_protein = TbMasterBahan::where('jenis', 2)->count();
        $jumlah_sayur = TbMasterBahan::where('jenis', 3)->count();
        $jumlah_buah = TbMasterBahan::where('jenis', 4)->count();
        $jumlah_tambahan = TbMasterBahan::where('jenis', 5)->count();
        $jumlah_bumbu = TbMasterBahan::where('jenis', 6)->count();
        $jumlah_penunjang = TbMasterBahan::where('jenis', 7)->count();
        DB::statement("SET @rownum := 0");
        $data_karbohidrat = DB::select("
                SELECT 
                    @rownum := @rownum + 1 AS nomor_urut,
                    mb.id,
                    mb.bahan,
                    mb.jenis,
                    IF(sb.spesifikasi IS NULL, '-', sb.spesifikasi) AS spesifikasi
                FROM 
                    tb_master_bahan mb
                LEFT JOIN 
                    tb_spesifikasi_bahan sb ON sb.id_bahan = mb.id
                WHERE 
                    mb.jenis = 1         
            ");
        DB::statement("SET @rownum := 0");
        $data_sayur = DB::select("
                 SELECT 
                    @rownum := @rownum + 1 AS nomor_urut,
                    mb.id,
                    mb.bahan,
                    mb.jenis,
                    IF(sb.spesifikasi IS NULL, '-', sb.spesifikasi) AS spesifikasi
                FROM 
                    tb_master_bahan mb
                LEFT JOIN 
                    tb_spesifikasi_bahan sb ON sb.id_bahan = mb.id
                WHERE 
                    mb.jenis = 2
            ");
        DB::statement("SET @rownum := 0");
        $data_protein = DB::select("
                SELECT 
                    @rownum := @rownum + 1 AS nomor_urut,
                    mb.id,
                    mb.bahan,
                    mb.jenis,
                    IF(sb.spesifikasi IS NULL, '-', sb.spesifikasi) AS spesifikasi
                FROM 
                    tb_master_bahan mb
                LEFT JOIN 
                    tb_spesifikasi_bahan sb ON sb.id_bahan = mb.id
                WHERE 
                    mb.jenis = 3
            ");
        DB::statement("SET @rownum := 0");
        $data_buah = DB::select("
               SELECT 
                    @rownum := @rownum + 1 AS nomor_urut,
                    mb.id,
                    mb.bahan,
                    mb.jenis,
                    IF(sb.spesifikasi IS NULL, '-', sb.spesifikasi) AS spesifikasi
                FROM 
                    tb_master_bahan mb
                LEFT JOIN 
                    tb_spesifikasi_bahan sb ON sb.id_bahan = mb.id
                WHERE 
                    mb.jenis = 4
            ");
        DB::statement("SET @rownum := 0");
        $data_tambahan = DB::select("
               SELECT 
                    @rownum := @rownum + 1 AS nomor_urut,
                    mb.id,
                    mb.bahan,
                    mb.jenis,
                    IF(sb.spesifikasi IS NULL, '-', sb.spesifikasi) AS spesifikasi
                FROM 
                    tb_master_bahan mb
                LEFT JOIN 
                    tb_spesifikasi_bahan sb ON sb.id_bahan = mb.id
                WHERE 
                    mb.jenis = 5
            ");
        DB::statement("SET @rownum := 0");
        $data_bumbu = DB::select("
                 SELECT 
                    @rownum := @rownum + 1 AS nomor_urut,
                    mb.id,
                    mb.bahan,
                    mb.jenis,
                    IF(sb.spesifikasi IS NULL, '-', sb.spesifikasi) AS spesifikasi
                FROM 
                    tb_master_bahan mb
                LEFT JOIN 
                    tb_spesifikasi_bahan sb ON sb.id_bahan = mb.id
                WHERE 
                    mb.jenis = 6
            ");
            DB::statement("SET @rownum := 0");
        $data_penunjang = DB::select("
                 SELECT 
                    @rownum := @rownum + 1 AS nomor_urut,
                    mb.id,
                    mb.bahan,
                    mb.jenis,
                    IF(sb.spesifikasi IS NULL, '-', sb.spesifikasi) AS spesifikasi
                FROM 
                    tb_master_bahan mb
                LEFT JOIN 
                    tb_spesifikasi_bahan sb ON sb.id_bahan = mb.id
                WHERE 
                    mb.jenis = 7
            ");
        $pdf = Pdf::loadView('office/masterbahan.template_laporan_master_bahan', compact(
           
            'dapur',
            'jumlah_karbohidrat',
            'jumlah_protein',
            'jumlah_sayur',
            'jumlah_buah',
            'jumlah_tambahan',
            'jumlah_bumbu',
            'jumlah_penunjang',
            'data_karbohidrat',
            'data_protein',
            'data_sayur',
            'data_buah',
            'data_tambahan',
            'data_bumbu',
            'data_penunjang'


        ));
        return $pdf->download($this->buildMasterBahanExportFilename($dapur, 'pdf'));
    }

    public function Lap_data_bahan_export(Request $request)
    {
        $start =  Carbon::today();
        $end   =  Carbon::today();


        $start =  Carbon::today();
        $end =  Carbon::today();
        $tanggalMulai =  Carbon::today();
        $tanggalSelesai =  Carbon::today();
        // 
        $dapur = DataDapur::first();
        $satuan = TbSatuan::all();
        $jumlah_karbohidrat = TbMasterBahan::where('jenis', 1)->count();


        $jumlah_protein = TbMasterBahan::where('jenis', 2)->count();
        $jumlah_sayur = TbMasterBahan::where('jenis', 3)->count();
        $jumlah_buah = TbMasterBahan::where('jenis', 4)->count();
        $jumlah_tambahan = TbMasterBahan::where('jenis', 5)->count();
        $jumlah_bumbu = TbMasterBahan::where('jenis', 6)->count();
        $jumlah_penunjang = TbMasterBahan::where('jenis', 7)->count();

        $karbohidrat =  DB::table('tb_menu_bahan as mb')
            ->join('tb_resep as resep', 'mb.menu_id', '=', 'resep.id')
            ->join('tb_master_bahan as bahan', 'mb.bahan_id', '=', 'bahan.id')
            ->join('tb_satuan as satuan', 'bahan.satuan_bahan', '=', 'satuan.id')
            ->select(
                'resep.nama_resep',

                DB::raw("GROUP_CONCAT(bahan.bahan SEPARATOR ', ') AS bahan_dan_satuan"),
                DB::raw("CASE resep.id_komponen_sehat
                WHEN 1 THEN 'karbohidrat'
                WHEN 2 THEN 'protein'
                WHEN 3 THEN 'sayur'
                WHEN 4 THEN 'buah'
                WHEN 5 THEN 'pendamping'
                ELSE 'lainnya'
            END AS komponen")
            )
            ->where('resep.id_komponen_sehat', 1) // Jika mau filter komponen tertentu
            ->groupBy('resep.id', 'resep.nama_resep', 'resep.id_komponen_sehat')
            ->get();
        $protein =  DB::table('tb_menu_bahan as mb')
            ->join('tb_resep as resep', 'mb.menu_id', '=', 'resep.id')
            ->join('tb_master_bahan as bahan', 'mb.bahan_id', '=', 'bahan.id')
            ->join('tb_satuan as satuan', 'bahan.satuan_bahan', '=', 'satuan.id')
            ->select(
                'resep.nama_resep',
                DB::raw("GROUP_CONCAT(bahan.bahan SEPARATOR ', ') AS bahan_dan_satuan"),
                DB::raw("CASE resep.id_komponen_sehat
                WHEN 1 THEN 'karbohidrat'
                WHEN 2 THEN 'protein'
                WHEN 3 THEN 'sayur'
                WHEN 4 THEN 'buah'
                WHEN 5 THEN 'pendamping'
                ELSE 'lainnya'
            END AS komponen")
            )
            ->where('resep.id_komponen_sehat', 2) // Jika mau filter komponen tertentu
            ->groupBy('resep.id', 'resep.nama_resep', 'resep.id_komponen_sehat')
            ->get();

        $sayur =  DB::table('tb_menu_bahan as mb')
            ->join('tb_resep as resep', 'mb.menu_id', '=', 'resep.id')
            ->join('tb_master_bahan as bahan', 'mb.bahan_id', '=', 'bahan.id')
            ->join('tb_satuan as satuan', 'bahan.satuan_bahan', '=', 'satuan.id')
            ->select(
                'resep.nama_resep',
                DB::raw("GROUP_CONCAT(bahan.bahan SEPARATOR ', ') AS bahan_dan_satuan"),
                DB::raw("CASE resep.id_komponen_sehat
                WHEN 1 THEN 'karbohidrat'
                WHEN 2 THEN 'protein'
                WHEN 3 THEN 'sayur'
                WHEN 4 THEN 'buah'
                WHEN 5 THEN 'pendamping'
                ELSE 'lainnya'
            END AS komponen")
            )
            ->where('resep.id_komponen_sehat', 3) // Jika mau filter komponen tertentu
            ->groupBy('resep.id', 'resep.nama_resep', 'resep.id_komponen_sehat')
            ->get();
        $buah =  DB::table('tb_menu_bahan as mb')
            ->join('tb_resep as resep', 'mb.menu_id', '=', 'resep.id')
            ->join('tb_master_bahan as bahan', 'mb.bahan_id', '=', 'bahan.id')
            ->join('tb_satuan as satuan', 'bahan.satuan_bahan', '=', 'satuan.id')
            ->select(
                'resep.nama_resep',
                DB::raw("GROUP_CONCAT(bahan.bahan SEPARATOR ', ') AS bahan_dan_satuan"),
                DB::raw("CASE resep.id_komponen_sehat
                WHEN 1 THEN 'karbohidrat'
                WHEN 2 THEN 'protein'
                WHEN 3 THEN 'sayur'
                WHEN 4 THEN 'buah'
                WHEN 5 THEN 'pendamping'
                ELSE 'lainnya'
            END AS komponen")
            )
            ->where('resep.id_komponen_sehat', 4) // Jika mau filter komponen tertentu
            ->groupBy('resep.id', 'resep.nama_resep', 'resep.id_komponen_sehat')
            ->get();

        $tambahan =  DB::table('tb_menu_bahan as mb')
            ->join('tb_resep as resep', 'mb.menu_id', '=', 'resep.id')
            ->join('tb_master_bahan as bahan', 'mb.bahan_id', '=', 'bahan.id')
            ->join('tb_satuan as satuan', 'bahan.satuan_bahan', '=', 'satuan.id')
            ->select(
                'resep.nama_resep',
                DB::raw("GROUP_CONCAT(bahan.bahan SEPARATOR ', ') AS bahan_dan_satuan"),
                DB::raw("CASE resep.id_komponen_sehat
                WHEN 1 THEN 'karbohidrat'
                WHEN 2 THEN 'protein'
                WHEN 3 THEN 'sayur'
                WHEN 4 THEN 'buah'
                WHEN 5 THEN 'pendamping'
                ELSE 'lainnya'
            END AS komponen")
            )
            ->where('resep.id_komponen_sehat', 5) // Jika mau filter komponen tertentu
            ->groupBy('resep.id', 'resep.nama_resep', 'resep.id_komponen_sehat')
            ->get();

        $data_resep = DB::table('tb_menu_bahan as mb')
            ->join('tb_resep as resep', 'mb.menu_id', '=', 'resep.id')
            ->join('tb_master_bahan as bahan', 'mb.bahan_id', '=', 'bahan.id')
            ->join('tb_satuan as satuan', 'bahan.satuan_bahan', '=', 'satuan.id')
            ->select(
                'resep.nama_resep',
                'bahan.bahan',
                'satuan.satuan',
                DB::raw("
                    CASE resep.id_komponen_sehat
                        WHEN 1 THEN 'karbohidrat'
                        WHEN 2 THEN 'protein'
                        WHEN 3 THEN 'sayur'
                        WHEN 4 THEN 'buah'
                        WHEN 5 THEN 'pendamping'
                        ELSE 'lainnya'
                    END AS komponen
                ")
            )
            ->get();


        $master_karbo = $this->getMasterBahanWithSpesifikasi(1);
        $master_protein = $this->getMasterBahanWithSpesifikasi(2);
        $master_sayur = $this->getMasterBahanWithSpesifikasi(3);
        $master_buah = $this->getMasterBahanWithSpesifikasi(4);
        $master_tambahan = $this->getMasterBahanWithSpesifikasi(5);
        $master_bumbu = $this->getMasterBahanWithSpesifikasi(6);
        $master_penunjang = $this->getMasterBahanWithSpesifikasi(7);



        return Excel::download(
            new LaporanDataBahanExport(
                $master_karbo,
                $master_protein,
                $master_sayur,
                $master_buah,
                $master_tambahan,
                $master_bumbu,
                $master_penunjang,
                $data_resep,
                $dapur,
                $satuan,
                $jumlah_karbohidrat,
                $jumlah_protein,
                $jumlah_sayur,
                $jumlah_buah,
                $jumlah_tambahan,
                $jumlah_bumbu,
                $jumlah_penunjang,
                $karbohidrat,
                $protein,
                $sayur,
                $buah,
                $tambahan,
                $start,
                $end
            ),
            $this->buildMasterBahanExportFilename($dapur, 'xlsx')
        );
    }

    private function buildMasterBahanExportFilename($dapur, string $extension): string
    {
        $tanggal = Carbon::today()->format('d-m-Y');
        $namaSppg = trim((string) ($dapur->nama_dapur ?? 'Tanpa Nama'));
        $kotaKab = trim((string) ($dapur->kota ?? 'Tanpa Kota'));

        $base = 'master bahan ' . $tanggal . ' SPPG ' . $namaSppg . ' Kota/kab ' . $kotaKab;
        $safeBase = preg_replace('/[\\\\\/:*?"<>|]+/', ' ', $base);
        $safeBase = preg_replace('/\s+/', ' ', (string) $safeBase);
        $safeBase = trim((string) $safeBase);

        return $safeBase . '.' . $extension;
    }

    private function getMasterBahanWithSpesifikasi(int $jenis)
    {
        $spesifikasiSub = DB::table('tb_spesifikasi_bahan')
            ->select('id_bahan', DB::raw('MAX(spesifikasi) as spesifikasi'))
            ->groupBy('id_bahan');

        return DB::table('tb_master_bahan as mb')
            ->leftJoinSub($spesifikasiSub, 'sb', function ($join) {
                $join->on('sb.id_bahan', '=', 'mb.id');
            })
            ->where('mb.jenis', $jenis)
            ->select('mb.*', DB::raw("COALESCE(sb.spesifikasi, '-') as spesifikasi"))
            ->orderBy('mb.bahan')
            ->get();
    }
    
  
}
