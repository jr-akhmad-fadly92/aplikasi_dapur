<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PURCHASE ORDER</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin: 20px;
        }
        .container {
            width: 100%;
            margin: auto;
        }
        h2 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td {
            padding: 2px;
        }
        .border {
            border: 1px solid black;
        }
        th, td {
            border: 1px solid black;
            text-align: left;
            padding: 5px;
        }
        .no-border td {
            border: none;
            padding: 2px;
        }
         p {
            margin: 0;
            padding-left: 15px;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>

<div class="container">

    <table class="no-border">
        <tr>
            <td style="font-weight:bold;width:40    %">Yayasan Bina Bangsa</td>
            <td style="font-weight:bold; font-size:25px; padding-left:160px;width:60%">PURCHASE ORDER</td>
        </tr>
        <tr>
            <td>{{ $dapur->nama_dapur }}</td>
            <td style="padding-left:160px;">Tanggal : {{ \Carbon\Carbon::parse($po->tanggal_po)->format('d-m-Y') }}</td>
        </tr>
        <tr>
            <td>{{ $dapur->alamat_dapur }}"</td>
            <td style="padding-left:160px;">NO.PO : {{ $po->nomor_po }}</td>
        </tr>
        <tr>
            <td>{{ $dapur->kecamatan }} - {{$dapur->kota}}</td>
            <td style="padding-left:160px;"></td>
        </tr>
        <tr>
            <td>Telp    : {{ $dapur->no_telp }}</td>
            <td style="padding-left:160px;"></td>
        </tr>
        <tr>
            <td>Email : {{ $dapur->email }}</td>
            <td style="padding-left:160px;"></td>
        </tr>
        <tr>
            
            <td></td>
            <td style="padding-left:160px; height:10px"></td>
        </tr>
      
        <tr>
            
            <td style="font-weight:bold">Jenis Bahan Baku : {{ $keterangan_bahan ??'-'}}</td>
            <td style="padding-left:10px;font-weight:bold">Jumlah porsi : {{ number_format($jumlah_porsi,0,',','.') }} pax</td>
            
        </tr>
        <tr>
            @if($keterangan_bahan  == 'Bumbu')
            <td colspan=2 style="padding-left:0px;font-weight:bold;"></td>
        
            @else
            <td colspan=2 style="padding-left:0px;font-weight:bold;">Keterangan : {{ $sample->keterangan??'-' }}</td>
        
            @endif
         </tr>
        
    </table>
    <br>


    <!-- Tabel 1 -->
    <table style="margin-top:20px;font-size:11px">
        <tr>
            <th style="text-align: center;">No</th>
            <th style="text-align: center;">Nama Bahan Baku</th>
            <th style="text-align: center;">Tanggal Kirim</th>
            <th style="text-align: center;">Qty</th>
            <th style="text-align: center;">Satuans</th>
            <th style="text-align: center;">Box</th>
            <!--th style="text-align: center;">Harga</th>
            <th style="text-align: center;">Total Harga</th--!>
        </tr>
        @php
           $total_semua = 0; 
           $i = 0;
        @endphp
        @foreach($rincian_po as $row)
        @php $i++; @endphp
        <tr>
            <td style="text-align: center;">{{ $i }} </td>
            <td>{{ $row->bahan }} </td>
            <td>{{ \Carbon\Carbon::parse($row->tanggal_kedatangan)->translatedFormat('l, d M Y H:i') }}</td>
            <!--td style="text-align: right;">{{ $row->satuan_kontrak }}</!--td-->
            <td style="text-align: right;">{{ number_format($row->jumlah_bahan, 0, ',', '.') }} </td>
            <td style="text-align: right;"> {{ $row->satuan }}</td>
            <td style="text-align: right;">{{ $row->jumlah_box  }} </td>
            <!--td style="text-align: right;">Rp {{ number_format($row->harga_bahan, 0, ',', '.') }}</td--!>
            <!--td style="text-align: right;">Rp. {{ number_format($row->jumlah_po, 0, ',', '.') }} </td--!>
           
        </tr>
       
        @php
            $total_semua = $total_semua + $row->jumlah_po;
        @endphp
        @endforeach
        <!--tr>
            <td colspan="7" style="text-align: left;"><b>TOTAL</b></td>
            <td colspan="1" style="text-align: right;"><b>Rp. {{ number_format($total_semua, 0, ',', '.') }}</b></td>
            
        </tr-->
    </table>
   
    <br>
    <table class="no-border" style="margin-top:50px;">
        <tr>
            <td colspan="2">{{ $dapur->kota }}, {{ \Carbon\Carbon::parse($po->tanggal_po)->translatedFormat('j F Y') }}</td>
            <td style="width: 25%"></td>
            <td style="width: 25%"></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td style="width: 25%">Asisten Lapangan,</td>
            <td style="width: 25%;text-align:center">Ahli Gizi,</td>
            <td style="width: 25%;text-align:center">Ahli Akuntan</td>   
            <td style="width: 25%;text-align:center">SPPI</td>
        </tr>
        <tr>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>{{ $dapur->admin_dapur }}</td>
            <td style="width: 25%;text-align:center">{{ $dapur->ahli_gizi }}</td>
            <td style="width: 25%;text-align:center">{{ $dapur->ahli_akuntan }}</td>
            <td style="width: 25%;text-align:center">{{ $dapur->kepala_dapur }}</td>
        </tr>
    </table>

</div>

</body>
</html>
