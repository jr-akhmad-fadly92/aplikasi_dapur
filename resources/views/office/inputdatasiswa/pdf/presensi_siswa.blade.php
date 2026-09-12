<!DOCTYPE html>
<html>

<head>
    <title>Daftar Siswa - {{ $sekolah->nama_sekolah }}</title>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Times New Roman, serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px;
            padding-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }

        .header h2 {
            margin: 5px 0;
            font-size: 16px;
        }

        .info-sekolah {
            margin-bottom: 15px;
            padding: 8px;
            border-radius: 5px;
        }

        .info-sekolah table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-sekolah td {
            padding: 3px 0;
            vertical-align: top;
        }

        .info-sekolah td:first-child {
            width: 150px;
            font-weight: bold;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .data-table th,
        .data-table td {
            border: 1px solid;
            padding: 2px 3px;
            text-align: left;
            line-height: 1.2;
        }

        .data-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }

        .data-table td.text-center {
            text-align: center;
        }

        .data-table td.signature {
            text-align: center;
            height: 20px;
        }

        .footer {
            margin-top: 20px;
        }

        .footer table {
            width: 100%;
            border-collapse: collapse;
        }

        .footer td {
            padding: 5px 15px;
            text-align: center;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: ;
            font-style: italic;
        }

        .summary {
            margin-top: 15px;
            padding: 8px;
            border-radius: 5px;
        }

        .summary table {
            width: 100%;
            border-collapse: collapse;
        }

        .summary td {
            padding: 3px 0;
            vertical-align: top;
        }

        .summary td:first-child {
            font-weight: bold;
            width: 150px;
        }

        @page {
            margin: 1cm;
        }
         .print-date {
        position: fixed;
        bottom: 10px;
        right: 20px;
        font-size: 12px;
        color: #555;
    }
    </style>
</head>

<body>
    

    @if($sekolah)
        <div class="info-sekolah">
            <table style="width: 100%;">
                <tr>
                <td style="width: 25%;"></td>
                <td style="width: 50%;text-align:center;">
                    <h3 class="bold center"  style="text-align:center;">SURAT JALAN
                    <br>PROGRAM MAKAN BERGIZI GRATIS
                    <br>SPPG YAYASAN BINA BANGSA PURWAKARTA
                    </h3>
                </td>
                <td style="text-align: right;width: 25%;">
                </td>
                </tr>
            </table>
            <table>
               
                <tr>
                    <td>NPSN</td>
                    <td>: {{ $sekolah->npsn ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Nama Sekolah</td>
                    <td>: {{ $sekolah->nama_sekolah }}</td>
                </tr>
                
                <tr>
                    <td>Kelas </td>
                    <td>: {{ $kelas }}</td>
                </tr>
                <tr>
                    <td>Alamat</td>
                    <td>: {{ $sekolah->alamat_sekolah ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Periode</td>
                    <td>: dari ..................................... s.d .....................................</td>
                </tr>
            </table>
        </div>
    @endif

    @if($siswaData->count() > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;" rowspan="2">No</th>
                    <th style="width: 10%;" rowspan="2">NISN</th>
                    <th style="width: 25%;" rowspan="2">Nama</th>
                    <th style="width: 15%;" rowspan="2">Jenis Kelamin</th>
                    @if(!$sekolah)
                        <th style="width: 20%;" rowspan="2">Sekolah</th>
                    @endif
                    <th style="width: 30%;" colspan="5">Tanggal</th>
                </tr>
                <tr>
                    <td style="width: 6%;">...</td>
                    <td style="width: 6%;">...</td>
                    <td style="width: 6%;">...</td>
                    <td style="width: 6%;">...</td>
                    <td style="width: 6%;">...</td>
                </tr>
            </thead>
            <tbody>
                @foreach($siswaData as $index => $siswa)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $siswa->nisn }}</td>
                        <td>{{ $siswa->nama }}</td>
                        <td class="text-center">
                            @if($siswa->jenis_kelamin == 'L')
                                Laki-laki
                            @elseif($siswa->jenis_kelamin == 'P')
                                Perempuan
                            @else
                                -
                            @endif
                        </td>
                        @if(!$sekolah)
                            <td>{{ $siswa->sekolah->nama_sekolah ?? '-' }}</td>
                        @endif
                        <td class="signature"></td>
                        <td class="signature"></td>
                        <td class="signature"></td>
                        <td class="signature"></td>
                        <td class="signature"></td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary">
            <table>
                <tr>
                    <td>Total Siswa</td>
                    <td>: {{ $siswaData->count() }} orang</td>
                </tr>
                <tr>
                    <td>Laki-laki</td>
                    <td>: {{ $siswaData->where('jenis_kelamin', 'L')->count() }} orang</td>
                </tr>
                <tr>
                    <td>Perempuan</td>
                    <td>: {{ $siswaData->where('jenis_kelamin', 'P')->count() }} orang</td>
                </tr>
            </table>
        </div>

        <div class="footer">
            <table style="width: 100%;">
                <tr>
                    <td style="width: 40%; text-align: center;">
                        <div style="margin-top: 15px;">
                            <div style="border-bottom: 1px solid #000; margin-bottom: 8px; padding-bottom: 4px;">
                                Kepala Sekolah
                            </div>
                            <div style="height: 50px;"></div>
                            <div style="border-bottom: 1px solid #000; margin-bottom: 4px;">
                            </div>
                            <div style="text-align: center;">
                                @if($sekolah && $sekolah->kepala_sekolah)
                                    {{ $sekolah->kepala_sekolah }}
                                @else
                                    (Nama Kepala Sekolah)
                                @endif
                            </div>
                        </div>
                    </td>
                    <td style="width: 20%;"></td>
                    <td style="width: 40%; text-align: center;">
                        <div style="margin-top: 15px;">
                            <div style="border-bottom: 1px solid #000; margin-bottom: 8px; padding-bottom: 4px;">
                                Petugas
                            </div>
                            <div style="height: 50px;"></div>
                            <div style="border-bottom: 1px solid #000; margin-bottom: 4px;">
                            </div>
                            <div style="text-align: center;">
                                (Nama Petugas)
                            </div>
                        </div>
                    </td>
                </tr>
               
            </table>
        </div>

    @else
        <div class="no-data">
            <h3>Tidak ada data siswa untuk ditampilkan</h3>
            <p>Silakan periksa filter yang digunakan atau tambahkan data siswa terlebih dahulu.</p>
        </div>
    @endif
    <div class="print-date">
        Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}
    </div>
</body>

</html>