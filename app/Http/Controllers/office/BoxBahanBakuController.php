<?php

namespace App\Http\Controllers\office;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

use Illuminate\Http\Request;
use App\Models\BoxBahanBaku;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\RedirectResponse;

use App\Models\SpesifikasiBahan;
use App\Models\TbMasterBahan;
use App\Models\TbSatuan;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\BoxBahanBakuRuleService;

class BoxBahanBakuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Judul header
        $header = "Data Box";

        // Ambil daftar bahan beserta nama satuan
        $bahanlist = DB::table('tb_master_bahan')
            ->join('tb_satuan', 'tb_master_bahan.satuan_bahan', '=', 'tb_satuan.id')
            ->select(
                'tb_master_bahan.id',
                'tb_master_bahan.bahan',
                'tb_satuan.satuan'
            )
            ->get();

        // Jika request AJAX (misal dari DataTables)
        if (request()->ajax()) {

            // Ambil semua data Box
            $box = BoxBahanBaku::all();

            return DataTables::of($box)
                ->addIndexColumn() // Tambahkan nomor urut otomatis
                ->addColumn('action', function ($row) {
                    // Tombol aksi Edit dan Hapus
                    $action = '
                <button class="btn btn-sm btn-warning btn-edit-box"
                    data-id="' . $row->id . '"
                    data-isi="' . $row->isi_per_box . '"
                    data-penyusutan="' . $row->penyusutan . '">
                    Edit
                </button>
                <button class="btn btn-sm btn-danger btn-delete-box"
                    data-id="' . $row->id . '">
                    Hapus
                </button>
            ';
                    return $action;
                })
                ->addColumn('isi_box', function ($row) {
                    // Tampilkan isi per box + satuan
                    $data = TbMasterBahan::find($row->id_bahan);
                    if ($data) {
                        $satuan = TbSatuan::find($data->satuan_bahan);
                        return number_format($row->isi_per_box, 0, ',', '.') . ' | ' . ($satuan ? $satuan->satuan : '-');
                    } else {
                        return '-';
                    }
                })
                ->addColumn('isi_hasil_matang', function ($row) {
                    // Tampilkan isi per box + satuan
                    $data = TbMasterBahan::find($row->id_bahan);
                    if ($data) {
                        $satuan = TbSatuan::find($data->satuan_bahan);
                        return number_format($row->hasil_matang, 0, ',', '.') . ' | ' . ($satuan ? $satuan->satuan : '-');
                    } else {
                        return '-';
                    }
                })
                ->addColumn('penyusutan_persen', function ($row) {
                    // Tampilkan isi per box + satuan
                    $data = TbMasterBahan::find($row->id_bahan);
                    if ($data) {
                        $satuan = TbSatuan::find($data->satuan_bahan);
                        return number_format($row->penyusutan, 0, ',', '.').' %';
                    } else {
                        return '-';
                    }
                })
                ->addColumn('bahan', function ($row) {
                    // Nama bahan
                    $data = TbMasterBahan::find($row->id_bahan);
                    return $data ? $data->bahan : '-';
                })
                ->addColumn('satuan', function ($row) {
                    // Satuan bahan
                    $data = TbMasterBahan::find($row->id_bahan);
                    if ($data) {
                        $satuan = TbSatuan::find($data->satuan_bahan);
                        return $satuan ? $satuan->satuan : '-';
                    } else {
                        return '-';
                    }
                })
                ->rawColumns(['action', 'bahan', 'isi_box']) // biar HTML tidak di-escape
                ->make(true);
        }

        // Jika bukan AJAX, tampilkan halaman blade
        return view('office/box_bahan_baku.index', compact('header', 'bahanlist'));
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
        // Validasi input dari form request
        $request->validate([
            'bahan_id'      => 'required|exists:tb_master_bahan,id', // Wajib, harus ada di tabel tb_master_bahan kolom id
        ]);

        $bahan = TbMasterBahan::findOrFail($request->bahan_id);
        $satuan = TbSatuan::find($bahan->satuan_bahan);

        $ruleService = new BoxBahanBakuRuleService();
        $ruleData = $ruleService->calculate(
            (string) $bahan->bahan,
            (int) $bahan->jenis,
            (string) ($satuan ? $satuan->satuan : '')
        );

        // Menyimpan data baru ke tabel tb_box_bahan_baku
        BoxBahanBaku::create([
            'id_bahan'      => $request->bahan_id,       // relasi bahan baku
            'isi_per_box'   => $ruleData['isi_per_box'],
            'hasil_matang'  => $ruleData['hasil_matang'],
            'penyusutan'    => $ruleData['penyusutan'],
            'status'        => 1,     // jumlah penyusutan
            
        ]);

        // Kembalikan response JSON sukses
        return response()->json([
            'success' => true,
            'message' => 'Data Box berhasil ditambahkan.'
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
        // Validasi input dari form request
        $request->validate([
            'isi_box'      => 'required|integer|min:1',      // wajib, integer >= 1
            //'hasil_matang' => 'required|integer|min:0',      // wajib, integer >= 0
            'penyusutan'   => 'required|integer|min:0',      // wajib, integer >= 0
        ]);

        // Ambil data box berdasarkan id, jika tidak ditemukan akan error 404
        $box = BoxBahanBaku::findOrFail($id);
        $jumlah_penyusutan = $request->isi_box * $request->penyusutan / 100;
        // Update kolom-kolom sesuai input
        $box->isi_per_box   = $request->isi_box;
        $box->hasil_matang  = $request->isi_box-$jumlah_penyusutan;
        $box->penyusutan    = $request->penyusutan;
        $box->status        = 1;

        // Simpan perubahan ke database
        $box->save();

        // Kembalikan response JSON sukses
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
    public function destroy($id)
    {
        $box = BoxBahanBaku::find($id);

        if (!$box) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan.'
            ], 404);
        }
        $box->status        = 0;

        //$box->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil dihapus.'
        ]);
    }
}
