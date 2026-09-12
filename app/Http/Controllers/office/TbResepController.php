<?php

namespace App\Http\Controllers\office;

use App\Exports\LaporanDataMasterResepExport;
use App\Http\Controllers\Controller;
use App\Models\DataDapur;
use App\Models\GramasiResep;
use App\Models\MasterBahanNutrisi;
use App\Models\ResepRealisasiAkg;
use App\Models\Resep;
use App\Models\MenuBahan;
use App\Models\KomponenSehat;
use App\Models\TbMasterBahan;
use App\Models\PerhitunganBumbu;
use App\Models\TbSatuan;
use App\Services\ResepAkgRealisasiService;
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

class TbResepController extends Controller
{
    protected ResepAkgRealisasiService $realisasiAkgService;

    public function __construct()
    {
        $this->realisasiAkgService = new ResepAkgRealisasiService();
    }

    public function index()
    {
        $header = "Dashboard Resep";
        if (request()->ajax()) {
            //$users = User::query();
            $resep = Resep::join('tb_komponen_sehat','tb_resep.id_komponen_sehat','tb_komponen_sehat.id')
            ->select(['tb_resep.*', 'tb_komponen_sehat.komponen as nama_komponen']);


            return DataTables::of($resep)
                ->addIndexColumn() // Menambah index
                ->editColumn('nama_komponen', function ($row) {
                    return strtolower($row->nama_komponen) === 'susu' ? 'pendamping' : $row->nama_komponen;
                })

                ->addColumn('action', function ($row) {
                // $btn = '<a href="javascript:void(0)" class="edit btn btn-primary btn-sm " id="btn-edit-post" data-id="' . $row['id'] . '">Edit</a>';
                /* $btn = '<a href="'. route('master_bahan.edit', $row['id']) .'" class="edit btn btn-primary btn-sm " id="btn-edit-post" data-id="' . $row['id'] . '">Edit</a>
                            <a href="' . route('master_bahan.delete', $row['id']) . '" class="edit btn btn-danger btn-sm delete-button" id="btn-delete-post " data-id="' . $row['id'] . '" >Delete</a>';
                    */
                $btn = '<a href="' . route('detailresep.index', $row['id']) . '" class="edit btn btn-success btn-sm " id="btn-edit-post" data-id="' . $row['id'] . '">detail</a>
                            <a href="' . route('resep.tahap-masak.index', $row['id']) . '" class="edit btn btn-secondary btn-sm" data-id="' . $row['id'] . '">Tahap Masak</a>
                            <a href="' . route('resep.edit', $row['id']) . '" class="edit btn btn-primary btn-sm " id="btn-edit-post" data-id="' . $row['id'] . '">Edit</a>
                             <a  href="' . route('resep.delete', $row->id) . '" 
                            class="edit btn btn-danger btn-sm delete-button" 
                            onclick="return confirm(\'Apakah Anda yakin ingin menghapus data ini?\')">Delete</a>';
                /* $btn = '<a href="' . route('detailresep.index', $row['id']) . '" class="edit btn btn-success btn-sm " id="btn-edit-post" data-id="' . $row['id'] . '">detail</a>
                            <a href="' . route('resep.edit', $row['id']) . '" class="edit btn btn-primary btn-sm " id="btn-edit-post" data-id="' . $row['id'] . '">Edit</a>
                            <a href="javascript:void(0);" 
                            class="btn btn-danger btn-sm delete-button" 
                            data-id="'. $row->id .'"
                            data-toggle="modal" 
                            data-target="#deleteModal">
                            Delete
                            </a>
                ';*/
                return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('office/resep.index',compact('header'));
    }

    public function edit(string $id): View
    {
        $header = "Edit Resep";
        //get product by ID
        $resep = Resep::findOrFail($id);
        $KomponenSehat = KomponenSehat::all();
        //render view with product
        return view('office/resep.edit', compact( 'resep','header', 'KomponenSehat'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        //validate form
        $request->validate([
            'nama_resep'         => 'required|min:1',


        ]);

        //get product by ID
        $resep = Resep::findOrFail($id);



        //update product without image
        $resep->update([
            'nama_resep'                => $request->nama_resep,
            'jenis_resep'               => $request->jenis_resep ?? 'tidak diolah',
            'id_komponen_sehat'         => $request->id_komponen_sehat,
            'porsi_resep'               => $request->porsi_resep,
            'margin'                    => $request->margin
        ]);


        //redirect to index
        return redirect()->route('resep.index')->with(['success' => 'Data Berhasil Diubah!']);
    }

    public function create(): View
    {
        $header = "Tambah Resep";
        $KomponenSehat = KomponenSehat::all();
        return view('office/resep.create', compact('KomponenSehat', 'header'));
    }

    public function store(Request $request): RedirectResponse
    {
        //validate form
        $request->validate([
            'nama_resep'         => 'required|min:1',
        ]);


        //create product
        Resep::create([
            'nama_resep'                => $request->nama_resep,
            'jenis_resep'               => $request->jenis_resep ?? 'tidak diolah',
            'id_komponen_sehat'         => $request->id_komponen_sehat,
            'porsi_resep'               => $request->porsi_resep,
            'margin'                    => $request->margin

        ]);

        //redirect to index
        return redirect()->route('resep.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }

    public function destroy($id)
    {
        //get product by ID
        $resep = Resep::findOrFail($id);
        MenuBahan::where('menu_id', $id)->delete();

        //delete product
        $resep->delete();

        //redirect to index
        return redirect()->route('resep.index')->with(['success' => 'Data Berhasil Dihapus!']);
    }

    public function detailresep($id)
    {
        // Ambil data resep berdasarkan ID
        $resep = Resep::find($id);

        // Buat judul header dinamis
        $header = "Detail Resep " . $resep->nama_resep;

        // Simpan menu_id untuk digunakan di view
        $menu_id = $id;
        $basisPerhitunganBumbu = $this->buildBumbuBasisText($id);

        // Jika request dilakukan secara AJAX (biasanya dari DataTables)
        if (request()->ajax()) {
            // Ambil data bahan dari tabel tb_menu_bahan, join dengan master_bahan dan satuan
            $menu = MenuBahan::join('tb_master_bahan', 'tb_menu_bahan.bahan_id', 'tb_master_bahan.id')
                ->join('tb_satuan', 'tb_menu_bahan.id_satuan', 'tb_satuan.id')
                ->where('menu_id', $id)
                ->select(
                    'tb_menu_bahan.*',
                    'tb_master_bahan.bahan as nama_bahan',
                    'tb_satuan.satuan as nama_satuan_bahan',
                    'tb_menu_bahan.status_bahan_baku'
                )->get();

            // Kembalikan data sebagai response untuk DataTables
            return DataTables::of($menu)
                ->addIndexColumn() // Menambahkan kolom index otomatis
                ->addColumn('status_bahan', function ($row) {
                    // Buat tombol Edit dan Delete untuk setiap baris
                    if($row->status_bahan_baku == 1 )
                    {
                        return 'Ke 1';
                    }else if($row->status_bahan_baku == 2 )
                    {
                        return 'Ke 2';
                    }else if($row->status_bahan_baku == 4 )
                    {
                        return 'Ke 3';
                    }else if($row->status_bahan_baku == 5 )
                    {
                        return 'Ke 4';
                    }else{
                        return 'Bumbu';
                    }
                })
                ->addColumn('action', function ($row) use ($id) {
                    // Buat tombol Edit dan Delete untuk setiap baris
                    if($row->status_bahan_baku == 3 )
                    {
                        $gramasi_a = 0;
                        $gramasi_b = 0;
                    $btn = '<a href="' . route('detailresep.edit_detail_resep', $row['id']) . '" 
                                class="edit btn btn-primary btn-sm" 
                                id="btn-edit-post" 
                                data-id="' . $row['id'] . '">Edit</a>

                            <a href="' . route('prosesdeletebahan', $row['id']) . '" 
                                class="delete btn btn-danger btn-sm" 
                                id="btn-delete-post" 
                                data-id="' . $row['id'] . '">Delete</a>
                            
                                
                                ';
                    }else{
                        $data = GramasiResep::where('id_resep', $id)->where('status_bahan', $row->status_bahan_baku)->first();
                        if($data)
                        {
                            $gramasi_a = $data->gramasi_a;
                            $gramasi_b = $data->gramasi_b;          
                        }else{
                            $gramasi_a = 0;
                            $gramasi_b = 0;
                        }
                        $btn = '<a href="' . route('detailresep.edit_detail_resep', $row['id']) . '" 
                                class="edit btn btn-primary btn-sm" 
                                id="btn-edit-post" 
                                data-id="' . $row['id'] . '">Edit</a>

                            <a href="' . route('prosesdeletebahan', $row['id']) . '" 
                                class="delete btn btn-danger btn-sm" 
                                id="btn-delete-post" 
                                data-id="' . $row['id'] . '">Delete</a>
                            <button class="btn btn-sm btn-warning btn-gramasi"
                                data-idresep="' . $id . '"
                                data-gramasia="' . $gramasi_a . '"
                                data-gramasib="' . $gramasi_b . '"
                                data-statusbahanbaku="' . $row->status_bahan_baku  . '">
                                Gramasi
                            </button>    
                                
                                ';
                    }
                    


                    
                    return $btn;
                })
                ->addColumn('perhitungan_bahan', function ($row) use ($basisPerhitunganBumbu) {
                    // Buat tombol Edit dan Delete untuk setiap baris
                    $perihitungan_bahan = PerhitunganBumbu::where('id_menu_bahan',$row->id)->first();
                    if ((int) $row->status_bahan_baku === 3) {
                        return $this->buildBumbuPerhitunganText($row, $basisPerhitunganBumbu, $perihitungan_bahan);
                    }

                    return '-';
                    
                })
                ->addColumn('gramasi', function ($row) use ($id) {
                    // Buat tombol Edit dan Delete untuk setiap baris
                    if($row->status_bahan_baku == 3)
                    {
                        $btn = '-';
                    }else{
                        $data = GramasiResep::where('id_resep', $id)->where('status_bahan', $row->status_bahan_baku)->first();
                        if ($data) {
                            $btn = 'A: ' . $data->gramasi_a . ' | B: ' . $data->gramasi_b;
                        } else {
                            $btn = 'A: 0 | B: 0';
                        }
                    }
                    
                    return $btn ?? '-';
                })
                ->rawColumns(['action', 'status_bahan', 'perhitungan_bahan', 'gramasi']) // Menandai kolom action berisi HTML mentah
                ->make(true);
        }

        // Jika bukan request AJAX, tampilkan view detail resep
        return view('office/resep.detailresep', compact('menu_id', 'header'));
    }

    public function realisasiAkg($id)
    {
        $resep = Resep::findOrFail($id);
        $header = 'Realisasi AKG Resep ' . $resep->nama_resep;

        $realisasiList = ResepRealisasiAkg::with('masterBahanNutrisi')
            ->where('id_resep', $id)
            ->orderBy('id', 'asc')
            ->get();

        $totals = [
            'energi_kcal' => (float) $realisasiList->sum('energi_kcal'),
            'protein_g' => (float) $realisasiList->sum('protein_g'),
            'lemak_g' => (float) $realisasiList->sum('lemak_g'),
            'karbohidrat_g' => (float) $realisasiList->sum('karbohidrat_g'),
            'serat_g' => (float) $realisasiList->sum('serat_g'),
            'natrium_mg' => (float) $realisasiList->sum('natrium_mg'),
        ];

        $masterBahanList = MasterBahanNutrisi::query()
            ->orderBy('nama_bahan')
            ->get(['id', 'kode', 'nama_bahan', 'bdd', 'energi', 'protein', 'lemak', 'karbohidrat', 'serat', 'natrium']);

        return view('office.resep.realisasi_akg', compact('resep', 'header', 'realisasiList', 'totals', 'masterBahanList'));
    }

    public function storeRealisasiAkg(Request $request, $id)
    {
        $request->validate([
            'id_master_bahan_nutrisi' => 'required|integer',
            'jumlah_gram' => 'required|numeric|min:0.01',
            'bdd_pct' => 'nullable|numeric|min:0|max:100',
        ]);

        $masterBahan = MasterBahanNutrisi::findOrFail($request->id_master_bahan_nutrisi);
        $jumlahGram = (float) $request->input('jumlah_gram', 100);
        $bddPct = $request->filled('bdd_pct')
            ? (float) $request->bdd_pct
            : (float) ($masterBahan->bdd ?? 0);

        $this->realisasiAkgService->storeFromMasterBahan(
            (int) $id,
            $masterBahan,
            $jumlahGram,
            $bddPct
        );

        return redirect()
            ->route('resep.realisasi-akg.index', $id)
            ->with('success', 'Realisasi AKG berhasil disimpan.');
    }

    public function destroyRealisasiAkg($id, $realisasiId)
    {
        ResepRealisasiAkg::where('id', $realisasiId)
            ->where('id_resep', $id)
            ->delete();

        return redirect()
            ->route('resep.realisasi-akg.index', $id)
            ->with('success', 'Realisasi AKG berhasil dihapus.');
    }

    protected function buildBumbuBasisText($menuId): string
    {
        $bahanUtama = MenuBahan::join('tb_satuan', 'tb_menu_bahan.id_satuan', '=', 'tb_satuan.id')
            ->where('tb_menu_bahan.menu_id', $menuId)
            ->whereIn('tb_menu_bahan.status_bahan_baku', [1, 2, 4, 5])
            ->select(
                'tb_menu_bahan.jumlah',
                'tb_satuan.satuan as nama_satuan_bahan',
                'tb_menu_bahan.status_bahan_baku'
            )
            ->orderByRaw('CASE tb_menu_bahan.status_bahan_baku WHEN 1 THEN 1 WHEN 2 THEN 2 WHEN 4 THEN 3 WHEN 5 THEN 4 ELSE 5 END')
            ->orderBy('tb_menu_bahan.id')
            ->get();

        if ($bahanUtama->isEmpty()) {
            return '';
        }

        return $bahanUtama
            ->map(function ($item) {
                return $this->formatPerhitunganAngka($item->jumlah) . ' ' . $item->nama_satuan_bahan;
            })
            ->implode(' + ');
    }

    protected function buildBumbuPerhitunganText($row, string $basisPerhitunganBumbu, $perhitunganBumbu = null): string
    {
        if (!$perhitunganBumbu) {
            return '-';
        }

        $pembagi = (float) ($perhitunganBumbu->pembagi ?? 0);
        $pengali = (float) ($perhitunganBumbu->pengali ?? 0);

        if ($basisPerhitunganBumbu === '' || ($pembagi <= 1 && $pengali <= 1)) {
            return '-';
        }

        return 'setiap ' . $basisPerhitunganBumbu . ' menggunakan '
            . $this->formatPerhitunganAngka($row->jumlah) . ' ' . $row->nama_satuan_bahan;
    }

    protected function formatPerhitunganAngka($nilai): string
    {
        $angka = (float) $nilai;

        if (floor($angka) == $angka) {
            return number_format($angka, 0, ',', '.');
        }

        return rtrim(rtrim(number_format($angka, 2, ',', '.'), '0'), ',');
    }

    protected function defaultPerhitunganKeterangan($menuId, $statusBahanBaku, $jumlah, $satuan, $pembagi, $pengali): string
    {
        if ((int) $statusBahanBaku === 3) {
            $basisPerhitunganBumbu = $this->buildBumbuBasisText($menuId);

            if ($basisPerhitunganBumbu !== '') {
                return 'setiap ' . $basisPerhitunganBumbu . ' menggunakan '
                    . $this->formatPerhitunganAngka($jumlah) . ' ' . $satuan;
            }
        }

        return 'setiap ' . $this->formatPerhitunganAngka($pembagi) . ' ' . $satuan
            . ' dibagi menjadi ' . $this->formatPerhitunganAngka($pengali) . ' bagian';
    }


    public function tambahbahan(string $id)
    {
        // 🏷 Judul halaman
        $header = "Tambah Bahan Resep";

        // 🔢 Simpan ID menu yang akan ditambahkan bahannya
        $menu_id = $id;

        // 📦 Ambil semua data master bahan dan satuan, lalu join untuk mendapatkan nama satuan
        $masterbahan = TbMasterBahan::join('tb_satuan', 'tb_master_bahan.satuan_bahan', '=', 'tb_satuan.id')
            ->select('tb_master_bahan.*', 'tb_satuan.satuan as nama_satuan')
            ->get();

        $defaultPembagi = MenuBahan::where('menu_id', $menu_id)
            ->whereIn('status_bahan_baku', [1, 2, 4, 5])
            ->sum('jumlah');
        $defaultPengali = 1;

        // 📄 Kirim data ke view `office/resep.tambahdetailresep`
        return view('office/resep.tambahdetailresep', compact('masterbahan', 'menu_id', 'header', 'defaultPembagi', 'defaultPengali'));
    }



    public function storetambahbahan(Request $request): RedirectResponse
    {
        // ✅ Validasi input form
        $request->validate([
            'bahan_id'      => 'required|min:1',
            'jumlah'        => 'required|min:1',
            'pembagi'       => 'required',
            'pengali'       => 'required',
            //'keterangan'    => 'required',
        ]);

        // 🔍 Ambil data master bahan untuk mendapatkan satuan
        $masterbahan = TbMasterBahan::where('id', $request->bahan_id)->first();
        $satuan = TbSatuan::findOrFail($masterbahan->satuan_bahan);

        // 🟢 Simpan data ke tabel MenuBahan
        $menuBahan = MenuBahan::create([
            'menu_id'           => $request->menu_id,
            'bahan_id'          => $request->bahan_id,
            'jumlah'            => $request->jumlah,
            'id_satuan'         => $masterbahan->satuan_bahan,
            'status_bahan_baku' => $request->status_bahan_baku,
        ]);

        // 🧂 Simpan data perhitungan bumbu terkait menu_bahan yang baru dibuat
        PerhitunganBumbu::create([
            'id_menu_bahan' => $menuBahan->id,
            'pembagi'       => $request->pembagi,
            'pengali'       => $request->pengali,
            'keterangan'    => $request->keterangan
                ?: $this->defaultPerhitunganKeterangan(
                    $request->menu_id,
                    $request->status_bahan_baku,
                    $request->jumlah,
                    $satuan->satuan,
                    $request->pembagi,
                    $request->pengali
                ),
        ]);

        // ✅ Redirect kembali ke halaman detail resep dengan pesan sukses
        return redirect()
            ->route('detailresep.index', $request->menu_id)
            ->with(['success' => 'Data Berhasil Disimpan!']);
    }


    public function editbahan(string $id): View
    {
        // 🏷 Judul halaman
        $header = "Edit Bahan Resep";

        // 🔍 Ambil data MenuBahan berdasarkan ID
        $menubahan = MenuBahan::findOrFail($id);

        // 🔍 Ambil data perhitungan bumbu berdasarkan id_menu_bahan
        $perihitungan_bahan = PerhitunganBumbu::where('id_menu_bahan', $id)->first();
        $defaultPembagi = $perihitungan_bahan->pembagi ?? 1;
        $defaultPengali = $perihitungan_bahan->pengali ?? 1;

        if ((int) $menubahan->status_bahan_baku === 3) {
            $defaultPembagi = MenuBahan::where('menu_id', $menubahan->menu_id)
                ->whereIn('status_bahan_baku', [1, 2, 4, 5])
                ->sum('jumlah');
            $defaultPengali = $menubahan->jumlah;
        }

        // 📦 Ambil seluruh data master bahan, join dengan satuan
        $masterbahan = TbMasterBahan::join('tb_satuan', 'tb_master_bahan.satuan_bahan', '=', 'tb_satuan.id')
            ->select('tb_master_bahan.*', 'tb_satuan.satuan as nama_satuan')
            ->get();

        // 📄 Tampilkan ke view `office/resep.editdetailresep`
        return view('office/resep.editdetailresep', compact('menubahan', 'masterbahan', 'header', 'perihitungan_bahan', 'defaultPembagi', 'defaultPengali'));
    }


    public function edittambahbahan(Request $request): RedirectResponse
    {
        // ✅ Validasi input form
        $request->validate([
            'jumlah'       => 'required|min:1',
            'pembagi'      => 'required',
            'pengali'      => 'required',
            //'keterangan'   => 'required',
        ]);

        // 🔍 Ambil data master bahan untuk referensi (jika dibutuhkan)
        $masterbahan = TbMasterBahan::where('id', $request->bahan_id)->first();

        // 🔍 Ambil data MenuBahan berdasarkan ID yang dikirim dari form
        $MenuBahan = MenuBahan::findOrFail($request->id);
        $satuan = TbSatuan::findOrFail($MenuBahan->id_satuan);
        // 🔄 Update data jumlah & status bahan baku pada MenuBahan
        $MenuBahan->update([
            // 'bahan_id' => $request->bahan_id, // dikomentari karena tidak diubah
            'jumlah'               => $request->jumlah,
            'status_bahan_baku'    => $request->status_bahan_baku,
        ]);

        // 🔎 Cek apakah sudah ada entri PerhitunganBumbu untuk menu_bahan ini
        $cek_perhitungan = PerhitunganBumbu::where('id_menu_bahan', $MenuBahan->id)->first();

        if ($cek_perhitungan) {
            // 🔄 Jika ada, update data perhitungan
            $cek_perhitungan->update([
                'pembagi'     => $request->pembagi,
                'pengali'     => $request->pengali,
                'keterangan'    => $request->keterangan
                    ?: $this->defaultPerhitunganKeterangan(
                        $MenuBahan->menu_id,
                        $request->status_bahan_baku,
                        $request->jumlah,
                        $satuan->satuan,
                        $request->pembagi,
                        $request->pengali
                    ),

            ]);
        } else {
            // ➕ Jika belum ada, buat entri baru perhitungan bumbu
            PerhitunganBumbu::create([
                'id_menu_bahan' => $MenuBahan->id,
                'pembagi'       => $request->pembagi,
                'pengali'       => $request->pengali,
                'keterangan'    => $request->keterangan
                    ?: $this->defaultPerhitunganKeterangan(
                        $MenuBahan->menu_id,
                        $request->status_bahan_baku,
                        $request->jumlah,
                        $satuan->satuan,
                        $request->pembagi,
                        $request->pengali
                    ),
            ]);
        }

        // ✅ Redirect ke halaman detail resep setelah penyimpanan
        return redirect()->route('detailresep.index', $MenuBahan->menu_id)->with(['success' => 'Data Berhasil Disimpan!']);
    }


    public function deletedetailbahan($id)
    {
        // 🔍 Ambil data MenuBahan berdasarkan ID
        $MenuBahan = MenuBahan::findOrFail($id);

        // 📝 Simpan dulu ID menu untuk keperluan redirect nanti
        $menuId = $MenuBahan->menu_id;

        // ❌ Hapus data perhitungan bumbu yang terkait dengan MenuBahan ini
        DB::table('tb_perhitungan_bumbu')
            ->where('id_menu_bahan', $MenuBahan->id)
            ->delete();

        // ❌ Hapus data MenuBahan itu sendiri
        $MenuBahan->delete();

        // ✅ Redirect kembali ke halaman detail resep dengan pesan sukses
        return redirect()
            ->route('detailresep.index', $menuId)
            ->with(['success' => 'Data Berhasil Disimpan!']);
    }

    public function pdf_laporan_master_resep()
    {

        $dapur = DataDapur::first();
        $satuan = TbSatuan::all();
        $jumlah_karbohidrat = Resep::where('id_komponen_sehat',1)->count();
        
        
        $jumlah_protein = Resep::where('id_komponen_sehat', 2)->count();
        $jumlah_sayur = Resep::where('id_komponen_sehat', 3)->count();
        $jumlah_buah = Resep::where('id_komponen_sehat', 4)->count();
        $jumlah_tambahan = Resep::where('id_komponen_sehat', 5)->count();

        $karbohidrat =  DB::table('tb_menu_bahan as mb')
            ->join('tb_resep as resep', 'mb.menu_id', '=', 'resep.id')
            ->join('tb_master_bahan as bahan', 'mb.bahan_id', '=', 'bahan.id')
            ->join('tb_satuan as satuan', 'bahan.satuan_bahan', '=', 'satuan.id')
            ->select(
                'resep.nama_resep',
                DB::raw("GROUP_CONCAT(CONCAT(bahan.bahan, ' ', satuan.satuan) SEPARATOR ', ') AS bahan_dan_satuan"),
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
                DB::raw("GROUP_CONCAT(CONCAT(bahan.bahan, ' ', satuan.satuan) SEPARATOR ', ') AS bahan_dan_satuan"),
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
                DB::raw("GROUP_CONCAT(CONCAT(bahan.bahan, ' ', satuan.satuan) SEPARATOR ', ') AS bahan_dan_satuan"),
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
                DB::raw("GROUP_CONCAT(CONCAT(bahan.bahan, ' ', satuan.satuan) SEPARATOR ', ') AS bahan_dan_satuan"),
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
                DB::raw("GROUP_CONCAT(CONCAT(bahan.bahan, ' ', satuan.satuan) SEPARATOR ', ') AS bahan_dan_satuan"),
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
        $pdf = Pdf::loadView('office/resep.template_laporan_master_resep', compact(
            'data_resep',
            'dapur',
            'satuan',
            'jumlah_karbohidrat',
            'jumlah_protein',
            'jumlah_sayur',
            'jumlah_buah',
            'jumlah_tambahan',
            'karbohidrat',
            'protein',
            'sayur',
            'buah',
            'tambahan',



        ));
    return $pdf->download($this->buildMasterResepExportFilename($dapur, 'pdf'));
    }

    public function Lap_data_resep_export(Request $request)
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
        $jumlah_karbohidrat = Resep::where('id_komponen_sehat', 1)->count();


        $jumlah_protein = Resep::where('id_komponen_sehat', 2)->count();
        $jumlah_sayur = Resep::where('id_komponen_sehat', 3)->count();
        $jumlah_buah = Resep::where('id_komponen_sehat', 4)->count();
        $jumlah_tambahan = Resep::where('id_komponen_sehat', 5)->count();

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





        
        return Excel::download(
            new LaporanDataMasterResepExport(
                $data_resep,
                $dapur,
                $satuan,
                $jumlah_karbohidrat,
                $jumlah_protein,
                $jumlah_sayur,
                $jumlah_buah,
                $jumlah_tambahan,
                $karbohidrat,
                $protein,
                $sayur,
                $buah,
                $tambahan, $start, $end),
            $this->buildMasterResepExportFilename($dapur, 'xlsx')
        );
    }

    private function buildMasterResepExportFilename($dapur, string $extension): string
    {
        $tanggal = Carbon::today()->format('d-m-Y');
        $namaSppg = trim((string) ($dapur->nama_dapur ?? 'Tanpa Nama'));
        $kotaKab = trim((string) ($dapur->kota ?? 'Tanpa Kota'));

        $base = 'master resep ' . $tanggal . ' SPPG ' . $namaSppg . ' Kota/kab ' . $kotaKab;
        $safeBase = preg_replace('/[\\\\\/:*?"<>|]+/', ' ', $base);
        $safeBase = preg_replace('/\s+/', ' ', (string) $safeBase);
        $safeBase = trim((string) $safeBase);

        return $safeBase . '.' . $extension;
    }
}
