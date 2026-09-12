<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Menu</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 0.5cm;
        }
        
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
            padding: 2px;
            text-align: left;
            font-size: 12px;
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
            <td><strong>Jam Pelayanan</strong></td>
            <td>: {{ $dapur->jam_buka ?? '-' }} - {{ $dapur->jam_tutup ?? '-' }}</td>
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
        <h3 style="text-align: center;font-weight:bold">Rekap Menu</h3>

        <table class="bordered"  style="margin-left: 20px; margin-right: 20px;">
            <tr>
                <th>No</th>
                <th>Tanggal Menu </th>
                <th>Karbohidrat</th>
                <th>Protein</th>
                <th>Sayur</th>
                <th>Buah</th>
                <th>Pendamping</th>
                <th>Jumlah Porsi</th>
                <th>Energi (kkal)</th>
                <th>Protein (g)</th>
                <th>Lemak (g)</th>
                <th>Karbohidrat (g)</th>
                <th>Serat (g)</th>
                <th>Natrium (mg)</th>
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
                <td>{{ \Carbon\Carbon::parse($row->tanggal_kirim)->translatedFormat('l, d F Y') }} | gol @if(($row->total_penerima_a ?? 0) > 0) A @else B @endif
                </td>
                <td>{{ $row->nama_karbohidrat }}</td>
                <td>{{ $row->nama_protein }}</td>
                <td>{{ $row->nama_sayur }}</td>
                <td>{{ $row->nama_buah }}</td>
                <td>{{ $row->nama_susu }}</td> 
                <td>{{ $row->total_penerima ?? 0 }}</td>
                <td>{{ number_format($row->energi ?? 0, 2, ',', '.') }}</td>
                <td>{{ number_format($row->protein_gizi ?? 0, 2, ',', '.') }}</td>
                <td>{{ number_format($row->lemak ?? 0, 2, ',', '.') }}</td>
                <td>{{ number_format($row->karbohidrat_gizi ?? 0, 2, ',', '.') }}</td>
                <td>{{ number_format($row->serat ?? 0, 2, ',', '.') }}</td>
                <td>{{ number_format($row->natrium ?? 0, 2, ',', '.') }}</td>
            </tr>
            @endforeach
            
        </table>

        
    </div>

    

    <div >
        <table style="width: 100%;text-align:center">
            <tr>
                <td style="width: 50%"></td>
                <td  style="width: 50%">
                    {{ \Carbon\Carbon::now()->translatedFormat('l, j F Y') }}
                </td>
            </tr>
            <tr>
                <td>Ahli Gizi</td>
                <td>Kepala Dapur</td>
            </tr>
          
        </table>
    </div>
    
    
    

</div>

</body>
</html>
