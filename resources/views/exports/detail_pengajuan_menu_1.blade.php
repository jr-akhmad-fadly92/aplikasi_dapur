<style>
.auto-cols {
    table-layout: auto;
}
.auto-cols td:nth-child(2),
.auto-cols td:nth-child(4) {
    width: 1%;
    white-space: nowrap;
}
</style>

{{-- ============================= --}}
{{-- HEADER LAPORAN --}}
{{-- ============================= --}}
<table class="auto-cols" style="width:100%; border-collapse:collapse;">
    <tr>
        <td></td>
        <td  style="text-align:left; font-weight:bold;">
            Yayasan Bina bangsa Semarang
        </td>
    </tr>
    <tr><td></td><td colspan="30"></td></tr>
    <tr>
        <td></td>
        <td  style="text-align:left; font-weight:bold;">
            Unit Kegiatan MBG
        </td>
    </tr>
    <tr>
        <td></td>
        <td  style="text-align:left; font-weight:bold;">
            {{ $data_dapur->nama_dapur ?? '-'   }}
        </td>
    </tr>
    <tr>
        <td></td>
        <td  style="text-align:left; font-weight:bold;">
            Daftar Menu Minggu ke ... Februari 2026 (... - ... Februari 2026) 
        </td>
    </tr>
    <tr>
        <td></td>
        <td  style="text-align:left; font-weight:bold;">
            Pelayanan 6 hari
        </td>
    </tr>
    <tr>
        <td></td>
        <td  style="text-align:left; font-weight:bold;">
            1. Menu hari ke ... ( {{ $data_menu->tanggal_kirim  ? \Carbon\Carbon::parse($data_menu->tanggal_kirim)->locale('id')->translatedFormat('l, j F Y') : '-' }})
        </td>
    </tr>
    <tr><td></td><td colspan="30"></td></tr>
    <tr>
        <td></td>
        <td  style="text-align:left; font-weight:bold;">
            {{$keterangan_pax}} 
        </td>
    </tr>
    <tr><td></td><td colspan="30"></td></tr>
