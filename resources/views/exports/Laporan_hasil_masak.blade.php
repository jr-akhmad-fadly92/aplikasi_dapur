{{-- ============================= --}}
{{-- HEADER LAPORAN --}}
{{-- ============================= --}}
<table style="width:100%; border-collapse:collapse;">
    <tr>
        <td></td>
        <td colspan="6" style="text-align:center; font-weight:bold;">
            Hasil Masak {{ $dapur->nama_dapur }}
        </td>
    </tr>
    <tr><td></td><td colspan="7"></td></tr>
    
</table>

<table >
    <tr>
        <td></td>
        <td  colspan="6" style="text-align:left; font-weight:bold;">
            Hari / Tanggal :.........................
        </td>
        
    </tr>
    <tr>
        <td></td>
        <td  style="text-align:center; font-weight:bold;">
            No
        </td>
        <td  style="text-align:center; font-weight:bold;">
            {{ $nama_karbo->nama_resep }}
        </td>
        <td  style="text-align:center; font-weight:bold;">
            {{ $nama_lauk->nama_resep }}
        </td>
        <td  style="text-align:center; font-weight:bold;">
            {{ $nama_sayur->nama_resep }}
        </td>
        <td  style="text-align:center; font-weight:bold;">
            {{ $nama_buah->nama_resep }}
        </td>
        <td  style="text-align:center; font-weight:bold;">
            {{ $nama_suplemen->nama_resep }}
        </td>
        <td  style="text-align:center; font-weight:bold;">
            
        </td>
        <td  style="text-align:center; font-weight:bold;">
            {{ $nama_karbo_2->nama_resep }}
        </td>
        <td  style="text-align:center; font-weight:bold;">
            {{ $nama_lauk_2->nama_resep }}
        </td>
        <td  style="text-align:center; font-weight:bold;">
            {{ $nama_sayur_2->nama_resep }}
        </td>
        <td  style="text-align:center; font-weight:bold;">
            {{ $nama_buah_2->nama_resep }}
        </td>
        <td  style="text-align:center; font-weight:bold;">
            {{ $nama_suplemen_2->nama_resep }}
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
                {{ $no }}
            </td>
            <td  style="text-align:center; font-weight:bold;">
               {{ $karbo[$i]->jumlah ?? 0 }}
            </td>
            <td  style="text-align:center; font-weight:bold;">
                {{ $lauk[$i]->jumlah ?? 0 }}
            </td>
            <td  style="text-align:center; font-weight:bold;">
               {{ $sayur[$i]->jumlah ?? 0 }}
            </td>
            <td  style="text-align:center; font-weight:bold;">
                {{ $buah[$i]->jumlah ?? 0 }}
            </td>
            <td  style="text-align:center; font-weight:bold;">
                {{ $suplemen[$i]->jumlah ?? 0 }}
            </td>
            <td  style="text-align:center; font-weight:bold;">
                
            </td>
            <td  style="text-align:center; font-weight:bold;">
               {{ $karbo_2[$i]->jumlah ?? 0 }}
            </td>
            <td  style="text-align:center; font-weight:bold;">
                {{ $lauk_2[$i]->jumlah ?? 0 }}
            </td>
            <td  style="text-align:center; font-weight:bold;">
               {{ $sayur_2[$i]->jumlah ?? 0 }}
            </td>
            <td  style="text-align:center; font-weight:bold;">
                {{ $buah_2[$i]->jumlah ?? 0 }}
            </td>
            <td  style="text-align:center; font-weight:bold;">
                {{ $suplemen_2[$i]->jumlah ?? 0 }}
            </td>
        </tr>
    @endfor
    <tr>
        <td colspan="7"></td>
    </tr>
    <tr>
        <td colspan="7"></td>
    </tr>
    <tr>
        <td colspan="7"></td>
    </tr>
    <tr>
        <td colspan="7"></td>
    </tr>
    <tr>
        <td></td>
        <td  colspan=4 style="text-align:center; font-weight:bold;">
            (.....................)
        </td>
        <td  colspan=4 style="text-align:center; font-weight:bold;">
            (.....................)
        </td>
        <td  colspan=4 style="text-align:center; font-weight:bold;">
            (.....................)
        </td>
        
    </tr>
    
</table>