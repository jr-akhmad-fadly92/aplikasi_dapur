{{-- ============================= --}}
{{-- HEADER LAPORAN --}}
{{-- ============================= --}}
<table style="width:100%; border-collapse:collapse;">
    <tr>
        <td colspan="3" style="text-align:center; font-weight:bold;">
            DATA RESEP YAYASAN BINA BANGSA SEMARANG
        </td>
    </tr>
    <tr><td colspan="3"></td></tr>
    
</table>
<table>
    <tr style="background:#f2f2f2; font-weight:bold; text-align:center;">
        <th colspan=3 >Karbohidrat</th>
    </tr>
    {{-- HEADER KOLOM --}}
    <tr style="background:#f2f2f2; font-weight:bold; text-align:center;">
        <th>No</th>
        <th>Resep</th>
        <th  >Komposisi</th>
    </tr>
    
    {{-- ============================= --}}
    {{-- ISI DATA --}}
    @php
        $no = 0;
    @endphp
    @foreach ($karbohidrat as $row)
    @php
        $no =  $no + 1;
    @endphp
    <tr>
        <td>{{ $no }}</td>
        <td>{{ $row->nama_resep }}</td>
        <td >{{ $row->bahan_dan_satuan}}</td>
    </tr>
    @endforeach
    

    <tr style="background:#f2f2f2; font-weight:bold; text-align:center;">
        <th  colspan=3>Protein</th>
    </tr>
    {{-- HEADER KOLOM --}}
    <tr style="background:#f2f2f2; font-weight:bold; text-align:center;">
        <th>No</th>
        <th>Resep</th>
        <th  >Komposisi</th>
    </tr>
    
    {{-- ============================= --}}
    {{-- ISI DATA --}}
    @php
        $no = 0;
    @endphp
    @foreach ($protein as $row)
    @php
        $no =  $no + 1;
    @endphp
    <tr>
        <td>{{ $no }}</td>
        <td>{{ $row->nama_resep }}</td>
        <td >{{ $row->bahan_dan_satuan}}</td>
    </tr>
    @endforeach

    <tr style="background:#f2f2f2; font-weight:bold; text-align:center;">
        <th colspan=3 >Sayur</th>
    </tr>
    {{-- HEADER KOLOM --}}
    <tr style="background:#f2f2f2; font-weight:bold; text-align:center;">
        <th>No</th>
        <th>Resep</th>
        <th >Komposisi</th>
    </tr>
    
    {{-- ============================= --}}
    {{-- ISI DATA --}}
    @php
        $no = 0;
    @endphp
    @foreach ($sayur as $row)
    @php
        $no =  $no + 1;
    @endphp
    <tr>
        <td>{{ $no }}</td>
        <td>{{ $row->nama_resep }}</td>
        <td >{{ $row->bahan_dan_satuan}}</td>
    </tr>
    @endforeach

    <tr style="background:#f2f2f2; font-weight:bold; text-align:center;">
        <th colspan=3 >Buah</th>
    </tr>
    {{-- HEADER KOLOM --}}
    <tr style="background:#f2f2f2; font-weight:bold; text-align:center;">
        <th>No</th>
        <th>Resep</th>
        <th >Komposisi</th>
    </tr>
    
    {{-- ============================= --}}
    {{-- ISI DATA --}}
     @php
        $no = 0;
    @endphp
    @foreach ($buah as $row)
    @php
        $no =  $no + 1;
    @endphp
    <tr>
        <td>{{ $no }}</td>
        <td>{{ $row->nama_resep }}</td>
        <td >{{ $row->bahan_dan_satuan}}</td>
    </tr>
    @endforeach

    <tr style="background:#f2f2f2; font-weight:bold; text-align:center;">
        <th colspan=3 >Suplemen</th>
    </tr>
    {{-- HEADER KOLOM --}}
    <tr style="background:#f2f2f2; font-weight:bold; text-align:center;">
        <th>No</th>
        <th>Resep</th>
        <th  >Komposisi</th>
    </tr>
    
    {{-- ============================= --}}
    {{-- ISI DATA --}}
    @php
        $no = 0;
    @endphp
    @foreach ($tambahan as $row)
    @php
        $no =  $no + 1;
    @endphp
    <tr>
        <td>{{ $no }}</td>
        <td>{{ $row->nama_resep }}</td>
        <td >{{ $row->bahan_dan_satuan}}</td>
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
