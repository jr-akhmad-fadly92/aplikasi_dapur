<!DOCTYPE html>
<html>
<head>
    <title>Laporan Biaya Bahan Baku</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        h2, p { text-align: center; margin: 0; }
        p { margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 4px; vertical-align: middle; }
        th { text-align: center;}
        
        /* Tabel Header Info */
        .meta { border: none; margin-bottom: 15px; font-size: 11px; }
        .meta td { border: none; padding: 2px 0px; }
        .meta td:first-child { width: 100px; font-weight: bold; }

        /* Tabel Data Utama */
        /* .data-table tfoot th { background-color: #f2f2f2; } */
        
        /* Tabel Tanda Tangan */
        .ttd { width: 100%; margin-top: 40px; }
        .ttd td { border: none; text-align: center; vertical-align: top; }

        /* Catatan Kaki */
        .catatan { margin-top: 30px; font-size: 9px; text-align: left; }
        ol { margin: 0; padding-left: 15px; }

        /* Badge untuk status */
        .badge {
            display: inline-block;
            padding: 3px 6px;
            font-size: 9px;
            font-weight: bold;
            color: #fff;
            border-radius: 4px;
        }
        .badge-success { background-color: #28a745; }
        .badge-warning { background-color: #dc3545; }
    </style>
</head>

<body>
    {{ $data || '-----------------' }}
    <h2>Laporan Biaya Bahan Baku</h2>
        @if(isset($start) && isset($end) && $start && $end)
            <p>
                Periode: {{ \Carbon\Carbon::parse($start)->format('d-m-Y') }} s/d {{ \Carbon\Carbon::parse($end)->format('d-m-Y') }}
            </p>
        @endif

        <table class="meta">
            <tr><td>Nama SPPG</td><td>: Yayasan Bina Bangsa 02</td></tr>
            <tr><td>Kelurahan/Desa</td><td>: Sukatani</td></tr>
            <tr><td>Kecamatan</td><td>: Sukatani</td></tr>
            <tr><td>Kabupaten/Kota</td><td>: Purwakarta</td></tr>
            <tr><td>Provinsi</td><td>: Jawa Barat</td></tr>
        </table>
        
        <table class="data-table">
            <thead>
                <tr>
                    <th width="5%">No.</th>
                    <th width="15%">Tanggal</th>
                    <th width="40%">Uraian</th>
                    <th width="25%">Nominal (Rp)</th>
                    <th width="15%">Keterangan</th>
                </tr>
            </thead>
            
            <tbody>
                @forelse ($data as $row)
                    <tr>
                        <td style="text-align:center;">1</td>
                        <td style="text-align:center;">{{ \Carbon\Carbon::parse($row->tanggal_tutup_po)->format('d-m-Y') }}</td>
                        <td>{{ $row->deskripsi }}</td>
                        <td style="text-align:right;">{{ number_format($row->jumlah, 0, ',', '.') }}</td>
                        <td style="text-align:center;">
                            @if($row->status == 1)
                                ACC
                            @else
                                Revisi
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center;">Tidak ada data pada periode ini</td>
                    </tr>
                @endforelse
            </tbody>
        
            @if($data->count() > 0)
                <tfoot>
                    <tr>
                        <th colspan="3" style="text-align:right; font-weight:bold;">TOTAL</th>
                        <th style="text-align:right; font-weight:bold;">{{ number_format($total, 0, ',', '.') }}</th>
                        <th></th>
                    </tr>
                </tfoot>
            @endif
        </table>

        <table class="ttd">
            <tr>
                <td style="text-align:left; width: 30%;">
                    Mengetahui,<br>
                    Kepala SPPG
                    <br><br><br><br>
                    (....................................)
                </td>
		<td style="text-align:center; width: 40%;">
                    <br>
                    Asisten Lapangan
                    <br><br><br><br>
                    (....................................)
                </td>

                <td style="text-align:right; width: 30%;">
                    ................, ............20.......<br>
                    Akuntansi SPPG
                    <br><br><br><br>
                    (....................................)
                </td>
            </tr>
        </table>

        <div class="catatan">
            <strong>Catatan Penting:</strong>
                <ol>
                    <li>Seluruh transaksi uang keluar dan masuk wajib dicatat di LBB.</li>
                    <li>Pencatatan secara tertib dengan mengikuti kronologis waktu/keterjadian transaksi dan secara harian.</li>
                    <li>Periode adalah periode operasional dapur SPPG selama 2 pekan/minggu.</li>
                </ol>
        </div>

</body>
</html>