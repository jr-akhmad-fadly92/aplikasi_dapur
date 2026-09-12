<table>
    <!-- Header akan dihandle oleh registerEvents() untuk merge -->
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>

    @if(isset($isLimited) && $isLimited)
        <!-- Warning Row for Limited Data -->
        <tr>
            <td colspan="6">PERHATIAN: Data terbatas pada {{ number_format($maxRecords, 0, ',', '.') }} record terbaru dari
                total {{ number_format($totalRecords, 0, ',', '.') }} record untuk mengoptimalkan performa</td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    @endif

    <!-- Table Header -->
    <tr>
        <td>No</td>
        <td>Tanggal</td>
        <td>Nomor PO</td>
        <td>Bahan</td>
        <td>Jumlah</td>
        <td>Total Harga</td>
    </tr>

    <!-- Data Rows -->
    @php
        $no = 1;
        $dataCount = isset($data) && is_countable($data) ? count($data) : 0;
    @endphp
    
    @if($dataCount > 0)
        @foreach($data as $row)
            <tr>
                <td>{{ $no++ }}</td>
                <td>{{ isset($row->tanggal_approve) && $row->tanggal_approve ? \Carbon\Carbon::parse($row->tanggal_approve)->format('d/m/Y') : '-' }}</td>
                <td>{{ $row->nomor_po ?? '-' }}</td>
                <td>{{ $row->bahan ?? '-' }}</td>
                <td>{{ number_format($row->jumlah_bahan ?? 0, 0, ',', '.') }}</td>
                <td>{{ number_format($row->jumlah_po ?? 0, 0, ',', '.') }}</td>
            </tr>
        @endforeach
    @else
        <tr>
            <td colspan="6" style="text-align: center; font-style: italic;">Tidak ada data untuk periode yang dipilih</td>
        </tr>
    @endif

    <!-- Total Row -->
    <tr>
        <td colspan="5">TOTAL:</td>
        <td>{{ number_format($totalHarga ?? 0, 0, ',', '.') }}</td>
    </tr>
</table>