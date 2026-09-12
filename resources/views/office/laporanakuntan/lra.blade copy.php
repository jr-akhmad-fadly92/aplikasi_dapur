<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Biaya Realisasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; }
        .container { max-width: 800px; }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px;
            padding: 8px;
            text-align: left;
        }
        .text-right { text-align: right; }
        .total-row { font-weight: bold; background-color: #f2f2f2; }
    </style>
</head>

<body>
<div class="container mt-5">
    <div class="text-center mb-4">
        <h4>LAPORAN REALISASI ANGGARAN</h4>
        <p>Periode: {{ $reportData['periode'] }}</p>
    </div>

    <!-- Filter Tanggal -->
    <form action="{{ route('laporan.keuangan.index') }}" method="GET" class="my-4">
        <div class="row g-3 align-items-end">
            <div class="col-md-auto">
                <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
            </div>
            <div class="col-md-auto">
                <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
            </div>
            <div class="col-md-auto">
                <button type="submit" class="btn btn-primary">Cari LRA</button>
                <a href="{{ route('laporan.keuangan.cetak', ['start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" class="btn btn-primary ms-2" target="_blank">Cetak LRA</a>
            </div>
        </div>
    </form>
    
    <!-- Bagian Laporan dalam tabel -->
    <table class="table table-bordered mt-4">
        <thead>
            <tr>
                <th style="background-color: #00A6B4; color: white;">URAIAN</th>
                <th style="background-color: #00A6B4; color: white;" class="text-right">Jumlah (Rp)</th>
            </tr>
        </thead>
        
        <tbody>
            <tr>
                <td class="fw-bold">I. PENDAPATAN</td>
                <td style="background-color: #ecf578ff; color: black;" class="text-right fw-bold">{{ number_format($reportData['total_pendapatan'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="padding-left: 20px;">Penerimaan dari BGN</td>
                <td class="text-right">{{ number_format($reportData['pendapatan']['penerimaan_bgn'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="padding-left: 20px;">Penerimaan dari Yayasan</td>
                <td class="text-right">{{ number_format($reportData['pendapatan']['penerimaan_yayasan'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="padding-left: 20px;">Penerimaan dari Pihak Lainnya</td>
                <td class="text-right">{{ number_format($reportData['pendapatan']['penerimaan_pihak_lainnya'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="fw-bold">II. BELANJA</td>
                <td style="background-color: #f87070ff; color: black;" class="text-right fw-bold">{{ number_format($reportData['total_belanja'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="padding-left: 20px;">Belanja Bahan Pangan</td>
                <td class="text-right">{{ number_format($reportData['belanja']['bahan_pangan'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="padding-left: 20px;">Belanja Operasional</td>
                <td class="text-right">{{ number_format($reportData['belanja']['operasional'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="padding-left: 20px;">Belanja Sewa</td>
                <td class="text-right">{{ number_format($reportData['belanja']['sewa'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="fw-bold">III. SURPLUS / DEFISIT</td>
                <td style="background-color: #51f566ff; color: black;" class="text-right fw-bold">{{ number_format($reportData['surplus_defisit'], 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

</body>
</html>
