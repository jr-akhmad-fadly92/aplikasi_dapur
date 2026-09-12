<table style="border-collapse: collapse; width: 100%;">
    <tr>
        <td colspan="7" style="font-weight: bold; font-size: 16px; text-align: center; border: 1px solid #000; padding: 8px;">LAPORAN PO PEMBELIAN</td>
    </tr>
    @if($startDate && $endDate)
    <tr>
        <td colspan="7" style="text-align: center; font-size: 12px; border: 1px solid #000; padding: 5px;">Periode: {{ $startDate }} s/d {{ $endDate }}</td>
    </tr>
    @endif
    <tr>
        <td colspan="7" style="border: none; height: 10px;"></td>
    </tr>
    <tr style="background-color: #E2E2E2; font-weight: bold;">
        <td style="border: 1px solid #000; padding: 8px; text-align: center;">No</td>
        <td style="border: 1px solid #000; padding: 8px; text-align: center;">Tanggal</td>
        <td style="border: 1px solid #000; padding: 8px; text-align: center;">Nomor PO</td>
        <td style="border: 1px solid #000; padding: 8px; text-align: center;">Bahan</td>
        <td style="border: 1px solid #000; padding: 8px; text-align: center;">Jumlah</td>
        <td style="border: 1px solid #000; padding: 8px; text-align: center;">Satuan</td>
        <td style="border: 1px solid #000; padding: 8px; text-align: center;">Total Harga</td>
    </tr>
    @forelse($data as $index => $row)
    <tr>
        <td style="text-align: center; border: 1px solid #000; padding: 5px;">{{ $index + 1 }}</td>
        <td style="text-align: center; border: 1px solid #000; padding: 5px;">{{ $row->tanggal_formatted }}</td>
        <td style="text-align: center; border: 1px solid #000; padding: 5px;">{{ $row->nomor_po ?? '-' }}</td>
        <td style="border: 1px solid #000; padding: 5px;">{{ $row->bahan ?? '-' }}</td>
        <td style="text-align: right; border: 1px solid #000; padding: 5px;">{{ $row->jumlah_formatted }}</td>
        <td style="text-align: center; border: 1px solid #000; padding: 5px;">{{ $row->satuan ?? '-' }}</td>
        <td style="text-align: right; border: 1px solid #000; padding: 5px;">{{ $row->harga_formatted }}</td>
    </tr>
    @empty
    <tr>
        <td colspan="7" style="text-align: center; border: 1px solid #000; padding: 10px;">Tidak ada data yang ditemukan</td>
    </tr>
    @endforelse
    @if($data->count() > 0)
    <tr style="background-color: #F2F2F2; font-weight: bold;">
        <td colspan="6" style="text-align: right; border: 1px solid #000; padding: 8px;">TOTAL:</td>
        <td style="text-align: right; border: 1px solid #000; padding: 8px;">Rp {{ number_format($totalHarga, 0, ',', '.') }}</td>
    </tr>
    @endif
</table>
