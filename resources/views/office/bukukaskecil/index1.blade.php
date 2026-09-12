<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Kas Kecil PO</title>
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

    <h3 class="header-title">Buku Kas Kecil PO</h3>

    {{-- Filter Tanggal --}}
    <form method="get" action="{{ route('bkk1_po.index') }}" class="row g-2 filter-form">
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
            <button type="submit" class="btn btn-primary btn-custom">Cari BKK PO</button>
        </div>
        <div class="col-md-2 d-grid">
            <a href="{{ route('bkk1_po.cetak', ['start_date' => $start, 'end_date' => $end]) }}" 
               target="_blank" class="btn btn-primary btn-custom">Cetak BKK PO</a>
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
                @php 
                    $saldo = 0; 
                @endphp

                @foreach($data as $i => $po)
                    @php
                        // total pengeluaran per transaksi
                            $pengeluaran = $po->bahan->sum('jumlah_po'); 

                        // pendapatan kita lewatin dulu
                            $pendapatan = 0;

                        // hitung saldo kumulatif
                            $saldo += ($pendapatan - $pengeluaran);
                    @endphp
                
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($po->tanggal_approve)->format('d M Y') }}</td>
                        <td>{{ $po->nomor_po }}</td>
                        <td>
                            @foreach($po->bahan as $b)
                                {{ $b->masterBahan->bahan ?? '' }} ({{ $b->jumlah_bahan }} {{ $b->satuan }})@if(!$loop->last), @endif
                            @endforeach
                        </td>
                        <td class="text-end">
                            {{-- pendapatan kosong dulu --}}
                        </td>
                        <td class="text-end">
                            {{ number_format($pengeluaran, 0, ',', '.') }}
                        </td>
                        <td class="text-end">
                            {{ number_format($saldo, 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

</body>
</html>
