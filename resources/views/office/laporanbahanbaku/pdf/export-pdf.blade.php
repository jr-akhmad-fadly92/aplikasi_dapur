<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 12px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .header h2 {
            margin: 0;
            padding: 10px 0;
            font-size: 18px;
            font-weight: bold;
        }
        
        .filter-info {
            margin-bottom: 15px;
            font-size: 11px;
            color: #666;
        }
        
        .filter-info strong {
            color: #333;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        table, th, td {
            border: 1px solid #333;
        }
        
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
            padding: 8px 5px;
            font-size: 11px;
        }
        
        td {
            padding: 6px 5px;
            text-align: left;
            font-size: 10px;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .summary {
            margin-top: 15px;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
        }
        
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 10px;
            color: #666;
        }
        
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ $title }}</h2>
        @if(count($filterInfo) > 0)
            <div class="filter-info">
                <strong>Filter:</strong> {{ implode(' | ', $filterInfo) }}
            </div>
        @endif
        <div class="filter-info">
            <strong>Tanggal Cetak:</strong> {{ date('d/m/Y H:i:s') }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 12%;">Tanggal</th>
                <th style="width: 15%;">Nomor PO</th>
                <th style="width: 30%;">Nama Bahan</th>
                <th style="width: 18%;">Jenis Bahan</th>
                <th style="width: 20%;">Total Bahan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $row)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($row->tanggal_approve)->format('d/m/Y') }}</td>
                    <td class="text-center">{{ $row->nomor_po ?? '-' }}</td>
                    <td>{{ $row->nama_bahan ?? '-' }}</td>
                    <td class="text-center">{{ $row->jenis_nama }}</td>
                    <td class="text-right">{{ number_format($row->total_bahan, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data yang ditemukan</td>
                </tr>
            @endforelse
        </tbody>
        @if($data->count() > 0)
        <tfoot>
            <tr style="background-color: #f2f2f2; font-weight: bold;">
                <td colspan="5" class="text-right"><strong>Total Keseluruhan:</strong></td>
                <td class="text-right"><strong>{{ number_format($data->sum('total_bahan'), 0, ',', '.') }}</strong></td>
            </tr>
        </tfoot>
        @endif
    </table>

    @if($data->count() > 0)
    <div class="summary">
        <strong>Ringkasan:</strong><br>
        - Total Jenis Bahan: {{ $data->count() }} item<br>
        - Total Keseluruhan Bahan: {{ number_format($data->sum('total_bahan'), 0, ',', '.') }}
    </div>
    @endif

    <div class="footer">
        <p>Laporan ini digenerate secara otomatis oleh sistem pada {{ date('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
