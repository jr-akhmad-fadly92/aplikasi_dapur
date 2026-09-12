<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Master Bahan Baku</title>
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
    </style>
</head>
<body>

<div class="container">
    <h3>FORMULIR MASTER BAHAN BAKU</h3>

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
        <h3>A. Master Bahan Baku :</h3>
        <table style="border-collapse: collapse; width: 100%;">
            <tr>
                <td style="width: 35%">1. Karbohidrat</td>
                <td style="width: 15%">: {{ $jumlah_karbohidrat }}</td>
                <td style="width: 5%"> item</td>
                <td style="width: 40%"> </td>
            </tr>
            <tr>
                <td style="width: 35%">2. Sayur</td>
                <td style="width: 15%">: {{ $jumlah_sayur }}</td>
                <td style="width: 5%"> item</td>
                <td style="width: 40%"> </td>
            </tr>
            <tr>
                <td style="width: 35%">3. Protein</td>
                <td style="width: 15%">: {{ $jumlah_protein }}</td>
                <td style="width: 5%"> item</td>
                <td style="width: 40%"> </td>
            </tr>
            <tr>
                <td style="width: 35%">4. Buah</td>
                <td style="width: 15%">: {{ $jumlah_buah }}</td>
                <td style="width: 5%"> item</td>
                <td style="width: 40%"> </td>
            </tr>
            <tr>
                <td style="width: 35%">5. Suplemen</td>
                <td style="width: 15%">: {{ $jumlah_tambahan }}</td>
                <td style="width: 5%"> item</td>
                <td style="width: 40%"> </td>
            </tr>
            <tr>
                <td style="width: 35%">6. Bumbu</td>
                <td style="width: 15%">: {{ $jumlah_bumbu }}</td>
                <td style="width: 5%"> item</td>
                <td style="width: 40%"> </td>
            </tr>
            <tr>
                <td style="width: 35%">7. Non Pangan (Penunjang)</td>
                <td style="width: 15%">: {{ $jumlah_penunjang }}</td>
                <td style="width: 5%"> item</td>
                <td style="width: 40%"> </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h3>B. Rincian Bahan Baku:</h3>
        
        <p style="margin-top:10px;">1. Karbohidrat</p>
        <table class="bordered"  style="margin-left: 20px;">
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 35%">Bahan baku</th>
                <th style="width: 65%">Spesifikasi</th>
            </tr>
            @foreach($data_karbohidrat as $row)
            <tr>
                <td>{{ $row->nomor_urut }}</td>
                <td>{{ $row->bahan }}</td>
                <td>{{ $row->spesifikasi}}</td>
            </tr>
            @endforeach
            
        </table>

        <p  style="margin-top:10px;">2. Lauk </p>
        <table class="bordered"  style="margin-left: 20px;">
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 35%">Bahan baku</th>
                <th style="width: 65%">Spesifikasi</th>
            </tr>
            @foreach($data_sayur as $row)
            <tr>
                <td>{{ $row->nomor_urut }}</td>
                <td>{{ $row->bahan }}</td>
                <td>{{ $row->spesifikasi}}</td>
            </tr>
            @endforeach
        </table>
        
        <p class="page-break" style="margin-top:10px;">3. Bayur</p>
        <table class="bordered"  style="margin-left: 20px;">
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 35%">Bahan baku</th>
                <th style="width: 65%">Spesifikasi</th>
            </tr>
            @foreach($data_protein as $row)
            <tr>
                <td>{{ $row->nomor_urut }}</td>
                <td>{{ $row->bahan }}</td>
                <td>{{ $row->spesifikasi}}</td>
            </tr>
            @endforeach
        </table>

        <p style="margin-top:10px;">4. Buah</p>
        <table class="bordered"  style="margin-left: 20px;">
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 35%">Bahan baku</th>
                <th style="width: 65%">Spesifikasi</th>
            </tr>
            @foreach($data_buah as $row)
            <tr>
                <td>{{ $row->nomor_urut }}</td>
                <td>{{ $row->bahan }}</td>
                <td>{{ $row->spesifikasi}}</td>
            </tr>
            @endforeach
        </table>

        <p style="margin-top:10px;">5. Tambahan</p>
        <table class="bordered"  style="margin-left: 20px;">
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 35%">Bahan baku</th>
                <th style="width: 65%">Spesifikasi</th>
            </tr>
            @foreach($data_tambahan as $row)
            <tr>
                <td>{{ $row->nomor_urut }}</td>
                <td>{{ $row->bahan }}</td>
                <td>{{ $row->spesifikasi}}</td>
            </tr>
            @endforeach
        </table>

        <p style="margin-top:10px;">6. Bumbu</p>
        <table class="bordered"  style="margin-left: 20px;">
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 35%">Bahan baku</th>
                <th style="width: 65%">Spesifikasi</th>
            </tr>
            @foreach($data_bumbu as $row)
            <tr>
                <td>{{ $row->nomor_urut }}</td>
                <td>{{ $row->bahan }}</td>
                <td>{{ $row->spesifikasi}}</td>
            </tr>
            @endforeach
        </table>

        <p style="margin-top:10px;">7. Non Pangan (Penunjang)</p>
        <table class="bordered"  style="margin-left: 20px;">
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 35%">Bahan</th>
                <th style="width: 65%">Spesifikasi</th>
            </tr>
            @foreach($data_penunjang as $row)
            <tr>
                <td>{{ $row->nomor_urut }}</td>
                <td>{{ $row->bahan }}</td>
                <td>{{ $row->spesifikasi}}</td>
            </tr>
            @endforeach
        </table>
    </div>

    

    <div style="margin-top:50px;">
        <table class="no-border">
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td style="padding: 5px;">Ditinjau oleh :</td>
            </tr>
            <tr>
                <td style="padding: 5px;">Yang Mengajukan:</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td style="padding: 5px;">Hasil: disetujui / ditolak *</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="6" style="height: 30px;"></td>
            </tr>
        </table>
        <table>
            <tr>
                <td style="padding: 5px;">….………………….</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td style="padding: 5px;">………………………………</td>
            </tr>
        </table>
        <table>
            <tr>
                <td style="padding: 5px;">Ahli Gizi</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td style="padding: 5px;">Kepala Dapur</td>
            </tr>
        </table>
    </div>

</div>

</body>
</html>
