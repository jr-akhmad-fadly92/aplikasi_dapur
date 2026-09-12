<?php

namespace App\Http\Controllers\office;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\KasKecilTransaksi;
use App\Models\LaporanRealisasiAnggaran;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanRealisasiAnggaranExport;
use Yajra\DataTables\DataTables;

class LaporanRealisasiAnggaranController extends Controller
{
    /**
     * Data statis laporan (simulasi database).
     */
    private $dataRecords = [
        ['date' => '2025-06-09', 'type' => 'pendapatan', 'source' => 'penerimaan_bgn', 'amount' => 182880000],
        ['date' => '2025-06-10', 'type' => 'belanja', 'source' => 'belanja_bahan_pangan', 'amount' => 61601447],
        ['date' => '2025-06-12', 'type' => 'pendapatan', 'source' => 'penerimaan_yayasan', 'amount' => 50000000],
        ['date' => '2025-06-15', 'type' => 'belanja', 'source' => 'belanja_operasional', 'amount' => 13570000],
        ['date' => '2025-06-17', 'type' => 'pendapatan', 'source' => 'penerimaan_pihak_lainnya', 'amount' => 50000000],
        ['date' => '2025-06-18', 'type' => 'belanja', 'source' => 'belanja_sewa', 'amount' => 14534000],
        ['date' => '2025-06-19', 'type' => 'pendapatan', 'source' => 'penerimaan_bgn', 'amount' => 182880000],
        ['date' => '2025-06-20', 'type' => 'belanja', 'source' => 'belanja_bahan_pangan', 'amount' => 61601448],
        ['date' => '2025-06-20', 'type' => 'belanja', 'source' => 'belanja_operasional', 'amount' => 13570000],
        ['date' => '2025-06-20', 'type' => 'belanja', 'source' => 'belanja_sewa', 'amount' => 14534000],
        ['date' => '2025-07-01', 'type' => 'pendapatan', 'source' => 'penerimaan_yayasan', 'amount' => 50000000],
        ['date' => '2025-07-05', 'type' => 'pendapatan', 'source' => 'penerimaan_pihak_lainnya', 'amount' => 50000000],
    ];

    /**
     * Menampilkan laporan keuangan.
     */
    public function index(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Jika tidak ada tanggal yang dipilih, gunakan data default (semua data)
        if (!$startDate || !$endDate) {
            $filteredData = $this->dataRecords;
            $periode = 'Semua Periode';
        } else {
            $filteredData = collect($this->dataRecords)->filter(function ($record) use ($startDate, $endDate) {
                return $record['date'] >= $startDate && $record['date'] <= $endDate;
            })->all();
            $periode = "{$startDate} s.d. {$endDate}";
        }

        // Hitung total dari data yang difilter
        $totals = $this->calculateTotals($filteredData);

        $reportData = [
            'info' => [
                'nama_sppg' => 'Yayasan Bina Bangsa 01',
                'kelurahan' => 'Sadeng',
                'kecamatan' => 'Gunungpati',
                'kabupaten' => 'Kota Semarang',
                'provinsi' => 'Jawa Tengah',
            ],
            'periode' => $periode,
            'pendapatan' => [
                'penerimaan_bgn' => $totals['penerimaan_bgn'],
                'penerimaan_yayasan' => $totals['penerimaan_yayasan'],
                'penerimaan_pihak_lainnya' => $totals['penerimaan_pihak_lainnya'],
            ],
            'belanja' => [
                'bahan_pangan' => $totals['belanja_bahan_pangan'],
                'operasional' => $totals['belanja_operasional'],
                'sewa' => $totals['belanja_sewa'],
            ],
            'total_pendapatan' => $totals['total_pendapatan'],
            'total_belanja' => $totals['total_belanja'],
            'surplus_defisit' => $totals['total_pendapatan'] - $totals['total_belanja'],
        ];

        return view('office.laporanakuntan.lra', compact('reportData'));
    }

