<!DOCTYPE html>
<html>
<head>
    <title>Form Resep Menu - 4 in 1</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        /* Menggunakan @font-face untuk font yang didukung oleh Dompdf */
        /* Pastikan DejaVu Sans tersedia di lingkungan Dompdf Anda,
           atau ganti dengan font lain yang sudah di-bundle/diinstal */
        @font-face {
            font-family: 'DejaVu Sans';
            src: url('{{ public_path("fonts/DejaVuSans.ttf") }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        @font-face {
            font-family: 'DejaVu Sans';
            src: url('{{ public_path("fonts/DejaVuSans-Bold.ttf") }}') format('truetype');
            font-weight: bold;
            font-style: normal;
        }


        @page {
            size: A4;
            margin: 0;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif; /* Gunakan font yang didukung */
            font-size: 8px;
            margin: 0;
            padding: 0;
            position: relative;
        }
        .form-box {
            width: 100mm; /* Lebar sekitar 10cm */
            height: 143mm; /* Tinggi sekitar 14.3cm (setengah A4 tinggi) */
            padding: 10px;
            box-sizing: border-box;
            position: absolute; /* Penting untuk penempatan di halaman */
            border: 1px dashed #999; /* Garis putus-putus untuk batas */
        }
        /* Posisi untuk masing-masing dari 4 kotak resep di halaman A4 */
        .pos-1 { top: 7mm; left: 7mm; }    /* Kiri Atas */
        .pos-2 { top: 7mm; left: 110mm; }  /* Kanan Atas (7mm dari atas, 110mm dari kiri) */
        .pos-3 { top: 155mm; left: 7mm; }  /* Kiri Bawah (155mm dari atas, 7mm dari kiri) */
        .pos-4 { top: 155mm; left: 110mm; }/* Kanan Bawah */

        h4, p {
            margin: 2px 0;
        }
        .info-block {
            margin-top: 8px;
            margin-bottom: 10px;
        }
        .info-label {
            display: inline-block;
            width: 90px;
            font-weight: bold;
        }
        .info-value {
            display: inline-block;
            min-width: 80px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        table th, table td {
            border: 1px solid #000;
            padding: 2px 3px;
            text-align: center;
        }
        .signature-section {
            margin-top: 15px;
            display: flex;
            justify-content: space-between;
            font-size: 8px;
            min-height: 60px;
        }
        .signature {
            text-align: center;
            width: 48%;
        }
        .signature .label {
            margin-bottom: 35px;
            font-weight: bold;
            display: block;
        }
    </style>
</head>
<body>
    {{--
        Variabel $data akan dikirimkan dari Controller Anda (misalnya dari RekapCeklisController).
        $data diharapkan adalah array yang berisi 4 set data resep yang berbeda.
        Contoh struktur $data di controller:
        $data = [
            ['tanggal' => '...', 'menu' => '...', 'porsi' => '...', 'jumlah_kali_masak' => '...', 'bahan' => [['nama' => '...', 'jumlah' => '...', 'satuan' => '...'], ...]],
            // ... 3 set data lainnya ...
        ];
    --}}

    @foreach($data as $i => $item)
        @php
            $i = $i+1;
	    date_default_timezone_set('Asia/Jakarta');
        @endphp
        @if($i < 5 )
        <div class="form-box pos-{{ $i }}">
            <p><strong>YAYASAN BINA BANGSA SEMARANG</strong></p>
            <p>SATUAN PELAYANAN PEMENUHAN GIZI (SPPG YBBS) Sukatani</p>
            <p>FORM RESEP MENU</p>
            {{-- Menggunakan waktu saat ini untuk cetak, bukan dari data resep --}}
            <p style="font-size: 8px;">Printed on: {{ date('d-m-Y H:i:s') }}</p>

            <div class="info-block">
                <p><span class="info-label">Tanggal</span><span class="info-value">: {{ $item['tanggal'] ?? '-' }}</span></p>
                <p><span class="info-label">Resep</span><span class="info-value">: {{ $item['menu'] ?? '-' }}</span></p>
		<p>
    			<span class="info-label">Porsi</span>
    			<span class="info-value">: {{ isset($item['porsi']) ? number_format((float) $item['porsi'], 0, '.', '.') : '-' }} porsi</span>
		</p>
                <p><span class="info-label">Jumlah masak</span><span class="info-value">: {{ $item['jumlah_kali_masak'] ?? '-' }}</span></p>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Bahan</th>
                        <th>Jumlah</th>
                        <th>Satuan</th>
                    </tr>
                </thead>
                <tbody>
                    @php $rowNumber = 1; @endphp
                    @for($x = 0; $x < 15 && $rowNumber <= 15; $x++)
                        @php
                            $namaBahan = $item['bahan'][$x]['nama'] ?? '';
                            $jumlahBahan = $item['bahan'][$x]['jumlah'] ?? null;
                            $satuanBahan = $item['bahan'][$x]['satuan'] ?? '';
                            $isBeras = strtolower(trim($namaBahan)) === 'beras';
                            $isNumericJumlah = is_numeric($jumlahBahan);
                        @endphp

                        @if($isBeras && $isNumericJumlah && $jumlahBahan > 72)
                            @php
                                $remaining = (float) $jumlahBahan;
                                $chunkIndex = 1;
                                $chunkCap = 72;
                            @endphp
                            @while($remaining > 0 && $rowNumber <= 15)
                                @php
                                    $take = $remaining > $chunkCap ? $chunkCap : $remaining;
                                    $remaining -= $take;
                                @endphp
                                <tr>
                                    <td>{{ $rowNumber }}</td>
                                    <td>{{ ucwords($namaBahan) }} {{ $chunkIndex }}</td>
                                    <td>{{ number_format($take, 0, '.', '.') }}</td>
                                    <td>{{ $satuanBahan }}</td>
                                </tr>
                                @php
                                    $rowNumber++;
                                    $chunkIndex++;
                                @endphp
                            @endwhile
                        @else
                            <tr>
                                <td>{{ $rowNumber }}</td>
                                <td>{{ $namaBahan }}</td>
                                <td>{{ $isNumericJumlah ? number_format((float) $jumlahBahan, 0, '.', '.') : '' }}</td>
                                <td>{{ $satuanBahan }}</td>
                            </tr>
                            @php $rowNumber++; @endphp
                        @endif
                    @endfor
                </tbody>
            </table>

            <table style="width: 100%; margin-top: 20px; font-size: 8px; text-align: center;">
                <tr>
                    <td><strong>Dibuat oleh</strong></td>
                    <td><strong>Menyetujui</strong></td>
                </tr>
                <tr>
                    <td style="padding-top: 35px;">PL Masak</td>
                    <td style="padding-top: 35px;">Ahli Gizi</td>
                </tr>
            </table>

        </div>
        @else
        <div style="page-break-after: always;"></div>
        @endif
    @endforeach
    @foreach($data as $i => $item)
        @php
            $i = $i+1;
        @endphp
        @if($i == 5 )
        <div class="form-box pos-{{ $i }}">
            <p><strong>YAYASAN BINA BANGSA SEMARANG</strong></p>
            <p>SATUAN PELAYANAN PEMENUHAN GIZI </p>
            <p>FORM RESEP MENU</p>
            {{-- Menggunakan waktu saat ini untuk cetak, bukan dari data resep --}}
            <p style="font-size: 8px;">Printed on: {{ date('d-m-Y H:i:s') }}</p>

            <div class="info-block">
                <p><span class="info-label">Tanggal</span><span class="info-value">: {{ $item['tanggal'] ?? '-' }}</span></p>
                <p><span class="info-label">Resep</span><span class="info-value">: {{ $item['menu'] ?? '-' }}</span></p>
		<p>
    			<span class="info-label">Porsi</span>
    			<span class="info-value">: {{ isset($item['porsi']) ? number_format((float) $item['porsi'], 0, '.', '.') : '-' }} porsi</span>
		</p>

                <p><span class="info-label">Jumlah masak</span><span class="info-value">: {{ $item['jumlah_kali_masak'] ?? '-' }}</span></p>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Bahan</th>
                        <th>Jumlah</th>
                        <th>Satuan</th>
                    </tr>
                </thead>
                <tbody>
                    @php $rowNumber = 1; @endphp
                    @for($x = 0; $x < 10 && $rowNumber <= 10; $x++)
                        @php
                            $namaBahan = $item['bahan'][$x]['nama'] ?? '';
                            $jumlahBahan = $item['bahan'][$x]['jumlah'] ?? null;
                            $satuanBahan = $item['bahan'][$x]['satuan'] ?? '';
                            $isBeras = strtolower(trim($namaBahan)) === 'beras';
                            $isNumericJumlah = is_numeric($jumlahBahan);
                        @endphp

                        @if($isBeras && $isNumericJumlah && $jumlahBahan > 72)
                            @php
                                $remaining = (float) $jumlahBahan;
                                $chunkIndex = 1;
                                $chunkCap = 72;
                            @endphp
                            @while($remaining > 0 && $rowNumber <= 10)
                                @php
                                    $take = $remaining > $chunkCap ? $chunkCap : $remaining;
                                    $remaining -= $take;
                                @endphp
                                <tr>
                                    <td>{{ $rowNumber }}</td>
                                    <td>{{ ucwords($namaBahan) }} {{ $chunkIndex }}</td>
                                    <td>{{ number_format($take, 0, '.', '.') }}</td>
                                    <td>{{ $satuanBahan }}</td>
                                </tr>
                                @php
                                    $rowNumber++;
                                    $chunkIndex++;
                                @endphp
                            @endwhile
                        @else
                            <tr>
                                <td>{{ $rowNumber }}</td>
                                <td>{{ $namaBahan }}</td>
                                <td>{{ $isNumericJumlah ? number_format((float) $jumlahBahan, 0, '.', '.') : '' }}</td>
                                <td>{{ $satuanBahan }}</td>
                            </tr>
                            @php $rowNumber++; @endphp
                        @endif
                    @endfor
                </tbody>
            </table>

            <table style="width: 100%; margin-top: 20px; font-size: 8px; text-align: center;">
                <tr>
                    <td><strong>Dibuat oleh</strong></td>
                    <td><strong>Menyetujui</strong></td>
                </tr>
                <tr>
                    <td style="padding-top: 35px;">PL Masak</td>
                    <td style="padding-top: 35px;">Ahli Gizi</td>
                </tr>
            </table>

        </div>
        @else
        @endif
    @endforeach
</body>
</html>