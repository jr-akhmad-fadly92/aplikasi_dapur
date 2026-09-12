{{-- Generated from dokumen/contoh2.xlsx --}}
<table style="width:100%; border-collapse:collapse; font-family:Arial, Helvetica, sans-serif; font-size:11px;">
    <tr>
        <td style="font-weight:bold;text-align:general;"></td>
        <td style="font-weight:bold;text-align:general;">Yayasan Bina Bangsa Semarang</td>
        <td style="font-weight:bold;text-align:general;"></td>
        <td style="font-weight:bold;text-align:general;"></td>
        <td style="font-weight:bold;text-align:general;"></td>
        <td style="font-weight:bold;text-align:center;"></td>
        <td style="text-align:center;"></td>
        <td style="text-align:center;"></td>
        <td style="text-align:center;"></td>
        <td style="text-align:center;"></td>
        <td style="text-align:center;"></td>
        <td style="text-align:center;"></td>
    </tr>
    <tr>
        <td style="text-align:general;"></td>
        <td style="text-align:general;">Unit SPPG</td>
        <td style="text-align:general;"></td>
        <td style="text-align:general;"></td>
        <td style="text-align:general;"></td>
        <td style="text-align:center;"></td>
        <td style="text-align:center;"></td>
        <td style="text-align:center;"></td>
        <td style="text-align:center;"></td>
        <td style="text-align:center;"></td>
        <td style="text-align:center;"></td>
        <td style="text-align:center;"></td>
    </tr>
    <tr>
        <td style="text-align:general;"></td>
        <td style="text-align:general;">Rekap Periode Pelayanan SPPG YBBS</td>
        <td style="text-align:general;"></td>
        <td style="text-align:general;"></td>
        <td style="text-align:general;"></td>
        <td style="text-align:center;"></td>
        <td style="text-align:center;"></td>
        <td style="text-align:center;"></td>
        <td style="text-align:center;"></td>
        <td style="text-align:center;"></td>
        <td style="text-align:center;"></td>
        <td style="text-align:center;"></td>
    </tr>
    <tr>
        <td style="text-align:general;"></td>
        <td style="text-align:general;">{{$dapur->nama_dapur}} {{$dapur->kecamatan}},{{$dapur->kota}}</td>
        <td style="text-align:general;"></td>
        <td style="text-align:general;"></td>
        <td style="text-align:general;"></td>
        <td style="text-align:center;"></td>
        <td style="text-align:center;"></td>
        <td style="text-align:center;"></td>
        <td style="text-align:center;"></td>
        <td style="text-align:center;"></td>
        <td style="text-align:center;"></td>
        <td style="text-align:center;"></td>
    </tr>
    <tr>
        <td style="text-align:general;"></td>
        <td style="text-align:general;"></td>
        <td style="text-align:general;"></td>
        <td style="text-align:general;"></td>
        <td style="text-align:general;"></td>
        <td style="text-align:center;"></td>
        <td style="text-align:center;"></td>
        <td style="text-align:center;"></td>
        <td style="text-align:center;"></td>
        <td style="text-align:center;"></td>
        <td style="text-align:center;"></td>
        <td style="text-align:center;"></td>
    </tr>
    <tr>
        <td style="text-align:general;"></td>
        <td style="text-align:center;">No</td>
        <td style="text-align:center;">Periode</td>
        <td style="text-align:center;">Tanggal</td>
        <td style="text-align:center;">Hari Pelayanan</td>
        <td style="text-align:center;">Minggu</td>
        <td style="text-align:center;">Total Minggu</td>
        <td style="text-align:center;">Keterangan Sistem</td>
        
        <td style="text-align:center;">Sen</td>
        <td style="text-align:center;">Sel</td>
        <td style="text-align:center;">Rab</td>
        <td style="text-align:center;">Kam</td>
        <td style="text-align:center;">Jum</td>
        <td style="text-align:center;">Sab</td>
    </tr>
    
    @php
        // ukuran minggu (Sen-Sab) = 6 hari
        $weekSize = 6;
        // penghitung minggu per periode (reset saat periode berubah)
        $periodWeekCounters = [];
    @endphp

    @foreach($all_data as $index => $item)
    @php
        $data_anggaran = $item['data_anggaran'];
        $tanggalList = $item['tanggalList'];
        $pm = $item['pm'];
        // ambil status arrays dari item agar tidak undefined dalam view
        $menu_status = $item['menu_status'] ?? [];
        $po_status = $item['po_status'] ?? [];
        $penerimaan_status = $item['penerimaan_status'] ?? [];
        $masuk_gudang_status = $item['masuk_gudang_status'] ?? [];
        $keluar_gudang_status = $item['keluar_gudang_status'] ?? [];
        $hasil_masak_status = $item['hasil_masak_status'] ?? [];
        $hasil_scan_status = $item['hasil_scan_status'] ?? [];
        $surat_jalan_status = $item['surat_jalan_status'] ?? [];
        $jumlahHari = $data_anggaran->jumlah_hari ?? count($tanggalList);
        $rowspan = ($jumlahHari > $weekSize) ? 20 : 10;

        // Hitung berapa minggu yang diperlukan untuk item ini
        $weeksCount = max(1, (int) ceil($jumlahHari / $weekSize));

        // Tentukan periode sebagai key untuk penomoran mingguan per-periode
        $periodeKey = $data_anggaran->periode ?? 'periode_'.$index;

        // Minggu pertama dalam periode ini (1-based, reset ketika periode berubah)
        $startWeekInPeriod = isset($periodWeekCounters[$periodeKey]) ? $periodWeekCounters[$periodeKey] + 1 : 1;
        $endWeekInPeriod = $startWeekInPeriod + $weeksCount - 1;

        // Label Minggu mengikuti nomor dalam periode (contoh: "1" atau "1-2")
        $periodWeeksLabel = ($startWeekInPeriod == $endWeekInPeriod) ? (string)$startWeekInPeriod : ($startWeekInPeriod . '-' . $endWeekInPeriod);
        $mingguLabel = 'Minggu ' . $periodWeeksLabel;

        // Update counter untuk periode ini
        $periodWeekCounters[$periodeKey] = $endWeekInPeriod;
    @endphp
    <tr>
        <td style="text-align:general;"></td>
        <td rowspan="{{ $rowspan }}" style="text-align:center; vertical-align:middle;">{{ $index + 1 }}</td>
        <td rowspan="{{ $rowspan }}" style="text-align:center; vertical-align:middle;">{{ $data_anggaran->periode ?? '' }}</td>
        <td rowspan="{{ $rowspan }}" style="text-align:center; vertical-align:middle;">{{ \Carbon\Carbon::parse($data_anggaran->periode_awal)->format('d-m-Y') }} s.d {{ \Carbon\Carbon::parse($data_anggaran->periode_akhir)->format('d-m-Y') }}</td>
        <td rowspan="{{ $rowspan }}" style="text-align:center; vertical-align:middle;">{{ $jumlahHari }} hari</td>
        <td rowspan="{{ $rowspan }}" style="text-align:center; vertical-align:middle;">{{ $mingguLabel }}</td>
        <td rowspan="{{ $rowspan }}" style="text-align:center; vertical-align:middle;">{{ $index + 1 }}</td>
        <td style="text-align:general;"></td>
        
        <td style="text-align:center;">{{ isset($tanggalList[0]) ? \Carbon\Carbon::parse($tanggalList[0])->format('d') : '' }}</td>
        <td style="text-align:center;">{{ isset($tanggalList[1]) ? \Carbon\Carbon::parse($tanggalList[1])->format('d') : '' }}</td>
        <td style="text-align:center;">{{ isset($tanggalList[2]) ? \Carbon\Carbon::parse($tanggalList[2])->format('d') : '' }}</td>
        <td style="text-align:center;">{{ isset($tanggalList[3]) ? \Carbon\Carbon::parse($tanggalList[3])->format('d') : '' }}</td>
        <td style="text-align:center;">{{ isset($tanggalList[4]) ? \Carbon\Carbon::parse($tanggalList[4])->format('d') : '' }}</td>
        <td style="text-align:center;">{{ isset($tanggalList[5]) ? \Carbon\Carbon::parse($tanggalList[5])->format('d') : '' }}</td>
    </tr>
    <tr>
        <td style="text-align:general;"></td>
        
        <td style="text-align:center;">Jumlah Pax</td>
        <td style="text-align:center;">{{ $pm[0]->total_penerima ?? '0' }} pax </td>
        <td style="text-align:center;">{{ $pm[1]->total_penerima ?? '0' }} pax </td>
        <td style="text-align:center;">{{ $pm[2]->total_penerima ?? '0' }} pax </td>
        <td style="text-align:center;">{{ $pm[3]->total_penerima ?? '0' }} pax </td>
        <td style="text-align:center;">{{ $pm[4]->total_penerima ?? '0' }} pax </td>
        <td style="text-align:center;">{{ $pm[5]->total_penerima ?? '0' }} pax </td>
    </tr>
    <tr>
        <td style="text-align:general;"></td>
        <td style="text-align:center;">Status Menu</td>
        
        <td style="text-align:center;"> {{ isset($menu_status[0]) ? $menu_status[0] : '' }}</td>
        <td style="text-align:center;"> {{ isset($menu_status[1]) ? $menu_status[1] : '' }}</td>
        <td style="text-align:center;"> {{ isset($menu_status[2]) ? $menu_status[2] : '' }}</td>
        <td style="text-align:center;"> {{ isset($menu_status[3]) ? $menu_status[3] : '' }}</td>
        <td style="text-align:center;"> {{ isset($menu_status[4]) ? $menu_status[4] : '' }}</td>
        <td style="text-align:center;"> {{ isset($menu_status[5]) ? $menu_status[5] : '' }}</td>
    </tr>
    <tr>
        <td style="text-align:general;"></td>
        <td style="text-align:center;">Status PO</td>
        <td style="text-align:center;"> {{ isset($po_status[0]) ? $po_status[0] : '' }}</td>
        <td style="text-align:center;"> {{ isset($po_status[1]) ? $po_status[1] : '' }}</td>
        <td style="text-align:center;"> {{ isset($po_status[2]) ? $po_status[2] : '' }}</td>
        <td style="text-align:center;"> {{ isset($po_status[3]) ? $po_status[3] : '' }}</td>
        <td style="text-align:center;"> {{ isset($po_status[4]) ? $po_status[4] : '' }}</td>
        <td style="text-align:center;"> {{ isset($po_status[5]) ? $po_status[5] : '' }}</td>
    </tr>
    <tr>
        <td style="text-align:general;"></td>
        <td style="text-align:center;">Status Penerimaan</td>
        <td style="text-align:center;"> {{ isset($penerimaan_status[0]) ? $penerimaan_status[0] : '' }}</td>
        <td style="text-align:center;"> {{ isset($penerimaan_status[1]) ? $penerimaan_status[1] : '' }}</td>
        <td style="text-align:center;"> {{ isset($penerimaan_status[2]) ? $penerimaan_status[2] : '' }}</td>
        <td style="text-align:center;"> {{ isset($penerimaan_status[3]) ? $penerimaan_status[3] : '' }}</td>
        <td style="text-align:center;"> {{ isset($penerimaan_status[4]) ? $penerimaan_status[4] : '' }}</td>
        <td style="text-align:center;"> {{ isset($penerimaan_status[5]) ? $penerimaan_status[5] : '' }}</td>
    </tr>
    <tr>
        <td style="text-align:general;"></td>
        <td style="text-align:center;">Status Masuk Gudang</td>
        <td style="text-align:center;"> {{ isset($masuk_gudang_status[0]) ? $masuk_gudang_status[0] : '' }}</td>
        <td style="text-align:center;"> {{ isset($masuk_gudang_status[1]) ? $masuk_gudang_status[1] : '' }}</td>
        <td style="text-align:center;"> {{ isset($masuk_gudang_status[2]) ? $masuk_gudang_status[2] : '' }}</td>
        <td style="text-align:center;"> {{ isset($masuk_gudang_status[3]) ? $masuk_gudang_status[3] : '' }}</td>
        <td style="text-align:center;"> {{ isset($masuk_gudang_status[4]) ? $masuk_gudang_status[4] : '' }}</td>
        <td style="text-align:center;"> {{ isset($masuk_gudang_status[5]) ? $masuk_gudang_status[5] : '' }}</td>
    </tr>
    <tr>
        <td style="text-align:general;"></td>
        <td style="text-align:center;">Status Keluar Gudang</td>
        <td style="text-align:center;"> {{ isset($keluar_gudang_status[0]) ? $keluar_gudang_status[0] : '' }}</td>
        <td style="text-align:center;"> {{ isset($keluar_gudang_status[1]) ? $keluar_gudang_status[1] : '' }}</td>
        <td style="text-align:center;"> {{ isset($keluar_gudang_status[2]) ? $keluar_gudang_status[2] : '' }}</td>
        <td style="text-align:center;"> {{ isset($keluar_gudang_status[3]) ? $keluar_gudang_status[3] : '' }}</td>
        <td style="text-align:center;"> {{ isset($keluar_gudang_status[4]) ? $keluar_gudang_status[4] : '' }}</td>
        <td style="text-align:center;"> {{ isset($keluar_gudang_status[5]) ? $keluar_gudang_status[5] : '' }}</td>
    </tr>
    <tr>
        <td style="text-align:general;"></td>
        <td style="text-align:center;">Status Hasil Masak</td>
        <td style="text-align:center;"> {{ isset($hasil_masak_status[0]) ? $hasil_masak_status[0] : '' }}</td>
        <td style="text-align:center;"> {{ isset($hasil_masak_status[1]) ? $hasil_masak_status[1] : '' }}</td>
        <td style="text-align:center;"> {{ isset($hasil_masak_status[2]) ? $hasil_masak_status[2] : '' }}</td>
        <td style="text-align:center;"> {{ isset($hasil_masak_status[3]) ? $hasil_masak_status[3] : '' }}</td>
        <td style="text-align:center;"> {{ isset($hasil_masak_status[4]) ? $hasil_masak_status[4] : '' }}</td>
        <td style="text-align:center;"> {{ isset($hasil_masak_status[5]) ? $hasil_masak_status[5] : '' }}</td>
    </tr>
    <tr>
        <td style="text-align:general;"></td>
        <td style="text-align:center;">Status Scan</td>
        <td style="text-align:center;"> {{ isset($hasil_scan_status[0]) ? $hasil_scan_status[0] : '' }}</td>
        <td style="text-align:center;"> {{ isset($hasil_scan_status[1]) ? $hasil_scan_status[1] : '' }}</td>
        <td style="text-align:center;"> {{ isset($hasil_scan_status[2]) ? $hasil_scan_status[2] : '' }}</td>
        <td style="text-align:center;"> {{ isset($hasil_scan_status[3]) ? $hasil_scan_status[3] : '' }}</td>
        <td style="text-align:center;"> {{ isset($hasil_scan_status[4]) ? $hasil_scan_status[4] : '' }}</td>
        <td style="text-align:center;"> {{ isset($hasil_scan_status[5]) ? $hasil_scan_status[5] : '' }}</td>
    </tr>
    <tr>
        <td style="text-align:general;"></td>
        <td style="text-align:center;">Status Surat Jalan</td>
        <td style="text-align:center;"> {{ isset($surat_jalan_status[0]) ? $surat_jalan_status[0] : '' }}</td>
        <td style="text-align:center;"> {{ isset($surat_jalan_status[1]) ? $surat_jalan_status[1] : '' }}</td>
        <td style="text-align:center;"> {{ isset($surat_jalan_status[2]) ? $surat_jalan_status[2] : '' }}</td>
        <td style="text-align:center;"> {{ isset($surat_jalan_status[3]) ? $surat_jalan_status[3] : '' }}</td>
        <td style="text-align:center;"> {{ isset($surat_jalan_status[4]) ? $surat_jalan_status[4] : '' }}</td>
        <td style="text-align:center;"> {{ isset($surat_jalan_status[5]) ? $surat_jalan_status[5] : '' }}</td>
    </tr>
    @if($weeksCount > 1)
    <tr>
        <td style="text-align:general;"></td>
        <td rowspan="2" style="text-align:center;">{{ 'Minggu ' . ($startWeekInPeriod + 1) }}</td>
        <td style="text-align:general;"></td>
        
        <td style="text-align:center;">{{ isset($tanggalList[6]) ? \Carbon\Carbon::parse($tanggalList[6])->format('d') : '' }}</td>
        <td style="text-align:center;">{{ isset($tanggalList[7]) ? \Carbon\Carbon::parse($tanggalList[7])->format('d') : '' }}</td>
        <td style="text-align:center;">{{ isset($tanggalList[8]) ? \Carbon\Carbon::parse($tanggalList[8])->format('d') : '' }}</td>
        <td style="text-align:center;">{{ isset($tanggalList[9]) ? \Carbon\Carbon::parse($tanggalList[9])->format('d') : '' }}</td>
        <td style="text-align:center;">{{ isset($tanggalList[10]) ? \Carbon\Carbon::parse($tanggalList[10])->format('d') : '' }}</td>
        <td style="text-align:center;">{{ isset($tanggalList[11]) ? \Carbon\Carbon::parse($tanggalList[11])->format('d') : '' }}</td>
    </tr>
    <tr>
        <td style="text-align:general;"></td>
        <td style="text-align:general;">Jumlah Pax</td>
        <td style="text-align:center;">{{ $pm[6]->total_penerima ?? '0' }} pax </td>
        <td style="text-align:center;">{{ $pm[7]->total_penerima ?? '0' }} pax </td>
        <td style="text-align:center;">{{ $pm[8]->total_penerima ?? '0' }} pax </td>
        <td style="text-align:center;">{{ $pm[9]->total_penerima ?? '0' }} pax </td>
        <td style="text-align:center;">{{ $pm[10]->total_penerima ?? '0' }} pax </td>
        <td style="text-align:center;">{{ $pm[11]->total_penerima ?? '0' }} pax </td>
    </tr>
    <tr>
        <td style="text-align:general;"></td>
        <td style="text-align:center;">Status menu</td>
        
        <td style="text-align:center;"> {{ isset($menu_status[6]) ? $menu_status[6] : '' }}</td>
        <td style="text-align:center;"> {{ isset($menu_status[7]) ? $menu_status[7] : '' }}</td>
        <td style="text-align:center;"> {{ isset($menu_status[8]) ? $menu_status[8] : '' }}</td>
        <td style="text-align:center;"> {{ isset($menu_status[9]) ? $menu_status[9] : '' }}</td>
        <td style="text-align:center;"> {{ isset($menu_status[10]) ? $menu_status[10] : '' }}</td>
        <td style="text-align:center;"> {{ isset($menu_status[11]) ? $menu_status[11] : '' }}</td>
    </tr>
    <tr>
        <td style="text-align:general;"></td>
        <td style="text-align:center;">Status PO</td>
        <td style="text-align:center;"> {{ isset($po_status[6]) ? $po_status[0] : '' }}</td>
        <td style="text-align:center;"> {{ isset($po_status[7]) ? $po_status[1] : '' }}</td>
        <td style="text-align:center;"> {{ isset($po_status[8]) ? $po_status[2] : '' }}</td>
        <td style="text-align:center;"> {{ isset($po_status[9]) ? $po_status[3] : '' }}</td>
        <td style="text-align:center;"> {{ isset($po_status[10]) ? $po_status[4] : '' }}</td>
        <td style="text-align:center;"> {{ isset($po_status[11]) ? $po_status[5] : '' }}</td>
    </tr>
    <tr>
        <td style="text-align:general;"></td>
        <td style="text-align:center;">Status Penerimaan</td>
        <td style="text-align:center;"> {{ isset($penerimaan_status[6]) ? $penerimaan_status[0] : '' }}</td>
        <td style="text-align:center;"> {{ isset($penerimaan_status[7]) ? $penerimaan_status[1] : '' }}</td>
        <td style="text-align:center;"> {{ isset($penerimaan_status[8]) ? $penerimaan_status[2] : '' }}</td>
        <td style="text-align:center;"> {{ isset($penerimaan_status[9]) ? $penerimaan_status[3] : '' }}</td>
        <td style="text-align:center;"> {{ isset($penerimaan_status[10]) ? $penerimaan_status[4] : '' }}</td>
        <td style="text-align:center;"> {{ isset($penerimaan_status[11]) ? $penerimaan_status[5] : '' }}</td>
    </tr>
    <tr>
        <td style="text-align:general;"></td>
        <td style="text-align:center;">Status Masuk Gudang</td>
        <td style="text-align:center;"> {{ isset($masuk_gudang_status[6]) ? $masuk_gudang_status[0] : '' }}</td>
        <td style="text-align:center;"> {{ isset($masuk_gudang_status[7]) ? $masuk_gudang_status[1] : '' }}</td>
        <td style="text-align:center;"> {{ isset($masuk_gudang_status[8]) ? $masuk_gudang_status[2] : '' }}</td>
        <td style="text-align:center;"> {{ isset($masuk_gudang_status[9]) ? $masuk_gudang_status[3] : '' }}</td>
        <td style="text-align:center;"> {{ isset($masuk_gudang_status[10]) ? $masuk_gudang_status[4] : '' }}</td>
        <td style="text-align:center;"> {{ isset($masuk_gudang_status[11]) ? $masuk_gudang_status[5] : '' }}</td>
    </tr>
    <tr>
        <td style="text-align:general;"></td>
        <td style="text-align:center;">Status Keluar Gudang</td>
        <td style="text-align:center;"> {{ isset($keluar_gudang_status[6]) ? $keluar_gudang_status[0] : '' }}</td>
        <td style="text-align:center;"> {{ isset($keluar_gudang_status[7]) ? $keluar_gudang_status[1] : '' }}</td>
        <td style="text-align:center;"> {{ isset($keluar_gudang_status[8]) ? $keluar_gudang_status[2] : '' }}</td>
        <td style="text-align:center;"> {{ isset($keluar_gudang_status[9]) ? $keluar_gudang_status[3] : '' }}</td>
        <td style="text-align:center;"> {{ isset($keluar_gudang_status[10]) ? $keluar_gudang_status[4] : '' }}</td>
        <td style="text-align:center;"> {{ isset($keluar_gudang_status[11]) ? $keluar_gudang_status[5] : '' }}</td>
    </tr>
    <tr>
        <td style="text-align:general;"></td>
        <td style="text-align:center;">Status Hasil Masak</td>
        <td style="text-align:center;"> {{ isset($hasil_masak_status[6]) ? $hasil_masak_status[0] : '' }}</td>
        <td style="text-align:center;"> {{ isset($hasil_masak_status[7]) ? $hasil_masak_status[1] : '' }}</td>
        <td style="text-align:center;"> {{ isset($hasil_masak_status[8]) ? $hasil_masak_status[2] : '' }}</td>
        <td style="text-align:center;"> {{ isset($hasil_masak_status[9]) ? $hasil_masak_status[3] : '' }}</td>
        <td style="text-align:center;"> {{ isset($hasil_masak_status[10]) ? $hasil_masak_status[4] : '' }}</td>
        <td style="text-align:center;"> {{ isset($hasil_masak_status[11]) ? $hasil_masak_status[5] : '' }}</td>
    </tr>
    <tr>
        <td style="text-align:general;"></td>
        <td style="text-align:center;">Status Scan</td>
        <td style="text-align:center;"> {{ isset($hasil_scan_status[6]) ? $hasil_scan_status[0] : '' }}</td>
        <td style="text-align:center;"> {{ isset($hasil_scan_status[7]) ? $hasil_scan_status[1] : '' }}</td>
        <td style="text-align:center;"> {{ isset($hasil_scan_status[8]) ? $hasil_scan_status[2] : '' }}</td>
        <td style="text-align:center;"> {{ isset($hasil_scan_status[9]) ? $hasil_scan_status[3] : '' }}</td>
        <td style="text-align:center;"> {{ isset($hasil_scan_status[10]) ? $hasil_scan_status[4] : '' }}</td>
        <td style="text-align:center;"> {{ isset($hasil_scan_status[11]) ? $hasil_scan_status[5] : '' }}</td>
    </tr>
    <tr>
        <td style="text-align:general;"></td>
        <td style="text-align:center;">Status Surat Jalan</td>
        <td style="text-align:center;"> {{ isset($surat_jalan_status[6]) ? $surat_jalan_status[0] : '' }}</td>
        <td style="text-align:center;"> {{ isset($surat_jalan_status[7]) ? $surat_jalan_status[1] : '' }}</td>
        <td style="text-align:center;"> {{ isset($surat_jalan_status[8]) ? $surat_jalan_status[2] : '' }}</td>
        <td style="text-align:center;"> {{ isset($surat_jalan_status[9]) ? $surat_jalan_status[3] : '' }}</td>
        <td style="text-align:center;"> {{ isset($surat_jalan_status[10]) ? $surat_jalan_status[4] : '' }}</td>
        <td style="text-align:center;"> {{ isset($surat_jalan_status[11]) ? $surat_jalan_status[5] : '' }}</td>
    </tr>
    @endif
    @endforeach
    
</table>
