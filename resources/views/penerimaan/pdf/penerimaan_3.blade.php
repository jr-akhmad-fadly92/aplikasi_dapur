{{-- Generated from dokumen/penerimaan_1.pdf --}}
<table style="width:100%; border-collapse:collapse; font-family:Arial, Helvetica, sans-serif; font-size:11px;">
    <tr>
        <td style="font-weight:bold;text-align:left;" colspan="12">Yayasan Bina Bangsa Semarang</td>
    </tr>
    <tr>
        <td style="text-align:left;" colspan="12">Unit Satuan Penerimaan Pelayanan Gizi</td>
    </tr>
    <tr>
        <td style="text-align:left;" colspan="12">FORMULIR CHECKLIST PENERIMAAN BARANG HARIAN</td>
    </tr>
     <tr>
        <td style="text-align:left;" colspan="12"></td>
    </tr>
     <tr>
        <td style="text-align:left;" colspan="12"></td>
    </tr>
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
    <tr>
        <td style="height:20px;" colspan="12"></td>
    </tr>
    <tr>
        <td style="text-align:left;font-weight:bold;" colspan="12">Daftar Penerimaan Bahan Baku : Sayur</td>
    </tr>
   
    
    <tr style="background-color:#f0f0f0;">
        <td rowspan=2 style="font-weight:bold;text-align:center;border:1px solid #000;">No</td>
        <td rowspan=2 style="font-weight:bold;text-align:center;border:1px solid #000;">Jam datang</td>
        <td rowspan=2 style="font-weight:bold;text-align:center;border:1px solid #000;">Bahan</td>
        <td rowspan=2 style="font-weight:bold;text-align:center;border:1px solid #000;">Nomor PO</td>
        <td rowspan=2 style="font-weight:bold;text-align:center;border:1px solid #000;">Qty</td>    
        <td rowspan=2 style="font-weight:bold;text-align:center;border:1px solid #000;">Satuan</td>
        <td rowspan=2 style="font-weight:bold;text-align:center;border:1px solid #000;">Box ke </td>
        <td colspan=3 style="font-weight:bold;text-align:center;border:1px solid #000;">Pemeriksaan</td>
        <td colspan=2 style="font-weight:bold;text-align:center;border:1px solid #000;">Jumlah</td>
        <td colspan=2 style="font-weight:bold;text-align:center;border:1px solid #000;">Remark</td>
    </tr>
    <tr style="background-color:#f0f0f0;">
        
        <td  style="font-weight:bold;text-align:center;border:1px solid #000;">Visual</td>
        <td  style="font-weight:bold;text-align:center;border:1px solid #000;">Bau</td>
        <td  style="font-weight:bold;text-align:center;border:1px solid #000;">Textur</td>
        <td  style="font-weight:bold;text-align:center;border:1px solid #000;">Diterima</td>
        <td  style="font-weight:bold;text-align:center;border:1px solid #000;">Ditolak</td>
        <td  style="font-weight:bold;text-align:center;border:1px solid #000;">Scan</td>
        <td  style="font-weight:bold;text-align:center;border:1px solid #000;">Kirim Gudang</td>
        
    </tr>
    
    @php
        $no = 1;
        $jumlah_bahan = 0;
    @endphp
    @forelse($items as $item)
    @php
        $jumlah_bahan += $item->jumlah_bahan;
        $box_ke = '-';
        $jam_datang = $item->tanggal_kedatangan ? \Carbon\Carbon::parse($item->tanggal_kedatangan)->format('H:i') : '';
    @endphp
    <tr>
        <td style="text-align:center;border:1px solid #000;padding:5px;">{{ $no }}</td>
        <td style="text-align:center;border:1px solid #000;padding:5px;">{{ $jam_datang }}</td>
        <td style="text-align:left;border:1px solid #000;padding:5px;">{{ $item->bahan }}</td>
        <td style="text-align:center;border:1px solid #000;padding:5px;">{{ $item->nomor_po }}</td>
        <td style="text-align:center;border:1px solid #000;padding:5px;">{{ $item->jumlah_bahan }}</td>
        <td style="text-align:center;border:1px solid #000;padding:5px;">{{ $item->nama_satuan }}</td>
        <td style="text-align:center;border:1px solid #000;padding:5px;">{{ $box_ke }}</td>
        <td style="text-align:center;border:1px solid #000;padding:5px;"></td>
        <td style="text-align:center;border:1px solid #000;padding:5px;"></td>
        <td style="text-align:center;border:1px solid #000;padding:5px;"></td>
        <td style="text-align:center;border:1px solid #000;padding:5px;"></td>
        <td style="text-align:center;border:1px solid #000;padding:5px;"></td>
        <td style="text-align:center;border:1px solid #000;padding:5px;"></td>
        <td style="text-align:center;border:1px solid #000;padding:5px;"> </td>
        
    </tr>
    @php $no++; @endphp
    @empty
    <tr>
        <td colspan="14" style="text-align:center;border:1px solid #000;padding:5px;">Tidak ada data penerimaan.</td>
    </tr>
    @endforelse
    
    
    <tr style="background-color:#f0f0f0;">
        <td colspan="3" style="font-weight:bold;text-align:right;border:1px solid #000;padding:5px;">TOTAL</td>
        <td style="text-align:center;border:1px solid #000;padding:5px;"></td>
        <td style="font-weight:bold;text-align:center;border:1px solid #000;padding:5px;">{{ $jumlah_bahan }}</td>
        <td style="font-weight:bold;text-align:center;border:1px solid #000;padding:5px;"></td>
        <td style="font-weight:bold;text-align:center;border:1px solid #000;padding:5px;"></td>
        <td colspan="7" style="text-align:center;border:1px solid #000;padding:5px;"></td>
    </tr>
    
    <tr>
        <td style="height:20px;" colspan="14"></td>
    </tr>
    <tr>
        <td colspan="2">Supplier,</td>
        <td colspan="2">Petugas Penerimaan</td>
        <td colspan="3">Asisten Lapangan</td>
        <td colspan="3">Ahli Akuntansi</td>
        <td colspan="2">Diperikas oleh </td>
        <td colspan="2">: </td>
    </tr>
    <tr>
        <td colspan="2"></td>
        <td colspan="2"> </td>
        <td colspan="3"> </td>
        <td colspan="3"> </td>
        <td colspan="2">Hasil </td>
        <td colspan="2">: disetujui / ditolak </td>
    </tr>
    <tr>
        <td style="height:40px;" colspan="12"></td>
    </tr>
     <tr>
        <td colspan="2">___________</td>
        <td colspan="2">___________</td>
        <td colspan="3">___________</td>
        <td colspan="3">___________</td>
        <td colspan="2">___________</td>
        <td colspan="2">___________</td>
    </tr>
    
</table>
