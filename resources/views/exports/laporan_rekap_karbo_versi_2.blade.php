{{-- Generated from database for komponen sehat = 1 (Karbo) --}}
<table style="width:100%; border-collapse:collapse; font-family:Arial, Helvetica, sans-serif; font-size:11px; border: 1px solid #000;">
    <tr>
        <td style="text-align:center;"></td>
        <td colspan="3" style="font-weight:bold;text-align:left;font-weight:bold;">Yayasan Bina Bangsa Semarang</td>
    </tr>
    <tr>
        <td style="text-align:center;"></td>
        <td colspan="3" style="text-align:left;">Unit SPPG</td>
    </tr>
    <tr>
        <td style="text-align:center;"></td>
        <td colspan="3" style="text-align:left;">Rekap Karbohidrat Periode Pelayanan SPPG YBBS : {{ \Carbon\Carbon::parse($tanggal_awal)->format('j F Y') }} - {{ \Carbon\Carbon::parse($tanggal_akhir)->translatedFormat('j F Y') }}</td>
    </tr>
    <tr>
        <td style="text-align:center;"></td>
        <td colspan="3" style="text-align:left;">{{ $dapur->nama_dapur ?? '' }} {{ $dapur->kecamatan ?? '' }}, {{ $dapur->kota ?? '' }}</td>
    </tr>
    <tr>
        <td style="text-align:center;"></td>
        <td colspan="3" style="text-align:left;"></td>
    </tr>

    <tr>
        <td style="text-align:center"></td>
        <td style="text-align:center;border: 1px solid #000;font-weight:bold;">No</td>
        <td style="text-align:center;border: 1px solid #000;font-weight:bold;">Karbohidrat</td>
        <td style="text-align:center;border: 1px solid #000;font-weight:bold;">Menu</td>
        <td style="text-align:center;border: 1px solid #000;font-weight:bold;">Jumlah Masak</td>
    </tr>
    @forelse($grouped as $golongan => $data)
        @foreach($data['reseps'] as $idx => $resep)
        <tr>
            <td style="text-align:center"></td>
            @if($idx === 0)
            <td style="text-align:center;border: 1px solid #000;" rowspan="{{ count($data['reseps']) }}">{{ $data['rowIndex'] }}</td>
            <td style="text-align:center;border: 1px solid #000;" rowspan="{{ count($data['reseps']) }}">{{ $golongan }}</td>
            @endif
            <td style="text-align:center;border: 1px solid #000;">{{ $resep }}</td>
            <td style="text-align:center;border: 1px solid #000;">
                @php
                    $total = 0;
                    foreach($data['count_by_date'] as $jumlah) {
                        $total += in_array($resep, $jumlah, true) ? 1 : 0;
                    }
                @endphp
                {{ $total }}
            </td>
        </tr>
        @endforeach
    @empty
    <tr>
        <td colspan="5" style="text-align:center;border: 1px solid #000;">Tidak ada data</td>
    </tr>
    @endforelse
</table>
