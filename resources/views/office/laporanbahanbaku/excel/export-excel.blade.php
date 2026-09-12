<table>
    <tr>
        <td colspan="6" style="font-weight: bold; font-size: 16px; text-align: center;">{{ $title }}</td>
    </tr>
    @php
        $satuan='';
    @endphp
    @if(count($filterInfo) > 0)
    <tr>
        <td colspan="6" style="text-align: center; font-size: 12px;">Filter: {{ implode(' | ', $filterInfo) }}</td>
    </tr>
    @endif
    <tr>
        <td colspan="6"></td>
    </tr>
    <tr style="background-color: #E2E2E2; font-weight: bold; text-align: center;">
        <td style="border: 1px solid #000; padding: 8px;">No</td>
        <td style="border: 1px solid #000; padding: 8px;">Tanggal</td>
        <td style="border: 1px solid #000; padding: 8px;">Nomor PO</td>
        <td style="border: 1px solid #000; padding: 8px;">Nama Bahan</td>
        <td style="border: 1px solid #000; padding: 8px;">Jenis Bahan</td>
        <td style="border: 1px solid #000; padding: 8px;">Total Bahan</td>
        <td style="border: 1px solid #000; padding: 8px;">Satuan</td>
    </tr>
    @forelse($data as $index => $row)
    <tr>
        <td style="border: 1px solid #000; padding: 8px; text-align: center;">{{ $index + 1 }}</td>
        <td style="border: 1px solid #000; padding: 8px; text-align: center;">{{ $row->tanggal_formatted }}</td>
        <td style="border: 1px solid #000; padding: 8px; text-align: center;">{{ $row->nomor_po ?? '-' }}</td>
        <td style="border: 1px solid #000; padding: 8px;">{{ $row->nama_bahan ?? '-' }}</td>
        <td style="border: 1px solid #000; padding: 8px; text-align: center;">{{ $row->jenis_nama }}</td>
        <td style="border: 1px solid #000; padding: 8px; text-align: right;">{{ number_format($row->total_bahan, 0, ',', '.') }}</td>
        <td style="border: 1px solid #000; padding: 8px; text-align: center;">{{ $row->satuan }}</td>
    </tr>
    @php
        $satuan = $row->satuan;
    @endphp
    @empty
    <tr>
        <td colspan="6" style="text-align: center;">Tidak ada data yang ditemukan</td>
    </tr>
    @endforelse
    @if($data->count() > 0)
    <tr style="background-color: #F2F2F2; font-weight: bold;">
        <td colspan="5" style="text-align: right;">Total Keseluruhan:</td>
        <td style="text-align: right;">{{ number_format($totalBahan, 0, ',', '.') }}</td>
    </tr>
    @endif
    <tr>
        <td colspan="6"></td>
    </tr>
    <tr>
        <td colspan="6" style="font-weight: bold;">Ringkasan:</td>
    </tr>
    <tr>
        <td colspan="6">Total Jenis Bahan: {{ $totalRecords }} item</td>
    </tr>
    <tr>
        <td colspan="6">Total Keseluruhan Bahan: {{ number_format($totalBahan, 0, ',', '.') }} {{ $satuan }}</td>
    </tr>
    <tr>
        <td colspan="6">Tanggal Cetak: {{ date('d/m/Y H:i:s') }}</td>
    </tr>
</table>
