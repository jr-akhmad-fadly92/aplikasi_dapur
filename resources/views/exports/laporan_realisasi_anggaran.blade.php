{{-- ============================= --}}
{{-- HEADER LAPORAN --}}
{{-- ============================= --}}
<table style="width:100%; border-collapse:collapse;">
    <tr>
        <td colspan="8" style="text-align:center; font-weight:bold;">
            LAPORAN BIAYA INFRASTRUKTUR DAN PERALATAN
        </td>
    </tr>
    <tr>
        <td colspan="8" style="text-align:center;">
            Periode: {{ \Carbon\Carbon::parse($start)->format('d M Y') }}
            s.d.
            {{ \Carbon\Carbon::parse($end)->format('d M Y') }}
        </td>
    </tr>
    <tr><td colspan="8"></td></tr>
    <tr>
        <td colspan="2">Nama SPPG</td><td>: {{ $dapur->nama_dapur }}</td>
    </tr>
    <tr>
        <td colspan="2">Kelurahan/Desa</td><td>: {{ $dapur->kelurahan }}</td>
    </tr>
    <tr>
        <td colspan="2">Kecamatan</td><td>: {{ $dapur->kecamatan }}</td>
    </tr>
    <tr>
        <td colspan="2">Kabupaten/Kota</td><td>: {{ $dapur->kota }}</td>
    </tr>
    <tr>
        <td colspan="2">Provinsi</td><td>: {{ $dapur->provinsi }}</td>
    </tr>

    <tr><td colspan="8"></td></tr>

    {{-- HEADER KOLOM --}}
    <tr style="background:#f2f2f2; font-weight:bold; text-align:center;">
        <th colspan=3>Pendapatan</th>
        <th>:</th>
        <th></th>
        <th>{{ $data_anggaran->bgn+$data_anggaran->yayasan+$data_anggaran->pihak_lain }}</th>
        <th></th>
        <th></th>
        
    </tr>
    <tr style="font-size:12px; text-align:center; font-style:italic;">
        <td></td>
        <td colspan=2 >Penerimaan dari BGN</td>
        <td>:</td>
        <td>{{ number_format($data_anggaran->bgn,0,'.','.') }}</td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr style="font-size:12px; text-align:center; font-style:italic;">
        <td></td>
        <td colspan=2 >Penerimaan dari Yayasan</td>
        <td>:</td>
        @if($data_anggaran->yayasan == 0 )
        <td>xxxxxxxxx</td>
            
        @else
        <td>{{ number_format($data_anggaran->yayasan,0,'.','.') }}</td>
            
        @endif
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr style="font-size:12px; text-align:center; font-style:italic;">
        <td></td>
        <td colspan=2 >Penerimaan dari Pihak Lainnya</td>
        <td>:</td>
        @if($data_anggaran->pihak_lain == 0 )
        <td>xxxxxxxxx</td>
            
        @else
        <td>{{ number_format($data_anggaran->pihak_lain,0,'.','.') }}</td>
            
        @endif
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr><td colspan="8"></td></tr>
    <tr style="background:#f2f2f2; font-weight:bold; text-align:center;">
        <th colspan=3>Belanja</th>
        <th>:</th>
        <th></th>
        <td>{{ number_format($total_pengeluaran,0,'.','.') }}</td>
        <th></th>
        <th></th>
        
    </tr>
    <tr style="font-size:12px; text-align:center; font-style:italic;">
        <td></td>
        <td colspan=2 >Belanja Bahan Pangan</td>
        <td>:</td>
        <td>{{ $totalJumlahPoPangan }}</td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr style="font-size:12px; text-align:center; font-style:italic;">
        <td></td>
        <td colspan=2 >Belanja Operasional</td>
        <td>:</td>
        <td>{{ $jumlah_non_pangan }}</td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr style="font-size:12px; text-align:center; font-style:italic;">
        <td></td>
        <td colspan=2 >Belanja Sewa</td>
        <td>:</td>
        <td>{{ $jumlah_infra}}</td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr><td colspan="8"></td></tr>
    <tr style="background:#f2f2f2; font-weight:bold; text-align:center;">
        <th colspan=3>SURPLUS/DEFISIT</th>
        <th>:</th>
        <th></th>
        <td>{{ number_format($saldo,0,'.','.') }}</td>
        <th></th>
        <th></th>
        
    </tr>
</table>

{{-- ============================= --}}
{{-- BAGIAN TANDA TANGAN --}}
{{-- ============================= --}}
<table style="width:100%; margin-top:40px;">
    <tr>
        <td></td>
        <td colspan="2" style="vertical-align:top;">Mengetahui,</td>
        
        <td colspan="3"></td>
        <td colspan="2" style="text-align:right; vertical-align:top;">
            .................., .................. 20....
        </td>
    </tr>
    <tr>
        <td></td>
        <td colspan="2">Kepala SPPG,</td>
       
        <td colspan="3" style="text-align:center;">Asisten Lapangan</td>
        <td colspan="2" style="text-align:right;">Akuntan SPPG,</td>
    </tr>
    <tr>
        <td colspan="8" style="height:80px;"></td> {{-- ruang tanda tangan --}}
    </tr>
    <tr>
        <td></td>
        
        <td colspan="2">(...............................)</td>
        
        <td colspan="3" style="text-align:center;">(...............................)</td>
        <td colspan="2" style="text-align:right;">(...............................)</td>
    </tr>
</table>
