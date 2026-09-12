{{-- ============================= --}}
{{-- HEADER LAPORAN --}}
{{-- ============================= --}}
<table style="width:100%; border-collapse:collapse;">
    <tr>
        <td></td>
        <td colspan="6" style="text-align:center; font-weight:bold;">
            Form Pengeluaran Bahan Pangan {{ $dapur->nama_dapur }}
        </td>
    </tr>
    <tr><td></td><td colspan="7"></td></tr>
    
</table>

<table >
    <tr>
        <td></td>
        <td  colspan="9" style="text-align:left; font-weight:bold;">
            Hari / Tanggal :.........................
        </td>
        
    </tr>
    <tr>
        <td></td>
        <td  style="text-align:center; font-weight:bold;">
            No
        </td>
        <td  style="text-align:center; font-weight:bold;">
            Bahan Non Pangan
        </td>
        <td  style="text-align:center; font-weight:bold;">
            Jumlah Pengeluaran
        </td>
        <td  style="text-align:center; font-weight:bold;">
            Nama Relawan
        </td>
        <td  style="text-align:center; font-weight:bold;">
            Bagian
        </td>
        <td  style="text-align:center; font-weight:bold;">
            TTD
        </td>
        
    </tr>
    
    @php
    $no = 0;  
    $maks = 30 ;    
    @endphp
    @for ($i = $no; $i < $maks; $i++)
    @php
    $no = $no + 1;
    @endphp
        <tr>
            <td></td>
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
            <td  style="text-align:center; font-weight:bold;">
                
            </td>
            
        </tr>
    @endfor
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