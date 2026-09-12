<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap PO</title>
    <style>
        
        body {
            font-family: Arial, sans-serif;
            margin: 5px;
            padding: 5px;
            font-size: 15px;
        }
        h3 {
            font-size: 15px;
            margin: 5px 0;
            font-weight: normal;
        }
        .container {
            width: 100%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 3px;
            text-align: left;
            font-size: 15px;
            font-weight: normal;
        }
        .no-border th, .no-border td {
            border: none;
        }
        .bordered th, .bordered td {
            border: 1px solid black;
        }
        .section {
            margin: 5px 0;
        }
        p {
            margin: 0;
            padding-left: 15px;
        }
        .page-break {
            page-break-before: always;
        }
        .footer {
            position: fixed;
            bottom: 150px; /* Semakin besar, semakin naik ke atas */
            left: 0;
            right: 0;
            height: 100px;
        }

        .footer-table {
            width: 100%;
            text-align: center;
            border-collapse: collapse;
        }

        .footer-table td {
            padding: 5px;
        }
    </style>
</head>
<body>

<div class="container"> 

    <table class="no-border">
        <tr>
            <td><strong>Nama Dapur </strong></td>
            <td>: {{ $dapur->nama_dapur }}</td>
            <td><strong>Periode</strong></td>
            <td>: {{ $tanggal_awal }} s.d {{ $tanggal_akhir }}</td>
        </tr>
        <tr>
            <td><strong>ID Dapur</strong> </td>
            <td>: {{ $dapur->nomor_dapur }}</td>
            <td></td>
            <td></td>
        </tr>
        
        <tr>
            <td ><strong>Alamat Dapur</strong></td>
            <td colspan="3">:  {{ $dapur->alamat_dapur }}</td>
           
        </tr>
        <tr>
            <td colspan="4" style="border-top: 1px solid black;"></td>
        </tr>
    </table>

    <div class="section">
        <h3 style="text-align: center;font-weight:bold">Rekap PO</h3>
        <table class="bordered"  style="margin-left: 20px;">
            <tr>
                <th>No</th>
                <th>Nomor Po</th>
                <th>Tanggal Po</th>
                <th>Jumlah Yang Dibayarkan</th>
                <th>Status</th>
                
            </tr>
            @php
                $i = 0 ;    
            @endphp
            @foreach($data as $row)
            @php
                $i++;    
            @endphp
            
            <tr>
                <td>{{ $i }}</td>
                <td>{{ $row->nomor_po }}</td>
                
                <td>{{ \Carbon\Carbon::parse($row->tanggal_po)->translatedFormat('l, j F Y') }}</td>
                <td>Rp. {{ number_format($row->total_jumlah_po, 0, '.', '.') }}</td>
                <td>{{ $row->status_po }}</td>
            </tr>
            @endforeach
            
        </table>

        
    </div>

    

    <div >
        <table style="width: 100%;text-align:center">
            <tr>
                <td style="width: 33%"></td>
                <td style="width: 33%"></td>
                <td  style="width: 34%">
                    {{ \Carbon\Carbon::now()->translatedFormat('l, j F Y') }}
                </td>
            </tr>
            <tr>
                <td>Ahli Gizi</td>
                <td>Ahli Akuntan</td>
                <td>Kepala Dapur</td>
            </tr>
          
        </table>
    </div>
    
    

</div>

</body>
</html>
