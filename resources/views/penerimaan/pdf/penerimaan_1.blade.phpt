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
    <tr>
        <td style="text-align:left;font-weight:bold;" colspan="5">SPPG : Sadeng 01, Semarang</td>
        <td style="text-align:left;" colspan="2"></td>
    
        <td style="text-align:left;font-weight:bold;" colspan="7">Tanggal Penerimaan : 10 Desember 2025</td>
        
    </tr>
    <tr>
        <td style="text-align:left;" colspan="5"></td>
        <td style="text-align:left;" colspan="2"></td>
        <td style="text-align:left;font-weight:bold;" colspan="7">Tanggal Menu : 10 Desember 2025</td>
        
    </tr>
    <tr>
        <td style="height:20px;" colspan="12"></td>
    </tr>
    <tr>
        <td style="text-align:left;font-weight:bold;" colspan="12">Daftar Penerimaan Bahan Baku : Beras (Karbohidrat)</td>
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
        // Data contoh beras sesuai dokumen penerimaan_1.pdf
        $contoh_bahan = [
            ['bahan' => 'Beras 25 Kg', 'jumlah' => 25, 'satuan' => 'Kg'],
            ['bahan' => 'Beras 25 Kg', 'jumlah' => 25, 'satuan' => 'Kg'],
            ['bahan' => 'Beras 25 Kg', 'jumlah' => 25, 'satuan' => 'Kg'],
            ['bahan' => 'Beras 25 Kg', 'jumlah' => 25, 'satuan' => 'Kg'],
            ['bahan' => 'Beras 5 Kg', 'jumlah' => 5, 'satuan' => 'Kg'],
            ['bahan' => 'Beras 5 Kg', 'jumlah' => 5, 'satuan' => 'Kg'],
            ['bahan' => 'Beras 5 Kg', 'jumlah' => 5, 'satuan' => 'Kg'],
            ['bahan' => 'Beras 5 Kg', 'jumlah' => 5, 'satuan' => 'Kg'],
            ['bahan' => 'Beras 5 Kg', 'jumlah' => 5, 'satuan' => 'Kg'],
            ['bahan' => 'Beras 5 Kg', 'jumlah' => 5, 'satuan' => 'Kg'],
            ['bahan' => 'Beras 5 Kg', 'jumlah' => 5, 'satuan' => 'Kg'],
            ['bahan' => 'Beras 5 Kg', 'jumlah' => 5, 'satuan' => 'Kg'],
            ['bahan' => 'Beras 5 Kg', 'jumlah' => 5, 'satuan' => 'Kg'],
            ['bahan' => 'Beras 5 Kg', 'jumlah' => 5, 'satuan' => 'Kg'],
            ['bahan' => 'Beras 5 Kg', 'jumlah' => 5, 'satuan' => 'Kg'],
            ['bahan' => 'Beras 5 Kg', 'jumlah' => 5, 'satuan' => 'Kg'],
            ['bahan' => 'Beras 1 Kg', 'jumlah' => 1, 'satuan' => 'Kg'],
            ['bahan' => 'Beras 1 Kg', 'jumlah' => 1, 'satuan' => 'Kg'],
            ['bahan' => 'Beras 1 Kg', 'jumlah' => 1, 'satuan' => 'Kg'],
            ['bahan' => 'Beras 1 Kg', 'jumlah' => 1, 'satuan' => 'Kg'],
            ['bahan' => 'Beras 1 Kg', 'jumlah' => 1, 'satuan' => 'Kg'],
            ['bahan' => 'Beras 1 Kg', 'jumlah' => 1, 'satuan' => 'Kg'],
            ['bahan' => 'Beras 1 Kg', 'jumlah' => 1, 'satuan' => 'Kg'],
            ['bahan' => 'Beras 1 Kg', 'jumlah' => 1, 'satuan' => 'Kg'],
        ];
        
        $no = 1;
        $maks_row = 25;
        $jumlah_bahan = 0;
        
        // Hitung box numbering berurutan
        $box_counter = 0;
        $total_per_box = 0;
    @endphp
    
    @foreach($contoh_bahan as $item)
    @php
        $jumlah_bahan += $item['jumlah'];
        $box_ke = '-';
        
        if ($item['jumlah'] == 25) {
            $box_ke = '-';
        } else {
            // Untuk 5kg dan 1kg, hitung box secara berurutan
            $total_per_box += $item['jumlah'];
            
            // Setiap 10kg = 1 box
            if ($total_per_box <= 10) {
                $box_counter = (int) ceil($total_per_box / 10);
            } else {
                $box_counter = (int) ceil($total_per_box / 10);
            }
            $box_ke = $box_counter;
        }
    @endphp
    <tr>
        <td style="text-align:center;border:1px solid #000;padding:5px;">{{ $no }}</td>
        <td style="text-align:center;border:1px solid #000;padding:5px;">11:00</td>
        <td style="text-align:left;border:1px solid #000;padding:5px;">{{ $item['bahan'] }}</td>
        <td style="text-align:center;border:1px solid #000;padding:5px;"> </td>
        <td style="text-align:center;border:1px solid #000;padding:5px;">{{ $item['jumlah'] }}</td>
        <td style="text-align:center;border:1px solid #000;padding:5px;">{{ $item['satuan'] }}</td>
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
    @endforeach
    
    
    
    <tr style="background-color:#f0f0f0;">
        <td colspan="3" style="font-weight:bold;text-align:right;border:1px solid #000;padding:5px;">TOTAL</td>
        <td style="text-align:center;border:1px solid #000;padding:5px;"></td>
        <td style="font-weight:bold;text-align:center;border:1px solid #000;padding:5px;">{{ $jumlah_bahan }}</td>
        <td style="font-weight:bold;text-align:center;border:1px solid #000;padding:5px;">Kg</td>
        <td style="font-weight:bold;text-align:center;border:1px solid #000;padding:5px;">{{ $box_counter }}</td>
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
