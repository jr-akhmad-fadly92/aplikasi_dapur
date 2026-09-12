<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COUNTER PAX</title>
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
                COUNTER PAX
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
        </tr>
    </table>

    <table class="no-border" style="margin-bottom: 10px;width: 100%;">
        <tr>
            <td style="width: 55%;"></td>
            <td style="width: 45%; line-height: 2.2;">
                <strong>Tanggal:</strong> {{ strtolower(\Carbon\Carbon::parse($tanggal)->locale('id')->translatedFormat('l, d F Y')) }}
            </td>
        </tr>
    </table>

    <table style="margin-top:20px;">
        <tr>
            <th style="text-align: center; width: 5%;">No</th>
            <th style="text-align: center; width: 25%;">Nama Sekolah</th>
            <th style="text-align: center; width: 8%;">A</th>
            <th style="text-align: center; width: 8%;">B</th>
            <th style="text-align: center; width: 8%;">Total</th>
            <th style="text-align: center; width: 46%;">Counter Ompreng</th>
        </tr>
        @forelse($data as $index => $row)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>{{ $row->nama_sekolah }}</td>
                <td style="text-align: right;">{{ number_format($row->jumlah_penerima_a, 0, ',', '.') }}</td>
                <td style="text-align: right;">{{ number_format($row->jumlah_penerima_b, 0, ',', '.') }}</td>
                <td style="text-align: right;">{{ number_format($row->jumlah_penerima_total, 0, ',', '.') }}</td>
                <td style="min-height: 55px; height: 55px; width: 46%;"></td>
            </tr>
        @empty
            <tr>
                <td colspan="6" style="text-align: center;">Tidak ada data pada tanggal terpilih.</td>
            </tr>
        @endforelse

        <tr>
            <th colspan="2" style="text-align: center;">Total</th>
            <th style="text-align: right;">{{ number_format($totals['a'], 0, ',', '.') }}</th>
            <th style="text-align: right;">{{ number_format($totals['b'], 0, ',', '.') }}</th>
            <th style="text-align: right;">{{ number_format($totals['total'], 0, ',', '.') }}</th>
            <th></th>
        </tr>
    </table>

    <div class="footer">
        <table style="font-size: 13px;margin-top:10px;" class="no-border">
            <tr>
                <td style="text-align: center;">Ahli Akuntan</td>
                <td style="text-align: center;">Kepala Dapur</td>
            </tr>
            <tr>
                <td style="height: 30px"></td>
                <td style="text-align: center;"></td>
            </tr>
            <tr>
                <td style="text-align: center;">{{ $dapur->ahli_akuntan ?? '-' }}</td>
                <td style="text-align: center;">{{ $dapur->kepala_dapur ?? '-' }}</td>
            </tr>
        </table>
    </div>
</div>

</body>

</html>
