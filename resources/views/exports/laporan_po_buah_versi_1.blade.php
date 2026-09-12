{{-- Generated from database for komponen sehat = 4 (Buah) --}}
<table style="width:100%; border-collapse:collapse; font-family:Arial, Helvetica, sans-serif; font-size:11px; border: 1px solid #000;">
    <tr>
        <td style="text-align:center;"></td>
        <td colspan="5" style="font-weight:bold;text-align:left;font-weight:bold;">Yayasan Bina Bangsa Semarang</td>
    </tr>
    <tr>
        <td style="text-align:center;"></td>
        <td colspan="5" style="text-align:left;">Unit SPPG</td>
    </tr>
    <tr>
        <td style="text-align:center;"></td>
        <td colspan="5" style="text-align:left;">Rekap Buah Periode Pelayanan SPPG YBBS {{ \Carbon\Carbon::parse($tanggal_awal)->format('j') }} - {{ \Carbon\Carbon::parse($tanggal_akhir)->translatedFormat('j F Y') }}</td>
    </tr>
    <tr>
        <td style="text-align:center;"></td>
        <td colspan="5" style="text-align:left;">{{ $dapur->nama_dapur ?? '' }} {{ $dapur->kecamatan ?? '' }}, {{ $dapur->kota ?? '' }}</td>
    </tr>
    <tr>
        <td style="text-align:center;"></td>
        <td colspan="5" style="text-align:left;"></td>
    </tr>

    <tr>
        <td style="text-align:center"></td>
        <td style="text-align:center;border: 1px solid #000;font-weight:bold;">No</td>
        <td style="text-align:center;border: 1px solid #000;font-weight:bold;">Tanggal Digunakan</td>
        <td style="text-align:center;border: 1px solid #000;font-weight:bold;">Nomor PO</td>
        <td style="text-align:center;border: 1px solid #000;font-weight:bold;">Pax A</td>
        <td style="text-align:center;border: 1px solid #000;font-weight:bold;">Pax B</td>
    </tr>

    @forelse($records as $index => $row)
        <tr>
            <td style="text-align:center;"></td>
            <td style="text-align:center;border: 1px solid #000;">{{ $index + 1 }}</td>
            <td style="text-align:center;border: 1px solid #000;">{{ $row->tanggal_digunakan ? \Carbon\Carbon::parse($row->tanggal_digunakan)->translatedFormat('j F Y') : '' }}</td>
            <td style="text-align:center;border: 1px solid #000;">{{ $row->nomor_po ?? '' }}</td>
            @if(($row->total_penerima_a ?? 0) > 0)
                <td style="text-align:center;border: 1px solid #000;">{{ $row->bahan }}</td>
                <td style="text-align:center;border: 1px solid #000;">-</td>
            @else
                <td style="text-align:center;border: 1px solid #000;">-</td>
                <td style="text-align:center;border: 1px solid #000;">{{ $row->bahan }}</td>
            @endif
        </tr>
    @empty
        <tr>
            <td colspan="5" style="text-align:center;border: 1px solid #000;">Tidak ada data pada rentang tanggal ini</td>
        </tr>
    @endforelse

    <tr>
        <td style="text-align:center;"></td>
        <td colspan="5" style="text-align:left;font-weight:bold;"></td>
    </tr>

    <tr>
        <td style="text-align:center;"></td>
        <td colspan="5" style="text-align:left;font-weight:bold;">TOTAL BAHAN BAKU DIGUNAKAN </td>
    </tr>

    <tr>
        <td style="text-align:center;"></td>
        <td style="text-align:center;border: 1px solid #000;font-weight:bold;">No</td>
        <td style="text-align:left;border: 1px solid #000;font-weight:bold;">Bahan</td>
        <td style="text-align:center;border: 1px solid #000;font-weight:bold;">Golongan</td>
        <td style="text-align:center;border: 1px solid #000;font-weight:bold;">Pax A</td>
        <td style="text-align:center;border: 1px solid #000;font-weight:bold;">Pax B</td>
    </tr>

    @forelse($summary as $idx => $item)
        <tr>
            <td style="text-align:center;"></td>
            <td style="text-align:center;border: 1px solid #000;">{{ $idx + 1 }}</td>
            <td style="text-align:left;border: 1px solid #000;">{{ $item['bahan'] }}</td>
            <td style="text-align:center;border: 1px solid #000;">{{ $item['golongan'] }}</td>
            <td style="text-align:center;border: 1px solid #000;">{{ $item['count_pax_a'] }}</td>
            <td style="text-align:center;border: 1px solid #000;">{{ $item['count_pax_b'] }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="6" style="text-align:center;border: 1px solid #000;">Tidak ada summary</td>
        </tr>
    @endforelse
</table>
