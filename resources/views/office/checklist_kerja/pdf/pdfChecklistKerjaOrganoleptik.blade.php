<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            margin: 18px;
        }
        .title {
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td, th {
            border: 1px solid #000;
            padding: 4px;
            vertical-align: middle;
        }
        .no-border td {
            border: none;
            padding: 2px 4px;
        }
        .center { text-align: center; }
        .right { text-align: right; }
        .bold { font-weight: bold; }
        .small { font-size: 10px; }
        .section-gap { height: 8px; }
        .signature td { border: none; padding-top: 25px; }
    </style>
</head>
<body>
    @php
        $tanggalPelayanan = !empty($menu->tanggal_kirim) ? \Carbon\Carbon::parse($menu->tanggal_kirim)->format('d F Y') : '-';
        $dayName = !empty($menu->tanggal_kirim) ? \Carbon\Carbon::parse($menu->tanggal_kirim)->locale('id')->isoFormat('dddd') : '-';
    @endphp

    <div class="title">CHECKLIST UJI ORGANOLEPTIK</div>

    <table class="no-border" style="margin-bottom:10px;">
        <tr>
            <td style="width:22%;">Nama Pemeriksa</td>
            <td style="width:2%;">:</td>
            <td></td>
        </tr>
        <tr>
            <td>Tempat Pemeriksaan</td>
            <td>:</td>
            <td>SPPG/Satuan Pendidikan/Posyandu/Lainnya</td>
        </tr>
        <tr>
            <td>Nama Tempat Pemeriksa</td>
            <td>:</td>
            <td>{{ $dapur->nama_dapur ?? '-' }}</td>
        </tr>
        <tr>
            <td>Tanggal Pemeriksaan</td>
            <td>:</td>
            <td>{{ $tanggalPelayanan }}</td>
        </tr>
        <tr>
            <td>Waktu Pemeriksaan</td>
            <td>:</td>
            <td></td>
        </tr>
    </table>

    <table>
        <tr>
            <th rowspan="2" style="width:5%;">No</th>
            <th rowspan="2" style="width:20%;">Nama Makanan</th>
            <th colspan="4">Hasil Pemeriksaan<br>(diberi skor 1-5)</th>
            <th rowspan="2" style="width:28%;">Sebelum Pengantaran/<br>Saat Tiba di Lokasi/<br>Sebelum dikonsumsi</th>
            <th rowspan="2" style="width:6%;">Ket</th>
        </tr>
        <tr>
            <th style="width:8%;">Rasa</th>
            <th style="width:8%;">Warna</th>
            <th style="width:8%;">Aroma</th>
            <th style="width:8%;">Tekstur</th>
        </tr>

        @forelse($items as $index => $item)
            <tr>
                <td class="center">{{ $index + 1 }}</td>
                <td>{{ $item['nama_makanan'] }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="center">Tidak ada menu</td>
            </tr>
        @endforelse

        <tr>
            <td colspan="5" class="center bold">Kesimpulan Menu MBG</td>
            <td colspan="3" class="center bold">Aman/Tidak aman dikonsumsi</td>
        </tr>
    </table>

    <div class="section-gap"></div>

    <table class="no-border">
        <tr>
            <td class="bold">Catatan:</td>
        </tr>
        <tr>
            <td>1. Silakan dipilih salah satu untuk pengisian hasil pelaksanaan uji organoleptik (Sebelum Pengantaran/Saat Tiba di Lokasi/Sebelum dikonsumsi).</td>
        </tr>
        <tr>
            <td>2. Form dibawa oleh sopir untuk diberikan ke penanggung jawab program MBG dan diambil kembali bersama dengan ompreng MBG, dan dibawa kembali ke SPPG.</td>
        </tr>
    </table>

    <div class="section-gap"></div>

    <table class="no-border">
        <tr>
            <td style="width:50%;">Skor :</td>
            <td></td>
        </tr>
        <tr>
            <td>Sangat baik : 5</td>
            <td></td>
        </tr>
        <tr>
            <td>Baik : 4</td>
            <td></td>
        </tr>
        <tr>
            <td>Cukup : 3</td>
            <td></td>
        </tr>
        <tr>
            <td>Kurang : 2</td>
            <td></td>
        </tr>
        <tr>
            <td>Tidak baik : 1</td>
            <td></td>
        </tr>
    </table>

    <div style="margin-top: 55px; text-align: center;">
        <div style="margin-bottom: 35px; text-align: right;">................, ...... {{ now()->format('Y') }}</div>
        <table class="no-border signature" style="width:100%; margin: 0 auto;">
            <tr>
                <td class="center" style="width:50%;">Mengetahui,</td>
                <td class="center" style="width:50%;">Pemeriksa,</td>
            </tr>
            <tr>
                <td class="center" style="height:55px;"></td>
                <td class="center"></td>
            </tr>
            <tr>
                <td class="center">............................</td>
                <td class="center">............................</td>
            </tr>
        </table>
        <div style="margin-top: 18px; font-weight: bold; text-align: center;">Kepala SPPG</div>
    </div>
</body>
</html>