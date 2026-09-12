<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Pengajuan Menu Harian</title>
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
        .footer-signature {
            position: fixed;
            bottom: 60px; /* Naik dari bawah */
            left: 0;
            width: 100%;
            text-align: center; /* Supaya semua isi center */
        }
        .footer-signature table {
            margin-left: auto;
            margin-right: auto; /* Tabel tetap center */
        }
    </style>
</head>
<body>
<br><br>
<div class="container">
    <h3>FORMULIR PENGAJUAN MENU HARIAN</h3>

    <table class="no-border">
        <tr>
            <td><strong>Nama Dapur </strong></td>
            <td>: {{ $dapur->nama_dapur }}</td>
            <td><strong>ID Dapur </strong></td>
            <td>: {{ $dapur->nomor_dapur }}</td>
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
        <h3>A. Rincian Pengeluaran</h3>
        
        <table class="bordered"  style="margin-left: 20px;">
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <!--th>Kebutuhan/pax</!--th-->
                <th>Material</th>
                <th>Pengeluaran</th>
            </tr>
            @php
                $total = 0;
            @endphp
            @foreach($transaksi as $row)
            @php
                $total = $total+$row->jumlah;
            @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ \Carbon\Carbon::parse($row->tanggal)->format('d-m-Y H:i') }}</td>
                <td>{{ $row->bahan }}</td>
                <td style="text-align: right">Rp. {{ number_format($row->jumlah, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr>
                <th colspan="3">Total</th>
                <th style="text-align: right">Rp. {{ number_format($total, 0, ',', '.') }}</th>
            </tr>
        </table>

        
    </div>

    

    <div class="footer-signature">
        <table class="no-border" >
            <tr>
                <td style="padding:5px; text-align: center;">Yang Mengajukan:</td>
                <td style="padding:5px; text-align: center;">Ditinjau oleh:</td>
            </tr>
            <tr>
                <td style="padding:20px;text-align: center;">….………………….</td>
                <td style="padding:20px;text-align: center;">………………………………</td>
            </tr>
            <tr>
                <td style="padding:20px;text-align: center;">Ahli Akuntan</td>
                <td style="padding:20px;text-align: center;">Kepala Dapur</td>
            </tr>
        </table>
    </div>
    

</div>

</body>
</html>
