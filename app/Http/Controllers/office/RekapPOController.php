<?php

namespace App\Http\Controllers\Office;


use App\Http\Controllers\Controller;
use App\Models\BoxBahanBaku;
use App\Models\Buffer;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
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
use App\Models\KasKecilTransaksi;
use App\Models\Resep;
use App\Models\rincian_sekolah;
use App\Models\SpesifikasiBahan;
use App\Models\tb_karyawan;
use App\Models\TbBantuBahanPo;
use App\Models\TbRincianKontrak;
use Svg\Tag\Rect;

class RekapPOController extends Controller
{
    public function index()
    {
        // Menentukan judul halaman untuk dikirim ke view
        $header = "Rekap PO";

        // Cek apakah permintaan berasal dari AJAX (misalnya DataTables server-side)
        if (request()->ajax()) {

            // Get filter parameters
            $tanggal_kirim_awal = request('tanggal_kirim_awal');
            $tanggal_kirim_akhir = request('tanggal_kirim_akhir');

            // Query SQL untuk mengambil data rekap PO dengan join dan total jumlah bahan
            $query = "
            SELECT 
                tb_kontrak.nomor_kontrak,
                tb_kontrak.akhir_kontrak,
                tb_supplier.nama_supplier,
                tb_po.nomor_po,
                tb_po.id AS id_po,
                tb_po.status_po,
                tb_po.tanggal_po,
                tb_po.tanggal_approve,
                tb_po.manual,
                SUM(tb_po_bahan.jumlah_po) AS total_jumlah_bahan
            FROM tb_po
           
            JOIN tb_kontrak ON tb_po.id_kontrak = tb_kontrak.id
            JOIN tb_supplier ON tb_kontrak.id_supplier = tb_supplier.id
            LEFT JOIN tb_po_bahan ON tb_po.id = tb_po_bahan.id_po
            WHERE status_po IN ('acc', 'close','diterima','bayar')";

            // Add date filter if provided
            if ($tanggal_kirim_awal && $tanggal_kirim_akhir) {
                $query .= " AND tb_po_bahan.tanggal_digunakan BETWEEN '" . $tanggal_kirim_awal . "' AND '" . $tanggal_kirim_akhir . "'";
            }

            $query .= "
            GROUP BY 
                tb_kontrak.nomor_kontrak,
                tb_kontrak.akhir_kontrak,
                tb_supplier.nama_supplier,
                tb_po.nomor_po,
                tb_po.id,
                tb_po.status_po,
                tb_po.tanggal_po,
                tb_po.tanggal_approve,
                tb_po.manual
            ORDER BY tb_po.id DESC
            LIMIT 40
        ";

            $resep = DB::select(DB::raw($query));



            // Kembalikan data dalam format DataTables
            return DataTables::of($resep)
                ->addIndexColumn() // Tambahkan nomor urut otomatis

                ->addColumn('tanggal_po_dibuat', function ($row) {
                    // Format tanggal PO dalam format hari dan tanggal (Bahasa Indonesia)
                    setlocale(LC_TIME, 'id_ID.utf8', 'Indonesian', 'id_ID');
                    return strftime('%A, %d-%m-%Y', strtotime($row->tanggal_po));
                })
                ->addColumn('bahan_po', function ($row) {
                    // ngambil data bahan ti relasi
                    $bahanList = DB::table('tb_po_bahan')
                        ->join('tb_master_bahan', 'tb_po_bahan.id_bahan', '=', 'tb_master_bahan.id')
                        ->where('tb_po_bahan.id_po', $row->id_po)
                        ->pluck('tb_master_bahan.bahan')
                        ->unique() // ngaleungitkeun duplikat
                        ->toArray();

                    // balikkeun jadi string dipisah ku koma
                    return implode(', ', $bahanList);
                })

                ->addColumn('tanggal_acc', function ($row) {
                    // Tampilkan tanggal approve (ACC) jika ada, jika kosong tampilkan "-"
                    if (!empty($row->tanggal_approve)) {
                        setlocale(LC_TIME, 'id_ID.utf8', 'Indonesian', 'id_ID');
                        return strftime('%A, %d-%m-%Y', strtotime($row->tanggal_approve));
                    }
                    return '-';
                })

                ->addColumn('action', function ($row) {
                // Tombol "Detail" dengan link ke halaman edit bahan baku PO
                $kode_po = $row->nomor_po;

                if (strpos($kode_po, 'NP') !== false) {
                    $jenis_po = 'non pangan';
                    return '<a href="' . route('rincian_rekap_po', $row->id_po) . '" 
                            class="btn btn-info btn-sm" data-id="' . $row->id_po . '">
                            Detail
                        </a>
                        <a href="' . route('pdf_pengajuan_po_manual', $row->id_po) . '" 
                           class="edit btn btn-info btn-sm" 
                           data-id="' . $row->id_po . '">PDF1</a>';
                } else {
                    $jenis_po = 'bahan baku';
                    return '<a href="' . route('rincian_rekap_po', $row->id_po) . '" 
                            class="btn btn-info btn-sm" data-id="' . $row->id_po . '">
                            Detail
                        </a>
                        <a href="' . route('pdf_pengajuan_po', $row->id_po) . '" 
                           class="edit btn btn-info btn-sm" 
                           data-id="' . $row->id_po . '">PDF1</a>';
                }
                    return '<a href="' . route('rincian_rekap_po', $row->id_po) . '" 
                            class="btn btn-info btn-sm" data-id="' . $row->id_po . '">
                            Detail
                        </a>
                        <a href="' . route('pdf_pengajuan_po', $row->id_po) . '" 
                           class="edit btn btn-info btn-sm" 
                           data-id="' . $row->id_po. '">PDF1</a>';
                })
                ->addColumn('tanggal_kirim', function ($row) {
                    // Tombol "Detail" dengan link ke halaman edit bahan baku PO
                    $data = DB::table('tb_po_bahan')->where('id_po', $row->id_po)->first();
                    if($data)
                    {
                        return Carbon::parse($data->tanggal_kedatangan)
                    ->translatedFormat('l, d F Y') ;
                    }else{
                        return '-';
                    }                    
                })
                // Tandai kolom tertentu berisi HTML agar tidak di-escape
                ->rawColumns(['action', 'tanggal_po_dibuat', 'bahan_po'])

                ->make(true);
        }

        // Jika bukan request AJAX, kembalikan tampilan view halaman
        return view('office/PO.rekap_po', compact('header'));
    }


