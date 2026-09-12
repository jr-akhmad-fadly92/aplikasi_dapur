{{-- ============================= --}}
{{-- HEADER LAPORAN --}}
{{-- ============================= --}}
<table style="width:100%; border-collapse:collapse;">
    <tr>
        <td></td>
        <td colspan="9" style="text-align:center; font-weight:bold;">
          
        </td>
    </tr>
    <tr>
        <td></td>
        <td colspan="9" style="text-align:center; font-weight:bold;">
            Form Penerimaan Bahan Non Pangan {{ $dapur->nama_dapur }}
        </td>
    </tr>
    <tr><td></td><td colspan="9"></td></tr>
    
</table>

<table >
     
    <tr>
        <td></td>
        <td  colspan="9" style="text-align:left; font-weight:bold;">
            Hari / Tanggal : {{ \Carbon\Carbon::parse($start ?? date('Y-m-d'))->translatedFormat('d F Y') }}
        </td>
        
    </tr>
    <tr>
        <td></td>
        <td  style="text-align:center; font-weight:bold;">
            No
        </td>
        <td  style="text-align:center; font-weight:bold;">
            Bahan Pangan
        </td>
        <td  style="text-align:center; font-weight:bold;">
            Jumlah Pesan
        </td>
        <td  style="text-align:center; font-weight:bold;">
            Satuan
        </td>
        <td  style="text-align:center; font-weight:bold;">
            Jumlah Diterima
        </td>
        <td  style="text-align:center; font-weight:bold;">
            Pengembalian
        </td>
        <td  style="text-align:center; font-weight:bold;">
            Total
        </td>
        <td  style="text-align:center; font-weight:bold;">
            Keterangan
        </td>
        <td  style="text-align:center; font-weight:bold;">
            ttd Koperasi
        </td>
    </tr>
    @php
    $no = 0;    
    $maks = 30;    
    
    @endphp
    @foreach ($data_non_pangan as $item)
    @php
    $no = $no + 1;   
    $jumlah = 0;
    $satuan = 0;
    
    if($item->nama_satuan == 'Gram' )
    {
        $jumlah = $item->total_jumlah/ 1000;
        $satuan = 'Kg';
    }else if($item->nama_satuan == 'ml' )
    {
        $jumlah = $item->total_jumlah / 1000;
        $satuan = 'Liter';
    }else{
        $jumlah = $item->total_jumlah ;
        $satuan = $item->nama_satuan;
    } 
    @endphp
    @if($item->bahan != 'air')
    <tr>
        <td></td>
        <td  style="text-align:center; font-weight:bold;">
            {{ $no }}
        </td>
        <td  style="text-align:left; font-weight:bold;">
            {{ $item->bahan }}
        </td>
        
        <td  style="text-align:center; font-weight:bold;">
            {{ $jumlah }}
        </td>
        <td  style="text-align:left; font-weight:bold;">
            {{ $satuan }}
        </td>
        <td  style="text-align:center; font-weight:bold;">
            
        </td>
        <td  style="text-align:center; font-weight:bold;">
            
        </td>
        <td  style="text-align:center; font-weight:bold;">
            
        </td>
        <td  style="text-align:center; font-weight:bold;">
            
        </td>
        <td  style="text-align:center; font-weight:bold;">
            
        </td>
    </tr>
    @endif
    
    @endforeach
    @php
    $maks = 30 ;    
    @endphp
    @for ($i = $no; $i < $maks; $i++)
    @php
    $no = $no + 1;
    @endphp
        <tr>
            <td></td>
            <td  style="text-align:center; font-weight:bold;">
                {{ $no }}
            </td>
            <td  style="text-align:left; font-weight:bold;">
            -   
            </td>
            
            <td  style="text-align:center; font-weight:bold;">
                
            </td>
            <td  style="text-align:left; font-weight:bold;">
                
            </td>
            <td  style="text-align:center; font-weight:bold;">
                
            </td>
            <td  style="text-align:center; font-weight:bold;">
                
            </td>
            <td  style="text-align:center; font-weight:bold;">
                
            </td>
            <td  style="text-align:center; font-weight:bold;">
                
            </td>
            <td  style="text-align:center; font-weight:bold;">
                
            </td>
        </tr>
    @endfor

    <tr>
        <td ></td>
    </tr>
    <tr>
        <td ></td>
    </tr>
    <tr>
        <td></td>
        <td  colspan=3 style="text-align:center; font-weight:bold;">
            Petugas
        </td>
        <td  colspan=3 style="text-align:center; font-weight:bold;">
            Koperasi
        </td>
        <td  colspan=3 style="text-align:center; font-weight:bold;">
            Mengetahui
        </td>
        
    </tr>
    <tr>
        <td ></td>
    </tr>
    <tr>
        <td ></td>
    </tr>
    <tr>
        <td ></td>
    </tr>
    <tr>
        <td></td>
        <td  colspan=3 style="text-align:center; font-weight:bold;">
            (.....................)
        </td>
        <td  colspan=3 style="text-align:center; font-weight:bold;">
            (.....................)
        </td>
        <td  colspan=3 style="text-align:center; font-weight:bold;">
            (.....................)
        </td>
        
    </tr>
    
</table>