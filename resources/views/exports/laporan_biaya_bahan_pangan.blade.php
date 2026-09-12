{{-- ============================= --}}
{{-- HEADER LAPORAN --}}
{{-- ============================= --}}
<table style="width:100%; border-collapse:collapse;">
    <tr>
        <td colspan="9" style="text-align:center; font-weight:bold;">
            LAPORAN BIAYA BAHAN PANGAN
        </td>
    </tr>
    <tr>
        <td colspan="9" style="text-align:center;">
            Periode: {{ \Carbon\Carbon::parse($start)->format('d M Y') }}
            s.d.
            {{ \Carbon\Carbon::parse($end)->format('d M Y') }}
        </td>
    </tr>
    <tr><td colspan="9"></td></tr>
    <tr>
        <td colspan="2">Nama SPPG</td><td>: {{ $dapur->nama_dapur }}</td>
    </tr>
    <tr>
        <td colspan="2">Kelurahan/Desa</td><td>: {{ $dapur->kelurahan }}</td>
    </tr>
    <tr>
        <td colspan="2">Kecamatan</td><td>: {{ $dapur->kecamatan }}</td>
    </tr>
    <tr>
        <td colspan="2">Kabupaten/Kota</td><td>: {{ $dapur->kota }}</td>
    </tr>
    <tr>
        <td colspan="2">Provinsi</td><td>: {{ $dapur->provinsi }}</td>
    </tr>

    <tr><td colspan="9"></td></tr>

    {{-- HEADER KOLOM --}}
    <tr style="background:#f2f2f2; font-weight:bold; text-align:center;">
        <th>No</th>
        <th>Tanggal Kirim</th>
        <th>Nama Bahan</th>
        <th colspan="2">Volume</th>
        <th>Harga Satuan (Rp.)</th>
        <th>Total (Rp.)</th>
        <th>Penyuplai</th>
        <th>Survai Harga per Kg</th>
    </tr>
    <tr style="font-size:12px; text-align:center; font-style:italic;">
        <td></td>
        <td>1</td>
        <td>2</td>
        <td>3</td>
        <td>4</td>
        <td>5</td>
        <td>6 = 5x4</td>
        <td>7</td>
        <td>8</td>
    </tr>

    {{-- ============================= --}}
    {{-- ISI DATA --}}
    {{-- ============================= --}}
    @php
        $lastMenu = null;
        $subTotal = 0;
        $grandTotal = 0;
    @endphp

    @foreach($data as $row)
        {{-- Jika ganti menu, cetak subtotal --}}
        @if($lastMenu !== null && $lastMenu !== $row->menu)
            <tr style="background:#fafafa;">
                <td colspan="6" style="font-weight:bold; text-align:right;">TOTAL (Rp.)</td>
                <td style="font-weight:bold; text-align:right;">{{ number_format($subTotal,0,'.',',') }}</td>
                <td colspan="2"></td>
            </tr>
            @php
                $subTotal = 0;
            @endphp
        @endif

        {{-- Hitung harga satuan --}}
        @php
            $satuan_harga = 0;
            if($row->total_jumlah_bahan != 0 )
            {
                if($row->satuan == 'Gram' || $row->satuan == 'ml'){
                    $satuan_harga = $row->total_jumlah_po / ($row->total_jumlah_bahan/1000);
                }else{
                    $satuan_harga = $row->total_jumlah_po / $row->total_jumlah_bahan;
                }
            }
            
        @endphp

        {{-- Cetak data --}}
        <tr>
            <td>{{ $row->no_urut }}</td>
            <td>{{ \Carbon\Carbon::parse($row->tanggal_kirim)->format('d-m-Y') }}</td>
            <td>{{ $row->bahan }}</td>
            <td style="text-align:right;">{{ number_format($row->total_jumlah_bahan,0,'.',',') }}</td>
            <td style="text-align:right;">{{ $row->satuan }}</td>
            <td style="text-align:right;">{{ number_format($satuan_harga,0,'.',',') }}</td>
            <td style="text-align:right;">{{ number_format($row->total_jumlah_po,0,'.',',') }}</td>
            <td style="text-align:center;">Koperasi Seribu Impian Bersama</td>
            <td></td>
        </tr>

        {{-- Update subtotal dan grand total --}}
        @php
            $subTotal += $row->total_jumlah_po;
            $grandTotal += $row->total_jumlah_po;
            $lastMenu = $row->menu;
        @endphp
    @endforeach

    {{-- Tutup subtotal menu terakhir --}}
    @if($lastMenu !== null)
        <tr style="background:#fafafa;">
            <td colspan="6" style="font-weight:bold; text-align:right;">TOTAL (Rp.)</td>
            <td style="font-weight:bold; text-align:right;">{{ number_format($subTotal,0,'.',',') }}</td>
            <td colspan="2"></td>
        </tr>
    @endif

    {{-- Total keseluruhan --}}
    <tr style="background:#e6ffe6;">
        <td colspan="6" style="font-weight:bold; text-align:right;">TOTAL KESELURUHAN (Rp.)</td>
        <td style="font-weight:bold; text-align:right;">{{ number_format($grandTotal,0,'.',',') }}</td>
        <td colspan="2"></td>
    </tr>
</table>

{{-- ============================= --}}
{{-- BAGIAN TANDA TANGAN --}}
{{-- ============================= --}}
<table style="width:100%; margin-top:40px;">
    <tr>
        <td colspan="3" style="vertical-align:top;">Mengetahui,</td>
        <td ></td>
        <td colspan="2"></td>
        <td colspan="3" style="text-align:right; vertical-align:top;">
            .................., .................. 20....
        </td>
    </tr>
    <tr>
        <td colspan="3">Kepala SPPG,</td>
        <td ></td>
        <td colspan="2">Asisten Lapangan</td>
        <td colspan="3" style="text-align:right;">Akuntan SPPG,</td>
    </tr>
    <tr>
        <td colspan="9" style="height:80px;"></td> {{-- ruang tanda tangan --}}
    </tr>
    <tr>
        <td colspan="3">(...............................)</td>
        <td ></td>
        <td colspan="2">(...............................)</td>
        <td colspan="3" style="text-align:right;">(...............................)</td>
    </tr>
</table>
