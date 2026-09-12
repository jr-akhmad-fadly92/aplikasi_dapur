<?php

namespace App\Http\Controllers\office;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanPenggunaanDanaController extends Controller
{
    /** Info statis SPPG */
    private array $sppgInfo = [
        'nama_sppg' => 'Yayasan Bina Bangsa 01',
        'kelurahan' => 'Sadeng',
        'kecamatan' => 'Gunungpati',
        'kabupaten' => 'Kota Semarang',
        'provinsi'  => 'Jawa Tengah',
    ];

    /** Peta nama bulan */
    private array $bulanMap = [
        '01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni',
        '07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'
    ];

    /**
     * Data laporan per bulan (simulasi database).
     */
    private $dataRecords = [
        '2024-12' => [
            'saldo_awal' => 100000000,
            'penerimaan_dana' => 500000000,
            'pengeluaran' => [
                'bahan_pangan' => 50000000,
                'operasional' => 100000000,
                'sewa' => 100000000,
            ],
        ],
        '2025-01' => [
            'saldo_awal' => 20000000,
            'penerimaan_dana' => 500000000,
            'pengeluaran' => [
                'bahan_pangan' => 45000000,
                'operasional' => 20000000,
                'sewa' => 100000000,
            ],
        ],
        '2025-03' => [
            'saldo_awal' => 40000000,
            'penerimaan_dana' => 500000000,
            'pengeluaran' => [
                'bahan_pangan' => 80000000,
                'operasional' => 40000000,
                'sewa' => 100000000,
            ],
        ],
        '2025-07' => [
            'saldo_awal' => 5000000,
            'penerimaan_dana' => 10000000,
            'pengeluaran' => [
                'bahan_pangan' => 3500000,
                'operasional' => 1500000,
                'sewa' => 2000000,
            ],
        ],
    ];

    public function index(Request $request)
    {
        $bulan   = $request->input('bulan');
        $tahun   = $request->input('tahun');
        $periode = ($tahun && $bulan) ? "$tahun-$bulan" : null;

        $reportData = null;

        if ($periode && isset($this->dataRecords[$periode])) {
            $reportData = $this->dataRecords[$periode];

            $totalPengeluaran = array_sum($reportData['pengeluaran']);
            $saldoAkhir       = $reportData['saldo_awal'] + $reportData['penerimaan_dana'] - $totalPengeluaran;

            $reportData['total_pengeluaran'] = $totalPengeluaran;
            $reportData['saldo_akhir']       = $saldoAkhir;

            // >>> Tambahkan INFO supaya view & PDF tidak error
            $periodeLabel = ($this->bulanMap[$bulan] ?? $bulan) . ' ' . $tahun;
            $reportData['info'] = array_merge($this->sppgInfo, ['periode' => $periodeLabel]);
        }

        return view('office.laporanakuntan.lpd2m', compact('reportData', 'bulan', 'tahun'));
    }

    public function cetak(Request $request)
{
    $bulan   = $request->input('bulan');
    $tahun   = $request->input('tahun');
    $periode = ($tahun && $bulan) ? "$tahun-$bulan" : null;

    // Cek apakah periode valid
    if (!$periode || !isset($this->dataRecords[$periode])) {
        return redirect()->back()->with('error', 'Data tidak ada untuk periode yang dipilih.');
    }

    // Ambil data laporan sesuai periode
    $reportData = $this->dataRecords[$periode];

    // Hitung total pengeluaran
    $totalPengeluaran = array_sum($reportData['pengeluaran']);
    $saldoAkhir       = $reportData['saldo_awal'] + $reportData['penerimaan_dana'] - $totalPengeluaran;

    // Tambahkan ke data laporan
    $reportData['total_pengeluaran'] = $totalPengeluaran;
    $reportData['saldo_akhir']       = $saldoAkhir;

    // Tambahkan info tambahan untuk header laporan
    $periodeLabel = ($this->bulanMap[$bulan] ?? $bulan) . ' ' . $tahun;
    $reportData['info'] = array_merge($this->sppgInfo, [
        'periode' => $periodeLabel,
    ]);

    // Kirim ke view PDF
    $pdf = Pdf::loadView('office.laporanakuntan.lpd2mpdf', [
        'reportData' => $reportData,
        'bulan'      => $bulan,
        'tahun'      => $tahun,
    ]);

    return $pdf->download("laporan_penggunaan_dana_{$periode}.pdf");
}

}
