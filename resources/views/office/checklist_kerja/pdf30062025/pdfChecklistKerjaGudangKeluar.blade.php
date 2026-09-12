<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Checklist Pekerjaan Harian</title>
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
        .square {
            height: 20px;
            width: 20px;
            border: 1px solid black;
        }
        .bold{
            font-weight: bold;
        }
        .center{
            text-align: center;
        }
        .footer {
        position: fixed;
        bottom: -40px;
        left: 0;
        right: 0;
        height: 30px;
        text-align: right;
        font-size: 10px;
        color: #333;
        padding-right: 20px;
    }
        
    </style>
</head>
<body>
    @php
        $jumlah = count($jenis);
        $parent_page = 0;
    @endphp
@foreach ($jenis as $index => $jenis)
    @php
    $max_page = 20;
    $items = $dataByJenis[$jenis] ?? collect();
    $t_page = max(1, ceil(count($items->toArray()) / $max_page)); // minimal 1 halaman
    
    @endphp
    
    @for($i = 0; $i < $t_page; $i++)
    @php $start = $i * $max_page; @endphp
<div class="container">
    <h3 class="bold center">FORMULIR CHEKCLIST PENGELUARAN BARANG HARIAN</h3>

    <table class="no-border">
        <tr>
            <td><strong>Nama Dapur </strong></td>
            <td>: {{$dapur->nama_dapur}}</td>
            <td><strong>Tanggal Dikeluarkan</strong> </td>
            <td>: {{date('d F Y',strtotime($tanggal))}}</td>
        </tr>
        <tr>
            <td><strong>ID Dapur</strong> </td>
            <td>: {{$dapur->nomor_dapur}} </td>
            <td class="bold">ID Petugas</td>
            <td>: .....................</td>
        </tr>
        
        <tr>
            <td ><strong>Alamat Dapur</strong></td>
            <td colspan="3">:  {{$dapur->alamat_dapur}} </td>
           
        </tr>
        <tr>
            <td colspan="4" style="border-top: 1px solid black;"></td>
        </tr>
    </table>

    

    <div class="section">
        <h3 class="bold">Rincian Bahan {{$jenis}}:</h3>
        
        <table class="bordered"  style="margin-left: 20px;">
            <tr>
                <th class="bold center" style="width: 5%;">No</th>
                <th class="bold center" style="width: 25%;">Kode Penyimpanan</th>
                <th class="bold center" style="width: 25%;">Nama Barang</th>
                <th class="bold center" style="width: 10%;">Jumlah</th>
                <th class="bold center" style="width: 10%;">Satuan</th>
                <th class="bold center" style="width: 15%;">Keterangan</th>
                <th class="bold center" style="width: 10%;">Checklist</th>
                
            </tr>
            @forelse($items->slice($start, $max_page) as $index => $barang)
            <tr>
                <td>{{$index+1}}</td>
                <td>{{$barang->kode_wadah}}</td>
                <td>{{$barang->nama_barang}}</td>
                <td style="text-align: right;">{{$barang->jumlah}}</td>
                <td>{{$barang->satuan->satuan}}</td>
                <td>{{$barang->keterangan}}</td>
                
                <td style="text-align: center;">   <div class="square"></div></td>
            </tr>
            @empty
                <tr>
                    <td colspan="7" class="center">Data tidak ditemukan.</td>
                </tr>
            @endforelse
        </table>

        
    </div>

    <div style="margin-top:50px;">
        <table class="no-border">
            <tr>
                <td style="width: 50%;" class="center">
                    Petugas
                    <br><br><br><br><br><br>
                    ............................................
                </td>
                <td class="center">
                    Diperiksa oleh :<br>
                    Hasil: disetujui / ditolak *
                    <br><br><br><br><br>
                    ............................................
                </td>
                
            </tr>
            
        </table>
        
</div>

</div>
<div class="footer">
    <span class="pagenum"></span>
</div>

@if($i+1 < $t_page)
      <div class="page-break"></div>
      @endif
@endfor
@if($parent_page+1 < $jumlah)
@php $parent_page++; @endphp
<div class="page-break"></div>
    
@endif
@endforeach

</body>
</html>
