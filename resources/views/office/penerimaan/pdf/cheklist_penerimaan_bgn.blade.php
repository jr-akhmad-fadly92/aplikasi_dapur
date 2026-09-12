<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checklist Penerimaan BGN</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 14px;
        }

        body.landscape {
            font-size: 10px;
        }

        .container {
            width: 100%;
            margin: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .checklist-table {
            table-layout: fixed;
        }

        th,
        td {
            border: 1px solid black;
            text-align: left;
            padding: 4px;
            vertical-align: middle;
        }

        .no-border td {
            border: none;
            padding: 2px;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .row-fixed td {
            height: 19px;
        }

        .small-cell {
            padding: 0;
            text-align: center;
        }

        .page-break {
            page-break-after: always;
        }

        .scan-down {
            padding-top: 8px;
            padding-bottom: 1px;
        }
    </style>
</head>

<body class="{{ ($orientation ?? 'portrait') === 'landscape' ? 'landscape' : '' }}">
    <div class="container">
        <table class="no-border" style="margin-bottom: 10px;">
            <tr>
                <td style="text-align: center;width: 30%;">
                    <img src="{{ public_path('image/logo.png') }}" alt="Logo" style="width: 85px; height: auto;">
                </td>
                <td style="text-align: right;width: 70%; font-weight: bold; font-size: 22px; padding-top: 8px;">
                    CHECKLIST PENERIMAAN 
                </td>
            </tr>
            <tr>
                <td style="text-align: center; font-weight: bold; font-size: 13px;">
                    {{ $dapur->nama_dapur ?? '-' }}
                </td>
                <td></td>
            </tr>
            <tr>
                <td style="text-align: center;">
                    {{ ($dapur->kecamatan ?? '-') }} - {{ ($dapur->kota ?? '-') }}
                </td>
                <td></td>
            </tr>
        </table>

        <table class="no-border" style="margin-bottom: 8px; width: 100%;">
            <tr>
                <td style="width: 55%;"></td>
                <td style="width: 45%; line-height: 1.8;">
                    <strong>Tanggal Checklist :</strong>
                    {{ !empty($tanggalChecklist) ? strtolower(\Carbon\Carbon::parse($tanggalChecklist)->locale('id')->translatedFormat('l, d F Y')) : '-' }}
                </td>
            </tr>
        </table>

        @php
            $items = $rincian_po->values();
            $itemChunks = $items->chunk(20);
            if ($itemChunks->isEmpty()) {
                $itemChunks = collect([collect()]);
            }
            $globalCounter = 0;
        @endphp

        @foreach ($itemChunks as $pageIndex => $chunk)
            <table class="checklist-table">
                <tr>
                    <th class="center" rowspan="2" style="width: 3%;">No</th>
                    <th class="center" rowspan="2" style="width: 20%;">Nama Pesanan</th>
                    <th class="center" rowspan="2" style="width: 6%;">Qty</th>
                    <th class="center" rowspan="2" style="width: 6%;">Satuan</th>
                    <th class="center" rowspan="2" style="width: 8%;">Waktu Kedatangan</th>
                    <th class="center" rowspan="2" style="width: 22%;">Keterangan</th>
                    <th class="center" colspan="3" style="width: 13%;">Pemeriksaan</th>
                    <th class="center" colspan="2" style="width: 8%;">Jumlah</th>
                    <th class="center" colspan="2" style="width: 14%;">Remark</th>
                </tr>
                <tr>
                    <th class="center" style="width: 4.33%;">Visual</th>
                    <th class="center" style="width: 4.33%;">Bau</th>
                    <th class="center" style="width: 4.34%;">Textur</th>
                    <th class="center" style="width: 4%;">Diterima</th>
                    <th class="center" style="width: 4%;">Ditolak</th>
                    <th class="center scan-down" style="width: 7%;">Scan</th>
                    <th class="center" style="width: 7%;">Kirim Gudang</th>
                </tr>

                @foreach ($chunk as $item)
                    @php
                        $globalCounter++;
                        $nomorUrut = $globalCounter;
                    @endphp
                    <tr class="row-fixed">
                        <td class="center">{{ $nomorUrut }}</td>
                        <td>{{ $item->nama_pesanan ?? '' }}</td>
                        <td class="right">{{ isset($item->qty) ? number_format($item->qty, 0, ',', '.') : '' }}</td>
                        <td class="center">{{ $item->satuan ?? '' }}</td>
                        <td class="center">
                            {{ !empty($item->waktu_kedatangan) ? \Carbon\Carbon::parse($item->waktu_kedatangan)->format('H:i') : '' }}
                        </td>
                        <td>-</td>
                        <td class="small-cell"></td>
                        <td class="small-cell"></td>
                        <td class="small-cell"></td>
                        <td class="center"></td>
                        <td class="center"></td>
                        <td class="small-cell"></td>
                        <td class="small-cell"></td>
                    </tr>
                @endforeach

                @for ($emptyRow = $chunk->count(); $emptyRow < 20; $emptyRow++)
                    <tr class="row-fixed">
                        <td class="center"></td>
                        <td></td>
                        <td class="right"></td>
                        <td class="center"></td>
                        <td class="center"></td>
                        <td></td>
                        <td class="small-cell"></td>
                        <td class="small-cell"></td>
                        <td class="small-cell"></td>
                        <td class="center"></td>
                        <td class="center"></td>
                        <td class="small-cell"></td>
                        <td class="small-cell"></td>
                    </tr>
                @endfor
            </table>

            @if (!$loop->last)
                <div class="page-break"></div>
            @endif
        @endforeach

        <table class="no-border" style="margin-top: 12px; font-size: 12px;">
            <tr>
                <td class="center" style="width: 33.33%;">Admin Penerimaan</td>
                <td class="center" style="width: 33.33%;">Pengirim</td>
                <td class="center" style="width: 33.33%;">Ahli Akuntan</td>
            </tr>
            <tr>
                <td style="height: 42px;"></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td class="center"></td>
                <td class="center"></td>
                <td class="center">{{ $dapur->ahli_akuntan ?? '-' }}</td>
            </tr>
        </table>
    </div>
</body>

</html>
