<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penggunaan Dana</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .container { width: 100%; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 20px; }
        .info { margin-bottom: 20px; }
        .info table { width: 100%; border-collapse: collapse; }
        .info td { padding: 2px 0; }
        table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
    </style>
</head>
<body>

    <div class="container">
        <div class="header">
            <h4>LAPORAN PENGGUNAAN DANA</h4>
            <h5>REKAPITULASI BULANAN</h5>
        </div>

        <!-- Informasi SPPG -->
        <div class="info">
            <table>
                <tr><td>Nama SPPG</td><td>: {{ $reportData['info']['nama_sppg'] }}</td></tr>
                <tr><td>Kelurahan/Desa</td><td>: {{ $reportData['info']['kelurahan'] }}</td></tr>
                <tr><td>Kecamatan</td><td>: {{ $reportData['info']['kecamatan'] }}</td></tr>
                <tr><td>Kabupaten/Kota</td><td>: {{ $reportData['info']['kabupaten'] }}</td></tr>
                <tr><td>Provinsi</td><td>: {{ $reportData['info']['provinsi'] }}</td></tr>
            </table>
        </div>

        <!-- Informasi Periode -->
        <p class="fw-bold">Periode: {{ $reportData['info']['periode'] }}</p>

        <!-- Tabel Laporan -->
        <table>
            <thead>
                <tr>
                    <th rowspan="2">No</th>
                    <th rowspan="2">Saldo Awal</th>
                    <th rowspan="2">Penerimaan Dana</th>
                    <th colspan="3">Pengeluaran Dana</th>
                    <th rowspan="2">TOTAL</th>
                    <th rowspan="2">Saldo Akhir</th>
                </tr>
                <tr>
                    <th>Bahan Pangan</th>
                    <th>Operasional</th>
                    <th>Sewa</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>{{ number_format($reportData['saldo_awal'], 0, ',', '.') }}</td>
                    <td>{{ number_format($reportData['penerimaan_dana'], 0, ',', '.') }}</td>
                    <td>{{ number_format($reportData['pengeluaran']['bahan_pangan'], 0, ',', '.') }}</td>
                    <td>{{ number_format($reportData['pengeluaran']['operasional'], 0, ',', '.') }}</td>
                    <td>{{ number_format($reportData['pengeluaran']['sewa'], 0, ',', '.') }}</td>
                    <td>{{ number_format($reportData['total_pengeluaran'], 0, ',', '.') }}</td>
                    <td>{{ number_format($reportData['saldo_akhir'], 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>

</body>
</html>
