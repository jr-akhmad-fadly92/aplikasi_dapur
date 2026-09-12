<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Biaya Realisasi</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 5px; }
        .header h5, .header p { margin: 0; }
        .info { margin-bottom: 5px; }
        .info table { width: 100%; border-collapse: collapse; }
        .info td { padding: 0; }
        .section-title { font-weight: bold; border-bottom: 1px solid #000; padding-bottom: 5px; margin-bottom: 5px; }
        .report-row { display: flex; justify-content: space-between; margin-bottom: 3px; }
        .report-row .description { flex-grow: 1; }
        .report-row .amount { flex-shrink: 0; text-align: right; }
        .total-row { font-weight: bold; border-bottom: 2px solid #000; padding-bottom: 5px; }
        .text-end { text-align: right; }
        .indented { padding-left: 20px; }
        .ttd td { border: none; padding: 0; }
        .catatan ol { padding-left: 20px; }
    </style>
</head>

<body>
    <div class="header">
        <h5>LAPORAN REALISASI ANGGARAN</h5>
        <p>Periode: {{ $reportData['periode'] }}</p>
    </div>

    <!-- Informasi SPPG -->
    <div class="info">
        <table>
            <tr><td style="width: 25%;">Nama SPPG</td><td>: Yayasan Bina Bangsa 01</td></tr>
            <tr><td>Kelurahan/Desa</td><td>: Sadeng</td></tr>
            <tr><td>Kecamatan</td><td>: Gunungpati</td></tr>
            <tr><td>Kabupaten/Kota</td><td>: Kota Semarang</td></tr>
            <tr><td>Provinsi</td><td>: Jawa Tengah</td></tr>
        </table>    
    </div>

    <!-- Bagian Pendapatan -->
    <div class="section-title">
        <span>I. PENDAPATAN</span>
        <span style="float: right;">{{ number_format($reportData['total_pendapatan'], 0, ',', '.') }}</span>
    </div>
    <div class="report-row">
        <span class="indented">Penerimaan dari BGN</span>
        <span style="float: right;">{{ number_format($reportData['pendapatan']['penerimaan_bgn'], 0, ',', '.') }}</span>
    </div>
    <div class="report-row">
        <span class="indented">Penerimaan dari Yayasan</span>
        <span style="float: right;">{{ number_format($reportData['pendapatan']['penerimaan_yayasan'], 0, ',', '.') }}</span>
    </div>
    <div class="report-row">
        <span class="indented">Penerimaan dari Pihak Lainnya</span>
        <span style="float: right;">{{ number_format($reportData['pendapatan']['penerimaan_pihak_lainnya'], 0, ',', '.') }}</span>
    </div>

    <!-- Bagian Belanja -->
    <div class="section-title" style="margin-top: 20px;">
        <span>II. BELANJA</span>
        <span style="float: right;">{{ number_format($reportData['total_belanja'], 0, ',', '.') }}</span>
    </div>
    <div class="report-row">
        <span class="indented">Belanja Bahan Pangan</span>
        <span style="float: right;">{{ number_format($reportData['belanja']['bahan_pangan'], 0, ',', '.') }}</span>
    </div>
    <div class="report-row">
        <span class="indented">Belanja Operasional</span>
        <span style="float: right;">{{ number_format($reportData['belanja']['operasional'], 0, ',', '.') }}</span>
    </div>
    <div class="report-row">
        <span class="indented">Belanja Sewa</span>
        <span style="float: right;">{{ number_format($reportData['belanja']['sewa'], 0, ',', '.') }}</span>
    </div>

    <!-- Bagian Surplus/Defisit -->
    <div class="section-title" style="margin-top: 20px;">
        <span>III. SURPLUS/DEFISIT</span>
        <span style="float: right;">{{ number_format($reportData['surplus_defisit'], 0, ',', '.') }}</span>
    </div>

    <div style="margin-top: 50px;">
        <table class="ttd" style="width: 100%;">
            <tr>
                <td style="text-align:left; width: 50%;">
                    Mengetahui,<br>
                    Kepala SPPG
                </td>
                <td style="text-align:right; width: 50%;">
                   ................, ............20.......<br>
                    Akuntansi SPPG
                </td>
            </tr>
            <tr>
                <td style="text-align:left; width: 50%; padding-top: 50px;">
                    ....................................
                </td>
                <td style="text-align:right; width: 50%; padding-top: 50px;">
                    ....................................
                </td>
            </tr>
        </table>
    </div>

    <!-- Keterangan (Catatan) di Bawah, Rata Kiri -->
    <div class="catatan">
        <strong>Catatan Penting:</strong>
            <ol>
                <li>Seluruh transaksi uang keluar dan masuk wajib dicatat di LRA.</li>
                <li>Pencatatan secara tertib dengan mengikuti kronologis waktu/keterjadian transaksi dan secara harian.</li>
                <li>Periode adalah periode operasional dapur SPPG selama 1 bulan.</li>
            </ol>
        </div>

</body>
</html>
