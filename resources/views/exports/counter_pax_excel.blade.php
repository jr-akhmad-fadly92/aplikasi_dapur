<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Counter Pax</title>
</head>
<body>
<table style="width:100%; border-collapse:collapse; margin-bottom:10px;">
    <tr>
        <td colspan="6" style="text-align:center; font-weight:bold; font-size:16px; border:1px solid #000;">COUNTER PAX</td>
    </tr>
    <tr>
        <td colspan="6" style="text-align:center; border:1px solid #000;">{{ $dapur->nama_dapur ?? '-' }}</td>
    </tr>
    <tr>
        <td colspan="6" style="text-align:center; border:1px solid #000;">Tanggal: {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}</td>
    </tr>
</table>

<table style="width:100%; border-collapse:collapse;">
    <thead>
        <tr>
            <th style="border:1px solid #000; text-align:center;">No</th>
            <th style="border:1px solid #000; text-align:center;">Nama Sekolah</th>
            <th style="border:1px solid #000; text-align:center;">A</th>
            <th style="border:1px solid #000; text-align:center;">B</th>
            <th style="border:1px solid #000; text-align:center;">Total</th>
            <th style="border:1px solid #000; text-align:center; width:400px;">Counter Ompreng</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data as $index => $row)
            <tr style="height:50px;">
                <td style="border:1px solid #000; text-align:center;">{{ $index + 1 }}</td>
                <td style="border:1px solid #000;">{{ $row->nama_sekolah }}</td>
                <td style="border:1px solid #000; text-align:right;">{{ number_format($row->jumlah_penerima_a, 0, ',', '.') }}</td>
                <td style="border:1px solid #000; text-align:right;">{{ number_format($row->jumlah_penerima_b, 0, ',', '.') }}</td>
                <td style="border:1px solid #000; text-align:right;">{{ number_format($row->jumlah_penerima_total, 0, ',', '.') }}</td>
                <td style="border:1px solid #000;"></td>
            </tr>
        @empty
            <tr>
                <td colspan="6" style="border:1px solid #000; text-align:center;">Tidak ada data.</td>
            </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <th colspan="2" style="border:1px solid #000; text-align:center;">Total</th>
            <th style="border:1px solid #000; text-align:right;">{{ number_format($totals['a'], 0, ',', '.') }}</th>
            <th style="border:1px solid #000; text-align:right;">{{ number_format($totals['b'], 0, ',', '.') }}</th>
            <th style="border:1px solid #000; text-align:right;">{{ number_format($totals['total'], 0, ',', '.') }}</th>
            <th style="border:1px solid #000;"></th>
        </tr>
    </tfoot>
</table>
</body>
</html>
