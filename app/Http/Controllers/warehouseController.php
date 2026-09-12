<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Resep;
use App\Models\TbPenerimaan;
use App\Models\TransaksiWadah;
use App\Models\TbPo;
use Illuminate\Http\Request;
use App\Models\warehouseTransaksi;
use Yajra\DataTables\DataTables;
use App\Models\TbSatuan;
use App\Models\TbWadah;
use Illuminate\Support\Carbon;

use Mockery\Undefined;

use Illuminate\Support\Facades\DB;


class warehouseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function v_formTransaksiWarehouse(Request $request)
    {
        $header = "Form Transaksi Warehouse";
        $jenis_transaksi = $request->type;
        $list_po = TbPo::all();
        $satuan = TbSatuan::orderBy('satuan', 'asc')->get();
        $referensi = "WH".date('His').date('dmy').substr(microtime(FALSE), 2, 3);
        return view('warehouse.formTransaksiWarehouse', compact('header', 'referensi', 'list_po','jenis_transaksi', 'satuan'));

    }

    public function v_formTransaksiWarehouse_penerimaan(Request $request)
    {
        $header = "Form Transaksi Warehouse";
        $list_po = TbPo::all();
        $jenis_transaksi = $request->type;
        $referensi = "WH" . date('His') . date('dmy') . substr(microtime(FALSE), 2, 3);
        return view('penerimaan/transaksi-wadah.index_rev', compact('header', 'referensi', 'list_po','jenis_transaksi'));
    }

    public function dt_transaksiWarehouse(Request $request)
    {
        if($request->jenis_transaksi == 'in'){
            $table = warehouseTransaksi::query();
            if ($request->referensi) {
                $table->where('referensi_masuk', $request->referensi);
            } else {
                $table->whereDate('tanggal_masuk', now());
            }
            $table = $table->get();

        }
        else{
            $table = warehouseTransaksi::query();
            $table = $table->where('jenis', 1)
                ->where('status', 1)
                ->whereDate('tanggal_keluar', now())
                ->get();


        }
        //return $table;
        return DataTables::of($table)
            ->addIndexColumn()
            ->addColumn('tanggal_transaksi', function($row) use ($request){
                if($request->jenis_transaksi == 'in'){
                    return $row->tanggal_masuk;
                }
                else{
                    return $row->tanggal_keluar;
                }
            })
            ->addColumn('action', function ($row) use ($request) {
                if ($request->jenis_transaksi == 'out') {
                    return '<button onClick="ajax_kembalikanBarangKeluar('.$row->id.')" class="btn btn-xs btn-warning"><i class="fa fa-undo"></i> Kembalikan</button> '
                        . '<button onClick="ajax_koreksiJumlahKeluar('.$row->id.','.$row->jumlah.')" class="btn btn-xs btn-primary"><i class="fa fa-edit"></i> Koreksi Qty</button>';
                }
                return '<button onClick="ajax_hapusBarangTransakiWarehouse('.$row->id.')" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i> Hapus</button>';
            })
            ->addColumn('nama_satuan', function ($table) {
                $satuan = TbSatuan::where('id', $table->id_satuan)->first();
                return $satuan->satuan;
            })
            ->rawColumns(['action', 'tanggal_transaksi','nama_satuan'])
            ->make(true);
    }

    public function ajax_updateSatuanGudang(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:warehouse_transaksi,id',
            'id_satuan' => 'required|integer|exists:tb_satuan,id',
        ]);

        warehouseTransaksi::where('id', $request->id)
            ->update([
                'id_satuan' => $request->id_satuan,
                'updated_at' => now(),
            ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Satuan barang berhasil diperbarui',
        ]);
    }

    public function form_barangTransaksiMasuk(Request $request)
    {
        $cek = TbPenerimaan::where('qr_code_wadah', $request->kode_wadah)->where('status',1)->first();
        $satuan = TbSatuan::get();
        $list_po = DB::select("
            SELECT p.nomor_po
            FROM tb_po p
            WHERE p.status_po = 'acc'
              AND NOT EXISTS (
                SELECT 1
                FROM tb_po_bahan pb
                WHERE pb.id_po = p.id
                  AND pb.tanggal_digunakan < CURRENT_DATE - INTERVAL 2 DAY
              )
        ");
        if($cek)
        {
            $kode_wadah = $request->kode_wadah;
            $data = DB::table('tb_penerimaan as p')
                ->join('tb_po_bahan as pb', 'p.id_barang_po', '=', 'pb.id')
                ->join('rincian_menu_harian', 'pb.id_rincian_bahan', '=', 'rincian_menu_harian.id')
                ->join('tb_po as po', 'pb.id_po', '=', 'po.id')
                ->join('tb_master_bahan as mb', 'pb.id_bahan', '=', 'mb.id')
                ->join('tb_satuan as s', 'pb.satuan', '=', 's.id')
                ->leftJoin('warehouse_transaksi as wt', 'wt.id_penerimaan', '=', 'p.id')
                ->select(
                    'po.nomor_po',
                    'p.jumlah_datang',
                    'mb.bahan',
                    'mb.jenis as bahan_jenis',
                    'p.id as id_penerimaan',
                    's.satuan',
                    'pb.tanggal_digunakan',
                    'pb.satuan as satuan_bahan'
                )
                ->where('p.qr_code_wadah', $kode_wadah)
                ->whereNull('wt.id_penerimaan')
                ->first();
            $ceklist = DB::table('tb_penerimaan as p')
                ->join('tb_po_bahan as pb', 'p.id_barang_po', '=', 'pb.id')
                ->join('rincian_menu_harian', 'pb.id_rincian_bahan', '=', 'rincian_menu_harian.id')
                ->join('tb_po as po', 'pb.id_po', '=', 'po.id')
                ->join('tb_master_bahan as mb', 'pb.id_bahan', '=', 'mb.id')
                ->join('tb_satuan as s', 'pb.satuan', '=', 's.id')
                ->leftJoin('warehouse_transaksi as wt', 'wt.id_penerimaan', '=', 'p.id')
                ->select(
                    'po.nomor_po',
                    'p.jumlah_datang',
                    'mb.bahan',
                    'p.id as id_penerimaan',
                    's.satuan',
                    'pb.tanggal_digunakan',
                    'pb.satuan as satuan_bahan'
                )
                ->where('p.qr_code_wadah', $kode_wadah)
                ->whereNull('wt.id_penerimaan')
                ->count();
                if($ceklist == 0)
                {
                 $data = DB::table('tb_penerimaan as p')
                    ->join('tb_po_bahan as pb', 'p.id_barang_po', '=', 'pb.id')
                    ->join('tb_po as po', 'pb.id_po', '=', 'po.id')
                    ->join('tb_master_bahan as mb', 'pb.id_bahan', '=', 'mb.id')
                    ->join('tb_satuan as s', 'pb.satuan', '=', 's.id')
                    ->leftJoin('warehouse_transaksi as wt', 'wt.id_penerimaan', '=', 'p.id')
                    ->select(
                        'po.nomor_po',
                        'p.jumlah_datang',
                        'mb.bahan',
                        'mb.jenis as bahan_jenis',
                        'p.id as id_penerimaan',
                        's.satuan',
                        'pb.tanggal_digunakan',
                        'pb.satuan  as satuan_bahan'
                    )
                    ->where('p.qr_code_wadah', $kode_wadah)
                    ->whereNull('wt.id_penerimaan')
                    ->first();
                }

            // Ambil satuan terakhir yang dipakai untuk bahan yang sama di warehouse_transaksi
            // agar mengikuti hasil edit satuan di /laporan_penerimaan
            $satuan_dari_laporan = null;
            if ($data) {
                $satuan_dari_laporan = DB::table('warehouse_transaksi')
                    ->where('nama_barang', $data->bahan)
                    ->whereNotNull('id_satuan')
                    ->orderBy('tanggal_masuk', 'desc')
                    ->value('id_satuan');
            }

            return view('warehouse.form.formBarangTransaksiMasuk_rev', compact('kode_wadah', 'list_po', 'satuan', 'data', 'satuan_dari_laporan'));
        }
        if($request->kode_wadah != 'undefined'){
            $kode_wadah = $request->kode_wadah;
            return view('warehouse.form.formBarangTransaksiMasuk', compact('kode_wadah', 'list_po', 'satuan'));
        }
        
        $kode_wadah = 0;
        return view('warehouse.form.formBarangTransaksiMasuk', compact('satuan', 'list_po', 'kode_wadah'));
    }

    public function ajax_simpanBarangTransaksiMasuk(Request $request)
    {
        // Validasi dinamis berdasarkan no_po
        $rule = [
            'nama_barang' => $request->no_po ? 'nullable' : 'required',
            'nama_barang_po' => $request->no_po ? 'required' : 'nullable',
            'jumlah' => 'required',
            'id_satuan' => $request->no_po ? 'nullable' : 'nullable',
           // 'id_satuan_po' => $request->no_po ? 'required' : 'nullable',
            'lokasi' => 'required',
            'jenis' => 'required',
        ];

        $message = [
            'nama_barang.required' => 'Nama barang tidak boleh kosong',
            'nama_barang_po.required' => 'Nama barang (PO) tidak boleh kosong',
            'jumlah.required' => 'Jumlah tidak boleh kosong',
            'id_satuan.required' => 'Satuan tidak boleh kosong',
           // 'id_satuan_po.required' => 'Satuan (PO) tidak boleh kosong',
            'lokasi.required' => 'Lokasi/tempat penyimpanan tidak boleh kosong',
            'jenis.required' => 'Jenis barang tidak boleh kosong',
        ];

        $this->validate($request, $rule, $message);
        
        // Menentukan nama_barang dan id_satuan berdasarkan no_po
        if ($request->no_po) {
            $penerimaanBahan = TbPenerimaan::join('tb_po_bahan', 'tb_penerimaan.id_barang_po', 'tb_po_bahan.id')
                ->join('tb_master_bahan', 'tb_po_bahan.id_bahan', 'tb_master_bahan.id')
                ->where('tb_penerimaan.id', $request->nama_barang_po)
                ->select('tb_master_bahan.bahan')
                ->first();

            if (!$penerimaanBahan) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Data penerimaan tidak ditemukan. Pastikan item PO sudah diterima.',
                ], 422);
            }

            $nama_barang = $penerimaanBahan->bahan;
        } else {
            $nama_barang = $request->nama_barang;
        }
        $id_satuan = $request->no_po ? $request->id_satuan : $request->id_satuan;
        $id_penerimaan = $request->nama_barang_po ?? null;

        $loop = (int) $request->loop_kontainer;



        for ($i = 0; $i < $loop; $i++) {
            // Simpan ke database
            $warehouse_transaksi = warehouseTransaksi::create([
                'id_penerimaan'         => $id_penerimaan,
                'kode_wadah'            => $request->kode_wadah ?? '',
                'no_po'                 => $request->no_po ?? '',
                'nama_barang'           => $nama_barang,
                'tanggal_masuk'         => now(),
                'status'                => 0,
                'jumlah'                => $request->jumlah,
                'id_satuan'             => $id_satuan,
                'tanggal_akan_keluar'   => Carbon::createFromFormat('d/m/Y', $request->tanggal_akan_keluar)->format('Y-m-d H:i:s'),
                'referensi_masuk'       => $request->referensi_masuk,
                'lokasi'                => $request->lokasi,
                'jenis'                 => $request->jenis,
                'keterangan'           => $request->keterangan,
            ]);
            if($id_penerimaan !== null)
            {
                DB::table('tb_penerimaan')
                    ->where('id', $request->nama_barang_po)
                    ->update(['status' => 3]);
            }
           
            // Update table transaksi wadah
            if (!empty($id_penerimaan)) {
                $penerimaan = TbPenerimaan::find($id_penerimaan);

                if ($penerimaan) {
                    TransaksiWadah::where('id_penerimaan', $id_penerimaan)
                        ->update([
                            'jumlah_berat_sesudah'      => $request->jumlah,
                            'tanggal_dan_waktu_masuk'   => $penerimaan->created_at,
                            'tanggal_dan_waktu_sesudah' => Carbon::now('Asia/Jakarta'), // WIB Menggunakan timestamp saat ini
                            'lokasi'                    => $request->lokasi,
                            'status'                    => 0
                        ]);
                }
            }
        }
        





        return response()->json([
            'status' => 'success',
            'message' => 'data barang berhasil disimpan'
        ]);

    }

    public function ajax_simpanBarangTransaksiKeluar(Request $request)
    {
        $rule = [
            'referensi_keluar' => 'required',
            'kode_wadah' => 'nullable',
            'id_warehouse' => 'nullable|integer',
            
        ];
        $message = [
            'referensi_keluar.required' => 'form tidak valid. silakan muat ulang halaman',
        ];
        $this->validate($request, $rule, $message);

        if (!$request->id_warehouse && !$request->kode_wadah) {
            return response()->json([
                'status' => 'danger',
                'message' => 'id barang atau kode wadah wajib diisi'
            ], 422);
        }

        if ($request->id_warehouse) {
            $updated = WarehouseTransaksi::where('id', $request->id_warehouse)
                ->where('status', '!=', 1)
                ->update([
                    'referensi_keluar' => $request->referensi_keluar,
                    'tanggal_keluar' => now(),
                    'status' => 1
                ]);
        } else {
            $updated = WarehouseTransaksi::where('kode_wadah', $request->kode_wadah)
                ->where('status', '!=', 1)
                ->update([
                    'referensi_keluar' => $request->referensi_keluar,
                    'tanggal_keluar' => now(),
                    'status' => 1
                ]);
        }
        
        // Jika tidak ada data yang diperbarui, tampilkan error 404
        if ($updated == 0) {
            return response()->json([
                'status' => 'danger',
                'message' => $request->id_warehouse
                    ? 'data barang tidak ditemukan/sudah dikeluarkan'
                    : 'data dengan kode tag/tempat penyimpanan '.$request->kode_wadah.' tidak ditemukan/sudah dikeluarkan'
            ]);
        }
        
        //tambahan waktu mulai masak
        //cek tanggal hari masak 
       // $menu = Menu::where()->first();
        return response()->json([
            'status' => 'success',
            'message' => 'data barang berhasil disimpan'
        ]);

    }

    public function ajax_simpanBarangTransaksiKeluarSemua(Request $request)
    {
        $rule = [
            'referensi_keluar' => 'required',
        ];
        $message = [
            'referensi_keluar.required' => 'form tidak valid. silakan muat ulang halaman',
        ];
        $this->validate($request, $rule, $message);

        $updated = WarehouseTransaksi::where('status', 0)
            ->where('jumlah', '>', 0)
            ->where('jenis', 1)
            ->update([
                'referensi_keluar' => $request->referensi_keluar,
                'tanggal_keluar' => now(),
                'status' => 1
            ]);

        if ($updated == 0) {
            return response()->json([
                'status' => 'danger',
                'message' => 'tidak ada data bahan baku aktif yang bisa dikeluarkan'
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'berhasil mengeluarkan ' . $updated . ' data bahan baku'
        ]);
    }

    public function ajax_kembalikanBarangTransaksiKeluar(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer'
        ], [
            'id.required' => 'id transaksi wajib diisi'
        ]);

        $data = warehouseTransaksi::find($request->id);
        if (!$data) {
            return response()->json([
                'status' => 'danger',
                'message' => 'data transaksi tidak ditemukan'
            ], 404);
        }

        if ((int) $data->status !== 1) {
            return response()->json([
                'status' => 'danger',
                'message' => 'data ini belum berstatus keluar'
            ], 400);
        }

        $stokAktifRows = warehouseTransaksi::where('status', 0)
            ->where('no_po', $data->no_po)
            ->where('nama_barang', $data->nama_barang)
            ->where('id_satuan', $data->id_satuan)
            ->where('jenis', $data->jenis)
            ->get();

        if ($stokAktifRows->count() > 0) {
            $barisUtama = $stokAktifRows->first();
            $totalAktifLain = $stokAktifRows->sum('jumlah');

            $barisUtama->update([
                'jumlah' => (float) $totalAktifLain + (float) $data->jumlah,
                'updated_at' => Carbon::now('Asia/Jakarta'),
            ]);

            $idsHapus = $stokAktifRows->pluck('id')->filter(fn($id) => $id != $barisUtama->id)->values();
            if ($idsHapus->count() > 0) {
                warehouseTransaksi::whereIn('id', $idsHapus)->delete();
            }

            $data->delete();
        } else {
            $data->update([
                'status' => 0,
                'tanggal_keluar' => null,
                'referensi_keluar' => null,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'data keluar berhasil dikembalikan ke gudang'
        ]);
    }

    public function ajax_koreksiJumlahKeluar(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
            'jumlah_keluar_baru' => 'required|numeric|min:1'
        ], [
            'id.required' => 'id transaksi wajib diisi',
            'jumlah_keluar_baru.required' => 'jumlah keluar baru wajib diisi'
        ]);

        $data = warehouseTransaksi::find($request->id);
        if (!$data) {
            return response()->json([
                'status' => 'danger',
                'message' => 'data transaksi tidak ditemukan'
            ], 404);
        }

        if ((int) $data->status !== 1) {
            return response()->json([
                'status' => 'danger',
                'message' => 'koreksi hanya bisa untuk data yang sudah keluar'
            ], 400);
        }

        $jumlahLama = (float) $data->jumlah;
        $jumlahBaru = (float) $request->jumlah_keluar_baru;

        if ($jumlahBaru > $jumlahLama) {
            return response()->json([
                'status' => 'danger',
                'message' => 'jumlah keluar baru tidak boleh lebih besar dari jumlah keluar lama'
            ], 400);
        }

        if ($jumlahBaru == $jumlahLama) {
            return response()->json([
                'status' => 'success',
                'message' => 'tidak ada perubahan jumlah keluar'
            ]);
        }

        $sisaKembali = $jumlahLama - $jumlahBaru;

        $data->update([
            'jumlah' => $jumlahBaru,
        ]);

        $stokAktifRows = warehouseTransaksi::where('status', 0)
            ->where('no_po', $data->no_po)
            ->where('nama_barang', $data->nama_barang)
            ->where('id_satuan', $data->id_satuan)
            ->where('jenis', $data->jenis)
            ->get();

        if ($stokAktifRows->count() > 0) {
            $barisUtama = $stokAktifRows->first();
            $totalAktifLain = $stokAktifRows->sum('jumlah');

            $barisUtama->update([
                'jumlah' => (float) $totalAktifLain + $sisaKembali,
                'updated_at' => Carbon::now('Asia/Jakarta'),
            ]);

            $idsHapus = $stokAktifRows->pluck('id')->filter(fn($id) => $id != $barisUtama->id)->values();
            if ($idsHapus->count() > 0) {
                warehouseTransaksi::whereIn('id', $idsHapus)->delete();
            }
        } else {
            warehouseTransaksi::create([
                'id_penerimaan' => $data->id_penerimaan,
                'kode_wadah' => $data->kode_wadah,
                'no_po' => $data->no_po,
                'nama_barang' => $data->nama_barang,
                'tanggal_masuk' => $data->tanggal_masuk,
                'tanggal_keluar' => null,
                'status' => 0,
                'jumlah' => $sisaKembali,
                'id_satuan' => $data->id_satuan,
                'tanggal_akan_keluar' => $data->tanggal_akan_keluar,
                'referensi_masuk' => $data->referensi_masuk,
                'referensi_keluar' => null,
                'lokasi' => $data->lokasi,
                'jenis' => $data->jenis,
                'id_parent' => $data->id,
                'keterangan' => 'Sisa koreksi keluar dari ID ' . $data->id,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'koreksi berhasil. ' . $sisaKembali . ' dikembalikan ke stok gudang'
        ]);
    }

    public function dt_WarehouseInStock()
    {
        $table = warehouseTransaksi::where('status', 0)
            ->where('jumlah', '>', 0)
            ->where('jenis', 1)//jenis bahan masak
            ->orderBy('tanggal_masuk', 'asc')
            ->get();

            return DataTables::of($table)
            ->addIndexColumn()
            
            ->editColumn('status', function ($row) {
                return $row->status == 0 ? "in Stock" : "out of stock";
            })
            ->addColumn('lama_penyimpanan', function ($row) {
                return now()->diffInDays(\Carbon\Carbon::parse($row->tanggal_masuk)) . " hari";
            })
            ->addColumn('nama_satuan', function ($table) {
                $satuan = TbSatuan::where('id', $table->id_satuan)->first();
                return $satuan->satuan;
            })
            ->rawColumns(['status', 'nama_satuan'])
            ->make(true);
    }

    public function dt_warehouseMustOut()
    {
        $table = warehouseTransaksi::where('status', 0)
            ->where('jumlah', '>', 0)
            ->where('jenis', 1)
            ->whereDate('tanggal_akan_keluar', now()) // Membandingkan dengan format penuh
            ->get();

        return DataTables::of($table)
            ->addIndexColumn()
            ->addColumn('nama_satuan', function ($row) {
                $satuan = TbSatuan::where('id', 1)->first();
                return $satuan->satuan;
            })
            ->editColumn('status', function ($row) {
                return $row->status == 0 ? "di gudang" : "sudah dikeluarkan";
            })
            ->addColumn('lama_penyimpanan', function ($row) {
                return now()->diffInDays(\Carbon\Carbon::parse($row->tanggal_masuk)) . " hari";
            })
            ->rawColumns(['status'])
            ->make(true);
    }


    public function ajax_hapusBarangTransaksiMasuk(Request $request)
    {
        $data = warehouseTransaksi::find($request->id);
        TbPenerimaan::destroy($data->id_penerimaan);
        warehouseTransaksi::destroy($request->id);
            return response()->json([
                'message'=>'data berhasil dihapus',
                'status'=>'success',
            ]);
    }

    public function dt_warehouse()
    {
        $table = warehouseTransaksi::where('status', 0)
            ->where('jumlah', '>', 0)
            ->where('jenis', 1)
            //->whereNull('parent')
            //->orWhere('parent', 0)
            ->get();

        return DataTables::of($table)
            ->addIndexColumn()
            
            
            ->addColumn('lama_penyimpanan', function ($row) {
                return now()->diffInDays(\Carbon\Carbon::parse($row->tanggal_masuk)) . " hari";
            })
            ->addColumn('nama_satuan', function ($row) {
                $satuan = TbSatuan::where('id', $row->id_satuan)->first();
                return $satuan ? $satuan->satuan : '-';
            })
            ->addColumn('action', function($row){
                            $btn = '<button onClick="formTransaksiKeluarById('.$row->id.')" class="btn btn-xs btn-info"><i class="fa fa-pencil"></i> Keluarkan</button> ';
                            $btn .= '<button onClick="ajax_updateSatuanGudang('.$row->id.','.$row->id_satuan.')" class="btn btn-xs btn-primary"><i class="fa fa-balance-scale"></i> Ubah Satuan</button>';
               return $btn;
            })
            ->rawColumns(['lama_penyimpanan', 'action'])
            ->make(true);
    }
    public function getBahanByPo(Request $request)
    {
        $nomor_po = $request->input('nomor_po');

        // Ambil bahan berdasarkan nomor PO
        $bahanList = DB::table('tb_penerimaan')
            ->join('tb_po_bahan', 'tb_penerimaan.id_barang_po', '=', 'tb_po_bahan.id')
            ->join('tb_master_bahan', 'tb_po_bahan.id_bahan', '=', 'tb_master_bahan.id')
            ->join('tb_satuan', 'tb_po_bahan.satuan', '=', 'tb_satuan.id')
            ->join('tb_po', 'tb_po_bahan.id_po', '=', 'tb_po.id')
            //->leftJoin('warehouse_transaksi', 'tb_penerimaan.id', '=', 'warehouse_transaksi.id_penerimaan')
            ->where('tb_po.nomor_po', $nomor_po)
            
            //->whereNull('warehouse_transaksi.id_penerimaan') // Hanya ambil data yang belum ada di warehouse_transaksi
            ->select(
                'tb_penerimaan.jumlah_datang',
                'tb_penerimaan.id',
                'tb_master_bahan.bahan',
                'tb_master_bahan.jenis as bahan_jenis',
                'tb_satuan.satuan',
                'tb_satuan.id as satuan_id'
            )
            ->get();

        return response()->json($bahanList);

    }

    public function dt_list_bahan()
    {
        $table = DB::table('rincian_menu_harian as r')
            ->join('tb_menu as m', 'r.id_menu_harian', '=', 'm.id')
            ->join('tb_resep as resep', 'r.id_resep', '=', 'resep.id')
            ->join('tb_master_bahan as bahan', 'r.id_bahan', '=', 'bahan.id')
            ->join('tb_satuan as satuan', 'r.id_satuan', '=', 'satuan.id')
            ->select(
                'r.id as id_rincian',
                'resep.nama_resep as resep',
                'bahan.bahan as bahan',
                'satuan.satuan as satuan',
                'r.jumlah'
            )
            ->whereDate('m.tanggal_kirim', date('Y-m-d'))
            ->where('m.status_pengajuan', '!=', 'rejected') // Hindari data yang reject
            ->get();

        return DataTables::of($table)
            ->addIndexColumn()
            ->addColumn('jumlah_satuan', function ($row) {
                
                return $row->bahan.' '. number_format($row->jumlah, 0, ',', '.').' '.$row->satuan;
            })
            
            ->rawColumns(['jumlah_satuan'])
            ->make(true);
    }

}


