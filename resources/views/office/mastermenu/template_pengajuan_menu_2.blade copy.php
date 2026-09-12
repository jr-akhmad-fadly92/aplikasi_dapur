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
    </style>
</head>
<body>

<div class="container">
    <h3>FORMULIR PENGAJUAN MENU HARIAN</h3>

    <table class="no-border">
        <tr>
            <td><strong>Nama Dapur </strong></td>
            <td>: {{ $dapur->nama_dapur }}</td>
            <td><strong>No. Pengajuan </strong></td>
            <td>: {{ $data_menu_harian->nomor_pengajuan }}</td>
        </tr>
        <tr>
            <td><strong>ID Dapur</strong> </td>
            <td>: {{ $dapur->nomor_dapur }}</td>
            <td><strong>Tanggal Pengajuan</strong> </td>
            <td>: {{ $tanggal_pengajuan }}</td>
        </tr>
        <tr>
            <td><strong> Penerima </strong></td>
            <td>: {{ $totalPorsi }} Pack</td>
            <td><strong>Tanggal Kirim Bahan</strong>  </td>
            <td>: {{ 
            \Carbon\Carbon::parse($tanggal_kirim)
                ->subDays(\Carbon\Carbon::parse($tanggal_kirim)->isSaturday() ? 2 : 1)
                ->translatedFormat('l, d F Y 11:00')
        }}</td>

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
        <h3>A. Menu yang diajukan untuk {{ \Carbon\Carbon::parse($tanggal_kirim)->translatedFormat('l, d F Y') }}:</h3>
        <table style="border-collapse: collapse; width: 100%;">
            <tr>
                <td>1. Karbohidrat</td>
                <td>: {{ $karbohidrat->nama_resep }} || A : {{ $rumus_karbo->karbo_porsi_a }} gram || B : {{ $rumus_karbo->karbo_porsi_b }} gram</td>
            </tr>
            <tr>
                <td>2. Sayur</td>
                <td>: {{ $sayur->nama_resep }} || A : {{ $rumus_sayur->sayur_porsi_a }} gram || B : {{ $rumus_sayur->sayur_porsi_b }} gram</td>
            </tr>
            <tr>
                <td>3. Protein</td>
                <td>: {{ $protein->nama_resep }} || A : {{ $rumus_protein->protein_porsi_a }} gram || B : {{ $rumus_protein->protein_porsi_b }} gram</td>
            </tr>
            <tr>
                <td>4. Buah</td>
                <td>: {{ $buah->nama_resep }} || A : {{ $rumus_buah->buah_porsi_a }} pcs || B : {{ $rumus_buah->buah_porsi_b }} pcs</td>
            </tr>
            <tr>
               
                <td>5. Suplemen</td>
                <td>: {{ $susu->nama_resep }}  || A : {{ $rumus_suplemen->suplemen_porsi_a }} pcs / gram || B : {{ $rumus_suplemen->suplemen_porsi_b }} pcs / gram</td></td>
            </tr>
        </table>
    </div>



    <div class="section">
        <h3>B. Rincian Bahan Baku:</h3>
        
        <p style="margin-top:10px;">1. Beras</p>
        <table class="bordered"  style="margin-left: 20px;width:100%">
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 15%">Bahan baku</th>
                <!--th>Kebutuhan/pax</!--th-->
                <th style="width: 10%">Total kebutuhan</th>
                <td style="width: 10%">Jumlah Kemasan</td>
                <td style="width: 60%">Keterangan</td>
            </tr>
            @foreach($data_beras as $row)
            <tr>
                <td>{{ $row->nomor_urut }}</td>
                <td>{{ $row->bahan }}</td>
                
                <td>{{ number_format($row->jumlah_rincian_menu_harian, 0, ',', '.') }} {{ $row->satuan }}</td>
                
                <td>{{ $row->jumlah_box }}</td>
                <td>{{ $row->keterangan }}</td>
            
            </tr>
            @endforeach
            
        </table>

        <p  style="margin-top:10px;">2. Sayur</p>
        <table class="bordered"  style="margin-left: 20px;width:100%">
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 15%">Bahan baku</th>
                <!--th>Kebutuhan/pax</!--th-->
                <th style="width: 10%">Total kebutuhan</th>
                <td style="width: 10%">Jumlah Box</td>
                <td style="width: 60%">Keterangan</td>
            </tr>
            @foreach($data_sayur as $row)
            <tr>
                <td>{{ $row->nomor_urut }}</td>
                <td>{{ $row->bahan }}</td>
                
                <td>{{ number_format($row->jumlah_rincian_menu_harian, 2, ',', '.') }} {{ $row->satuan }}</td>
                
                <td>{{ $row->jumlah_box }} Box ( @ {{ number_format(($row->jumlah_rincian_menu_harian/$row->jumlah_box), 2, ',', '.') }} {{ $row->satuan }} +-)</td>
                <td>{{ $row->keterangan }}</td>
            </tr>
            @endforeach
        </table>

        <p  style="margin-top:10px;">3. Lauk</p>
        <table class="bordered"  style="margin-left: 20px;width:100%">
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 15%">Bahan baku</th>
                <!--th>Kebutuhan/pax</!--th-->
                <th style="width: 10%">Total kebutuhan</th>
                <td style="width: 10%">Jumlah Box</td>
                <td style="width: 60%">Keterangan</td>
            </tr>
            @foreach($data_lauk as $row)
            <tr>
                <td>{{ $row->nomor_urut }}</td>
                <td>{{ $row->bahan }}</td>
                @if($row->satuan == 'Potong' )
                <td>{{ number_format($row->jumlah_rincian_menu_harian, 2, ',', '.') }} {{ $row->satuan }} / {{ number_format(($row->jumlah_rincian_menu_harian *$rumus_protein->protein_porsi_b /1000), 2, ',', '.') }} Kg</td>
                @else
                <td>{{ number_format($row->jumlah_rincian_menu_harian, 2, ',', '.') }} {{ $row->satuan }}</td>
                @endif
                <td>{{ $row->jumlah_box }} Box ( @ {{ number_format(($row->jumlah_rincian_menu_harian/$row->jumlah_box), 0, ',', '.') }} {{ $row->satuan }} +-)</td>
                <td>{{ $row->keterangan }}</td>
            </tr>
            @endforeach
        </table>

        <p style="margin-top:10px;">4. buah</p>
        <table class="bordered"  style="margin-left: 20px;width:100%">
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 15%">Bahan baku</th>
                <!--th>Kebutuhan/pax</!--th-->
                <th style="width: 10%">Total kebutuhan</th>
                <td style="width: 10%">Jumlah Box</td>
                <td style="width: 60%">Keterangan</td>
            </tr>
            @foreach($data_buah as $row)
            <tr>
                <td>{{ $row->nomor_urut }}</td>
                <td>{{ $row->bahan }}</td>
                
                <td>{{ number_format($row->jumlah_rincian_menu_harian, 0, ',', '.') }} {{ $row->satuan }}</td>
                
                <td>{{ $row->jumlah_box }} Box ( @ {{ number_format(($row->jumlah_rincian_menu_harian/$row->jumlah_box), 0, ',', '.') }} {{ $row->satuan }} +-)</td>
                <td>{{ $row->keterangan }}</td>
            </tr>
            @endforeach
        </table>

        <p style="margin-top:10px;">5. Suplemen</p>
        <table class="bordered"  style="margin-left: 20px;width:100%">
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 15%">Bahan baku</th>
                <!--th>Kebutuhan/pax</!--th-->
                <th style="width: 10%">Total kebutuhan</th>
                <td style="width: 10%">Jumlah Box</td>
                <td style="width: 60%">Keterangan</td>
            </tr>
            @foreach($data_suplemen as $row)
            <tr>
                <td>{{ $row->nomor_urut }}</td>
                <td>{{ $row->bahan }}</td>
                
                <td>{{ rtrim(rtrim(number_format($row->jumlah_rincian_menu_harian , 2, ',', '.'), '0'), ',') }} {{ $row->satuan }}</td>
                
                <td>{{ $row->jumlah_box }} ( @ {{ number_format(($row->jumlah_rincian_menu_harian/$row->jumlah_box), 0, ',', '.') }} {{ $row->satuan }} +-)</td>
                <td>{{ $row->keterangan }}</td>
            </tr>
            @endforeach
        </table>

        <p style="margin-top:10px;">6. Bumbu</p>
        <table class="bordered"  style="margin-left: 20px;width:100%">
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 15%">Bahan baku</th>
                <!--th>Kebutuhan/pax</!--th-->
                <th style="width: 10%">Total kebutuhan</th>
                <td style="width: 10%">Jumlah Box</td>
                <td style="width: 60%">Keterangan</td>
            </tr>
            @foreach($data_bumbu as $row)
            <tr>
                <td>{{ $row->nomor_urut }}</td>
                <td>{{ $row->resep }}||{{ $row->bahan }}</td>
                @if($row->satuan == 'gram' || $row->satuan == 'Gram')
                <td>{{ rtrim(rtrim(number_format($row->jumlah_rincian_menu_harian / 1000, 3, ',', '.'), '0'), ',') }} Kg</td>

                @elseif( $row->satuan == 'ml')
                <td>{{ rtrim(rtrim(number_format($row->jumlah_rincian_menu_harian / 1000, 2, ',', '.'), '0'), ',') }} Liter</td>
                @else
                <td>{{ number_format($row->jumlah_rincian_menu_harian, 0, ',', '.') }} {{ $row->satuan }}</td>
                @endif
                <td>{{ $row->jumlah_box }}</td>
                <td>{{ $row->keterangan }}</td>
            </tr>
            @endforeach
        </table>
    </div>

    <div class="section page-break" >
        <h3>C. Daftar Sekolah : </h3>
        <table class="bordered"  style="margin-left: 20px;">
            <tr>
                <th>No</th>
                <th>Nama Sekolah</th>
                <th>Jumlah A</th>
                <th>Jumlah B</th>
                <th>Jumlah Total</th>
            </tr>
            @foreach($data_sekolah as $row)
            <tr>
                <td>{{ $row->nomor_urut }}</td>
                <td>{{ $row->nama_sekolah }}</td>
               <td>{{ $row->jumlah_penerima_a }} anak</td>
               <td>{{ $row->jumlah_penerima_b }} anak</td>
               <td>{{ $row->jumlah_penerima_total }} anak</td>
               
                
            </tr>
            @endforeach
            <tr>
                <td colspan="2">Total</td>
                <td>{{ $total_a }} anak</td>
                <td>{{ $total_b }} anak</td>
                <td>{{ $totalPorsi }} anak</td>
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
        <table>
            <tr>
                <td style="padding: 5px;">Ahli Gizi</td>
                <td></td>
                <td style="padding: 5px;">Ahli Akuntansi</td>
                <td></td>
                <td style="padding: 5px;">Asiten Lapangan</td>
                <td></td>
                <td style="padding: 5px;">Kepala Dapur</td>
            </tr>
        </table>
    </div>

</div>

</body>
</html>
