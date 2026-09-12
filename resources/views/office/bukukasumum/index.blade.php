<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Kas Umum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-size: 14px;
        }
        .table thead th {
            background-color: #00A6B4;
            color: #fff;
            text-align: center;
            vertical-align: middle;
        }
        .table tbody td {
            vertical-align: top;
        }
        .header-title {
            text-align: center;
            margin-bottom: 20px;
        }
        .filter-form {
            margin-bottom: 20px;
        }
        .btn-custom {
            border-radius: 8px;
        }
    </style>
</head>
<body class="container py-4">

    <h3 class="header-title">Buku Kas Umum</h3>

    {{-- Filter Tanggal --}}
    <form method="get" action="{{ route('buku_kas.index') }}" class="row g-2 filter-form">
        <div class="col-md-1 d-flex align-items-center">
            <label class="form-label mb-0"><strong>Periode:</strong></label>
        </div>
        <div class="col-md-3">
            <input type="date" name="start_date" class="form-control" value="{{ $start }}">
        </div>
        <div class="col-md-3">
            <input type="date" name="end_date" class="form-control" value="{{ $end }}">
        </div>
        <div class="col-md-2 d-grid">
            <button type="submit" class="btn btn-primary btn-custom">Cari BKU</button>
        </div>
        <div class="col-md-2 d-grid">
            <a href="{{ route('buku_kas.cetak', ['start_date' => $start, 'end_date' => $end]) }}" 
               target="_blank" class="btn btn-primary btn-custom">Cetak BKU</a>
        </div>
    </form>

    {{-- Tabel Data --}}
    <div class="table-responsive">
        <table class="table table-bordered table-hover table-sm">
            <thead>
                <tr>
                    <th style="width:50px;">No</th>
                    <th style="width:120px;">Tanggal</th>
                    <th style="width:120px;">No. Bukti</th>
                    <th>Uraian</th>
                    <th style="width:150px;">Pemasukan (Debet)</th>
                    <th style="width:150px;">Pengeluaran (Kredit)</th>
                    <th style="width:150px;">Saldo</th>
                </tr>
            </thead>
       <tbody>
@php $saldo = 0; @endphp
@forelse($data as $i => $item)
    @php
        if($item->source == 'po'){
            $debet = 0;
            $kredit = (float) $item->bahan->sum('jumlah_po');
        } else {
            // Kas Kecil: misal jenis_transaksi "Masuk" = debet, "Keluar" = kredit
            if(strtolower($item->jenis_transaksi) == 'masuk'){
                $debet = $item->jumlah;
                $kredit = 0;
            } else {
                $debet = 0;
                $kredit = $item->jumlah;
            }
        }
        $saldo += ($debet - $kredit);
    @endphp
    <tr>
        <td class="text-center">{{ $i+1 }}</td>
        <td>{{ \Carbon\Carbon::parse($item->tanggal_for_sort)->format('d M Y') }}</td>

        <td>{{ $item->source == 'po' ? $item->nomor_po : $item->nomor_transaksi }}</td>
        <td>
            @if($item->source == 'po')
                @foreach($item->bahan as $bahan)
                    {{ $bahan->masterBahan->bahan ?? '' }} ({{ $bahan->jumlah_bahan }} {{ $bahan->satuan }})@if(!$loop->last), @endif
                @endforeach
            @else
                {{ $item->deskripsi }}
            @endif
        </td>
        <td class="text-end">{{ $debet > 0 ? number_format($debet,0,',','.') : '-' }}</td>
        <td class="text-end">{{ $kredit > 0 ? number_format($kredit,0,',','.') : '-' }}</td>
        <td class="text-end">{{ number_format($saldo,0,',','.') }}</td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center text-muted">Tidak ada data pada periode ini</td>
    </tr>
@endforelse
</tbody>

        </table>
    </div>

</body>
</html>