    /**
     * Mencetak laporan keuangan menjadi PDF.
     */
    public function cetak(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if (!$startDate || !$endDate) {
            $filteredData = $this->dataRecords;
            $periode = 'Semua Periode';
        } else {
            $filteredData = collect($this->dataRecords)->filter(function ($record) use ($startDate, $endDate) {
                return $record['date'] >= $startDate && $record['date'] <= $endDate;
            })->all();
            $periode = "{$startDate} s.d. {$endDate}";
        }

        $totals = $this->calculateTotals($filteredData);

        $reportData = [
            'info' => [
                'nama_sppg' => 'Yayasan Bina Bangsa 01',
                'kelurahan' => 'Sadeng',
                'kecamatan' => 'Gunungpati',
                'kabupaten' => 'Kota Semarang',
                'provinsi' => 'Jawa Tengah',
            ],
            'periode' => $periode,
            'pendapatan' => [
                'penerimaan_bgn' => $totals['penerimaan_bgn'],
                'penerimaan_yayasan' => $totals['penerimaan_yayasan'],
                'penerimaan_pihak_lainnya' => $totals['penerimaan_pihak_lainnya'],
            ],
            'belanja' => [
                'bahan_pangan' => $totals['belanja_bahan_pangan'],
                'operasional' => $totals['belanja_operasional'],
                'sewa' => $totals['belanja_sewa'],
            ],
            'total_pendapatan' => $totals['total_pendapatan'],
            'total_belanja' => $totals['total_belanja'],
            'surplus_defisit' => $totals['total_pendapatan'] - $totals['total_belanja'],
        ];

        $pdf = Pdf::loadView('office.laporanakuntan.lrapdf', compact('reportData'));
        return $pdf->download('laporan_keuangan.pdf');
    }

    /**
     * Fungsi untuk menghitung total berdasarkan data yang difilter.
     */
    private function calculateTotals($records)
    {
        $totals = [
            'penerimaan_bgn' => 0,
            'penerimaan_yayasan' => 0,
            'penerimaan_pihak_lainnya' => 0,
            'belanja_bahan_pangan' => 0,
            'belanja_operasional' => 0,
            'belanja_sewa' => 0,
            'total_pendapatan' => 0,
            'total_belanja' => 0
        ];

        foreach ($records as $record) {
            if (isset($totals[$record['source']])) {
                $totals[$record['source']] += $record['amount'];
            }
            if ($record['type'] === 'pendapatan') {
                $totals['total_pendapatan'] += $record['amount'];
            } else if ($record['type'] === 'belanja') {
                $totals['total_belanja'] += $record['amount'];
            }
        }
        return $totals;
    }

    public function v_laporan_biaya_realisasi() {
        $header = "Dashboard Biaya Realisasi";
        return view('office.laporanakuntan.lra', compact('header'));
    }

