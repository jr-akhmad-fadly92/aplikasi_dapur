<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penggunaan Dana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: sans-serif; font-size: 13px; }
        .container { width: 95%; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 20px; }
        table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #fff;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #f8eaeae7;
            padding: 6px;
            text-align: center;
            vertical-align: middle;
        }
        .fw-bold { font-weight: bold; }
    </style>
</head>

<body>
<div class="container">
    <div class="header">
        <h4 class="fw-bold">LAPORAN PENGGUNAAN DANA</h4>
        <h5>REKAPITULASI BULANAN</h5>
    </div>

    <!-- Filter Bulan & Tahun -->
    <div class="card mb-3">
        <div class="card-body">
            <form action="{{ route('lpd2m.index') }}" method="GET" class="row g-3 align-items-end">

                <!-- Dropdown Bulan -->
                <div class="col-md-3">
                    <label for="bulan" class="form-label fw-bold">Bulan</label>
                    <select name="bulan" id="bulan" class="form-select">
                        <option value="">-- Pilih Bulan --</option>
                        @foreach ([
                            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
                            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
                            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                        ] as $num => $nama)
                            <option value="{{ $num }}" {{ request('bulan') == $num ? 'selected' : '' }}>
                                {{ $nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Dropdown Tahun -->
                <div class="col-md-3">
                    <label for="tahun" class="form-label fw-bold">Tahun</label>
                    <select name="tahun" id="tahun" class="form-select">
                        <option value="">-- Pilih Tahun --</option>
                        @for ($y = date('Y'); $y >= 2020; $y--)
                            <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                </div>

                <!-- Tombol -->
                <div class="col-md-auto d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Cari LPD2M</button>
                    @if(request('bulan') && request('tahun'))
                        <a href="{{ route('lpd2mpdf.cetak', ['bulan' => request('bulan'), 'tahun' => request('tahun')]) }}"
                           class="btn btn-primary" target="_blank">Cetak LPD2M</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Laporan -->
    @if($reportData)
        <table>
            <thead>
                <tr>
                    <th style="background-color: #00A6B4; color: #fff;" rowspan="2">No</th>
                    <th style="background-color: #00A6B4; color: #fff;" rowspan="2">Saldo Awal</th>
                    <th style="background-color: #00A6B4; color: #fff;" rowspan="2">Penerimaan Dana</th>
                    <th style="background-color: #00A6B4; color: #fff;" colspan="4">Pengeluaran Dana</th>
                    <th style="background-color: #00A6B4; color: #fff;" rowspan="2">Saldo Akhir</th>
                </tr>
                <tr>
                    <th style="background-color: #00A6B4; color: #fff;">Bahan Pangan</th>
                    <th style="background-color: #00A6B4; color: #fff;">Operasional</th>
                    <th style="background-color: #00A6B4; color: #fff;">Sewa</th>
                    <th style="background-color: #00A6B4; color: #fff;">Total</th>
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
    @else
        <div class="alert alert-warning mt-3">
            <strong>Data tidak ada</strong> untuk periode yang dipilih.
        </div>
    @endif
</div>

</body>
</html>
