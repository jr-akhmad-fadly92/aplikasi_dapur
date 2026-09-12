<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Ceklist Bahan Datang</title>
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
    <h3>FORMULIR CEKLIST BAHAN </h3>

    <table class="no-border">
        <tr>
            <td><strong>Nama Dapur </strong></td>
            <td>: {{ $dapur->nama_dapur }}</td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td><strong>ID Dapur</strong> </td>
            <td>: {{ $dapur->nomor_dapur }}</td>
            <td><strong>Tanggal Datang</strong> </td>
            <td>: </td>
        </tr>
        <tr>
            <td><strong> Penerima MBG </strong></td>
            <td>: {{ $totalPorsi }} Pack</td>
            <td> </td>
            <td></td>
        </tr>
        
        <tr>
            <td colspan="4" style="border-top: 1px solid black;"></td>
        </tr>
    </table>

    <div class="section">
        <h3>A. Menu :</h3>
        <p>1. Karbohidrat: {{ $karbohidrat->nama_resep }}</p>
        <p>2. Sayur: {{ $sayur->nama_resep }}</p>
        <p>3. Protein: {{ $protein->nama_resep }}</p>
        <p>4. Buah: {{ $buah->nama_resep }}</p>
        <p>5. Susu: {{ $susu->nama_resep }}</p>
    </div>

    <div class="section">
        <h3>B. Rincian Bahan Baku:</h3>
        
        <p style="margin-top:10px;">1. {{ $karbohidrat->nama_resep }}</p>
        <table class="bordered"  style="margin-left: 20px;">
            <tr>
                <th>No</th>
                <th>Bahan baku</th>
                <th>Kebutuhan/pax</th>
                <th>Jml kebutuhan</th>
                <th>Total kebutuhan</th>
            </tr>
            @foreach($rincian_karbohidrat as $row)
            <tr>
                <td>{{ $row->nomor_urut }}</td>
                <td>{{ $row->bahan }}</td>
                @if(($row->jumlah_menu_bahan /$karbohidrat->porsi_resep) < 1 )
                <td>---</td>
                
                @else 
                <td>{{ number_format(($row->jumlah_menu_bahan /$karbohidrat->porsi_resep), 0, ',', '.') }} {{ $row->satuan }}</td>
                
                @endif
                <td>{{ number_format($totalPorsi, 0, ',', '.') }} pack</td>
                <td>{{ number_format($row->jumlah_rincian_menu_harian, 0, ',', '.') }} {{ $row->satuan }}</td>
            </tr>
            @endforeach
            
        </table>

        <p  style="margin-top:10px;">2. {{ $sayur->nama_resep }}</p>
        <table class="bordered"  style="margin-left: 20px;">
            <tr>
                <th>No</th>
                <th>Bahan baku</th>
                <th>Kebutuhan/pax</th>
                <th>Jml kebutuhan</th>
                <th>Total kebutuhan</th>
            </tr>
            @foreach($rincian_sayur as $row)
            <tr>
                <td>{{ $row->nomor_urut }}</td>
                <td>{{ $row->bahan }}</td>
                @if(($row->jumlah_menu_bahan /$sayur->porsi_resep) < 1 )
                <td>---</td>
                
                @else 
                <td>{{ number_format(($row->jumlah_menu_bahan /$sayur->porsi_resep), 0, ',', '.') }} {{ $row->satuan }}</td>
                
                @endif
                <td>{{ number_format($totalPorsi, 0, ',', '.') }} pack</td>
                <td>{{ number_format($row->jumlah_rincian_menu_harian, 0, ',', '.') }} {{ $row->satuan }}</td>
            </tr>
            @endforeach
        </table>

        <p class="page-break"style="margin-top:10px;">3. {{ $protein->nama_resep }}</p>
        <table class="bordered"  style="margin-left: 20px;">
            <tr>
                <th>No</th>
                <th>Bahan baku</th>
                <th>Kebutuhan/pax</th>
                <th>Jml kebutuhan</th>
                <th>Total kebutuhan</th>
            </tr>
            @foreach($rincian_protein as $row)
            <tr>
                <td>{{ $row->nomor_urut }}</td>
                <td>{{ $row->bahan }}</td>
                @if(($row->jumlah_menu_bahan /$protein->porsi_resep) < 1 )
                <td>---</td>
                
                @else 
                <td>{{ number_format(($row->jumlah_menu_bahan /$protein->porsi_resep), 0, ',', '.') }} {{ $row->satuan }}</td>
                
                @endif
                
                <td>{{ number_format($totalPorsi, 0, ',', '.') }} pack</td>
                <td>{{ number_format($row->jumlah_rincian_menu_harian, 0, ',', '.') }} {{ $row->satuan }}</td>
            </tr>
            @endforeach
        </table>

        <p style="margin-top:10px;">4. {{ $buah->nama_resep }}</p>
        <table class="bordered"  style="margin-left: 20px;">
            <tr>
                <th>No</th>
                <th>Bahan baku</th>
                <th>Kebutuhan/pax</th>
                <th>Jml kebutuhan</th>
                <th>Total kebutuhan</th>
            </tr>
            @foreach($rincian_buah as $row)
            <tr>
                <td>{{ $row->nomor_urut }}</td>
                <td>{{ $row->bahan }}</td>
                @if(($row->jumlah_menu_bahan /$buah->porsi_resep) < 1 )
                <td>---</td>
                
                @else 
                <td>{{ number_format(($row->jumlah_menu_bahan /$buah->porsi_resep), 0, ',', '.') }} {{ $row->satuan }}</td>
                
                @endif
                
                <td>{{ number_format($totalPorsi, 0, ',', '.') }} pack</td>
                <td>{{ number_format($row->jumlah_rincian_menu_harian, 0, ',', '.') }} {{ $row->satuan }}</td>
            </tr>
            @endforeach
        </table>

        <p style="margin-top:10px;">5. {{ $susu->nama_resep }}</p>
        <table class="bordered"  style="margin-left: 20px;">
            <tr>
                <th>No</th>
                <th>Bahan baku</th>
                <th>Kebutuhan/pax</th>
                <th>Jml kebutuhan</th>
                <th>Total kebutuhan</th>
            </tr>
            @foreach($rincian_susu as $row)
            <tr>
                <td>{{ $row->nomor_urut }}</td>
                <td>{{ $row->bahan }}</td>
                @if(($row->jumlah_menu_bahan /$susu->porsi_resep) < 1 )
                <td>---</td>
                
                @else 
                <td>{{ number_format(($row->jumlah_menu_bahan /$susu->porsi_resep), 0, ',', '.') }} {{ $row->satuan }}</td>
                
                @endif
                
                <td>{{ number_format($totalPorsi, 0, ',', '.') }} pack</td>
                <td>{{ number_format($row->jumlah_rincian_menu_harian, 0, ',', '.') }} {{ $row->satuan }}</td>
            </tr>
            @endforeach
        </table>

        <p style="margin-top:10px;">6. Tambahan</p>
        <table class="bordered"  style="margin-left: 20px;">
            <tr>
                <th>No</th>
                <th>Bahan baku</th>
                <th>Kebutuhan/pax</th>
                <th>Jml kebutuhan</th>
                <th>Total kebutuhan</th>
            </tr>
            @foreach($rincian_tambahan as $row)
            <tr>
                <td>{{ $row->nomor_urut }}</td>
                <td>{{ $row->bahan }}</td>
                <td>-</td>
                <td>{{ number_format($totalPorsi, 0, ',', '.') }} pack</td>
                <td>{{ number_format($row->jumlah_rincian_menu_harian, 0, ',', '.') }} {{ $row->satuan }}</td>
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
</div>

</div>

</body>
</html>