    public function dt_laporan_biaya_realisasi()
    {
        $table = LaporanRealisasiAnggaran::all();
       
        //return $table;
        return DataTables::of($table)
            ->addIndexColumn()
            ->addColumn('periode', function ($row) {
                $keterangan = $row->periode_awal .' s.d '.$row->periode_akhir;
                return $keterangan;
            })
            ->addColumn('dana_bgn', function ($row) {
                return 'Rp.'.number_format($row->bgn,0,'.',',');
            })
            ->addColumn('dana_yayasan', function ($row) {
                return 'Rp.' . number_format($row->yayasan, 0, '.', ',');
            })
            ->addColumn('dana_pihak_lain', function ($row) {
                return 'Rp.' . number_format($row->pihak_lain, 0, '.', ',');
            })
            ->addColumn('total_pemasukan', function ($row) {
                return 'Rp.' . number_format(($row->pihak_lain+ $row->yayasan+ $row->bgn), 0, '.', ',');
            })
            ->addColumn('total_pengeluaran', function ($row) {
                $totalJumlahPoPangan = DB::table('tb_po_bahan as pb')
                ->join('tb_po as tp', 'pb.id_po', '=', 'tp.id')
                ->where('pb.jumlah_bahan', '<>', 0)
                ->where('pb.id_rincian_bahan', '<>', 0)
                ->whereBetween('pb.tanggal_digunakan', [$row->periode_awal, $row->periode_akhir])
                ->where('tp.status_po', '=', 'close')
                ->sum('pb.jumlah_po'); // langsung SUM di Query Builder

                 $totalJumlahPo = DB::table('tb_po_bahan as pb')
                ->join('tb_po as tp', 'pb.id_po', '=', 'tp.id')
                ->where('pb.jumlah_bahan', '>', 0)
                ->where('pb.id_rincian_bahan', '==', 0)
                ->whereBetween('pb.tanggal_digunakan', [$row->periode_awal, $row->periode_akhir])
                ->where('tp.status_po', '=', 'close')
                ->sum('pb.jumlah_po'); // langsung SUM di Query Builder

                $tanggalMulai = Carbon::parse($row->periode_awal)->format('Y-m-d 00:00:00');
                $tanggalSelesai = Carbon::parse($row->periode_akhir)->format('Y-m-d 00:00:00');
                $totalJumlahNonPo = KasKecilTransaksi::where('status', 1)
                ->whereNull('nomor_po')
                ->whereBetween('tanggal', [
                    $tanggalMulai,
                    $tanggalSelesai
                ])
                ->sum('jumlah');
                $jumlah_non_pangan = $totalJumlahNonPo + $totalJumlahPo ;
                $jumlah_infra = KasKecilTransaksi::where('status', 1)
                ->whereIn('master_bahan_id',[171,200])
                ->whereBetween('tanggal', [
                    $tanggalMulai,
                    $tanggalSelesai
                ])
                ->sum('jumlah');
                $total_pengeluaran = $jumlah_infra+ $jumlah_non_pangan+ $totalJumlahPoPangan;
                
                return 'Rp.' . number_format($total_pengeluaran, 0, '.', ',');
            })
            ->addColumn('saldo', function ($row) {
                $totalJumlahPoPangan = DB::table('tb_po_bahan as pb')
                ->join('tb_po as tp', 'pb.id_po', '=', 'tp.id')
                ->where('pb.jumlah_bahan', '<>', 0)
                ->where('pb.id_rincian_bahan', '<>', 0)
                ->whereBetween('pb.tanggal_digunakan', [$row->periode_awal, $row->periode_akhir])
                ->where('tp.status_po', '=', 'close')
                ->sum('pb.jumlah_po'); // langsung SUM di Query Builder

                 $totalJumlahPo = DB::table('tb_po_bahan as pb')
                ->join('tb_po as tp', 'pb.id_po', '=', 'tp.id')
                ->where('pb.jumlah_bahan', '>', 0)
                ->where('pb.id_rincian_bahan', '==', 0)
                ->whereBetween('pb.tanggal_digunakan', [$row->periode_awal, $row->periode_akhir])
                ->where('tp.status_po', '=', 'close')
                ->sum('pb.jumlah_po'); // langsung SUM di Query Builder

                $tanggalMulai = Carbon::parse($row->periode_awal)->format('Y-m-d 00:00:00');
                $tanggalSelesai = Carbon::parse($row->periode_akhir)->format('Y-m-d 00:00:00');
                $totalJumlahNonPo = KasKecilTransaksi::where('status', 1)
                ->whereNull('nomor_po')
                ->whereBetween('tanggal', [
                    $tanggalMulai,
                    $tanggalSelesai
                ])
                ->sum('jumlah');
                $jumlah_non_pangan = $totalJumlahNonPo + $totalJumlahPo ;
                $jumlah_infra = KasKecilTransaksi::where('status', 1)
                ->where('master_bahan_id', 171)
                ->whereBetween('tanggal', [
                    $tanggalMulai,
                    $tanggalSelesai
                ])
                ->sum('jumlah');
                $total_pengeluaran = $jumlah_infra+ $jumlah_non_pangan+ $totalJumlahPoPangan;
                $saldo = $row->bgn + $row->yayasan + $row->pihak_lain - $total_pengeluaran;
                if($saldo < 0)
                {
                    return 'Minus Rp.' . number_format(abs($saldo), 0, '.', ',');    
                }
                return 'Rp.' . number_format($saldo, 0, '.', ',');
            })
            ->addColumn('biaya_bahan_baku', function ($row) {
                $totalJumlahPo = DB::table('tb_po_bahan as pb')
                ->join('tb_po as tp', 'pb.id_po', '=', 'tp.id')
                ->where('pb.jumlah_bahan', '<>', 0)
                ->where('pb.id_rincian_bahan', '<>', 0)
                ->whereBetween('pb.tanggal_digunakan', [$row->periode_awal, $row->periode_akhir])
                ->where('tp.status_po', '=', 'close')
                ->sum('pb.jumlah_po'); // langsung SUM di Query Builder
                return 'Rp.' . number_format($totalJumlahPo, 0, '.', ',');
            })
            ->addColumn('biaya_non_pangan', function ($row) {
                $totalJumlahPo = DB::table('tb_po_bahan as pb')
                ->join('tb_po as tp', 'pb.id_po', '=', 'tp.id')
                ->where('pb.jumlah_bahan', '>', 0)
                ->where('pb.id_rincian_bahan', '==', 0)
                ->whereBetween('pb.tanggal_digunakan', [$row->periode_awal, $row->periode_akhir])
                ->where('tp.status_po', '=', 'close')
                ->sum('pb.jumlah_po'); // langsung SUM di Query Builder

                $tanggalMulai = Carbon::parse($row->periode_awal)->format('Y-m-d 00:00:00');
                $tanggalSelesai = Carbon::parse($row->periode_akhir)->format('Y-m-d 00:00:00');
                $totalJumlahNonPo = KasKecilTransaksi::where('status', 1)
                ->whereNull('nomor_po')
                ->whereBetween('tanggal', [
                    $tanggalMulai,
                    $tanggalSelesai
                ])
                ->sum('jumlah');
                $jumlah = $totalJumlahNonPo + $totalJumlahPo ;
           // $jumlah = 0;
            //return '';
                return 'Rp.' . number_format($jumlah, 0, '.', ',');
            })
            ->addColumn('biaya_infrastuktur_dan_peralatan', function ($row) {
                $tanggalMulai = Carbon::parse($row->periode_awal)->format('Y-m-d 00:00:00');
                $tanggalSelesai = Carbon::parse($row->periode_akhir)->format('Y-m-d 00:00:00');
                $jumlah = KasKecilTransaksi::where('status', 1)
                ->where('master_bahan_id', 171)
                ->whereBetween('tanggal', [
                    $tanggalMulai,
                    $tanggalSelesai
                ])
                ->sum('jumlah');
                return 'Rp.' . number_format($jumlah, 0, '.', ',');
            })
            ->addColumn('action', function ($row) {
               // return '<button onClick="ajax_hapusBarangTransakiWarehouse(' . $row->id . ')" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i> Hapus</button>';
                return '<button class="btn btn-sm btn-warning btn-edit-box"
                    data-id="' . $row->id . '"
                    data-periodeawal="' . $row->periode_awal . '"
                    data-periodeakhir="' . $row->periode_akhir . '"
                    data-periode="' . ($row->periode ?? '') . '"
                    data-jumlahhari="' . ($row->jumlah_hari ?? 0) . '"
                    data-bgn="' . $row->bgn . '"
                    data-yayasan="' . $row->yayasan . '"
                    data-pihak="' . $row->pihak_lain . '"
                    
                    >
                    Edit
                </button>
                <a href="' . route('realisasi.cetak', $row->id) . '" class="btn btn-info btn-sm">Laporan</a>
                <a href="' . route('laporan_harian_dapur.excel', ['periode_awal' => $row->periode_awal, 'periode_akhir' => $row->periode_akhir]) . '" class="btn btn-success btn-sm">Laporan Harian</a>' ;
            })
           
            ->rawColumns(['action', 'total_pemasukan', 'total_pengeluaran', 'saldo',
             'biaya_bahan_baku', 'biaya_non_pangan', 'biaya_infrastuktur_dan_peralatan',
            'dana_bgn',
            'dana_yayasan',
            'dana_pihak_lain',
            'periode'])
            ->make(true);
        return view('office.laporanakuntan.lra', compact('header'));
    }

