<!DOCTYPE html>
<html>
<head>
    <title>Buku Kas Kecil PO</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        th, td { border: 1px solid #000; padding: 2px; } 
        .meta { 
            border: none; 
            margin-bottom: 15px; 
            font-size: 12px;
        }
        .meta td { 
            border: none; 
            padding: 2px 6px; 
        }
        .meta td:first-child { 
            width: 150px; 
            font-weight: bold;
        }
    </style>
</head>

<body>
    <h3 style="text-align:center; margin:0;">BUKU KAS KECIL PO</h3>
    <p style="text-align:center; margin:0 0 15px 0;">Periode: {{ $start }} s.d. {{ $end }}</p>

    <table class="meta">
        <tr>
            <td>Nama SPPG</td>
            <td>: Yayasan Bina Bangsa 01</td>
        </tr>
        <tr>
            <td>Kelurahan/Desa</td>
            <td>: Sadeng</td>
        </tr>
        <tr>
            <td>Kecamatan</td>
            <td>: Gunungpati</td>
        </tr>
        <tr>
            <td>Kabupaten/Kota</td>
            <td>: Kota Semarang</td>
        </tr>
        <tr>
            <td>Provinsi</td>
            <td>: Jawa Tengah</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>No. Bukti</th>
                <th>Uraian</th>
                <th>Pemasukan (Debet)</th>
                <th>Pengeluaran (Kredit)</th>
                <th>Saldo</th>
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

        <br><br><br>
        <div style="width:100%; font-size:11px;">
            <div style="float:left; width:50%;">
                Mengetahui,<br>
                Kepala SPPG
                <br><br><br><br>
                ....................................
            </div>
            <div style="float:right; width:50%; text-align:right;">
                ................, ........20....<br>
                Akuntansi SPPG
                <br><br><br><br>
                ....................................
            </div>
        </div>

        <br style="clear:both;"><br>

        <div style="font-size:10px; margin-top:40px;">
            <strong>Catatan Penting:</strong>
                <ol style="margin:0; padding-left:15px;">
                    <li>Seluruh transaksi uang keluar dan masuk wajib ditercatat di BKU.</li>
                    <li>Pencantatan secara tertib dengan mengikuti kronologis waktu/keterjadian transaksi dan secara harian.</li>
                    <li>Preode adalah preode opreasional dapur SPPG selama 2 pekan/minggu.</li>
                </ol>
        </div>
</body>
</html>
