<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Laporan PO Pembelian</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            margin: 20px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 7.5px;
            border-bottom: 2px solid #333;
            padding-bottom: 7.5px;
        }

        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }

        .header h2 {
            margin: 15px 0 0 0;
            font-size: 14px;
            font-weight: normal;
            color: #666;
        }

        .period-info {
            text-align: center;
            margin: 7.5px 0 7.5px 0;
            font-size: 12px;
            color: #666;
        }

        .warning-box {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 10px;
            margin-bottom: 15px;
            text-align: center;
            font-weight: bold;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 7.5px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-align: center;
            color: #333;
        }

        td.number {
            text-align: center;
        }

        td.currency {
            text-align: right;
        }

        .total-row {
            background-color: #e9ecef;
            font-weight: bold;
        }

        .total-row td {
            border-top: 2px solid #333;
        }

        .footer {
            margin-top: 7.5px;
            text-align: right;
            font-size: 12px;
            color: #666;
        }

        .no-data {
            text-align: center;
            color: #666;
            font-style: italic;
        }

        /* Untuk menghindari page break di tengah row */
        tr {
            page-break-inside: avoid;
        }

        /* Header tetap muncul di setiap halaman */
        thead {
            display: table-header-group;
        }

        tfoot {
            display: table-footer-group;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <h1>LAPORAN PO PEMBELIAN</h1>
        <h1>Dapur Bartec</h1>
    </div>

    <!-- Period Information -->
    <div class="period-info">
        Periode: {{ $startDate }} sampai {{ $endDate }}
        <br>
        Dicetak pada: {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}
    </div>

    <!-- Warning untuk data terbatas -->
    @if(isset($isLimited) && $isLimited)
        <div class="warning-box">
            PERHATIAN: Data terbatas pada {{ number_format($maxRecords, 0, ',', '.') }} record terbaru
            dari total {{ number_format($totalRecords, 0, ',', '.') }} record untuk mengoptimalkan performa
        </div>
    @endif

    <!-- Tabel Data -->
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="12%">Tanggal</th>
                <th width="25%">Nomor PO</th>
                <th width="28%">Bahan</th>
                <th width="12%">Jumlah</th>
                <th width="18%">Total Harga</th>
            </tr>
        </thead>
        <tbody>
            @php
                $no = 1;
            @endphp
            @forelse($data as $row)
                <tr>
                    <td class="number">{{ $no++ }}</td>
                    <td class="number">
                        {{ $row->tanggal_approve ? \Carbon\Carbon::parse($row->tanggal_approve)->format('d/m/Y') : '-' }}
                    </td>
                    <td>{{ $row->nomor_po ?? '-' }}</td>
                    <td>{{ $row->bahan ?? '-' }}</td>
                    <td class="number">{{ number_format($row->total_jumlah_bahan ?? 0, 0, ',', '.') }}</td>
                    <td class="currency">Rp {{ number_format($row->jumlah_po ?? 0, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="no-data">Tidak ada data untuk periode yang dipilih</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5" style="text-align: right; font-weight: bold;">TOTAL:</td>
                <td class="currency" style="font-weight: bold;">Rp {{ number_format($totalHarga ?? 0, 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>

    <!-- Summary Info -->
    <div style="margin-top: 7.5px; font-size: 12px; color: #666;">
        <p><strong>Ringkasan:</strong></p>
        <ul style="margin: 5px 0; padding-left: 20px;">
            <li>Total Record: {{ number_format($data->count(), 0, ',', '.') }} data</li>
            <li>Total Nilai: Rp {{ number_format($totalHarga ?? 0, 0, ',', '.') }}</li>
            <li>Periode: {{ $startDate }} - {{ $endDate }}</li>
        </ul>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh sistem</p>
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>

</html>