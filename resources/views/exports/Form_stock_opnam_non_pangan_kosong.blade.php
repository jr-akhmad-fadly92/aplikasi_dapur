{{-- ============================= --}}
{{-- HEADER LAPORAN --}}
{{-- ============================= --}}
<table style="width:100%; border-collapse:collapse;">
    <tr>
        <td></td>
        <td colspan="8" style="text-align:center; font-weight:bold;">
            Form Stok Opnam Bahan Non Pangan {{ $dapur->nama_dapur }}
        </td>
    </tr>
    <tr><td></td><td colspan="8"></td></tr>
    
</table>

<table >
    <tr>
        <td></td>
        <td  colspan="8" style="text-align:left; font-weight:bold;">
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
            Satuan
        </td>
        <td  style="text-align:center; font-weight:bold;">
            Stok Awal
        </td>
        <td  style="text-align:center; font-weight:bold;">
            barang Masuk
        </td>
        <td  style="text-align:center; font-weight:bold;">
            stok Akhir
        </td>
        <td  style="text-align:center; font-weight:bold;">
            barang Keluar
        </td>
        <td  style="text-align:center; font-weight:bold;">
            Keterangan
        </td>
    </tr>
    
    @php
    $rows = $rows ?? $count ?? 30; // jumlah baris yang akan dicetak
    @endphp
    @for ($i = 1; $i <= $rows; $i++)
        <tr>
            <td></td>
            <td style="text-align:center; font-weight:bold;">{{ $i }}</td>
            <td style="text-align:left;">&nbsp;</td>
            <td style="text-align:center;">&nbsp;</td>
            <td style="text-align:center;">&nbsp;</td>
            <td style="text-align:center;">&nbsp;</td>
            <td style="text-align:center;">&nbsp;</td>
            <td style="text-align:center;">&nbsp;</td>
            <td style="text-align:center;">&nbsp;</td>
        </tr>
    @endfor
    <tr>
        <td></td>
        <td colspan=8></td>
    </tr>
    <tr>
        <td></td>
        <td colspan=8></td>
    </tr>
    <tr>
        <td></td>
        <td colspan=8></td>
    </tr>
    <tr>
        <td></td>
        <td colspan=8></td>
    </tr>
    <tr>
        <td></td>
        <td  colspan=3 style="text-align:center; font-weight:bold;">
            (.....................)
        </td>
        <td  colspan=3 style="text-align:center; font-weight:bold;">
            (.....................)
        </td>
        <td  colspan=2 style="text-align:center; font-weight:bold;">
            (.....................)
        </td>
        
    </tr>
    
</table>