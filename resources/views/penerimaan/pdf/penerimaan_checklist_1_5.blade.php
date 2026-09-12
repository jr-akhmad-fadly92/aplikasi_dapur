{{-- Checklist Pendamping --}}
<table style="width:100%; border-collapse:collapse; font-family:Arial, Helvetica, sans-serif; font-size:11px;">
    <tr><td style="font-weight:bold;text-align:left;" colspan="12">Yayasan Bina Bangsa Semarang</td></tr>
    <tr><td style="text-align:left;" colspan="12">Unit Satuan Penerimaan Pelayanan Gizi</td></tr>
    <tr><td style="text-align:left;" colspan="12">FORMULIR CHECKLIST PENERIMAAN BARANG GUDANG HARIAN</td></tr>
    <tr><td style="text-align:left;" colspan="12"></td></tr>
    <tr><td style="text-align:left;" colspan="12"></td></tr>
    @php
        $tanggalPenerimaan = $tanggal ? \Carbon\Carbon::parse($tanggal)->format('d F Y') : '-';
        $items = $items ?? collect();
    @endphp
    <tr>
        <td style="text-align:left;font-weight:bold;" colspan="5">SPPG : {{ $dapur->nama_dapur ?? '-' }}</td>
        <td style="text-align:left;" colspan="2"></td>
        <td style="text-align:left;font-weight:bold;" colspan="7">Tanggal Penerimaan : {{ $tanggalPenerimaan }}</td>
    </tr>
    <tr>
        <td style="text-align:left;" colspan="5"></td>
        <td style="text-align:left;" colspan="2"></td>
        <td style="text-align:left;font-weight:bold;" colspan="7">Tanggal Menu : {{ \Carbon\Carbon::parse($tanggal)->addDay()->format('d F Y') }}</td>
    </tr>
    <tr><td style="height:20px;" colspan="12"></td></tr>
    <tr><td style="text-align:left;font-weight:bold;" colspan="12">Daftar Penerimaan Bahan Baku : Pendamping</td></tr>
    <tr style="background-color:#f0f0f0;">
        <td style="font-weight:bold;text-align:center;border:1px solid #000;">No</td>
        <td style="font-weight:bold;text-align:center;border:1px solid #000;">Jam datang</td>
        <td style="font-weight:bold;text-align:center;border:1px solid #000;">Bahan</td>
        <td style="font-weight:bold;text-align:center;border:1px solid #000;">Qty</td>
        <td style="font-weight:bold;text-align:center;border:1px solid #000;">Satuan</td>
        <td style="font-weight:bold;text-align:center;border:1px solid #000;">Box ke</td>
        <td style="font-weight:bold;text-align:center;border:1px solid #000;">Remark Scan</td>
        <td style="font-weight:bold;text-align:center;border:1px solid #000;">Remark Gudang</td>
    </tr>
    @forelse($items as $index => $item)
        <tr style="background-color:#f0f0f0;">
            <td style="text-align:center;border:1px solid #000;">{{ $index + 1 }}</td>
            <td style="text-align:center;border:1px solid #000;">{{ \Carbon\Carbon::parse($item->tanggal_kedatangan)->format('H:i') }}</td>
            <td style="text-align:center;border:1px solid #000;">{{ $item->bahan }}</td>
            <td style="text-align:center;border:1px solid #000;">{{ $item->jumlah_bahan }}</td>
            <td style="text-align:center;border:1px solid #000;">{{ $item->nama_satuan }}</td>
            <td style="text-align:center;border:1px solid #000;">-</td>
            <td style="text-align:center;border:1px solid #000;"></td>
            <td style="text-align:center;border:1px solid #000;"></td>
        </tr>
    @empty
        <tr><td colspan="8" style="text-align:center;border:1px solid #000;">Tidak ada data</td></tr>
    @endforelse
    <tr><td style="height:20px;" colspan="14"></td></tr>
    <tr>
        <td colspan="2">Supplier,</td><td colspan="2">Petugas Penerimaan</td><td colspan="2">Asisten Lapangan</td><td colspan="2">Ahli Akuntansi</td><td colspan="2">Diperiksa oleh</td><td colspan="2">:</td>
    </tr>
    <tr>
        <td colspan="2"></td><td colspan="2"></td><td colspan="2"></td><td colspan="2"></td><td colspan="2">Hasil</td><td colspan="2">: disetujui / ditolak</td>
    </tr>
    <tr><td style="height:40px;" colspan="12"></td></tr>
    <tr>
        <td colspan="2">___________</td><td colspan="2">___________</td><td colspan="2">___________</td><td colspan="2">___________</td><td colspan="2">___________</td>
    </tr>
</table>