</table>
{{-- ============================= --}}
{{-- ISI LAPORAN --}}
{{-- ============================= --}}
<table class="auto-cols" style="width:100%; border-collapse:collapse;">
   
    <tr>
        <td></td>
        <td style="text-align:left;border: 1px solid #000;">{{$jumlah_pax}}</td>
        <td style="text-align:left;border: 1px solid #000;">pax golongan {{$keterangan_pax == 'ANAK SEKOLAH PAX A' ? 'A' : 'B'}}</td>
        <td style="text-align:left;border: 1px solid #000;">koefisien</td>
        <td style="text-align:left;border: 1px solid #000;">0%</td>
        <td style="text-align:left;border: 1px solid #000;">{{$jumlah_pax}}</td>
    </tr>
    <tr>
        <td></td>
        <td rowspan=2 style="text-align:left;border: 1px solid #000;">No</td>
        <td rowspan=2 style="text-align:left;border: 1px solid #000;">jenis</td>
        <td rowspan=2 style="text-align:left;border: 1px solid #000;">Menu</td>
        <td  rowspan=2  style="text-align:left;border: 1px solid #000;">Total Komoditas</td>
        <td  colspan=2  style="text-align:left;border: 1px solid #000;">No Bahan Pangan Lain</td>
        <td rowspan=2 style="text-align:left;border: 1px solid #000;">Pengulangan</td>
        <td  rowspan=2 colspan=3 style="text-align:left;border: 1px solid #000;">No Pengulangan</td>
        <td  colspan=4 style="text-align:left;border: 1px solid #000;">Qty bahan pangan lain</td>
        <td colspan=2 rowspan=2 style="text-align:left;border: 1px solid #000;">Bahan Mentah</td>
        <td rowspan=2 style="text-align:left;border: 1px solid #000;">Berat Mentah</td>
        <td rowspan=2 style="text-align:left;border: 1px solid #000;">Edit Berat Mentah</td>
        <td rowspan=2 style="text-align:left;border: 1px solid #000;">Total Qty</td>
        <td rowspan=2 style="text-align:left;border: 1px solid #000;">Edit Qty</td>
        <td rowspan=2 style="text-align:left;border: 1px solid #000;">Edit 2 %</td>
        <td rowspan=2 style="text-align:left;border: 1px solid #000;">Unit</td>
        <td rowspan=2 style="text-align:left;border: 1px solid #000;">Total Pcs</td>
        <td rowspan=2 style="text-align:left;border: 1px solid #000;">Edit pcs</td>
        <td rowspan=2 style="text-align:left;border: 1px solid #000;">Unit</td>
        <td rowspan=2 style="text-align:left;border: 1px solid #000;">Total Qty</td>
        <td rowspan=2 style="text-align:left;border: 1px solid #000;">Edit Qty (tulis manual)</td>
        <td rowspan=2 style="text-align:left;border: 1px solid #000;">Final PO</td>
        <td rowspan=2 style="text-align:left;border: 1px solid #000;">Sat</td>
        <td rowspan=2 style="text-align:left;border: 1px solid #000;">Refrensi Harga</td>
    </tr>
    <tr>
         <td></td>
        <td style="text-align:left;border: 1px solid #000;">urut</td>
        <td style="text-align:left;border: 1px solid #000;">Item</td>
        <td style="text-align:left;border: 1px solid #000;">1</td>
        <td style="text-align:left;border: 1px solid #000;">2</td>
        <td style="text-align:left;border: 1px solid #000;">3</td>
        <td style="text-align:left;border: 1px solid #000;">Total BPL</td>
    </tr>
    <tr>
        <td></td>
        <td colspan="30" style="text-align:left;border: 1px solid #000;">Gol. A : 1003 porsi </td>
    </tr>
     <!-- ============================= -->
        {{-- Karbo --}}
        {{-- ============================= --}} 
    @php
    $rows_karbo_utama = $rows_karbo_utama ?? collect();
    $nomor_urut_bahan = 0;
    @endphp

    @foreach ($rows_karbo_utama as $row)
    @php
    $satuan = $row->satuan ?? '-';
    $jumlah = $row->jumlah ?? 0;
    if($satuan == 'kg' || $satuan == 'Kg' || $satuan == 'KG'){
        $satuan = 'gram';
        $jumlah = $jumlah * 1000;
    } elseif($satuan == 'liter' || $satuan == 'Liter' || $satuan == 'LITER'){
        $satuan = 'ml';
        $jumlah = $jumlah * 1000;
    } elseif($satuan == 'pcs' || $satuan == 'Pcs' || $satuan == 'PCS'){
        $satuan = 'Pcs';
        $jumlah = $jumlah;
    } else {
        $satuan = $row->satuan ?? '-';
        $jumlah = $row->jumlah ?? 0;
    }
    @endphp
    <tr>
        <td></td>
        <td style="text-align:left;border: 1px solid #000;">{{ $loop->iteration }}</td> {{-- urut --}}
        <td style="text-align:left;border: 1px solid #000;">Karbo</td>{{-- Jenis --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $row->nama_resep ?? '-' }}</td>{{-- Menu --}}
        <td style="text-align:left;border: 1px solid #000;"></td>{{-- Total Komoditas --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- No Bahan Pangan Lain : urut --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- No Bahan Pangan Lain : Item --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Pengulangan --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- No Pengulangan 1 --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- No Pengulangan 2 --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- No Pengulangan 3 --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Qty bahan pangan lain 1 --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Qty bahan pangan lain 2 --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Qty bahan pangan lain 3 --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Qty bahan pangan lain total --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $row->bahan ?? '-' }}</td>{{-- Bahan Mentah utama --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Bahan Mentah bumbu lainnya --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $row->karbo_porsi_a?? '-' }}</td>{{-- Berat Mentah --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Edit Berat Mentah --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $jumlah ?? '-' }}</td>{{-- Total Qty --}}
        <td style="text-align:left;border: 1px solid #000;"></td>{{-- Edit Qty --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Edit 2 % --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $satuan }}</td>{{-- Unit --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Total Pcs --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Edit pcs --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $satuan }}</td>{{-- Unit --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $row->jumlah ?? '-' }}</td>{{-- Total Qty --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Edit Qty (tulis manual) --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Final PO --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $row->satuan ?? '-' }}</td>{{-- Sat --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $row->harga ?? '-' }}</td>{{-- Refrensi Harga --}}
    </tr>
   
    @endforeach

    @php
    $rows_karbo_bumbu = $rows_karbo_bumbu ?? collect();
    @endphp

    @foreach ($rows_karbo_bumbu as $row)
    @php
    $satuan = $row->satuan ?? '-';
    $jumlah = $row->jumlah ?? 0;
    if($satuan == 'kg' || $satuan == 'Kg' || $satuan == 'KG'){
        $satuan = 'gram';
        $jumlah = $jumlah * 1000;
    } elseif($satuan == 'liter' || $satuan == 'Liter' || $satuan == 'LITER'){
        $satuan = 'ml';
        $jumlah = $jumlah * 1000;
    } elseif($satuan == 'pcs' || $satuan == 'Pcs' || $satuan == 'PCS'){
        $satuan = 'Pcs';
        $jumlah = $jumlah;
    } else {
        $satuan = $row->satuan ?? '-';
        $jumlah = $row->jumlah ?? 0;
    }
    $nomor_urut_bahan = $nomor_urut_bahan + 1;
    @endphp
    <tr>
        <td></td>
        <td style="text-align:left;border: 1px solid #000;">{{ $loop->iteration }}</td> {{-- urut --}}
        <td style="text-align:left;border: 1px solid #000;"></td>{{-- Jenis --}}
        <td style="text-align:left;border: 1px solid #000;"></td>{{-- Menu --}}
        <td style="text-align:left;border: 1px solid #000;"></td>{{-- Total Komoditas --}}
        <td style="text-align:left;border: 1px solid #000;">{{$nomor_urut_bahan}}</td>{{-- No Bahan Pangan Lain : urut --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- No Bahan Pangan Lain : Item --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Pengulangan --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- No Pengulangan 1 --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- No Pengulangan 2 --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- No Pengulangan 3 --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Qty bahan pangan lain 1 --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Qty bahan pangan lain 2 --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Qty bahan pangan lain 3 --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Qty bahan pangan lain total --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $row->bahan ?? '-' }}</td>{{-- Bahan Mentah utama --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Bahan Mentah bumbu lainnya --}}
        <td style="text-align:left;border: 1px solid #000;"></td>{{-- Berat Mentah --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Edit Berat Mentah --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $jumlah ?? '-' }}</td>{{-- Total Qty --}}
        <td style="text-align:left;border: 1px solid #000;"></td>{{-- Edit Qty --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Edit 2 % --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $satuan }}</td>{{-- Unit --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Total Pcs --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Edit pcs --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $satuan }}</td>{{-- Unit --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $row->jumlah ?? '-' }}</td>{{-- Total Qty --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Edit Qty (tulis manual) --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Final PO --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $row->satuan ?? '-' }}</td>{{-- Sat --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $row->harga ?? '-' }}</td>{{-- Refrensi Harga --}}
    </tr>
   
    @endforeach

    @php
    $rows_protein_utama = $rows_protein_utama ?? collect();
    @endphp

    @foreach ($rows_protein_utama as $row)
    @php
    $satuan = $row->satuan ?? '-';
    $jumlah = $row->jumlah ?? 0;
    if($satuan == 'kg' || $satuan == 'Kg' || $satuan == 'KG'){
        $satuan = 'gram';
        $jumlah = $jumlah * 1000;
    } elseif($satuan == 'liter' || $satuan == 'Liter' || $satuan == 'LITER'){
        $satuan = 'ml';
        $jumlah = $jumlah * 1000;
    } elseif($satuan == 'pcs' || $satuan == 'Pcs' || $satuan == 'PCS'){
        $satuan = 'Pcs';
        $jumlah = $jumlah;
    } else {
        $satuan = $row->satuan ?? '-';
        $jumlah = $row->jumlah ?? 0;
    }
    @endphp
    <tr>
        <td></td>
        <td style="text-align:left;border: 1px solid #000;">{{ $loop->iteration }}</td> {{-- urut --}}
        <td style="text-align:left;border: 1px solid #000;">Protein</td>{{-- Jenis --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $row->nama_resep ?? '-' }}</td>{{-- Menu --}}
        <td style="text-align:left;border: 1px solid #000;"></td>{{-- Total Komoditas --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- No Bahan Pangan Lain : urut --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- No Bahan Pangan Lain : Item --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Pengulangan --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- No Pengulangan 1 --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- No Pengulangan 2 --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- No Pengulangan 3 --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Qty bahan pangan lain 1 --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Qty bahan pangan lain 2 --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Qty bahan pangan lain 3 --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Qty bahan pangan lain total --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $row->bahan ?? '-' }}</td>{{-- Bahan Mentah utama --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Bahan Mentah bumbu lainnya --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $row->protein_porsi_a ?? '-' }}</td>{{-- Berat Mentah --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Edit Berat Mentah --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $jumlah ?? '-' }}</td>{{-- Total Qty --}}
        <td style="text-align:left;border: 1px solid #000;"></td>{{-- Edit Qty --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Edit 2 % --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $satuan }}</td>{{-- Unit --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Total Pcs --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Edit pcs --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $satuan }}</td>{{-- Unit --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $row->jumlah ?? '-' }}</td>{{-- Total Qty --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Edit Qty (tulis manual) --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Final PO --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $row->satuan ?? '-' }}</td>{{-- Sat --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $row->harga ?? '-' }}</td>{{-- Refrensi Harga --}}
    </tr>
   
    @endforeach

    @php
    $rows_protein_bumbu = $rows_protein_bumbu ?? collect();
    @endphp

    @foreach ($rows_protein_bumbu as $row)
    @php
    $satuan = $row->satuan ?? '-';
    $jumlah = $row->jumlah ?? 0;
    if($satuan == 'kg' || $satuan == 'Kg' || $satuan == 'KG'){
        $satuan = 'gram';
        $jumlah = $jumlah * 1000;
    } elseif($satuan == 'liter' || $satuan == 'Liter' || $satuan == 'LITER'){
        $satuan = 'ml';
        $jumlah = $jumlah * 1000;
    } elseif($satuan == 'pcs' || $satuan == 'Pcs' || $satuan == 'PCS'){
        $satuan = 'Pcs';
        $jumlah = $jumlah;
    } else {
        $satuan = $row->satuan ?? '-';
        $jumlah = $row->jumlah ?? 0;
    }
    $nomor_urut_bahan = $nomor_urut_bahan + 1;
    @endphp
    <tr>
        <td></td>
        <td style="text-align:left;border: 1px solid #000;">{{ $loop->iteration }}</td> {{-- urut --}}
        <td style="text-align:left;border: 1px solid #000;"></td>{{-- Jenis --}}
        <td style="text-align:left;border: 1px solid #000;"></td>{{-- Menu --}}
        <td style="text-align:left;border: 1px solid #000;"></td>{{-- Total Komoditas --}}
        <td style="text-align:left;border: 1px solid #000;">{{$nomor_urut_bahan}}</td>{{-- No Bahan Pangan Lain : urut --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- No Bahan Pangan Lain : Item --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Pengulangan --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- No Pengulangan 1 --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- No Pengulangan 2 --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- No Pengulangan 3 --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Qty bahan pangan lain 1 --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Qty bahan pangan lain 2 --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Qty bahan pangan lain 3 --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Qty bahan pangan lain total --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $row->bahan ?? '-' }}</td>{{-- Bahan Mentah utama --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Bahan Mentah bumbu lainnya --}}
        <td style="text-align:left;border: 1px solid #000;"></td>{{-- Berat Mentah --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Edit Berat Mentah --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $jumlah ?? '-' }}</td>{{-- Total Qty --}}
        <td style="text-align:left;border: 1px solid #000;"></td>{{-- Edit Qty --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Edit 2 % --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $satuan }}</td>{{-- Unit --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Total Pcs --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Edit pcs --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $satuan }}</td>{{-- Unit --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $row->jumlah ?? '-' }}</td>{{-- Total Qty --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Edit Qty (tulis manual) --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Final PO --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $row->satuan ?? '-' }}</td>{{-- Sat --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $row->harga ?? '-' }}</td>{{-- Refrensi Harga --}}
    </tr>
   
    @endforeach

    @php
    $rows_sayur_utama = $rows_sayur_utama ?? collect();
    @endphp

    @foreach ($rows_sayur_utama as $row)
    @php
    $satuan = $row->satuan ?? '-';
    $jumlah = $row->jumlah ?? 0;
    if($satuan == 'kg' || $satuan == 'Kg' || $satuan == 'KG'){
        $satuan = 'gram';
        $jumlah = $jumlah * 1000;
    } elseif($satuan == 'liter' || $satuan == 'Liter' || $satuan == 'LITER'){
        $satuan = 'ml';
        $jumlah = $jumlah * 1000;
    } elseif($satuan == 'pcs' || $satuan == 'Pcs' || $satuan == 'PCS'){
        $satuan = 'Pcs';
        $jumlah = $jumlah;
    } else {
        $satuan = $row->satuan ?? '-';
        $jumlah = $row->jumlah ?? 0;
    }
    @endphp
    <tr>
        <td></td>
        <td style="text-align:left;border: 1px solid #000;">{{ $loop->iteration }}</td> {{-- urut --}}
        <td style="text-align:left;border: 1px solid #000;">Sayur</td>{{-- Jenis --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $row->nama_resep ?? '-' }}</td>{{-- Menu --}}
        <td style="text-align:left;border: 1px solid #000;"></td>{{-- Total Komoditas --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- No Bahan Pangan Lain : urut --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- No Bahan Pangan Lain : Item --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Pengulangan --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- No Pengulangan 1 --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- No Pengulangan 2 --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- No Pengulangan 3 --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Qty bahan pangan lain 1 --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Qty bahan pangan lain 2 --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Qty bahan pangan lain 3 --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Qty bahan pangan lain total --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $row->bahan ?? '-' }}</td>{{-- Bahan Mentah utama --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Bahan Mentah bumbu lainnya --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $row->sayur_porsi_a ?? '-' }}</td>{{-- Berat Mentah --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Edit Berat Mentah --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $jumlah ?? '-' }}</td>{{-- Total Qty --}}
        <td style="text-align:left;border: 1px solid #000;"></td>{{-- Edit Qty --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Edit 2 % --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $satuan }}</td>{{-- Unit --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Total Pcs --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Edit pcs --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $satuan }}</td>{{-- Unit --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $row->jumlah ?? '-' }}</td>{{-- Total Qty --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Edit Qty (tulis manual) --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Final PO --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $row->satuan ?? '-' }}</td>{{-- Sat --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $row->harga ?? '-' }}</td>{{-- Refrensi Harga --}}
    </tr>
   
    @endforeach

    @php
    $rows_sayur_bumbu = $rows_sayur_bumbu ?? collect();
    @endphp

    @foreach ($rows_sayur_bumbu as $row)
    @php
    $satuan = $row->satuan ?? '-';
    $jumlah = $row->jumlah ?? 0;
    if($satuan == 'kg' || $satuan == 'Kg' || $satuan == 'KG'){
        $satuan = 'gram';
        $jumlah = $jumlah * 1000;
    } elseif($satuan == 'liter' || $satuan == 'Liter' || $satuan == 'LITER'){
        $satuan = 'ml';
        $jumlah = $jumlah * 1000;
    } elseif($satuan == 'pcs' || $satuan == 'Pcs' || $satuan == 'PCS'){
        $satuan = 'Pcs';
        $jumlah = $jumlah;
    } else {
        $satuan = $row->satuan ?? '-';
        $jumlah = $row->jumlah ?? 0;
    }
    $nomor_urut_bahan = $nomor_urut_bahan + 1;
    @endphp
    <tr>
        <td></td>
        <td style="text-align:left;border: 1px solid #000;">{{ $loop->iteration }}</td> {{-- urut --}}
        <td style="text-align:left;border: 1px solid #000;"></td>{{-- Jenis --}}
        <td style="text-align:left;border: 1px solid #000;"></td>{{-- Menu --}}
        <td style="text-align:left;border: 1px solid #000;"></td>{{-- Total Komoditas --}}
        <td style="text-align:left;border: 1px solid #000;">{{$nomor_urut_bahan}}</td>{{-- No Bahan Pangan Lain : urut --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- No Bahan Pangan Lain : Item --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Pengulangan --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- No Pengulangan 1 --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- No Pengulangan 2 --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- No Pengulangan 3 --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Qty bahan pangan lain 1 --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Qty bahan pangan lain 2 --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Qty bahan pangan lain 3 --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Qty bahan pangan lain total --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $row->bahan ?? '-' }}</td>{{-- Bahan Mentah utama --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Bahan Mentah bumbu lainnya --}}
        <td style="text-align:left;border: 1px solid #000;"></td>{{-- Berat Mentah --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Edit Berat Mentah --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $jumlah ?? '-' }}</td>{{-- Total Qty --}}
        <td style="text-align:left;border: 1px solid #000;"></td>{{-- Edit Qty --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Edit 2 % --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $satuan }}</td>{{-- Unit --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Total Pcs --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Edit pcs --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $satuan }}</td>{{-- Unit --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $row->jumlah ?? '-' }}</td>{{-- Total Qty --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Edit Qty (tulis manual) --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Final PO --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $row->satuan ?? '-' }}</td>{{-- Sat --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $row->harga ?? '-' }}</td>{{-- Refrensi Harga --}}
    </tr>
   
    @endforeach

    
    @php
    $rows_buah_utama = $rows_buah_utama ?? collect();
    @endphp

    @foreach ($rows_buah_utama as $row)
    @php
    $satuan = $row->satuan ?? '-';
    $jumlah = $row->jumlah ?? 0;
    if($satuan == 'kg' || $satuan == 'Kg' || $satuan == 'KG'){
        $satuan = 'gram';
        $jumlah = $jumlah * 1000;
    } elseif($satuan == 'liter' || $satuan == 'Liter' || $satuan == 'LITER'){
        $satuan = 'ml';
        $jumlah = $jumlah * 1000;
    } elseif($satuan == 'pcs' || $satuan == 'Pcs' || $satuan == 'PCS'){
        $satuan = 'Pcs';
        $jumlah = $jumlah;
    } else {
        $satuan = $row->satuan ?? '-';
        $jumlah = $row->jumlah ?? 0;
    }
    @endphp
    <tr>
        <td></td>
        <td style="text-align:left;border: 1px solid #000;">{{ $loop->iteration }}</td> {{-- urut --}}
        <td style="text-align:left;border: 1px solid #000;">Buah</td>{{-- Jenis --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $row->nama_resep ?? '-' }}</td>{{-- Menu --}}
        <td style="text-align:left;border: 1px solid #000;"></td>{{-- Total Komoditas --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- No Bahan Pangan Lain : urut --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- No Bahan Pangan Lain : Item --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Pengulangan --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- No Pengulangan 1 --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- No Pengulangan 2 --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- No Pengulangan 3 --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Qty bahan pangan lain 1 --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Qty bahan pangan lain 2 --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Qty bahan pangan lain 3 --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Qty bahan pangan lain total --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $row->bahan ?? '-' }}</td>{{-- Bahan Mentah utama --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Bahan Mentah bumbu lainnya --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $row->buah_porsi_a ?? '-' }}</td>{{-- Berat Mentah --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Edit Berat Mentah --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $jumlah ?? '-' }}</td>{{-- Total Qty --}}
        <td style="text-align:left;border: 1px solid #000;"></td>{{-- Edit Qty --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Edit 2 % --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $satuan }}</td>{{-- Unit --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Total Pcs --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Edit pcs --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $satuan }}</td>{{-- Unit --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $row->jumlah ?? '-' }}</td>{{-- Total Qty --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Edit Qty (tulis manual) --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Final PO --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $row->satuan ?? '-' }}</td>{{-- Sat --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $row->harga ?? '-' }}</td>{{-- Refrensi Harga --}}
    </tr>
   
    @endforeach

    @php
    $rows_buah_bumbu = $rows_buah_bumbu ?? collect();
    @endphp

    @foreach ($rows_buah_bumbu as $row)
    @php
    $satuan = $row->satuan ?? '-';
    $jumlah = $row->jumlah ?? 0;
    if($satuan == 'kg' || $satuan == 'Kg' || $satuan == 'KG'){
        $satuan = 'gram';
        $jumlah = $jumlah * 1000;
    } elseif($satuan == 'liter' || $satuan == 'Liter' || $satuan == 'LITER'){
        $satuan = 'ml';
        $jumlah = $jumlah * 1000;
    } elseif($satuan == 'pcs' || $satuan == 'Pcs' || $satuan == 'PCS'){
        $satuan = 'Pcs';
        $jumlah = $jumlah;
    } else {
        $satuan = $row->satuan ?? '-';
        $jumlah = $row->jumlah ?? 0;
    }
    $nomor_urut_bahan = $nomor_urut_bahan + 1;
    @endphp
    <tr>
        <td></td>
        <td style="text-align:left;border: 1px solid #000;">{{ $loop->iteration }}</td> {{-- urut --}}
        <td style="text-align:left;border: 1px solid #000;"></td>{{-- Jenis --}}
        <td style="text-align:left;border: 1px solid #000;"></td>{{-- Menu --}}
        <td style="text-align:left;border: 1px solid #000;"></td>{{-- Total Komoditas --}}
        <td style="text-align:left;border: 1px solid #000;">{{$nomor_urut_bahan}}</td>{{-- No Bahan Pangan Lain : urut --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- No Bahan Pangan Lain : Item --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Pengulangan --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- No Pengulangan 1 --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- No Pengulangan 2 --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- No Pengulangan 3 --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Qty bahan pangan lain 1 --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Qty bahan pangan lain 2 --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Qty bahan pangan lain 3 --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Qty bahan pangan lain total --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $row->bahan ?? '-' }}</td>{{-- Bahan Mentah utama --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Bahan Mentah bumbu lainnya --}}
        <td style="text-align:left;border: 1px solid #000;"></td>{{-- Berat Mentah --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Edit Berat Mentah --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $jumlah ?? '-' }}</td>{{-- Total Qty --}}
        <td style="text-align:left;border: 1px solid #000;"></td>{{-- Edit Qty --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Edit 2 % --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $satuan }}</td>{{-- Unit --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Total Pcs --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Edit pcs --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $satuan }}</td>{{-- Unit --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $row->jumlah ?? '-' }}</td>{{-- Total Qty --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Edit Qty (tulis manual) --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Final PO --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $row->satuan ?? '-' }}</td>{{-- Sat --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $row->harga ?? '-' }}</td>{{-- Refrensi Harga --}}
    </tr>
   
    @endforeach

    @php
    $rows_pendamping_utama = $rows_pendamping_utama ?? collect();
    @endphp

    @foreach ($rows_pendamping_utama as $row)
    @php
    $satuan = $row->satuan ?? '-';
    $jumlah = $row->jumlah ?? 0;
    if($satuan == 'kg' || $satuan == 'Kg' || $satuan == 'KG'){
        $satuan = 'gram';
        $jumlah = $jumlah * 1000;
    } elseif($satuan == 'liter' || $satuan == 'Liter' || $satuan == 'LITER'){
        $satuan = 'ml';
        $jumlah = $jumlah * 1000;
    } elseif($satuan == 'pcs' || $satuan == 'Pcs' || $satuan == 'PCS'){
        $satuan = 'Pcs';
        $jumlah = $jumlah;
    } else {
        $satuan = $row->satuan ?? '-';
        $jumlah = $row->jumlah ?? 0;
    }
    @endphp
    <tr>
        <td></td>
        <td style="text-align:left;border: 1px solid #000;">{{ $loop->iteration }}</td> {{-- urut --}}
        <td style="text-align:left;border: 1px solid #000;">Pendamping</td>{{-- Jenis --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $row->nama_resep ?? '-' }}</td>{{-- Menu --}}
        <td style="text-align:left;border: 1px solid #000;"></td>{{-- Total Komoditas --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- No Bahan Pangan Lain : urut --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- No Bahan Pangan Lain : Item --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Pengulangan --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- No Pengulangan 1 --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- No Pengulangan 2 --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- No Pengulangan 3 --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Qty bahan pangan lain 1 --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Qty bahan pangan lain 2 --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Qty bahan pangan lain 3 --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Qty bahan pangan lain total --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $row->bahan ?? '-' }}</td>{{-- Bahan Mentah utama --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Bahan Mentah bumbu lainnya --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $row->suplemen_porsi_a ?? '-' }}</td>{{-- Berat Mentah --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Edit Berat Mentah --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $jumlah ?? '-' }}</td>{{-- Total Qty --}}
        <td style="text-align:left;border: 1px solid #000;"></td>{{-- Edit Qty --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Edit 2 % --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $satuan }}</td>{{-- Unit --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Total Pcs --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Edit pcs --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $satuan }}</td>{{-- Unit --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $row->jumlah ?? '-' }}</td>{{-- Total Qty --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Edit Qty (tulis manual) --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Final PO --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $row->satuan ?? '-' }}</td>{{-- Sat --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $row->harga ?? '-' }}</td>{{-- Refrensi Harga --}}
    </tr>
   
    @endforeach

     @php
    $rows_pendamping_bumbu = $rows_pendamping_bumbu ?? collect();
    @endphp

    @foreach ($rows_pendamping_bumbu as $row)
    @php
    $satuan = $row->satuan ?? '-';
    $jumlah = $row->jumlah ?? 0;
    if($satuan == 'kg' || $satuan == 'Kg' || $satuan == 'KG'){
        $satuan = 'gram';
        $jumlah = $jumlah * 1000;
    } elseif($satuan == 'liter' || $satuan == 'Liter' || $satuan == 'LITER'){
        $satuan = 'ml';
        $jumlah = $jumlah * 1000;
    } elseif($satuan == 'pcs' || $satuan == 'Pcs' || $satuan == 'PCS'){
        $satuan = 'Pcs';
        $jumlah = $jumlah;
    } else {
        $satuan = $row->satuan ?? '-';
        $jumlah = $row->jumlah ?? 0;
    }
    $nomor_urut_bahan = $nomor_urut_bahan + 1;
    @endphp
    <tr>
        <td></td>
        <td style="text-align:left;border: 1px solid #000;">{{ $loop->iteration }}</td> {{-- urut --}}
        <td style="text-align:left;border: 1px solid #000;"></td>{{-- Jenis --}}
        <td style="text-align:left;border: 1px solid #000;"></td>{{-- Menu --}}
        <td style="text-align:left;border: 1px solid #000;"></td>{{-- Total Komoditas --}}
        <td style="text-align:left;border: 1px solid #000;">{{$nomor_urut_bahan}}</td>{{-- No Bahan Pangan Lain : urut --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- No Bahan Pangan Lain : Item --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Pengulangan --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- No Pengulangan 1 --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- No Pengulangan 2 --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- No Pengulangan 3 --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Qty bahan pangan lain 1 --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Qty bahan pangan lain 2 --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Qty bahan pangan lain 3 --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Qty bahan pangan lain total --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $row->bahan ?? '-' }}</td>{{-- Bahan Mentah utama --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Bahan Mentah bumbu lainnya --}}
        <td style="text-align:left;border: 1px solid #000;"></td>{{-- Berat Mentah --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Edit Berat Mentah --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $jumlah ?? '-' }}</td>{{-- Total Qty --}}
        <td style="text-align:left;border: 1px solid #000;"></td>{{-- Edit Qty --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Edit 2 % --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $satuan }}</td>{{-- Unit --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Total Pcs --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Edit pcs --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $satuan }}</td>{{-- Unit --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $row->jumlah ?? '-' }}</td>{{-- Total Qty --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Edit Qty (tulis manual) --}}
        <td style="text-align:left;border: 1px solid #000;">-</td>{{-- Final PO --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $row->satuan ?? '-' }}</td>{{-- Sat --}}
        <td style="text-align:left;border: 1px solid #000;">{{ $row->harga ?? '-' }}</td>{{-- Refrensi Harga --}}
    </tr>
   
    @endforeach
</table>
