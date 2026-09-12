<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PURCHASE ORDER</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin: 20px;
        }

        .container {
            width: 100%;
            margin: auto;
        }

        h2 {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 2px;
        }

        .border {
            border: 1px solid black;
        }

        th,
        td {
            border: 1px solid black;
            text-align: left;
            padding: 5px;
        }

        .no-border td {
            border: none;
            padding: 2px;
        }
    </style>
</head>

<body>

    <div class="container">

        <!-- Header dengan Logo -->
        <table class="no-border" style="margin-bottom: 20px;">
            <tr>
                <td style="text-align: center;width: 30%;">
                    <img src="{{ public_path('image/logo.png') }}" alt="Logo" style="width: 100px; height: auto;">
                </td>
                <td style="text-align: right;width: 70%; font-weight: bold; font-size: 24px; padding-top: 10px;">
                    PURCHASE ORDER
                </td>
            </tr>
          
            <tr>
                <td style="text-align: center; font-weight: bold; font-size: 16px;">
                    {{ $dapur->nama_dapur }}
                </td>
                <td></td>
            </tr>
            <tr>
                <td style="text-align: center;">
                    {{ $dapur->kecamatan }} - {{ $dapur->kota }}
                </td>
            </tr>
        </table>

        <!-- Info PO -->
        <!--table class="no-border" style="margin-bottom: 10px;">
            <tr>
                <td style="width: 70%;">
                    <strong>Yayasan Bina Bangsa</strong><br>
                    {{ $dapur->alamat_dapur }}<br>
                    Telp : {{ $dapur->no_telp }}<br>
                    Email: {{ $dapur->email }}
                </td>
                <td style="width: 30%; vertical-align: top;">
                    <strong>Tanggal Pemesanan :</strong> {{ \Carbon\Carbon::parse($po->tanggal_po)->format('d-m-Y') }}<br>
                    <strong>Tanggal Pengiriman :</strong> {{ \Carbon\Carbon::parse($po->tanggal_pengajuan)->format('d M Y') }}<br>
                    <strong>NO.PO :</strong> {{ $po->nomor_po }}
                </td>
            </tr>
        </!--table--->

        <table class="no-border" style="margin-bottom: 10px;width: 100%; ">
            <tr>
                <td style="width: 55%; vertical-align: top;tect-align: right;">
                </td>
                <td style="width: 45%; vertical-align: top;tect-align: right; line-height: 2.2;">
                    <strong>NO.PO :</strong> {{ $po->nomor_po }}<br>
                    <strong>Tanggal Pemesanan :</strong>  {{ strtolower(\Carbon\Carbon::parse($po->tanggal_po)->locale('id')->translatedFormat('l, d F Y')) }}<br>
                    <strong>Tanggal Pengiriman :</strong> {{ strtolower(\Carbon\Carbon::parse($po->tanggal_pengajuan)->locale('id')->translatedFormat('l, d F Y')) }}<br>
                    
                </td>
            </tr>
        </table>

        


        <!-- Tabel 1 -->
        <table style="margin-top:20px;">
            <tr>
                <th style="text-align: center;">No</th>
                <th style="text-align: center;">Nama Pesanan</th>
                <th style="text-align: center;">Qty</th>
                <th style="text-align: center;">Satuan</th>
                <th style="text-align: center;">Harga Satuan</th>
                <th style="text-align: center;">Harga Total</th>
            </tr>
            @php
            $total_semua = 0;
            @endphp
            @foreach($rincian_po as $row)
            <tr>
                <td style="text-align: center;">{{ $row->nomor_urut }}</td>
                <td>{{ $row->bahan }}</td>
                <td style="text-align: right;">{{ number_format($row->jumlah_bahan, 0, ',', '.') }} </td>
                <td style="text-align: right;"> {{ $row->satuan }}</td>
                <td style="text-align: right;">{{ number_format(($row->jumlah_po/$row->jumlah_bahan), 0, ',', '.') }} </td>
                <td style="text-align: right;">{{ number_format($row->jumlah_po, 0, ',', '.') }} </td>
            </tr>
            @php
            $total_semua = $total_semua + $row->jumlah_po;
            @endphp
            @endforeach
            <tr>
                <th style="text-align: center;" colspan="5">Total</th>
                
                <th style="text-align: right;" >Rp.{{ number_format($total_semua, 0, ',', '.') }}</th>
            </tr>
        </table>
        <div class="footer">
            <table style="font-size: 13px;margin-top:10px;" class="no-border">
                <tr>
                    <td style="text-align: center;"></td>

                    <td style="text-align: center;"></td>
                </tr>
                <!--tr>
                    <td style="text-align: center;">Asisten Lapangan</td>
                    <td style="text-align: center;">Ahli Gizi</td>
                    <td style="text-align: center;">Ahli Akuntan</td>

                    <td style="text-align: center;">Kepala Dapur</td>
                </!--tr-->
                <tr>
                    <td style="text-align: center;">Ahli Akuntan</td>

                    <td style="text-align: center;">Kepala Dapur</td>
                </tr>
                <tr>
                    <td style="height: 30px"></td>
                    <td style="text-align: center;"></td>
                </tr>
                <!--tr>
                    <td style="text-align: center;">{{ $dapur->admin_dapur }}</td>
                    <td style="text-align: center;">{{ $dapur->ahli_gizi }}</td>
                    <td style="text-align: center;">{{ $dapur->ahli_akuntan }}</td>
                    <td style="text-align: center;">{{ $dapur->kepala_dapur }}</td>
                </!--tr-->
                <tr>
                    <td style="text-align: center;">{{ $dapur->ahli_akuntan }}</td>
                    <td style="text-align: center;">{{ $dapur->kepala_dapur }}</td>
                </tr>
            </table>
        </div>

    </div>

</body>

</html>