    public function store(Request $request)
    {
        // 🔹 1. Validasi input dari form
        $request->validate([
            'periode_awal'  => 'required|date',   // wajib diisi & harus format tanggal
            'periode_akhir' => 'required|date',   // wajib diisi & harus format tanggal
            'bgn'           => 'required|integer', // wajib angka
            'yayasan'       => 'required|integer', // wajib angka
            'pihak_lain'    => 'required|integer', // wajib angka
        ]);

        // 🔹 2. Ambil data terakhir berdasarkan id (untuk generate kode unik)
        $cek = LaporanRealisasiAnggaran::orderBy('id', 'desc')->first();
        $id  = 'LRA-' . (($cek?->id ?? 0) + 1);
        // kalau tabel masih kosong → mulai dari LRA-1

        // 🔹 3. Cek apakah periode_awal sudah ada di database
        $cek_periode = LaporanRealisasiAnggaran::where('periode_awal', $request->periode_awal)->count();
        if ($cek_periode > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Periode sudah ada.'
            ]);
        }

        // 🔹 4. Simpan data ke database
        LaporanRealisasiAnggaran::create([
            'id_laporan'    => $id, // ⚠️ pastikan di tabel nama kolomnya benar (bukan id_bahan)
            'bgn'           => $request->bgn,
            'yayasan'       => $request->yayasan,
            'pihak_lain'    => $request->pihak_lain,
            'periode_awal'  => $request->periode_awal,
            'periode_akhir' => $request->periode_akhir,
            'periode'       => $request->periode,
            'jumlah_hari'   => $request->jumlah_hari ?? 0,
        ]);

        // 🔹 5. Kembalikan response JSON sukses
        return response()->json([
            'success' => true,
            'message' => 'Data berhasil ditambahkan.'
        ]);
    }

    public function update(Request $request, $id)
    {
      
        // 🔹 1. Validasi input dari form
        $request->validate([
            'periode_awal'  => 'required|date',   // wajib diisi & harus format tanggal
            'periode_akhir' => 'required|date',   // wajib diisi & harus format tanggal
            'bgn'           => 'required|integer', // wajib angka
            'yayasan'       => 'required|integer', // wajib angka
            'pihak_lain'    => 'required|integer', // wajib angka
            'periode'       => 'nullable|string',  // opsional string
            'jumlah_hari'   => 'nullable|integer|min:0', // opsional angka >= 0
        ]);

        // Ambil data box berdasarkan id, jika tidak ditemukan akan error 404
        $laporan = LaporanRealisasiAnggaran::findOrFail($id);
        // Update kolom-kolom sesuai input
        $laporan->bgn   = $request->bgn;
        $laporan->yayasan   = $request->yayasan;
        $laporan->pihak_lain   = $request->pihak_lain;
        $laporan->periode_awal   = $request->periode_awal;
        $laporan->periode_akhir   = $request->periode_akhir;
        $laporan->periode   = $request->periode;
        $laporan->jumlah_hari   = $request->jumlah_hari ?? 0;

        // Simpan perubahan ke database
        $laporan->save();

        // Kembalikan response JSON sukses
        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diperbarui.'
        ]);
    }

    public function Lap_Realisasi_Anggaran($id)
    {
        $data_anggaran = LaporanRealisasiAnggaran::find($id);
        
        $totalJumlahPoPangan = DB::table('tb_po_bahan as pb')
            ->join('tb_po as tp', 'pb.id_po', '=', 'tp.id')
            ->where('pb.jumlah_bahan', '<>', 0)
            ->where('pb.id_rincian_bahan', '<>', 0)
            ->whereBetween('pb.tanggal_digunakan', [$data_anggaran->periode_awal, $data_anggaran->periode_akhir])
            ->where('tp.status_po', '=', 'close')
            ->sum('pb.jumlah_po'); // langsung SUM di Query Builder

        $totalJumlahPo = DB::table('tb_po_bahan as pb')
            ->join('tb_po as tp', 'pb.id_po', '=', 'tp.id')
            ->where('pb.jumlah_bahan', '>', 0)
            ->where('pb.id_rincian_bahan', '==', 0)
            ->whereBetween('pb.tanggal_digunakan', [$data_anggaran->periode_awal, $data_anggaran->periode_akhir])
            ->where('tp.status_po', '=', 'close')
            ->sum('pb.jumlah_po'); // langsung SUM di Query Builder

        $tanggalMulai = Carbon::parse($data_anggaran->periode_awal)->format('Y-m-d 00:00:00');
        $tanggalSelesai = Carbon::parse($data_anggaran->periode_akhir)->format('Y-m-d 23:59:00');
        $totalJumlahNonPo = KasKecilTransaksi::where('status', 1)
            ->whereNull('nomor_po')
            ->whereBetween('tanggal', [
            $tanggalMulai,
                $tanggalSelesai
            ])
            ->sum('jumlah');
        $jumlah_non_pangan = $totalJumlahNonPo + $totalJumlahPo;
        $jumlah_infra = KasKecilTransaksi::where('status', 1)
            ->where('master_bahan_id', 171)
            ->whereBetween('tanggal', [
                $tanggalMulai,
                $tanggalSelesai
            ])
            ->sum('jumlah');
        $total_pengeluaran = $jumlah_infra + $jumlah_non_pangan + $totalJumlahPoPangan;
        $saldo = $data_anggaran->bgn + $data_anggaran->yayasan + $data_anggaran->pihak_lain - $total_pengeluaran;
        
        
        return Excel::download(
            new LaporanRealisasiAnggaranExport(
                $data_anggaran, 
                $totalJumlahPoPangan, 
                $jumlah_non_pangan, 
                $jumlah_infra, 
                $total_pengeluaran,
                $saldo),
            'laporan_Realisasi_anggaran' . date('d-m-Y') . '.xlsx'
        );
    }
}
