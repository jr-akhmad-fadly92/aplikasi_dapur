{{-- ============================= --}}
{{-- HEADER LAPORAN --}}
{{-- ============================= --}}
<table style="width:100%; border-collapse:collapse;">
    <tr>
        <td colspan="7" style="text-align:center; font-weight:bold;">
            LAPORAN BIAYA INFRASTRUKTUR DAN PERALATAN
        </td>
    </tr>
    <tr>
        <td colspan="7" style="text-align:center;">
            Periode: {{ \Carbon\Carbon::parse($start)->format('d M Y') }}
            s.d.
            {{ \Carbon\Carbon::parse($end)->format('d M Y') }}
        </td>
    </tr>
    <tr><td colspan="7"></td></tr>
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

    <tr><td colspan="7"></td></tr>

    {{-- HEADER KOLOM --}}
    <tr style="background:#f2f2f2; font-weight:bold; text-align:center;">
        <th>No</th>
        <th>Tanggal</th>
        <th>Uraian</th>
        <th>Jumlah</th>
        <th>Satuan</th>
        <th>Nominal (Rp.)</th>
        <th>Keterangan</th>
    </tr>
    <tr style="font-size:12px; text-align:center; font-style:italic;">
        <td></td>
        <td>1</td>
        <td>2</td>
        <td>3</td>
        <td>4</td>
        <td>5</td>
        <td>6</td>
    </tr>

    {{-- ============================= --}}
    {{-- ISI DATA --}}
    {{-- ============================= --}}
    @php
        $lastMenu = null;
        $subTotal = 0;
        $grandTotal = 0;
        $nomor = 0;
    @endphp

   
    @foreach($data2 as $row)
       
          @php
            
            $nomor = $nomor+1;
        @endphp
        {{-- Cetak data --}}
        <tr>
            <td>{{ $nomor}}</td>
            <td>{{ \Carbon\Carbon::parse($row->tanggal)->format('d-m-Y') }}</td>
            <td>{{ $row->deskripsi }} </td>
            <td style="text-align:right;">1</td>
            <td style="text-align:right;">-</td>
            <td style="text-align:right;">{{ number_format($row->jumlah,0,'.',',') }}</td>
            <td></td>
        </tr>

        {{-- Update subtotal dan grand total --}}
        @php
            $grandTotal += $row->jumlah;
            
        @endphp
    @endforeach
    

    {{-- Total keseluruhan --}}
    <tr style="background:#e6ffe6;">
        <td colspan="5" style="font-weight:bold; text-align:right;">TOTAL KESELURUHAN (Rp.)</td>
        <td colspan="2" style="font-weight:bold; text-align:right;">{{ number_format($grandTotal,0,'.',',') }}</td>
     
    </tr>
</table>

{{-- ============================= --}}
{{-- BAGIAN TANDA TANGAN --}}
{{-- ============================= --}}
<table style="width:100%; margin-top:40px;">
    <tr>
        <td colspan="2" style="vertical-align:top;">Mengetahui,</td>
        
        <td colspan="3"></td>
        <td colspan="2" style="text-align:right; vertical-align:top;">
            .................., .................. 20....
        </td>
    </tr>
    <tr>
        <td colspan="2">Kepala SPPG,</td>
       
        <td colspan="3" style="text-align:center;">Asisten Lapangan</td>
        <td colspan="2" style="text-align:right;">Akuntan SPPG,</td>
    </tr>
    <tr>
        <td colspan="7" style="height:80px;"></td> {{-- ruang tanda tangan --}}
    </tr>
    <tr>
        <td colspan="2">(...............................)</td>
        
        <td colspan="3" style="text-align:center;">(...............................)</td>
        <td colspan="2" style="text-align:right;">(...............................)</td>
    </tr>
</table>
