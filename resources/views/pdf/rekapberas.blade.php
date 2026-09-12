<!DOCTYPE html>
<html>
<head>
    <title>Rekap Steamer</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            margin: 20px;
        }

        h3, h4, p {
            margin: 0;
            padding: 0;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 10px;
        }

        td, th {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
        }

        .no-border td {
            border: none;
            text-align: left;
            padding: 2px;
        }

        .highlight {
            font-weight: bold;
        }

        .packing th, .packing td {
            background-color: #fcd5b4;
        }

        .label-cell {
            width: 170px;
        }

        .colon-cell {
            width: 10px;
            text-align: center;
        }

        .value-cell {
            text-align: left;
        }

        .section {
            margin-bottom: 10px;
        }

        .tabel-kombo {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
        }

        .tabel-kombo .box {
            width: 48%;
        }

        .tabel-kombo .box table {
            width: 100%;
        }

        .ttd {
            margin-top: 50px;
            text-align: center;
        }

        .ttd td {
            padding-top: 50px;
        }
    </style>
</head>
<body>

    <h3>YAYASAN BINA BANGSA SEMARANG</h3>
    <p>SATUAN PELAYANAN PEMENUHAN GIZI</p>
    <p><strong>Perhitungan Memasak Nasi dengan Steamer 2 Pintu (12 Tray)</strong></p>
    <p>Prepared by : {{ $dapur->ahli_gizi ?? 'admin' }}</p>
    <p>Printed on : {{ date('d-m-Y H:i:s') }}</p>

    {{-- Informasi Jumlah Pax dan Pemorsian --}}
    <table class="no-border" style="width: 60%;">
        <tr>
            <td class="label-cell">Jumlah Pax (dgn Buffer 1%)</td>
            <td class="colon-cell">:</td>
            <td class="value-cell highlight">{{ $pax ?? 'N/A' }} pax</td>
        </tr>
        <tr>
            <td class="label-cell">Pemorsian A</td>
            <td class="colon-cell">:</td>
            <td class="value-cell highlight">{{ $pemorsian_a ?? 'N/A' }} gram</td>
        </tr>
        <tr>
            <td class="label-cell">Pemorsian B</td>
            <td class="colon-cell">:</td>
            <td class="value-cell highlight">{{ $pemorsian_b ?? 'N/A' }} gram</td>
        </tr>
    </table>

    {{-- Informasi Kebutuhan --}}
    <table class="no-border" style="width: 65%;">
        <tr><td class="label-cell">Kebutuhan Beras</td><td class="colon-cell">:</td><td class="value-cell">{{ $kebutuhan['beras'] ?? 'N/A' }} kg</td></tr>
        <tr><td class="label-cell">Kebutuhan Tray</td><td class="colon-cell">:</td><td class="value-cell">{{ $kebutuhan['tray'] ?? 'N/A' }} Tray</td></tr>
        <tr><td class="label-cell">Kebutuhan Steamer</td><td class="colon-cell">:</td><td class="value-cell">{{ $kebutuhan['steamer'] ?? 'N/A' }} Steamer (2 Pintu = 24 Tray)</td></tr>
        <tr><td class="label-cell">Kebutuhan Pintu Steamer</td><td class="colon-cell">:</td><td class="value-cell">{{ $kebutuhan['pintu'] ?? 'N/A' }} Pintu (1 Pintu = 12 Tray)</td></tr>
        <tr><td class="label-cell">Kebutuhan Air Total</td><td class="colon-cell">:</td><td class="value-cell">{{ $kebutuhan['air'] ?? 'N/A' }} Liter</td></tr>
    </table>

    {{-- Hasil Produksi --}}
    <table class="no-border" style="width: 50%;">
        <tr><td class="label-cell">Hasil Produksi (kg)</td><td class="colon-cell">:</td><td class="value-cell">{{ $produksi['kg'] ?? 'N/A' }} kg</td></tr>
        <tr><td class="label-cell">Hasil Produksi (gram)</td><td class="colon-cell">:</td><td class="value-cell">{{ number_format($produksi['gram'] ?? 0, 0, ',', '.') }} gram</td></tr>
    </table>

    {{-- Tabel Cuci Beras dan Packing --}}
    <p><strong>Hitungan Cuci Beras:</strong> {{ $cuci ?? 'N/A' }} kali pencucian (max 72kg)</p>
    
    <table>
        <thead>
            <tr>
                <th colspan="2">Hitungan Cuci Beras</th>
                <th colspan="3" class="packing">Packing Beras</th>
            </tr>
            <tr>
                <th>Urutan</th>
                <th>Jumlah (kg)</th>
                <th class="packing">25</th>
                <th class="packing">5</th>
                <th class="packing">1</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $maxRow = max(count($pencucian), count($packing)); 
                $kebutuhans = $kebutuhan['beras'];
                $berat_25kg = 0;
                $berat_5kg = 0;
                $berat_1kg = 0;
                
            @endphp
            @for ($i = 0; $i < $maxRow; $i++)
            @php
                
                if($kebutuhans > 72)
                {
                    $berat_25kg = 2;
                    $nilai_beras_cuci = $kebutuhans;
                    $berat_5kg = 4;
                    $berat_1kg = 2;
                    $kebutuhans = $kebutuhans - 72;
                }elseif($kebutuhans > 0){
                    $nilai_beras_cuci = $kebutuhans;
                    
                    $berat_25kg = $kebutuhans/25;
                    $berat_25kg = number_format($berat_25kg,0,0);
                    if($kebutuhans > 0 )
                    {
                        $kebutuhans = $kebutuhans - $berat_25kg *25;
                        $berat_5kg = $kebutuhans/5 ?? 0;
                        $berat_5kg = number_format($berat_5kg,0,0);
                        $berat_1kg = $kebutuhans%5 ?? 0;
                        $berat_1kg = number_format($berat_1kg,0,0); 
                        $kebutuhans = 0 ?? 0;
                    }else{
                        $berat_25kg = 0;
                        $berat_5kg = $kebutuhans/5 ?? 0;
                        $berat_5kg = number_format($berat_5kg,0,0);
                        $berat_1kg = $berat_5kg%5 ?? 0;
                        $berat_1kg = number_format($berat_1kg,0,0); 
                        $kebutuhans = $kebutuhans - $kebutuhans ?? 0;
                    }
                    $kebutuhans = 0;
                }else{
                    $nilai_beras_cuci = $kebutuhans;
                        $berat_25kg = 0;
                        $berat_5kg =  0;
                        $berat_1kg =  0;
                }
            @endphp
            <tr>
                <td>{{ ['Pertama','Kedua','Ketiga','Keempat','Kelima'][$i] ?? 'Lainnya' }}</td>
                <td>{{ $nilai_beras_cuci  }}  kg </td>
                <td class="packing">{{ $berat_25kg }} </td>
                <td class="packing">{{ $berat_5kg }}</td>
                <td class="packing">{{ $berat_1kg }}</td>
            </tr>
            @endfor
        </tbody>
    </table>

    {{-- Tabel Pintu Steamer --}}
    <h4>Tabel Pintu Steamer</h4>
    @php
        $totalTray = $kebutuhan['tray'] ;
        $trayPerBaris = 12;
        $pintu_2 = 1;
        $sisaTray = 0;
        $jumlah_pintu = $kebutuhan['pintu'];
        $jumlah_pintu_terakhir =  0;
        if ($totalTray < 12) {
            $pintu_2 = 0;
            $jumlah_pintu_terakhir = 0;
            $sisaTray = $totalTray % $trayPerBaris;
        } else {
            $jumlah_pintu = $kebutuhan['pintu'] - 1;
            $jumlah_pintu_terakhir = $kebutuhan['pintu'];
            $sisaTray = $totalTray % $trayPerBaris;
        }
        
    @endphp
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                @if($pintu_2 == 1 )
                <tr>
                    <th style="border: 1px solid #000;">Pintu 1 ( pintu 1 s.d {{ $jumlah_pintu  }})</th>
                    <th style="border: 1px solid #000;">Pintu 2 ( pintu {{ $jumlah_pintu_terakhir }} )</th>
                </tr>
                @else
                <tr>
                    <th style="border: 1px solid #000;">Pintu 1 ( pintu {{ $jumlah_pintu }} )</th>
                    <th style="border: 1px solid #000;">Pintu 2 ( pintu - )</th>
                </tr>
                @endif
                
            </thead>
            <tbody>
               
            @for ($i = 1; $i <= 12; $i++)
                <tr>
                    @if($pintu_2 == 1 )
                    <td style="border: 1px solid #000; background-color: #c6efce;">Tray 1 </td>
    
                    @php
                        // Tray 2 diwarnai hijau hanya jika $i <= sisa
                        $tray2Style = $i <= $sisaTray ? 'background-color: #c6efce;' : '';
                    @endphp
                
                    <td style="border: 1px solid #000; {{ $tray2Style }}">Tray 2 {{ $i }}</td>
                    @else
                    @php
                        // Tray 2 diwarnai hijau hanya jika $i <= sisa
                        $tray2Style = $i <= $sisaTray ? 'background-color: #c6efce;' : '';
                    @endphp
                    <td style="border: 1px solid #000; {{ $tray2Style }}">Tray 1 {{ $i }}</td>
    
                    
                
                    <td style="border: 1px solid #000; ">Tray 2 {{ $i }}</td>
                    @endif
                    
                </tr>
            @endfor
            </tbody>
        </table>


    {{-- Tanda Tangan --}}
    <table style="width: 100%; margin: 50px auto 0; font-size: 12px; border-collapse: collapse;">
    <tr>
        <td style="width: 33.33%; text-align: center; vertical-align: top;">
            <strong><small>Dibuat Oleh:</small></strong><br><br><br><br><br><br>
            <span style="font-size: 10px;"><strong>( Asisten Dapur )</strong></span>
        </td>
        <td style="width: 33.33%; text-align: center; vertical-align: top;">
            <strong><small>Diketahui:</small></strong><br><br><br><br><br><br>
            <span style="font-size: 12px;"><strong>( Ahli Gizi )</strong></span>
        </td>
        <td style="width: 33.33%; text-align: center; vertical-align: top;">
            <strong><small>Disetujui Oleh:</small></strong><br><br><br><br><br><br>
            <span style="font-size: 10px;"><strong>( Kepala Dapur )</strong></span>
        </td>
    </tr>
</table>

</body>

</html>