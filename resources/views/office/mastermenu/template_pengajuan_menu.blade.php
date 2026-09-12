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
            <td><strong> Penerima MBG </strong></td>
            <td>: {{ $totalPorsi }} Pack</td>
            <td><strong>Periode Pengajuan</strong>  </td>
            <td>: {{ $tanggal_kirim }}</td>
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
        <h3>A. Menu yang diajukan:</h3>
        <table style="border-collapse: collapse; width: 100%;">
            <tr>
                <td>1. Karbohidrat</td>
                <td>: {{ $karbohidrat->nama_resep }} || A : {{ $rumus_karbo->karbo_porsi_a }} gram || B : {{ $rumus_karbo->karbo_porsi_b }} gram</td>
            </tr>
            <tr>
                <td>2. Sayur</td>
                <td>: {{ $sayur->nama_resep }} || A : {{ $rumus_protein->protein_porsi_a }} gram || B : {{ $rumus_protein->protein_porsi_b }} gram</td>
            </tr>
            <tr>
                <td>3. Protein</td>
                <td>: {{ $protein->nama_resep }} || A : {{ $rumus_sayur->sayur_porsi_a }} gram || B : {{ $rumus_sayur->sayur_porsi_b }} gram</td>
            </tr>
            <tr>
                <td>4. Buah</td>
                <td>: {{ $buah->nama_resep }} || A : {{ $rumus_buah->buah_porsi_a }} pcs || B : {{ $rumus_buah->buah_porsi_a }} pcs</td>
            </tr>
            <tr>
                @php
                    $susuLabel = '1 pcs'; // default
                    if ($susu->nama_resep === 'telur puyuh**') {
                        $susuLabel = '3 biji';
                    } elseif ($susu->nama_resep === 'tempe**') {
                        $susuLabel = '50 gram';
                    }
                @endphp
                <td>5. Suplemen</td>
                <td>: {{ $susu->nama_resep }} || {{ $susuLabel }} || || A : {{ $rumus_suplemen->suplemen_porsi_a }} pcs / gram || B : {{ $rumus_suplemen->suplemen_porsi_b }} pcs / gram</td></td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h3>B. Rincian Bahan Baku:</h3>
        
        <p style="margin-top:10px;">1. {{ $karbohidrat->nama_resep }}</p>
        <table class="bordered"  style="margin-left: 20px;">
            <tr>
                <th>No</th>
                <th>Bahan baku</th>
                <!--th>Kebutuhan/pax</!--th-->
                <th>Jml kebutuhan</th>
                <th>Total kebutuhan</th>
            </tr>
            @foreach($rincian_karbohidrat as $row)
            <tr>
                <td>{{ $row->nomor_urut }}</td>
                <td>{{ $row->bahan }}</td>
                @if(($row->jumlah_menu_bahan /$karbohidrat->porsi_resep) < 1 )
                
                @else 
                
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
                <!--th>Kebutuhan/pax</!--th-->
                <th>Jml kebutuhan</th>
                <th>Total kebutuhan</th>
            </tr>
            @foreach($rincian_sayur as $row)
            <tr>
                <td>{{ $row->nomor_urut }}</td>
                <td>{{ $row->bahan }}</td>
                @if(($row->jumlah_menu_bahan /$sayur->porsi_resep) < 1 )
                
                @else 
               
                
                @endif
                <td>{{ number_format($totalPorsi, 0, ',', '.') }} pack</td>
                <td>{{ number_format($row->jumlah_rincian_menu_harian, 0, ',', '.') }} {{ $row->satuan }}</td>
            </tr>
            @endforeach
        </table>

        <p class="page-break" style="margin-top:10px;">3. {{ $protein->nama_resep }}</p>
        <table class="bordered"  style="margin-left: 20px;">
            <tr>
                <th>No</th>
                <th>Bahan baku</th>
                <!--th>Kebutuhan/pax</!--th-->
                <th>Jml kebutuhan</th>
                <th>Total kebutuhan</th>
            </tr>
            @foreach($rincian_protein as $row)
            <tr>
                <td>{{ $row->nomor_urut }}</td>
                <td>{{ $row->bahan }}</td>
                @if(($row->jumlah_menu_bahan /$protein->porsi_resep) < 1 )
                
                @else 
               
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
                <!--th>Kebutuhan/pax</!--th-->
                <th>Jml kebutuhan</th>
                <th>Total kebutuhan</th>
            </tr>
            @foreach($rincian_buah as $row)
            <tr>
                <td>{{ $row->nomor_urut }}</td>
                <td>{{ $row->bahan }}</td>
                @if(($row->jumlah_menu_bahan /$buah->porsi_resep) < 1 )
                <!--td>---</!--td-->
                
                @else 
                <!--td>{{ number_format(($row->jumlah_menu_bahan /$buah->porsi_resep), 0, ',', '.') }} {{ $row->satuan }}</!--td-->
                
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
                <!--th>Kebutuhan/pax</!--th-->
                <th>Jml kebutuhan</th>
                <th>Total kebutuhan</th>
            </tr>
            @foreach($rincian_susu as $row)
            <tr>
                <td>{{ $row->nomor_urut }}</td>
                <td>{{ $row->bahan }}</td>
                @if(($row->jumlah_menu_bahan /$susu->porsi_resep) < 1 )
                <!--td>---</!--td-->
                
                @else 
                <!--td>{{ number_format(($row->jumlah_menu_bahan / $susu->porsi_resep), 0, ',', '.') }} {{ $row->satuan }}</!--td-->
               
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
                <!--th>Kebutuhan/pax</!--th-->
                <th>Jml kebutuhan</th>
                <th>Total kebutuhan</th>
            </tr>
            @foreach($rincian_tambahan as $row)
            <tr>
                <td>{{ $row->nomor_urut }}</td>
                <td>{{ $row->bahan }}</td>
                <!--td>-</!--td-->
                <td>{{ number_format($totalPorsi, 0, ',', '.') }} pack</td>
                <td>{{ number_format($row->jumlah_rincian_menu_harian, 0, ',', '.') }} {{ $row->satuan }}</td>
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
