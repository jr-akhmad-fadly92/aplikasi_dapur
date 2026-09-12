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
    </style>
</head>
<body>

<div class="container">
    <h3>FORMULIR PENGAJUAN MENU HARIAN</h3>

    <table class="no-border">
        <tr>
            <td>Nama Dapur:</td>
            <td>No. Pengajuan:</td>
        </tr>
        <tr>
            <td>ID Dapur:</td>
            <td>Tanggal Pengajuan:</td>
        </tr>
        <tr>
            <td>Kode Dapur:</td>
            <td>Periode Pengajuan:</td>
        </tr>
        <tr>
            <td>Alamat Dapur:</td>
            <td>Total Penerima MBG:</td>
        </tr>
        <tr>
            <td colspan="2" style="border-top: 1px solid black;"></td>
        </tr>
    </table>

    <div class="section">
        <h3>A. Menu yang diajukan:</h3>
        <p>1. Karbohidrat: Nasi</p>
        <p>2. Sayur: Tumis buncis teri</p>
        <p>3. Protein: Semur ayam</p>
        <p>4. Buah: Semangka</p>
        <p>5. Susu: Susu sapi</p>
    </div>

    <div class="section">
        <h3>B. Rincian Bahan Baku:</h3>
        
        <p style="margin-top:10px;">1. Nasi</p>
        <table class="bordered"  style="margin-left: 20px;">
            <tr>
                <th>No</th>
                <th>Bahan baku</th>
                <th>Kebutuhan/pax</th>
                <th>Jml kebutuhan</th>
                <th>Total kebutuhan</th>
            </tr>
            <tr>
                <td>1</td>
                <td>Beras</td>
                <td>100 gram</td>
                <td>3000 pax</td>
                <td>30Kg</td>
            </tr>
        </table>

        <p style="margin-top:10px;">2. Sayur tumis buncis teri</p>
        <table class="bordered"  style="margin-left: 20px;">
            <tr>
                <th>No</th>
                <th>Bahan baku</th>
                <th>Kebutuhan/pax</th>
                <th>Jml kebutuhan</th>
                <th>Total kebutuhan</th>
            </tr>
            <tr>
                <td>1</td>
                <td>Buncis</td>
                <td>100 gram</td>
                <td>3000 pax</td>
                <td>300Kg</td>
            </tr>
            <tr>
                <td>2</td>
                <td>Bawang merah</td>
                <td>40 gram</td>
                <td>3000 pax</td>
                <td>120Kg</td>
            </tr>
            <tr>
                <td>3</td>
                <td>Bawang putih</td>
                <td>25 gram</td>
                <td>3000 pax</td>
                <td>75Kg</td>
            </tr>
            <tr>
                <td>4</td>
                <td>Teri</td>
                <td>20 gram</td>
                <td>3000 pax</td>
                <td>60Kg</td>
            </tr>
            <tr>
                <td>5</td>
                <td>Garam</td>
                <td>2 gram</td>
                <td>3000 pax</td>
                <td>6Kg</td>
            </tr>
        </table>

        <p style="margin-top:10px;">3. Semur Ayam</p>
        <table class="bordered"  style="margin-left: 20px;">
            <tr>
                <th>No</th>
                <th>Bahan baku</th>
                <th>Kebutuhan/pax</th>
                <th>Jml kebutuhan</th>
                <th>Total kebutuhan</th>
            </tr>
            <tr>
                <td>1</td>
                <td>Ayam</td>
                <td>120 gram</td>
                <td>3000 pax</td>
                <td>360Kg</td>
            </tr>
            <tr>
                <td>2</td>
                <td>Bumbu semur</td>
                <td>50 gram</td>
                <td>3000 pax</td>
                <td>150Kg</td>
            </tr>
        </table>

        <p style="margin-top:10px;">4. Buah Semangka</p>
        <table class="bordered"  style="margin-left: 20px;">
            <tr>
                <th>No</th>
                <th>Bahan baku</th>
                <th>Kebutuhan/pax</th>
                <th>Jml kebutuhan</th>
                <th>Total kebutuhan</th>
            </tr>
            <tr>
                <td>1</td>
                <td>Semangka</td>
                <td>200 gram</td>
                <td>3000 pax</td>
                <td>600Kg</td>
            </tr>
        </table>

        <p style="margin-top:10px;">5. Susu</p>
        <table class="bordered"  style="margin-left: 20px;">
            <tr>
                <th>No</th>
                <th>Bahan baku</th>
                <th>Kebutuhan/pax</th>
                <th>Jml kebutuhan</th>
                <th>Total kebutuhan</th>
            </tr>
            <tr>
                <td>1</td>
                <td>Susu sapi</td>
                <td>250 ml</td>
                <td>3000 pax</td>
                <td>750L</td>
            </tr>
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
</div>

</div>

</body>
</html>
