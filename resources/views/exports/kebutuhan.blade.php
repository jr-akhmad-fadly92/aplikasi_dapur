<table>
    <tr>
        <td>Hari / tanggal</td>
        <td>: Senin, 14 April 2025</td>
    </tr>
    <tr>
        <td>Menu</td>
        <td>: {{ $daftar_menu }}</td>
    </tr>
    <tr>
        <td>Jumlah pax</td>
        <td>: {{ $jumlah_porsi }} pack</td>
    </tr>
</table>

<br>

<table border="1" style="border-collapse: collapse; width: 100%;">
    <thead>
        <tr>
            <th style="border: 1px solid black; padding: 5px;">No</th>
            <th style="border: 1px solid black; padding: 5px;">Nama Masakan</th>
            <th style="border: 1px solid black; padding: 5px;">Qty</th>
            <th style="border: 1px solid black; padding: 5px;">Satuan</th>
            <th colspan="4" style="border: 1px solid black; padding: 5px;">Hasil Produksi (Matang)</th>
            <th colspan="3" style="border: 1px solid black; padding: 5px;">Realisasi Pax</th>
        </tr>
        <tr>
            <th style="border: 1px solid black; padding: 5px;"></th><th style="border: 1px solid black; padding: 5px;"></th><th style="border: 1px solid black; padding: 5px;"></th><th style="border: 1px solid black; padding: 5px;"></th>
            <th style="border: 1px solid black; padding: 5px;">Qty</th><th style="border: 1px solid black; padding: 5px;">Satuan</th><th style="border: 1px solid black; padding: 5px;">Jumlah Gastronom</th><th style="border: 1px solid black; padding: 5px;">Keterangan</th>
            <th style="border: 1px solid black; padding: 5px;">Qty</th><th style="border: 1px solid black; padding: 5px;">Satuan</th><th style="border: 1px solid black; padding: 5px;">Keterangan</th>
        </tr>
    </thead>
    <tbody>
        <!--  nasi  -->
        <tr>
            <td style="border: 1px solid black; padding: 5px;">1</td>
            <td style="border: 1px solid black; padding: 5px;">{{  $datamenu->nama_karbohidrat }}</td>
            <td style="border: 1px solid black; padding: 5px;">{{ ceil($hasil_pack_karbo)  }}</td>
            <td style="border: 1px solid black; padding: 5px;">pack</td>
            <td style="border: 1px solid black; padding: 5px;">{{ $jumlah_hasil_karbo  }}</td>
            <td style="border: 1px solid black; padding: 5px;">{{ $satuan_pack_karbo }}</td>
            <td style="border: 1px solid black; padding: 5px;">{{ $data_gastronom->jumlah_gastronom_karbo }}</td>
            <td style="border: 1px solid black; padding: 5px;">180 gram / pack | {{ $selisihMenit_karbo }} menit</td>
            <td style="border: 1px solid black; padding: 5px;">{{$jumlah_ompreng}}</td>
            <td style="border: 1px solid black; padding: 5px;">pack</td>
            <td style="border: 1px solid black; padding: 5px;">sisa {{ ceil($hasil_pack_karbo) - $jumlah_ompreng }} pack</td>
        </tr>
        <!--  lauk  -->
        <tr>
            <td style="border: 1px solid black; padding: 5px;">2</td>
            <td style="border: 1px solid black; padding: 5px;">{{  $datamenu->nama_protein }}</td>
            <td style="border: 1px solid black; padding: 5px;">{{ ceil($hasil_pack_protein)  }}</td>
            <td style="border: 1px solid black; padding: 5px;">pack</td>
            <td style="border: 1px solid black; padding: 5px;">{{ $jumlah_hasil_protein  }}</td>
            <td style="border: 1px solid black; padding: 5px;">{{ $satuan_pack_protein }}</td>
            <td style="border: 1px solid black; padding: 5px;">{{ $data_gastronom->jumlah_gastronom_protein }}</td>
            <td style="border: 1px solid black; padding: 5px;">100 gram / pack | {{ $selisihMenit_protein }} menit</td>
            <td style="border: 1px solid black; padding: 5px;">{{$jumlah_ompreng}}</td>
            <td style="border: 1px solid black; padding: 5px;">pack</td>
            <td style="border: 1px solid black; padding: 5px;">sisa {{ ceil($hasil_pack_protein) - $jumlah_ompreng }} pack</td>
        </tr>
        <!--  sayur  -->
        <tr>
            <td style="border: 1px solid black; padding: 5px;">3</td>
            <td style="border: 1px solid black; padding: 5px;">{{  $datamenu->nama_sayur }}</td>
            <td style="border: 1px solid black; padding: 5px;">{{ ceil($hasil_pack_sayur)  }}</td>
            <td style="border: 1px solid black; padding: 5px;">pack</td>
            <td style="border: 1px solid black; padding: 5px;">{{ $jumlah_hasil_sayur  }}</td>
            <td style="border: 1px solid black; padding: 5px;">{{ $satuan_pack_sayur }}</td>
            <td style="border: 1px solid black; padding: 5px;">{{ $data_gastronom->jumlah_gastronom_sayur }}</td>
            <td style="border: 1px solid black; padding: 5px;">100 gram / pack | {{ $selisihMenit_sayur }} menit</td>
            <td style="border: 1px solid black; padding: 5px;">{{$jumlah_ompreng}}</td>
            <td style="border: 1px solid black; padding: 5px;">pack</td>
            <td style="border: 1px solid black; padding: 5px;">sisa {{ ceil($hasil_pack_sayur) - $jumlah_ompreng }} pack</td>
        </tr>
        <!--  buah  -->
        <tr>
            <td style="border: 1px solid black; padding: 5px;">4</td>
            <td style="border: 1px solid black; padding: 5px;">{{  $datamenu->nama_buah }}</td>
            <td style="border: 1px solid black; padding: 5px;">{{ ceil($hasil_pack_buah)  }}</td>
            <td style="border: 1px solid black; padding: 5px;">pack</td>
            <td style="border: 1px solid black; padding: 5px;">{{ $jumlah_hasil_buah  }}</td>
            <td style="border: 1px solid black; padding: 5px;">{{ $satuan_pack_buah }}</td>
            <td style="border: 1px solid black; padding: 5px;">{{ $data_gastronom->jumlah_gastronom_buah }}</td>
            <td style="border: 1px solid black; padding: 5px;">100 gram / pack | {{ $selisihMenit_buah }} menit </td>
            <td style="border: 1px solid black; padding: 5px;">{{$jumlah_ompreng}}</td>
            <td style="border: 1px solid black; padding: 5px;">pack</td>
            <td style="border: 1px solid black; padding: 5px;">sisa {{ ceil($hasil_pack_buah) - $jumlah_ompreng }} pack</td>
        </tr>

        <!--  susu  -->
        <tr>
            <td style="border: 1px solid black; padding: 5px;">5</td>
            <td style="border: 1px solid black; padding: 5px;">{{  $datamenu->nama_susu }}</td>
            <td style="border: 1px solid black; padding: 5px;">{{ ceil($hasil_pack_susu)  }}</td>
            <td style="border: 1px solid black; padding: 5px;">pack</td>
            <td style="border: 1px solid black; padding: 5px;">{{ $jumlah_hasil_susu  }}</td>
            <td style="border: 1px solid black; padding: 5px;">{{ $satuan_pack_susu }}</td>
            <td style="border: 1px solid black; padding: 5px;">{{ $data_gastronom->jumlah_gastronom_susu }}</td>
            <td style="border: 1px solid black; padding: 5px;"> 1 pcs / 50 gram / 30 gram | {{ $selisihMenit_susu }} menit</td>
            <td style="border: 1px solid black; padding: 5px;">{{$jumlah_ompreng}}</td>
            <td style="border: 1px solid black; padding: 5px;">pack</td>
            <td style="border: 1px solid black; padding: 5px;">sisa {{ ceil($hasil_pack_susu) - $jumlah_ompreng }} pack</td>
        </tr>

    </tbody>
</table>
    