@php
    $styleByValue = function ($value, $extra = '') {
        $filled = !is_null($value) && trim((string) $value) !== '' && (string) $value !== '-';
        $base = $filled ? 'border:1px solid #000;' : 'border:none;';
        return $base . $extra;
    };

    $formatRupiah = function ($value) {
        $nominal = is_null($value) || $value === '' ? 0 : (float) $value;
        return 'Rp ' . number_format($nominal, 0, ',', '.');
    };

    $headerStyle = 'border:1px solid #000; background:#f2f2f2; font-weight:bold; text-align:center;';
    $sectionHeaderStyle = 'border:1px solid #000; background:orange; font-weight:bold; text-align:center;';
@endphp
@php
    $startDate = \Carbon\Carbon::parse($start);
    $endDate   = \Carbon\Carbon::parse($end);
    $weekOfMonth = (int) ceil($startDate->day / 7);
    $monthName   = $startDate->translatedFormat('F');
    $periodeStr  = $start !== $end
        ? $startDate->format('j') . ' - ' . strtolower($endDate->translatedFormat('j F Y'))
        : strtolower($startDate->translatedFormat('j F Y'));
@endphp
<table class="auto-cols" style="width:100%; border-collapse:collapse;">
    <tr>
        <td style="text-align:left; font-weight:bold;">Yayasan Bina bangsa Semarang</td>
    </tr>
    <tr>
        <td style="text-align:left; font-weight:bold;">Unit Kegiatan MBG</td>
    </tr>
    <tr>
        <td style="text-align:left; font-weight:bold;">{{ $dapur->nama_dapur ?? '-' }}</td>
    </tr>
    <tr>
        <td style="text-align:left; font-weight:bold;">Daftar Menu {{ $monthName }} Minggu ke {{ $weekOfMonth }}</td>
    </tr>
    <tr>
        <td style="text-align:left; font-weight:bold;">Periode {{ $periodeStr }}</td>
    </tr>
    <tr><td colspan="30"></td></tr>
</table>
<table style="width:100%; border-collapse:collapse; margin-bottom:10px;">
    <tr>
        <td style="border:1px solid #000; font-weight:bold; text-align:center;">
            DATA MENU {{ \Carbon\Carbon::parse($start)->translatedFormat('d F Y') }}
            @if($start !== $end)
                - {{ \Carbon\Carbon::parse($end)->translatedFormat('d F Y') }}
            @endif
        </td>
    </tr>
</table>

