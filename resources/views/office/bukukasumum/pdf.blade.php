<!DOCTYPE html>
<html>
<head>
    <title>Buku Kas Umum</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        th, td { border: 1px solid #000; padding: 4px; vertical-align: top; }
        th { text-align: center; }
        .meta { border: none; margin-bottom: 15px; font-size: 12px; }
        .meta td { border: none; padding: 2px 6px; }
        .meta td:first-child { width: 150px; font-weight: bold; }
        .ttd { width: 100%; margin-top: 50px; }
        .ttd td { border: none; text-align: center; vertical-align: top; }
        .catatan { margin-top: 40px; font-size: 10px; text-align: left; }
        ol { margin: 0; padding-left: 18px; }
    </style>
</head>

<body>
    <h3 style="text-align:center; margin:0;">BUKU KAS UMUM</h3>
    <p style="text-align:center; margin:0 0 15px 0;">Periode: {{ $start }} s.d. {{ $end }}</p>

    <table class="meta">
        <tr><td>Nama SPPG</td><td>: Yayasan Bina Bangsa 01</td></tr>
        <tr><td>Kelurahan/Desa</td><td>: Sadeng</td></tr>
        <tr><td>Kecamatan</td><td>: Gunungpati</td></tr>
        <tr><td>Kabupaten/Kota</td><td>: Kota Semarang</td></tr>
        <tr><td>Provinsi</td><td>: Jawa Tengah</td></tr>
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
            @php $saldo = 0; @endphp
            @forelse($data as $i => $item)
                @php
                    if($item->source == 'po'){
                        $debet = 0;
                        $kredit = (float) ($item->bahan->sum('jumlah_po') ?? 0);
                    } else {
                        if(strtolower($item->jenis_transaksi ?? '') == 'masuk'){
                            $debet = $item->jumlah ?? 0;
                            $kredit = 0;
                        } else {
                            $debet = 0;
                            $kredit = $item->jumlah ?? 0;
                        }
                    }
                    $saldo += ($debet - $kredit);
                @endphp
                <tr>
                    <td style="text-align:center;">{{ $i+1 }}</td>
                    <td>{{ $item->tanggal_for_sort ? \Carbon\Carbon::parse($item->tanggal_for_sort)->format('d M Y') : '-' }}</td>
                    <td>{{ $item->source == 'po' ? ($item->nomor_po ?? '-') : ($item->nomor_transaksi ?? '-') }}</td>
                    <td>
                        @if($item->source == 'po')
                            @foreach($item->bahan ?? [] as $bahan)
                                {{ $bahan->masterBahan->bahan ?? '' }} 
                                ({{ $bahan->jumlah_bahan ?? 0 }} {{ $bahan->satuan ?? '' }})
                                @if(!$loop->last), @endif
                            @endforeach
                        @else
                            {{ $item->deskripsi ?? '' }}
                        @endif
                    </td>
                    <td style="text-align:right;">{{ $debet > 0 ? number_format($debet,0,',','.') : '-' }}</td>
                    <td style="text-align:right;">{{ $kredit > 0 ? number_format($kredit,0,',','.') : '-' }}</td>
                    <td style="text-align:right;">{{ number_format($saldo,0,',','.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center; color:#888;">Tidak ada data pada periode ini</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="ttd">
        <tr>
            <td style="text-align:left;">
                Mengetahui,<br>
                Kepala SPPG
                <br><br><br><br>
                ....................................
            </td>
            <td style="text-align:right;">
                ................, ............20....<br>
                Akuntansi SPPG
                <br><br><br><br>
                ....................................
            </td>
        </tr>
    </table>

    <div class="catatan">
        <strong>Catatan Penting:</strong>
        <ol>
            <li>Seluruh transaksi uang keluar dan masuk wajib dicatat di BKU.</li>
            <li>Pencatatan secara tertib dengan mengikuti kronologis waktu/keterjadian transaksi dan secara harian.</li>
            <li>Periode adalah periode operasional dapur SPPG selama 2 pekan/minggu.</li>
        </ol>
    </div>
</body>
</html>
