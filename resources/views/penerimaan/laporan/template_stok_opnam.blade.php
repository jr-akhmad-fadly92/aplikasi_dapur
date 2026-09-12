<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STOK OPNAM</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin: 20px;
        }

        .container {
            width: 100%;
            margin: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 2px;
        }

        th,
        td {
            border: 1px solid black;
            text-align: left;
            padding: 5px;
        }

        .no-border td {
            border: none;
            padding: 2px;
        }
    </style>
</head>

<body>
    <div class="container">
        <table class="no-border" style="margin-bottom: 20px;">
            <tr>
                <td style="text-align: center;width: 30%;">
                    <img src="{{ public_path('image/logo.png') }}" alt="Logo" style="width: 100px; height: auto;">
                </td>
                <td style="text-align: right;width: 70%; font-weight: bold; font-size: 24px; padding-top: 10px;">
                    STOK OPNAM
                </td>
            </tr>
            <tr>
                <td style="text-align: center; font-weight: bold; font-size: 16px;">
                    {{ $dapur->nama_dapur ?? '-' }}
                </td>
                <td></td>
            </tr>
            <tr>
                <td style="text-align: center;">
                    {{ $dapur->kecamatan ?? '-' }} - {{ $dapur->kota ?? '-' }}
                </td>
                <td></td>
            </tr>
        </table>

        <table class="no-border" style="margin-bottom: 10px;width: 100%;">
            <tr>
                <td style="width: 55%;"></td>
                <td style="width: 45%; vertical-align: top; line-height: 2.2;">
                    <strong>Tanggal Stok Opnam :</strong>
                    {{ strtolower(\Carbon\Carbon::parse($tanggal)->locale('id')->translatedFormat('l, d F Y')) }}
                </td>
            </tr>
        </table>

        <table style="margin-top:20px;">
            <tr>
                <th style="text-align: center;">No</th>
                <th style="text-align: center;">Nama Barang</th>
                <th style="text-align: center;">Jumlah Awal</th>
                <th style="text-align: center;">Jumlah Keluar</th>
                <th style="text-align: center;">Jumlah Akhir</th>
            </tr>
            @php
                $totalAwal = 0;
                $totalKeluar = 0;
                $totalAkhir = 0;
            @endphp
            @forelse($stokOpnam as $row)
                <tr>
                    <td style="text-align: center;">{{ $row->nomor_urut }}</td>
                    <td>{{ $row->nama_barang ?? '-' }}</td>
                    <td style="text-align: right;">{{ number_format($row->jumlah_awal, 0, ',', '.') }}</td>
                    <td style="text-align: right;">{{ number_format($row->jumlah_keluar, 0, ',', '.') }}</td>
                    <td style="text-align: right;">{{ number_format($row->jumlah_akhir, 0, ',', '.') }}</td>
                </tr>
                @php
                    $totalAwal += $row->jumlah_awal;
                    $totalKeluar += $row->jumlah_keluar;
                    $totalAkhir += $row->jumlah_akhir;
                @endphp
            @empty
                <tr>
                    <td colspan="5" style="text-align: center;">Tidak ada data</td>
                </tr>
            @endforelse
            <tr>
                <th style="text-align: center;" colspan="2">Total</th>
                <th style="text-align: right;">{{ number_format($totalAwal, 0, ',', '.') }}</th>
                <th style="text-align: right;">{{ number_format($totalKeluar, 0, ',', '.') }}</th>
                <th style="text-align: right;">{{ number_format($totalAkhir, 0, ',', '.') }}</th>
            </tr>
        </table>
    </div>
</body>

</html>