    public function v_rincian_rekapan($id)
    {
        $header = "Rincian Rekap PO";
        $po = TbPo::where('id',$id)->first();
        $total_po = TbPoBahan::where('id_po', $id)->sum('jumlah_po');
        if (request()->ajax()) {
            $data = DB::select(DB::raw("
                SELECT 
                    tb_po_bahan.id,
                    tb_master_bahan.bahan,
                    tb_po_bahan.jumlah_bahan,
                    tb_po_bahan.jumlah_box AS jumlah_box_pesanan,
                    COALESCE(SUM(CASE WHEN tb_penerimaan.status <> 0 THEN tb_penerimaan.jumlah_datang ELSE 0 END), 0) AS jumlah_diterima,
                    COALESCE(SUM(CASE WHEN tb_penerimaan.status <> 0 THEN 1 ELSE 0 END), 0) AS jumlah_box_diterima,
                    MAX(CASE WHEN tb_penerimaan.status <> 0 THEN tb_penerimaan.created_at ELSE NULL END) AS tanggal_terima,
                    tb_satuan.satuan,
                    tb_po_bahan.jumlah_po,
                    MAX(tb_penerimaan.keterangan) AS keterangan
                FROM tb_po_bahan
                JOIN tb_master_bahan ON tb_po_bahan.id_bahan = tb_master_bahan.id
                JOIN tb_satuan ON tb_po_bahan.satuan = tb_satuan.id
                LEFT JOIN tb_penerimaan ON tb_po_bahan.id = tb_penerimaan.id_barang_po
                WHERE tb_po_bahan.id_po = :id_po
                GROUP BY 
                    tb_po_bahan.id,
                    tb_master_bahan.bahan,
                    tb_po_bahan.jumlah_bahan,
                    tb_po_bahan.jumlah_box,
                    tb_satuan.satuan,
                    tb_po_bahan.jumlah_po
                ORDER BY tb_po_bahan.id ASC
            "), ['id_po' => $id]);

                return datatables()->of($data)
                    ->addIndexColumn()
                    ->addColumn('jumlah_pesanan', function ($row) {
                        // Tampilkan tanggal approve (ACC) jika ada, jika kosong tampilkan "-"
                        
                        //return number_format($row->jumlah_bahan,3, ',', '.') .' '.$row->satuan;
                        $value = $row->jumlah_bahan ;
                        $hasil = rtrim(rtrim(number_format($value, 3, '.', ''), '0'), '.');
                        return  number_format($hasil, 0, ',', '.') . ' ' . $row->satuan;
                    })
                    ->addColumn('jumlah_diterima_dapur', function ($row) {
                        // Konsisten dengan laporan_penerimaan: tampilkan nilai sesuai satuan PO tanpa konversi otomatis.
                        $hasil = rtrim(rtrim(number_format($row->jumlah_diterima, 3, '.', ''), '0'), '.');
                        return number_format($hasil, 0, ',','.') . ' ' . $row->satuan;
                    })
                    ->addColumn('jumlah_harga', function ($row) {
                        // Tampilkan tanggal approve (ACC) jika ada, jika kosong tampilkan "-"
                        
                        return 'Rp. '.number_format($row->jumlah_po, 0, ',', '.');
                    })
                    ->addColumn('tanggal_terima_bahan', function ($row) {
                        if (!empty($row->tanggal_terima)) {
                            return Carbon::parse($row->tanggal_terima)->format('d-m-Y H:i');
                        }

                        return '-';
                    })
                    ->addColumn('action', function ($row) use ($po) {
                        // Tampilkan tanggal approve (ACC) jika ada, jika kosong tampilkan "-"
                        if($po->status_po == 'acc' || $po->status_po == 'bayar')
                        {
                            return '<!--button 
                                class="btn btn-warning btn-sm editPoBtn" 
                                data-id="' . $row->id . '" 
                                data-jumlah_po="' . $row->jumlah_po . '">
                                Update PO
                            </!--button-->
                            <button class="btn btn-primary btn-sm" onclick="editModal(' . $row->id . ')">Edit</button>';
                        }else{
                            return '-';
                        }
                        
                    })
                    
                    ->rawColumns(['action','jumlah_pesanan','jumlah_diterima_dapur'])
                    ->make(true);
        }

        return view('office/PO.rincian_rekap_po', compact('header', 'po', 'total_po'));
    }

    public function update_jumlah_po(Request $request)
    {
        // Validasi input
        $request->validate([
            'id' => 'required|exists:tb_po_bahan,id',
            'jumlah_po' => 'required|min:0'
        ]);

        // Update ke database
        DB::table('tb_po_bahan')
            ->where('id', $request->id)
            ->update(['jumlah_po' => $request->jumlah_po]);

        return response()->json(['message' => 'Jumlah PO berhasil diupdate']);
    }

    public function closePo(Request $request, $id)
    {
        // ✅ 1. Validasi PO berdasarkan ID
        $po = DB::table('tb_po')->where('id', $id)->first();
        if (!$po) {
            return redirect()->route('pengajuan_po.index')->with('error', 'PO tidak ditemukan.');
        }

        // ✅ 2. Ambil daftar bahan terkait PO, lalu format jadi string deskripsi
        /*$list_bahan = DB::table('tb_penerimaan')
            ->join('tb_po_bahan as pb', 'tb_penerimaan.id_barang_po', '=', 'pb.id')
            ->join('tb_master_bahan as mb', 'pb.id_bahan', '=', 'mb.id')
            ->join('tb_satuan as s', 'pb.satuan', '=', 's.id')
            ->select(
                'mb.bahan',
                's.satuan',
                DB::raw("
            REPLACE(
                TRIM(TRAILING '.' FROM TRIM(TRAILING '0' FROM FORMAT(
                    CASE 
                        WHEN LOWER(s.satuan) = 'kg' 
                            THEN SUM(tb_penerimaan.jumlah_datang) / 1000
                        ELSE SUM(tb_penerimaan.jumlah_datang)
                    END, 
                    3, 'de_DE'
                ))),
                ',', '.'
            ) as jumlah_datang
        ")
            )
            ->where('pb.id_po', $id)
            ->groupBy('mb.bahan', 's.satuan')
            ->get();*/
        $list_bahan = DB::table('tb_po_bahan as pb')
            ->join('tb_master_bahan as mb', 'pb.id_bahan', '=', 'mb.id')
            ->join('tb_satuan as s', 'pb.satuan', '=', 's.id')
            ->select(
                'mb.bahan',
                's.satuan',
                DB::raw("
            REPLACE(
                TRIM(TRAILING '.' FROM TRIM(TRAILING '0' FROM FORMAT(
                    CASE 
                        WHEN LOWER(s.satuan) = 'kg' 
                            THEN SUM(pb.jumlah_bahan)
                        ELSE SUM(pb.jumlah_bahan)
                    END, 
                    3, 'de_DE'
                ))),
                ',', '.'
            ) as jumlah_datang
        ")
            )
            ->where('pb.id_po', $id)
            ->groupBy('mb.bahan', 's.satuan')
            ->havingRaw('SUM(pb.jumlah_bahan) > 0')   // ⬅️ filter supaya 0 tidak muncul
            ->get();
        $diskripsi = $list_bahan->map(function ($item) {

            return "{$item->bahan} {$item->jumlah_datang} {$item->satuan}";
        })->implode(', ');

        // ✅ 3. Ambil data dapur (untuk akuntan yang akan dicatat)
        $dapur = DataDapur::first();

        // ✅ 4. Bersihkan format total biaya (hilangkan titik agar jadi angka murni)
        $total_biaya = str_replace('.', '', $request->total_biaya);
        $total_biaya = (int) $total_biaya; // pakai float kalau butuh desimal

        // ✅ 5. Cek apakah nomor PO sudah ada di transaksi kas kecil
        
        $cek = KasKecilTransaksi::where('nomor_po', $request->nomor_po)->count();
        
        if ($cek == 0) {
            // ➕ Tambahkan transaksi baru
            KasKecilTransaksi::create([
                'tanggal'         => $request->tanggal_close,
                'jenis_transaksi' => 'keluar',
                'deskripsi'       => 'pembelian '.$diskripsi .' pada Koperasi Seribu Impian',
                'jumlah'          => $total_biaya,
                'nama_karyawan'   => $dapur->ahli_akuntan,
                'nomor_transaksi' => $request->nomor_referensi,
                'status'          => 1,
                'nomor_po'        => $request->nomor_po,
            ]);
            DB::table('tb_po')->where('id', $id)->update([
                'status_po' => 'close',
            ]);

            // ✅ 7. Simpan data ke tabel tb_po_close
            DB::table('tb_po_close')->insert([
                'id_po'           => $id,
                'tanggal_tutup_po' => Carbon::parse($request->tanggal_close, 'Asia/Jakarta')
                ->format('Y-m-d H:i:s'),
                'nomor_po'        => $request->nomor_po,
                'jenis_po'        => $request->jenis_po,
                'created_at'      => Carbon::now('Asia/Jakarta'),
                'updated_at'      => Carbon::now('Asia/Jakarta'),
            ]);
        } else {
            // ✏️ Update transaksi yang sudah ada
            KasKecilTransaksi::where('nomor_po', $request->nomor_po)->delete();
            DB::table('tb_po_close')->where('nomor_po', $request->nomor_po)->delete();
            KasKecilTransaksi::where('nomor_po', $request->nomor_po)->create([
                'tanggal'         => $request->tanggal_close,
                'jenis_transaksi' => 'keluar',
                'deskripsi'       => 'pembelian ' . $diskripsi . ' pada Koperasi Seribu Impian',
                'jumlah'          => $total_biaya,
                'nama_karyawan'   => $dapur->ahli_akuntan,
                'nomor_transaksi' => $request->nomor_referensi,
                'status'          => 1,
                'nomor_po'        => $request->nomor_po,
            ]);
            DB::table('tb_po')->where('id', $id)->update([
                'status_po' => 'close',
            ]);

            // ✅ 7. Simpan data ke tabel tb_po_close
            DB::table('tb_po_close')->insert([
                'id_po'           => $id,
                'tanggal_tutup_po' => Carbon::parse($request->tanggal_close, 'Asia/Jakarta')
                ->format('Y-m-d H:i:s'),
                'nomor_po'        => $request->nomor_po,
                'jenis_po'        => $request->jenis_po,
                'created_at'      => Carbon::now('Asia/Jakarta'),
                'updated_at'      => Carbon::now('Asia/Jakarta'),
            ]);
        }

        // ✅ 6. Update status PO menjadi "close"
       

        // ✅ 8. Redirect dengan pesan sukses
        return redirect()->route('Rekap_po.index')->with(['success' => 'PO berhasil ditutup.']);
    }
}
