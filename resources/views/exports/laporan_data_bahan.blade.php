{{-- ============================= --}}
{{-- HEADER LAPORAN --}}
{{-- ============================= --}}
<table style="width:100%; border-collapse:collapse;">
    <tr>
        <td style="font-weight:bold;text-align:left;" colspan="3">Yayasan Bina Bangsa Semarang</td>
        
    </tr>
    <tr>
       
         <td style="font-weight:bold;text-align:left;" colspan="3">Unit SPPG</td>
        
    </tr>
    <tr>
     
         <td style="font-weight:bold;text-align:left;" colspan="3">Rekap Periode Pelayanan SPPG YBBS</td>
        
    </tr>
    <tr>
        
        <td style="font-weight:bold;text-align:left;" colspan="3">{{ $dapur->nama_dapur }} {{ $dapur->kota }}</td>
        
    </tr>
    
</table>
<table>
    <tr style="background:#f2f2f2; font-weight:bold; text-align:center;">
        <th colspan=3 >Karbohidrat</th>
    </tr>
    {{-- HEADER KOLOM --}}
    <tr style="background:#f2f2f2; font-weight:bold; text-align:center;">
        <th>No</th>
        <th>Bahan</th>
        <th>Keterangan</th>
    </tr>
    
    {{-- ============================= --}}
    {{-- ISI DATA --}}
    @php
        $no = 0;
    @endphp
    @foreach ($master_karbo as $row)
    @php
        $no =  $no + 1;
    @endphp
    <tr>
        <td>{{ $no }}</td>
        <td>{{ $row->bahan }}</td>
        <td>{{ $row->spesifikasi ?? '-' }}</td>
    </tr>
    @endforeach
    

    <tr style="background:#f2f2f2; font-weight:bold; text-align:center;">
        <th  colspan=3>Protein</th>
    </tr>
    {{-- HEADER KOLOM --}}
    <tr style="background:#f2f2f2; font-weight:bold; text-align:center;">
        <th>No</th>
        <th>Bahan</th>
        <th  >Keterangan</th>
    </tr>
    
    {{-- ============================= --}}
    {{-- ISI DATA --}}
    @php
        $no = 0;
    @endphp
    @foreach ($master_protein as $row)
    @php
        $no =  $no + 1;
    @endphp
    <tr>
        <td>{{ $no }}</td>
        <td>{{ $row->bahan }}</td>
        <td>{{ $row->spesifikasi ?? '-' }}</td>
    </tr>
    @endforeach

    <tr style="background:#f2f2f2; font-weight:bold; text-align:center;">
        <th colspan=3 >Sayur</th>
    </tr>
    {{-- HEADER KOLOM --}}
    <tr style="background:#f2f2f2; font-weight:bold; text-align:center;">
        <th>No</th>
        <th>Bahan</th>
        <th  >Keterangan</th>
    </tr>
    
    {{-- ============================= --}}
    {{-- ISI DATA --}}
    @php
        $no = 0;
    @endphp
    @foreach ($master_sayur as $row)
    @php
        $no =  $no + 1;
    @endphp
    <tr>
        <td>{{ $no }}</td>
        <td>{{ $row->bahan }}</td>
        <td>{{ $row->spesifikasi ?? '-' }}</td>
    </tr>
    @endforeach

    <tr style="background:#f2f2f2; font-weight:bold; text-align:center;">
        <th colspan=3 >Buah</th>
    </tr>
    {{-- HEADER KOLOM --}}
    <tr style="background:#f2f2f2; font-weight:bold; text-align:center;">
        <th>No</th>
        <th>Bahan</th>
        <th  >Keterangan</th>
    </tr>
    
    {{-- ============================= --}}
    {{-- ISI DATA --}}
     @php
        $no = 0;
    @endphp
    @foreach ($master_buah as $row)
    @php
        $no =  $no + 1;
    @endphp
    <tr>
        <td>{{ $no }}</td>
        <td>{{ $row->bahan }}</td>
        <td>{{ $row->spesifikasi ?? '-' }}</td>
    </tr>
    @endforeach

    <tr style="background:#f2f2f2; font-weight:bold; text-align:center;">
        <th colspan=3 >Suplemen</th>
    </tr>
    {{-- HEADER KOLOM --}}
    <tr style="background:#f2f2f2; font-weight:bold; text-align:center;">
        <th>No</th>
        <th>Bahan</th>
        <th  >Keterangan</th>
    </tr>
    
    {{-- ============================= --}}
    {{-- ISI DATA --}}
    @php
        $no = 0;
    @endphp
    @foreach ($master_tambahan as $row)
    @php
        $no =  $no + 1;
    @endphp
    <tr>
        <td>{{ $no }}</td>
        <td>{{ $row->bahan }}</td>
        <td>{{ $row->spesifikasi ?? '-' }}</td>
    </tr>
    @endforeach

    <tr style="background:#f2f2f2; font-weight:bold; text-align:center;">
        <th colspan=3 >Bumbu</th>
    </tr>
    <tr style="background:#f2f2f2; font-weight:bold; text-align:center;">
        <th>No</th>
        <th>Bahan</th>
        <th>Keterangan</th>
    </tr>
    @php
        $no = 0;
    @endphp
    @foreach ($master_bumbu as $row)
    @php
        $no =  $no + 1;
    @endphp
    <tr>
        <td>{{ $no }}</td>
        <td>{{ $row->bahan }}</td>
        <td>{{ $row->spesifikasi ?? '-' }}</td>
    </tr>
    @endforeach

    <tr style="background:#f2f2f2; font-weight:bold; text-align:center;">
        <th colspan=3 >Non Pangan (Penunjang)</th>
    </tr>
    <tr style="background:#f2f2f2; font-weight:bold; text-align:center;">
        <th>No</th>
        <th>Bahan</th>
        <th>Keterangan</th>
    </tr>
    @php
        $no = 0;
    @endphp
    @foreach ($master_penunjang as $row)
    @php
        $no =  $no + 1;
    @endphp
    <tr>
        <td>{{ $no }}</td>
        <td>{{ $row->bahan }}</td>
        <td>{{ $row->spesifikasi ?? '-' }}</td>
    </tr>
    @endforeach

   
</table>

{{-- ============================= --}}
{{-- BAGIAN TANDA TANGAN --}}
{{-- ============================= --}}
<table style="width:100%; margin-top:40px;">
    <tr>
        <td style="vertical-align:top;">Mengetahui,</td>
        
        <td></td>
        <td  style="text-align:right; vertical-align:top;">
            .................., .................. 20....
        </td>
    </tr>
    <tr>
        <td >Kepala SPPG,</td>
       
        <td  style="text-align:center;">Asisten Lapangan</td>
        <td  style="text-align:right;">Akuntan SPPG,</td>
    </tr>
    <tr>
        <td colspan="3" style="height:80px;"></td> {{-- ruang tanda tangan --}}
    </tr>
    <tr>
        <td >(...............................)</td>
        
        <td  style="text-align:center;">(...............................)</td>
        <td style="text-align:right;">(...............................)</td>
    </tr>
</table>
