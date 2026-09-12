<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir PO (Purchase Order)</title>
    <style>
         
    
        body {
            font-family: Arial, sans-serif;
            padding-bottom: 120px; /* sisakan ruang untuk footer */
        }
    
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            page-break-inside: auto;
        }
    
        th, td {
            border: 1px solid black;
            padding: 2px;
            text-align: center;
            margin: 0;
        }
    
        th {
            background-color: #d9e4dd;
        }
    
        td {
            background-color: #f9f9f9;
        }
    
        .header, .footer {
            margin: 20px 0;
        }
    
        .footer td {
            border: none;
        }
    
        .label {
            display: inline-block;
            width: 160px;
        }
    
        .value {
            display: inline-block;
        }
    
        p {
            margin: 1;
            padding: 1;
        }
    
        .page-break {
            page-break-after: always;
        }
        .footer {
        position: absolute;
        
        width: 100%;
    }

    
    
        
    </style>
    
</head>
<body>
    @php
    $chunks = $rincian_po->chunk(6); // pecah per 8 baris
        $i = 1; // untuk nomor urut global
        $jenis_Resep = 0;
    @endphp
    @foreach($chunks as $page)
    <div class="header">
        <table style="margin: 0px; padding: 0px; border-collapse: collapse;">
            <tr>
                <td style="height:5%; text-align: center; font-weight: bold; font-size: 16pt;">
                    Formulir PO (PURCHASE ORDER)
                </td>
                
            </tr>
        </table>
        <p><span ><h3>Badan Gizi Nasional<br>Satuan Pelayanan Pemenuhan Gizi Yayasan Bina Bangsa</h3></span></p>
        <p><span ></p>
        <p><span class="label"><strong>Nomor PO</strong></span> <span class="value">: {{ $po->nomor_po }} </span></p>
        <p><span class="label"><strong>Pembuat</strong></span> <span class="value">: {{ $dapur->ahli_akuntan }} </span></p>
        <p><span class="label"><strong>Jumlah Porsi</strong></span> <span class="value">: {{ $jumlah_pack }} pack</span></p>
        <p style="margin-left: 160px;">
            @foreach($pack_breakdown as $pack)
                <span class="value">{{ $pack['label'] }}: {{ $pack['quantity'] }} pack</span>
                @if(!$loop->last) | @endif
            @endforeach
        </p>
        <p><span class="label"><strong>Ditujukan pada</strong></span> <span class="value">: Koperasi Pemasaran Seribu Impian Bersama</span></p>
    
    </div>
    


<div class="page">
    <div class="body">
        <table style="font-size: 13px">
            <thead>
                <tr>
                    <th style="width: 2%">No.</th>
                    <th style="width: 10%">Menu</th>
                    <th style="width: 12%">Bahan</th>
                    <th style="width: 6%">Kebutuhan</th>
                    <th style="width: 4%">Satuan</th>
                    <th style="width: 4%">Kemasan(box / pack)</th>
                    <th style="width: 15%">Tanggal Kirim</th>
                    <th style="width: 30%">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($page as $row)
                
                <tr>
                    <td style="text-align: left;">{{ $i++ }}</td>
                    @if($jenis_Resep != ($row->id_resep ?? 0))
                    <td style="text-align: left;">{{ $row->nama_resep }} </td>
                    @else
                    <td style="text-align: left;"></td>
                    @endif

                    <td style="text-align: left;"> {{ $row->bahan }}</td>
                    @if($row->satuan == 'Gram' || $row->satuan == 'gram')
                    <td style="text-align: right;">{{ rtrim(rtrim(number_format(($row->jumlah_bahan / 1000), 3, ',', '.'), '0'), ',') }}</td>
                    <td style="text-align: right;">kg </td>
		            <td style="text-align: right;">{{$row->jumlah_box ?? 1}}  </td>
                    @elseif($row->satuan == 'ml' || $row->satuan == 'Ml')
                    <td style="text-align: right;">{{ rtrim(rtrim(number_format(($row->jumlah_bahan / 1000), 3, ',', '.'), '0'), ',') }}</td>
                    <td style="text-align: right;">Liter </td>
		            <td style="text-align: right;">{{$row->jumlah_box ?? 1}} </td>
                    @else
                    <td style="text-align: right;">{{ number_format($row->jumlah_bahan, 2, ',', '.') }}</td>
                    <td style="text-align: right;">{{ $row->satuan }}</td>
		    <td style="text-align: right;">{{$row->jumlah_box ?? 1}} </td>
                    @endif
                    
                    <td style="text-align: right;">{{ \Carbon\Carbon::parse($row->tanggal_kedatangan)->translatedFormat('l, j F Y H:i') }}</td>
                    <td style="text-align: left;">
                        @if($row->bahan == 'Beras')
                            25kg = {{ $pack25 }} pcs, 5kg = {{ $pack5 }} pcs, 1kg = {{ $pack1 }} pcs
                        @else
                            {{ $row->keterangan ?? '-' }}
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        <table style="font-size: 13px">
            <tr>
                <td style="text-align: center;"></td>
                <td style="text-align: center;"></td>
                <td style="text-align: center;"></td>
                <td style="text-align: center;"></td>
                
                <td style="text-align: center;">{{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('l, d F Y ') }}</td>
            </tr>
            <tr>
                <td style="text-align: center;">Asisten Lapangan</td>
                <td style="text-align: center;">Ahli Gizi</td>
                <td style="text-align: center;">Ahli Akuntan</td>
                
                <td style="text-align: center;">Kepala Dapur</td>
                <td style="text-align: center;">Yayasan</td>
            </tr>
            <tr>
                <td style="height: 30px"></td>
                <td></td>
                <td style="text-align: center;"></td>
            </tr>
            <tr>
                <td style="text-align: center;">{{ $dapur->admin_dapur }}</td>
                <td style="text-align: center;">{{ $dapur->ahli_gizi }}</td>
                <td style="text-align: center;">{{ $dapur->ahli_akuntan }}</td>
                <td style="text-align: center;">{{ $dapur->kepala_dapur }}</td>
                <td style="text-align: center;"></td>
            </tr>
        </table>
    </div>

    @if (!$loop->last)
        <div class="page-break"></div>
    @endif
</div>
@endforeach
<footer style="position: fixed; right: 10px; bottom: 10px; font-size: 12px; font-style: italic;">
    print rangkap 4
</footer>
</body>
</html>
