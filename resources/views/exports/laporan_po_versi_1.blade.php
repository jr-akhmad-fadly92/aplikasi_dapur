{{-- Generated from database tb_po dan tb_po_bahan --}}
<table style="width:100%; border-collapse:collapse; font-family:Arial, Helvetica, sans-serif; font-size:11px; border: 1px solid #000;">
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
    </tr>
    <tr>
        <td style="text-align:general;"></td>
        <td style="text-align:general;">Rekap Periode Pelayanan SPPG YBBS {{ \Carbon\Carbon::parse($tanggal_awal)->format('j') }} - {{ \Carbon\Carbon::parse($tanggal_akhir)->translatedFormat('j F Y') }}</td>
        <td style="text-align:general;"></td>
        <td style="text-align:general;"></td>
        <td style="text-align:general;"></td>
        <td style="text-align:center;"></td>
        <td style="text-align:center;"></td>
        <td style="text-align:center;"></td>
        <td style="text-align:center;"></td>
    </tr>
    <tr>
        <td style="text-align:general;"></td>
        <td style="text-align:general;">{{ $dapur->nama_dapur ?? '' }} {{ $dapur->kecamatan ?? '' }}, {{ $dapur->kota ?? '' }}</td>
        <td style="text-align:general;"></td>
        <td style="text-align:general;"></td>
        <td style="text-align:general;"></td>
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
    </tr>
    
    @php
        // Proses tanggal untuk 3 minggu (21 hari)
        $weeks = [];
        $week_num = 0;
        
        foreach ($tanggal_list as $index => $tanggal) {
            if ($index % 7 == 0) {
                $week_num++;
                $weeks[$week_num] = [];
            }
            $weeks[$week_num][] = [
                'tanggal' => $tanggal,
                'hari_ke' => ($index % 7) + 1,
                'hari_nama' => \Carbon\Carbon::parse($tanggal)->translatedFormat('l'),
                'tgl_angka' => \Carbon\Carbon::parse($tanggal)->format('j')
            ];
        }
    @endphp
    
    @foreach($weeks as $week_no => $dates)
    <tr>
        <td style="text-align:general;"></td>
        <td style="text-align:center;border: 1px solid #000;">Hari</td>
        @foreach($dates as $d)
        <td style="text-align:center;border: 1px solid #000;">{{ substr($d['hari_nama'], 0, 3) }}</td>
        @endforeach
    </tr>
    <tr>
        <td style="text-align:general;"></td>
        <td style="text-align:center;border: 1px solid #000;">Tanggal Pelayanan</td>
        @foreach($dates as $d)
        <td style="text-align:center;border: 1px solid #000;">{{ $d['tgl_angka'] }}</td>
        @endforeach
    </tr>
    <tr>
        <td style="text-align:general;"></td>
        <td style="text-align:center;border: 1px solid #000;">Tanggal PO Dibuat</td>
        @foreach($dates as $d)
        <td style="text-align:center;border: 1px solid #000;">
            @php
                $po = $po_per_tanggal[$d['tanggal']] ?? null;
            @endphp
            @if($po)
                {{ \Carbon\Carbon::parse($po->tanggal_pengajuan)->translatedFormat('j F Y') }}
            @endif
        </td>
        @endforeach
    </tr>
    @endforeach
    
    
    
</table>
