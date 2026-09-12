<table style="width:100%; border-collapse:collapse; font-family: Arial, sans-serif; font-size: 12px;">
    <tr>
        <td colspan="8" style="text-align:center; font-weight:bold; font-size:16px; padding:4px;">
            CHECKLIST UJI ORGANOLEPTIK
        </td>
    </tr>
    <tr>
        <td colspan="8" style="padding:3px 0;"></td>
    </tr>
    <tr>
        <td style="width:18%;">Nama SPPG</td>
        <td colspan="7">: {{ $dapur->nama_dapur ?? '-' }}</td>
    </tr>
    <tr>
        <td>Realisasi Menu ID</td>
        <td colspan="7">: {{ $menu->id ?? '-' }}</td>
    </tr>
    <tr>
        <td>Menu</td>
        <td colspan="7">: {{ $menu->menu ?? '-' }}</td>
    </tr>
    <tr>
        <td>Tanggal Pelayanan</td>
        <td colspan="7">: {{ !empty($menu->tanggal_kirim) ? \Carbon\Carbon::parse($menu->tanggal_kirim)->format('d-m-Y') : '-' }}</td>
    </tr>
    <tr>
        <td>Jumlah Realisasi</td>
        <td colspan="7">: {{ number_format($realisasiPax, 0, ',', '.') }} pax</td>
    </tr>
    <tr>
        <td>Nama Pemeriksa</td>
        <td colspan="7">: </td>
    </tr>
    <tr>
        <td>Tempat Pemeriksaan</td>
        <td colspan="7">: SPPG/Satuan Pendidikan/Posyandu/Lainnya</td>
    </tr>
    <tr>
        <td>Tanggal Pemeriksaan</td>
        <td colspan="7">: </td>
    </tr>
    <tr>
        <td>Waktu Pemeriksaan</td>
        <td colspan="7">: </td>
    </tr>

    <tr>
        <td colspan="8" style="padding:6px 0;"></td>
    </tr>

    <tr>
        <th rowspan="2" style="border:1px solid #000; padding:6px;">No</th>
        <th rowspan="2" style="border:1px solid #000; padding:6px;">Nama Makanan</th>
        <th colspan="4" style="border:1px solid #000; padding:6px;">Hasil Pemeriksaan (diberi skor 1-5)</th>
        <th rowspan="2" style="border:1px solid #000; padding:6px;">Sebelum Pengantaran/Saat Tiba di Lokasi/Sebelum dikonsumsi</th>
        <th rowspan="2" style="border:1px solid #000; padding:6px;">Ket</th>
    </tr>
    <tr>
        <th style="border:1px solid #000; padding:6px;">Rasa</th>
        <th style="border:1px solid #000; padding:6px;">Warna</th>
        <th style="border:1px solid #000; padding:6px;">Aroma</th>
        <th style="border:1px solid #000; padding:6px;">Tekstur</th>
    </tr>

    @forelse($items as $index => $item)
        <tr>
            <td style="border:1px solid #000; padding:6px; text-align:center;">{{ $index + 1 }}</td>
            <td style="border:1px solid #000; padding:6px;">{{ $item['nama_makanan'] }}</td>
            <td style="border:1px solid #000; padding:6px;"></td>
            <td style="border:1px solid #000; padding:6px;"></td>
            <td style="border:1px solid #000; padding:6px;"></td>
            <td style="border:1px solid #000; padding:6px;"></td>
            <td style="border:1px solid #000; padding:6px;"></td>
            <td style="border:1px solid #000; padding:6px;"></td>
        </tr>
    @empty
        <tr>
            <td colspan="8" style="border:1px solid #000; padding:6px; text-align:center;">Tidak ada menu</td>
        </tr>
    @endforelse

    <tr>
        <td colspan="5" style="border:1px solid #000; padding:6px; text-align:center;">Kesimpulan Menu MBG</td>
        <td colspan="3" style="border:1px solid #000; padding:6px; text-align:center;">Aman/Tidak aman dikonsumsi</td>
    </tr>
</table>

<br>

<table style="width:100%; font-family: Arial, sans-serif; font-size: 12px;">
    <tr>
        <td>Catatan:</td>
    </tr>
    <tr>
        <td>1. Silakan dipilih salah satu untuk pengisian hasil pelaksanaan uji organoleptik.</td>
    </tr>
    <tr>
        <td>2. Form dibawa oleh sopir untuk diberikan ke penanggung jawab program MBG dan diambil kembali bersama ompreng MBG.</td>
    </tr>
</table>

<br>

<table style="width:100%; font-family: Arial, sans-serif; font-size: 12px;">
    <tr>
        <td style="width:50%; text-align:center;">Mengetahui,</td>
        <td style="width:50%; text-align:center;">Pemeriksa,</td>
    </tr>
    <tr>
        <td style="height:50px;"></td>
        <td></td>
    </tr>
    <tr>
        <td style="text-align:center;">............................</td>
        <td style="text-align:center;">............................</td>
    </tr>
    <tr>
        <td style="text-align:center;">Kepala SPPG</td>
        <td></td>
    </tr>
</table>