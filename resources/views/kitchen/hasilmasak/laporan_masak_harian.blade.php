<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Masak Harian</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            margin: 14px;
            color: #000;
        }

        .container {
            width: 100%;
            margin: auto;
        }

        .menu-page {
            page-break-after: always;
            padding: 12px;
        }

        .menu-page:last-child {
            page-break-after: auto;
        }

        h2 {
            text-align: center;
            margin: 0;
            padding: 4px 0;
        }

        h4 {
            margin: 6px 0 2px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            page-break-inside: auto;
        }

        thead {
            display: table-header-group;
        }

        tfoot {
            display: table-row-group;
        }

        td, th {
            border: 1px solid #000;
            padding: 3px 5px;
            text-align: left;
            page-break-inside: avoid;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        tbody {
            page-break-inside: auto;
            page-break-after: auto;
        }

        .komponen-group {
            page-break-inside: avoid;
        }

        .table-block {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .no-border td {
            border: none;
            padding: 1px 3px;
        }

        .th-center {
            text-align: center;
        }

        .td-right {
            text-align: right;
        }

        .td-center {
            text-align: center;
        }

        .section-break {
            margin-top: 18px;
        }

        .bold {
            font-weight: bold;
        }

        .header-title {
            font-size: 20px;
            font-weight: bold;
            text-align: right;
        }

        .komponen-header {
            background-color: #d9d9d9;
            font-weight: bold;
        }

        .total-row {
            font-weight: bold;
            background-color: #f0f0f0;
        }

        .sub-header {
            font-size: 13px;
            font-weight: bold;
            margin: 8px 0 3px 0;
        }

        .sign-table {
            width: 100%;
            margin-top: 30px;
        }

        .sign-table td {
            border: none;
            text-align: center;
            padding: 4px;
        }
    </style>
</head>
<body>
    @forelse ($dataPerMenu as $item)
        @php $menu = $item['menu']; @endphp
        <div class="menu-page">
        <div class="container">
            <table class="no-border table-block" style="margin-bottom: 16px;">
                <tr>
                    <td style="text-align: center; width: 25%;">
                        <img src="{{ public_path('image/logo.png') }}" alt="Logo" style="width: 90px; height: auto;">
                    </td>
                    <td style="width: 75%;" class="header-title">
                        LAPORAN MASAK HARIAN
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center; font-weight: bold; font-size: 14px;">
                        {{ $dapur->nama_dapur ?? '' }}
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <td style="text-align: center; font-size: 12px;">
                        {{ $dapur->kecamatan ?? '' }} - {{ $dapur->kota ?? '' }}
                    </td>
                    <td></td>
                </tr>
            </table>

            <hr style="border: 1px solid #000; margin: 6px 0 10px 0;">

            {{-- Info Paket Menu --}}
            <table class="no-border table-block" style="margin-bottom: 6px;">
                <tr>
                    <td class="bold" style="width: 30%;">Paket Menu</td>
                    <td style="width: 2%;">:</td>
                    <td>{{ $menu->menu ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="bold">Jam Mulai Masak</td>
                    <td>:</td>
                    <td>
                        @if($item['jamMulai'])
                            {{ \Carbon\Carbon::parse($item['jamMulai'])->format('H:i') }} WIB
                        @else
                            <em>-</em>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="bold">Jumlah Porsi</td>
                    <td>:</td>
                    <td>{{ number_format($item['jumlahPorsi'] ?? 0, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="bold">Karbohidrat</td>
                    <td>:</td>
                    <td>{{ $menu->nama_karbohidrat }}</td>
                </tr>
                <tr>
                    <td class="bold">Protein / Lauk</td>
                    <td>:</td>
                    <td>{{ $menu->nama_protein }}</td>
                </tr>
                <tr>
                    <td class="bold">Sayur</td>
                    <td>:</td>
                    <td>{{ $menu->nama_sayur }}</td>
                </tr>
                <tr>
                    <td class="bold">Buah</td>
                    <td>:</td>
                    <td>{{ $menu->nama_buah }}</td>
                </tr>
                <tr>
                    <td class="bold">Susu / Pendamping</td>
                    <td>:</td>
                    <td>{{ $menu->nama_susu }}</td>
                </tr>
            </table>

            {{-- Tabel Hasil Masak per Komponen --}}
            <table style="margin-top: 6px;">
                <thead>
                    <tr>
                        <th class="th-center" style="width: 5%;">No</th>
                        <th class="th-center" style="width: 22%;">Komponen</th>
                        <th class="th-center" style="width: 28%;">Nama</th>
                        <th class="th-center" style="width: 10%;">No. Input</th>
                        <th class="th-center" style="width: 13%;">Berat / Qty</th>
                        <th class="th-center" style="width: 10%;">Satuan</th>
                        <th class="th-center" style="width: 12%;">Waktu Jadi</th>
                    </tr>
                </thead>
                @php $nomor = 1; @endphp
                @foreach ($item['perKomponen'] as $k => $komp)
                    @php
                        $satuanAsli = trim((string) ($komp['satuan'] ?? '-'));
                        $labelKomponen = strtolower((string) ($komp['label'] ?? ''));
                        $isLaukAtauPendamping =
                            (stripos($labelKomponen, 'protein') !== false) ||
                            (stripos($labelKomponen, 'lauk') !== false) ||
                            (stripos($labelKomponen, 'susu') !== false) ||
                            (stripos($labelKomponen, 'pendamping') !== false);

                        $satuanTampil = $satuanAsli;
                        if ($isLaukAtauPendamping && $satuanAsli !== '-') {
                            $satuanTampil = $satuanAsli . ' / gram';
                        }
                    @endphp
                    <tbody class="komponen-group">
                            @if ($komp['entries']->count() > 0)
                                @foreach ($komp['entries'] as $idx => $entry)
                                    <tr>
                                        <td class="td-center">{{ $idx === 0 ? $nomor : '' }}</td>
                                        <td>{{ $idx === 0 ? $komp['label'] : '' }}</td>
                                        <td>{{ $idx === 0 ? $komp['nama'] : '' }}</td>
                                        <td class="td-center">{{ $idx + 1 }}</td>
                                        <td class="td-right">{{ number_format($entry->jumlah, 0, ',', '.') }}</td>
                                        <td class="td-center">{{ $satuanTampil }}</td>
                                        <td class="td-center">{{ \Carbon\Carbon::parse($entry->created_at)->format('H:i') }}</td>
                                    </tr>
                                @endforeach
                                {{-- Baris total per komponen --}}
                                <tr class="total-row">
                                    <td></td>
                                    <td></td>
                                    <td class="td-right bold">Total</td>
                                    <td class="td-center bold">{{ $komp['entries']->count() }}</td>
                                    <td class="td-right bold">{{ number_format($komp['total'], 0, ',', '.') }}</td>
                                    <td class="td-center bold">{{ $satuanTampil }}</td>
                                    <td></td>
                                </tr>
                            @else
                                <tr>
                                    <td class="td-center">{{ $nomor }}</td>
                                    <td>{{ $komp['label'] }}</td>
                                    <td>{{ $komp['nama'] }}</td>
                                    <td class="td-center" colspan="4" style="text-align:center; color:#888;"><em>Belum diinput</em></td>
                                </tr>
                            @endif
                    </tbody>
                    @php $nomor++; @endphp
                @endforeach
            </table>

            {{-- Ringkasan Global --}}
            <div class="sub-header" style="margin-top: 12px;">Ringkasan Total per Komponen</div>
            <table class="table-block">
                <thead>
                    <tr>
                        <th class="th-center" style="width: 5%;">No</th>
                        <th>Komponen</th>
                        <th>Nama</th>
                        <th class="th-center">Total</th>
                        <th class="th-center">Satuan</th>
                    </tr>
                </thead>
                <tbody>
                    @php $n = 1; @endphp
                    @foreach ($item['perKomponen'] as $komp)
                        <tr>
                            <td class="td-center">{{ $n++ }}</td>
                            <td>{{ $komp['label'] }}</td>
                            <td>{{ $komp['nama'] }}</td>
                            <td class="td-right bold">{{ number_format($komp['total'], 0, ',', '.') }}</td>
                            @php
                                $satuanRingkasanAsli = trim((string) ($komp['satuan'] ?? '-'));
                                $labelRingkasan = strtolower((string) ($komp['label'] ?? ''));
                                $isLaukAtauPendampingRingkasan =
                                    (stripos($labelRingkasan, 'protein') !== false) ||
                                    (stripos($labelRingkasan, 'lauk') !== false) ||
                                    (stripos($labelRingkasan, 'susu') !== false) ||
                                    (stripos($labelRingkasan, 'pendamping') !== false);

                                $satuanRingkasanTampil = $satuanRingkasanAsli;
                                if ($isLaukAtauPendampingRingkasan && $satuanRingkasanAsli !== '-') {
                                    $satuanRingkasanTampil = $satuanRingkasanAsli . ' / gram';
                                }
                            @endphp
                            <td class="td-center">{{ $satuanRingkasanTampil }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="sub-header" style="margin-top: 12px;">Sisa Bahan Baku Setelah Packing</div>
            <table class="table-block">
                <thead>
                    <tr>
                        <th class="th-center" style="width: 5%;">No</th>
                        <th>Komponen</th>
                        <th class="th-center">Sisa</th>
                        <th class="th-center">Satuan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="td-center">1</td>
                        <td>Karbohidrat</td>
                        <td class="td-right bold">{{ number_format($item['sisaBahan']['karbo']['qty'] ?? 0, 0, ',', '.') }}</td>
                        <td class="td-center">{{ $item['sisaBahan']['karbo']['satuan'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="td-center">2</td>
                        <td>Protein / Lauk</td>
                        <td class="td-right bold">{{ number_format($item['sisaBahan']['protein']['qty'] ?? 0, 0, ',', '.') }}</td>
                        @php $satuanSisaProtein = trim((string) ($item['sisaBahan']['protein']['satuan'] ?? '-')); @endphp
                        <td class="td-center">{{ $satuanSisaProtein !== '-' ? ($satuanSisaProtein . ' / gram') : '-' }}</td>
                    </tr>
                    <tr>
                        <td class="td-center">3</td>
                        <td>Sayur</td>
                        <td class="td-right bold">{{ number_format($item['sisaBahan']['sayur']['qty'] ?? 0, 0, ',', '.') }}</td>
                        <td class="td-center">{{ $item['sisaBahan']['sayur']['satuan'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="td-center">4</td>
                        <td>Buah</td>
                        <td class="td-right bold">{{ number_format($item['sisaBahan']['buah']['qty'] ?? 0, 0, ',', '.') }}</td>
                        <td class="td-center">{{ $item['sisaBahan']['buah']['satuan'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="td-center">5</td>
                        <td>Susu / Pendamping</td>
                        <td class="td-right bold">{{ number_format($item['sisaBahan']['susu']['qty'] ?? 0, 0, ',', '.') }}</td>
                        @php $satuanSisaSusu = trim((string) ($item['sisaBahan']['susu']['satuan'] ?? '-')); @endphp
                        <td class="td-center">{{ $satuanSisaSusu !== '-' ? ($satuanSisaSusu . ' / gram') : '-' }}</td>
                    </tr>
                    <tr>
                        <td class="td-center bold" colspan="1">6</td>
                        <td class="bold">Keterangan</td>
                        <td colspan="2">{{ $item['sisaBahan']['keterangan'] ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        </div>
    @empty
        <div class="menu-page">
        <div class="container">
            <table class="no-border table-block" style="margin-bottom: 16px;">
                <tr>
                    <td style="text-align: center; width: 25%;">
                        <img src="{{ public_path('image/logo.png') }}" alt="Logo" style="width: 90px; height: auto;">
                    </td>
                    <td style="width: 75%;" class="header-title">
                        LAPORAN MASAK HARIAN
                    </td>
                </tr>
            </table>
            <p style="text-align:center; color:#888;">Tidak ada data menu untuk hari ini.</p>
        </div>
        </div>
    @endforelse
</body>
</html>