<table style="width:100%; border-collapse:collapse;">

    @forelse($menuReports as $report)
        @php
            $menu = $report['menu'];
            $porsi = (int) ($report['total_porsi'] ?? 0);

            $gramSayurMap = [
                optional($report['gram_sayur'])->sayur_porsi_a,
                optional($report['gram_sayur'])->sayur_porsi_b,
                optional($report['gram_sayur'])->sayur_porsi_c,
                optional($report['gram_sayur'])->sayur_porsi_d,
            ];

            $bahanPokokRows = [
                [
                    'menu' => optional($report['karbo'])->nama_resep,
                    'bahan' => optional($report['karbo'])->bahan,
                    'gram' => optional($report['gram_karbo'])->karbo_porsi_a,
                    'kebutuhan' => optional($report['karbo'])->jumlah,
                    'satuan' => optional($report['karbo'])->satuan,
                    'harga' => optional($report['karbo'])->harga,
                    'total_harga' => optional($report['karbo'])->total_harga,
                    'keterangan' => optional($report['karbo'])->keterangan,
                ],
                [
                    'menu' => optional($report['lauk'])->nama_resep,
                    'bahan' => optional($report['lauk'])->bahan,
                    'gram' => optional($report['gram_protein'])->protein_porsi_a,
                    'kebutuhan' => optional($report['lauk'])->jumlah,
                    'satuan' => optional($report['lauk'])->satuan,
                    'harga' => optional($report['lauk'])->harga,
                    'total_harga' => optional($report['lauk'])->total_harga,
                    'keterangan' => optional($report['lauk'])->keterangan,
                ],
                [
                    'menu' => optional($report['buah'])->nama_resep,
                    'bahan' => optional($report['buah'])->bahan,
                    'gram' => optional($report['gram_buah'])->buah_porsi_a,
                    'kebutuhan' => optional($report['buah'])->jumlah,
                    'satuan' => optional($report['buah'])->satuan,
                    'harga' => optional($report['buah'])->harga,
                    'total_harga' => optional($report['buah'])->total_harga,
                    'keterangan' => optional($report['buah'])->keterangan,
                ],
                [
                    'menu' => optional($report['suplemen'])->nama_resep,
                    'bahan' => optional($report['suplemen'])->bahan,
                    'gram' => optional($report['gram_suplemen'])->suplemen_porsi_a,
                    'kebutuhan' => optional($report['suplemen'])->jumlah,
                    'satuan' => optional($report['suplemen'])->satuan,
                    'harga' => optional($report['suplemen'])->harga,
                    'total_harga' => optional($report['suplemen'])->total_harga,
                    'keterangan' => optional($report['suplemen'])->keterangan,
                ],
            ];
        @endphp

        <tr>
            <th colspan="6" style="{{ $sectionHeaderStyle }}">MENU - {{ $menu->menu ?? '-' }}</th>
            <th style="{{ $sectionHeaderStyle }}">{{ $report['golongan'] ?? '-' }}</th>
            <th style="{{ $sectionHeaderStyle }}">{{ number_format($porsi, 0, ',', '.') }}</th>
        </tr>
        <tr>
            <td colspan="8" style="border:1px solid #000; background:#fff7e6; font-weight:bold; text-align:center;">Tanggal {{ \Carbon\Carbon::parse($menu->tanggal_kirim)->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <th style="{{ $headerStyle }}">Menu</th>
            <th style="{{ $headerStyle }}">Bahan Makanan</th>
            <th style="{{ $headerStyle }}">Gram</th>
            <th style="{{ $headerStyle }}">Kebutuhan</th>
            <th style="{{ $headerStyle }}">Satuan</th>
            <th style="{{ $headerStyle }}">Harga</th>
            <th style="{{ $headerStyle }}">Total Harga</th>
            <th style="{{ $headerStyle }}">Keterangan</th>
        </tr>
        <tr>
            <td colspan="8" style="border:1px solid #000; background:#f2f2f2; font-weight:bold; text-align:center;">Bahan Pokok</td>
        </tr>

        @foreach($bahanPokokRows as $row)
            <tr>
                <td style="{{ $styleByValue($row['menu'] ?? null) }}">{{ $row['menu'] ?? '-' }}</td>
                <td style="{{ $styleByValue($row['bahan'] ?? null) }}">{{ $row['bahan'] ?? '-' }}</td>
                <td style="{{ $styleByValue($row['gram'] ?? null, 'text-align:center;') }}">{{ $row['gram'] ?? '-' }}</td>
                <td style="{{ $styleByValue($row['kebutuhan'] ?? null, 'text-align:right;') }}">{{ $row['kebutuhan'] ?? '-' }}</td>
                <td style="{{ $styleByValue($row['satuan'] ?? null, 'text-align:center;') }}">{{ $row['satuan'] ?? '-' }}</td>
                <td style="{{ $styleByValue($row['harga'] ?? null, 'text-align:right;') }}">{{ $row['harga'] !== null ? $formatRupiah($row['harga']) : '-' }}</td>
                <td style="{{ $styleByValue($row['total_harga'] ?? null, 'text-align:right;') }}">{{ $row['total_harga'] !== null ? $formatRupiah($row['total_harga']) : '-' }}</td>
                <td style="{{ $styleByValue($row['keterangan'] ?? null) }}">{{ $row['keterangan'] ?? '-' }}</td>
            </tr>
        @endforeach

        @foreach(($report['bahan_sayur'] ?? collect()) as $idx => $sayur)
            @php
                $gramSayur = $gramSayurMap[$idx] ?? null;
            @endphp
            <tr>
                <td style="{{ $styleByValue($sayur->nama_resep ?? null) }}">{{ $sayur->nama_resep ?? '-' }}</td>
                <td style="{{ $styleByValue($sayur->bahan ?? null) }}">{{ $sayur->bahan ?? '-' }}</td>
                <td style="{{ $styleByValue($gramSayur, 'text-align:center;') }}">{{ $gramSayur ?? '-' }}</td>
                <td style="{{ $styleByValue($sayur->jumlah ?? null, 'text-align:right;') }}">{{ $sayur->jumlah ?? '-' }}</td>
                <td style="{{ $styleByValue($sayur->satuan ?? null, 'text-align:center;') }}">{{ $sayur->satuan ?? '-' }}</td>
                <td style="{{ $styleByValue($sayur->harga ?? null, 'text-align:right;') }}">{{ isset($sayur->harga) ? $formatRupiah($sayur->harga) : '-' }}</td>
                <td style="{{ $styleByValue($sayur->total_harga ?? null, 'text-align:right;') }}">{{ isset($sayur->total_harga) ? $formatRupiah($sayur->total_harga) : '-' }}</td>
                <td style="{{ $styleByValue($sayur->keterangan ?? null) }}">{{ $sayur->keterangan ?? '-' }}</td>
            </tr>
        @endforeach

        <tr>
            <td colspan="8" style="border:1px solid #000; background:#f2f2f2; font-weight:bold; text-align:center;">Bahan Bumbu</td>
        </tr>
        @php $namaResep = ''; @endphp
        @forelse(($report['bumbu'] ?? collect()) as $bumbu)
            @php
                $resepLabel = '';
                if ($namaResep !== ($bumbu->nama_resep ?? '')) {
                    $namaResep = $bumbu->nama_resep ?? '';
                    $resepLabel = $bumbu->nama_resep ?? '-';
                }
            @endphp
            <tr>
                <td style="{{ $styleByValue($resepLabel) }}">{{ $resepLabel }}</td>
                <td style="{{ $styleByValue($bumbu->bahan ?? null) }}">{{ $bumbu->bahan ?? '-' }}</td>
                <td style="{{ $styleByValue(null, 'text-align:center;') }}">-</td>
                <td style="{{ $styleByValue($bumbu->jumlah ?? null, 'text-align:right;') }}">{{ $bumbu->jumlah ?? '-' }}</td>
                <td style="{{ $styleByValue($bumbu->satuan ?? null, 'text-align:center;') }}">{{ $bumbu->satuan ?? '-' }}</td>
                <td style="{{ $styleByValue($bumbu->harga ?? null, 'text-align:right;') }}">{{ isset($bumbu->harga) ? $formatRupiah($bumbu->harga) : '-' }}</td>
                <td style="{{ $styleByValue($bumbu->total_harga ?? null, 'text-align:right;') }}">{{ isset($bumbu->total_harga) ? $formatRupiah($bumbu->total_harga) : '-' }}</td>
                <td style="{{ $styleByValue($bumbu->keterangan ?? null) }}">{{ $bumbu->keterangan ?? '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="8" style="border:none; text-align:center;">-</td>
            </tr>
        @endforelse

        <tr><td colspan="8" style="border:none;"></td></tr>
    @empty
        <tr>
            <td colspan="8" style="border:none; text-align:center;">Data menu tidak ditemukan</td>
        </tr>
    @endforelse

    <tr>
        <th colspan="6" style="{{ $sectionHeaderStyle }}">REKAP MENU (SEMUA BAHAN)</th>
        <th style="{{ $sectionHeaderStyle }}">Golongan</th>
        <th style="{{ $sectionHeaderStyle }}">{{ number_format($totalPorsi ?? 0, 0, ',', '.') }}</th>
    </tr>
    <tr>
        <th style="{{ $headerStyle }}">Menu</th>
        <th style="{{ $headerStyle }}">Bahan Makanan</th>
        <th style="{{ $headerStyle }}">Gram</th>
        <th style="{{ $headerStyle }}">Kebutuhan</th>
        <th style="{{ $headerStyle }}">Satuan</th>
        <th style="{{ $headerStyle }}">Harga</th>
        <th style="{{ $headerStyle }}">Total Harga</th>
        <th style="{{ $headerStyle }}">Keterangan</th>
    </tr>

    @forelse($bumbu_total as $data)
        <tr>
            <td style="border:none;"></td>
            <td style="{{ $styleByValue($data->bahan ?? null) }}">{{ $data->bahan ?? '-' }}</td>
            <td style="{{ $styleByValue(null, 'text-align:center;') }}">-</td>
            <td style="{{ $styleByValue($data->total_jumlah ?? null, 'text-align:right;') }}">{{ $data->total_jumlah ?? '-' }}</td>
            <td style="{{ $styleByValue($data->satuan ?? null, 'text-align:center;') }}">{{ $data->satuan ?? '-' }}</td>
            <td style="{{ $styleByValue($data->harga ?? null, 'text-align:right;') }}">{{ isset($data->harga) ? $formatRupiah($data->harga) : '-' }}</td>
            <td style="{{ $styleByValue($data->total_harga ?? null, 'text-align:right;') }}">{{ isset($data->total_harga) ? $formatRupiah($data->total_harga) : '-' }}</td>
            <td style="{{ $styleByValue($data->keterangan ?? null) }}">{{ $data->keterangan ?? '-' }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="8" style="border:none; text-align:center;">Data rekap tidak ditemukan</td>
        </tr>
    @endforelse
</table>
