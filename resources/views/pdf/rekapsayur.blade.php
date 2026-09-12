<!DOCTYPE html>
<html>
<head>
    <title>Rekap Sayur Lengkap</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9px; line-height: 1.2; padding: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 8px; font-size: 9px; }
        th, td { border: 1px solid #000; padding: 3px 5px; text-align: left; vertical-align: top; }
        th { background-color: #f2f2f2; font-weight: bold; }

        /* --- Header Dokumen Styling --- */
        .report-header {
            margin-bottom: 10px; /* Jarak keseluruhan header dengan konten di bawahnya */
        }
        .report-header h3 {
            margin-top: 0; /* Pastikan tidak ada margin atas */
            margin-bottom: 2px; /* Mengurangi jarak di bawah H3 */
            font-size: 12px; /* Ukuran font H3 */
            text-align: left;
        }
        .report-header p {
            margin: 0; /* Menghilangkan margin default pada p */
            padding: 0; /* Menghilangkan padding default pada p */
            line-height: 1.2; /* Mengatur tinggi baris agar lebih rapat */
            font-size: 9px;
            text-align: left;
        }
        .header-title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin-top: 15px; /* Jarak judul utama dari header di atasnya */
            margin-bottom: 15px; /* Jarak judul utama dengan konten di bawahnya */
        }


        /* General styling for the boxed sections (manual input, isian sayur, kebutuhan) */
        .info-box { border: 1px solid #ccc; padding: 5px; margin-bottom: 5px; }
        .info-box-title { font-weight: bold; margin-bottom: 3px; }
        .info-row { display: table; width: 100%; } /* Using table display for better alignment in Dompdf */
        .info-label, .info-value, .info-unit { display: table-cell; vertical-align: middle; padding: 1px 0; }
        .info-label { width: 40%; } /* Adjust width for labels */
        .info-value { width: 30%; text-align: right; }
        .info-unit { width: 10%; text-align: left; padding-left: 2px; } /* For 'kg', 'gram', 'pax' */

        .info-value.green { background-color: #d4edda; border: 1px solid #28a745; text-align: center; padding: 1px 3px; display: inline-block; min-width: 30px; }
        .info-value.yellow-bg { background-color: #fff3cd; } /* For yellow background in values */

        .align-right { text-align: right; }
        .bold { font-weight: bold; }
        .small-text { font-size: 8px; }
        .cell-yellow { background-color: #fff3cd; } /* For yellow cells directly in table or specific divs */

        /* Column layout using floats for the main two-column structure */
        .col-float { float: left; width: 49%; padding: 0 5px; box-sizing: border-box; }
        .col-float.right { margin-left: 2%; }
        .clearfix::after { content: ""; clear: both; display: table; }

        /* Specific styling for the 'Isian Sayur' inner table-like structure */
        .isian-sayur-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }
        .isian-sayur-row {
            display: table-row;
        }
        .isian-sayur-cell {
            display: table-cell;
            padding: 2px 3px;
            border: 1px solid #000; /* mimic cell borders */
            vertical-align: middle;
        }
        /* Custom widths for isian sayur cells based on image */
        .isian-sayur-cell:nth-child(1) { width: 30%; } /* Labu Siam, Wortel, Total */
        .isian-sayur-cell:nth-child(2) { width: 15%; text-align: right;} /* kg (mentah) */
        .isian-sayur-cell:nth-child(3) { width: 10%; } /* kg (satuan mentah) */
        .isian-sayur-cell:nth-child(4) { width: 15%; text-align: right;} /* Matang (kg) */
        .isian-sayur-cell:nth-child(5) { width: 10%; } /* kg (satuan matang) */
        .isian-sayur-cell:nth-child(6) { width: 10%; text-align: center;} /* % */
        .isian-sayur-cell:nth-child(7) { width: 10%; } /* Jenis */

        /* Remove border for specific cells in isian-sayur-grid to match image */
        .no-top-border { border-top: none; }
        .no-bottom-border { border-bottom: none; }
        .no-left-border { border-left: none; }
        .no-right-border { border-right: none; }

        /* New styles for separate tables (Resep & Penerimaan) */
        .table-container {
            width: 49%; /* Half width for two tables side-by-side */
            float: left;
            box-sizing: border-box;
            padding: 0 5px; /* Add some padding if needed */
        }
        .table-container.left {
            margin-right: 2%; /* Gap between tables */
        }
        .table-container h4 {
            margin-top: 0;
            margin-bottom: 5px;
            text-align: center;
            font-size: 11px;
        }


        /* --- Signatures Section Styling --- */
        .signatures-section {
            width: 100%;
            margin-top: 50px; /* Jarak dari tabel di atasnya */
            display: table;
            table-layout: fixed; /* Memastikan kolom memiliki lebar yang sama */
        }
        .signature-column {
            display: table-cell;
            width: 50%; /* Setiap kolom mengambil setengah lebar */
            text-align: center;
            vertical-align: top;
            padding: 0 10px;
        }
        .signature-label {
            font-weight: bold;
            margin-bottom: 50px; /* Jarak untuk garis tanda tangan manual */
            display: block;
        }
        .signature-name {
            font-weight: bold;
            /* text-decoration: underline; */ /* Hapus ini jika ingin garis manual */
            margin-top: 5px; /* Jarak nama dari garis (jika garis dibuat manual) */
            display: block;
        }
        .signature-position {
            font-size: 8px;
            margin-top: 2px; /* Jarak posisi dari nama */
            display: block;
        }
    </style>
</head>
<body>

    <div class="report-header">
        <h3>YAYASAN BINA BANGSA SEMARANG</h3>
        <p>SATUAN PELAYANAN PEMENUHAN GIZI</p>
        <p>Perhitungan Memasak Sayur </p>
        <p>Prepared by : {{ $dapur->ahli_gizi }}</p>
        <p>Printed on : {{ date('d-m-Y H:i:s') }}</p>
    </div>

    <div class="clearfix">
        <div class="col-float">
            <div class="table-title bold"></div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 25%;">Pilihan Nama Sayuran</th>
                        <th style="width: 15%;">Berat Bahan Baku per Box</th>
                        <th style="width: 10%;">Satuan</th>
                        <th style="width: 10%;">Jenis</th>
                        <th style="width: 20%;">Hasil Matang Setelah Penyusutan (kg)</th>
                        <th style="width: 5%;">%</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sayurMasters as $item)
                    <tr>
                        <td>{{ $item->no }}</td>
                        <td>{{ $item->nama_sayuran }}</td>
                        <td class="align-right">
                            @if(is_numeric($item->berat_bahan_baku_per_box))
                                {{ number_format((float)$item->berat_bahan_baku_per_box, 2) }} 
                            @else
                                {{ $item->berat_bahan_baku_per_box }}
                            @endif
                        </td>
                        <td>{{ $item->satuan }}</td>
                        <td>{{ $item->jenis }}</td>
                        <td class="align-right">
                            @if(is_numeric($item->hasil_matang_setelah_penyusutan_kg))
                                {{ number_format((float)$item->hasil_matang_setelah_penyusutan_kg, 2) }} Gram
                            @else
                                {{ $item->hasil_matang_setelah_penyusutan_kg }}
                            @endif
                        </td>
                        <td class="align-right">
                            @if(is_numeric($item->persentase_penyusutan))
                                {{ number_format((float)$item->persentase_penyusutan, 0) }}%
                            @else
                                {{ $item->persentase_penyusutan }}
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="col-float right">
            <div class="info-box">
                <div class="info-box-title">Isi manual jumlah pax & pemorsian</div>
                <div class="info-row">
                    <span class="info-label">
                        @if(isset($dataKalkulasi['label_jumlah_pax']))
                            {{ $dataKalkulasi['label_jumlah_pax'] }}
                        @else
                            Jumlah Pax (dan Buffer 1%)
                        @endif:
                    </span>
                    <span class="info-value">
                        @if(is_numeric($dataKalkulasi['jumlah_pax_buffer']))
                            {{ number_format((float)$dataKalkulasi['jumlah_pax_buffer'], 0) }}
                        @else
                            {{ $dataKalkulasi['jumlah_pax_buffer'] ?? '' }}
                        @endif
                    </span>
                    <span class="info-unit small-text">max</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Pemorsian A : @if(is_numeric($dataKalkulasi['pemorsian_gram_a']))
                        {{ number_format((float)$dataKalkulasi['pemorsian_gram_a'], 0) }}
                    @else
                        {{ $dataKalkulasi['pemorsian_gram_a'] ?? '' }}
                    @endif gram</span>
                    <span class="info-value">
                        Pemorsian B : @if(is_numeric($dataKalkulasi['pemorsian_gram_b']))
                        {{ number_format((float)$dataKalkulasi['pemorsian_gram_b'], 0) }}
                    @else
                        {{ $dataKalkulasi['pemorsian_gram_b'] ?? '' }}
                    @endif gram
                    </span>
                    <span class="info-unit small-text"></span>

                    
                </div>
                <div class="info-row">
                   
                </div>
            </div>

            <div class="info-box">
                <div class="info-box-title">Isian Sayur - Mentah</div>
                <div class="isian-sayur-grid">
                    <div class="isian-sayur-row">
                        <div class="isian-sayur-cell "></div>
                        <div class="isian-sayur-cell ">Mentah</div>
                        <div class="isian-sayur-cell "></div>
                        <div class="isian-sayur-cell ">Matang</div>
                        <div class="isian-sayur-cell "></div>
                        <div class="isian-sayur-cell ">%</div>
                        <div class="isian-sayur-cell">Jenis</div>
                    </div>
                    <div class="isian-sayur-row">
                        <div class="isian-sayur-cell ">{{ $dataKalkulasi['isian_sayur_1_nama'] ?? '' }}:</div>
                        <div class="isian-sayur-cell align-right">
                            @if(is_numeric($dataKalkulasi['isian_sayur_1_kg_mentah'] ?? ''))
                                {{ number_format((float)($dataKalkulasi['isian_sayur_1_kg_mentah'] ?? ''), 2) }}
                            @else
                                {{ $dataKalkulasi['isian_sayur_1_kg_mentah'] ?? '' }}
                            @endif
                        </div>
                        <div class="isian-sayur-cell ">kg</div>
                        <div class="isian-sayur-cell align-right">
                            @if(is_numeric($dataKalkulasi['isian_sayur_1_matang'] ?? ''))
                                {{ number_format((float)($dataKalkulasi['isian_sayur_1_matang'] ?? ''), 2) }}
                            @else
                                {{ $dataKalkulasi['isian_sayur_1_matang'] ?? '' }}
                            @endif
                        </div>
                        <div class="isian-sayur-cell">kg</div>
                        <div class="isian-sayur-cell green-bg-percent">
                            @if(is_numeric($dataKalkulasi['isian_sayur_1_persen'] ?? ''))
                                {{ number_format((float)($dataKalkulasi['isian_sayur_1_persen'] ?? ''), 0) }}%
                            @else
                                {{ $dataKalkulasi['isian_sayur_1_persen'] ?? '' }}
                            @endif
                        </div>
                        <div class="isian-sayur-cell">{{ $dataKalkulasi['isian_sayur_1_jenis'] ?? '' }}</div>
                    </div>
                    <div class="isian-sayur-row">
                        <div class="isian-sayur-cell ">{{ $dataKalkulasi['isian_sayur_2_nama'] ?? '' }}:</div>
                        <div class="isian-sayur-cell align-right">
                            @if(is_numeric($dataKalkulasi['isian_sayur_2_kg_mentah'] ?? ''))
                                {{ number_format((float)($dataKalkulasi['isian_sayur_2_kg_mentah'] ?? ''), 2) }}
                            @else
                                {{ $dataKalkulasi['isian_sayur_2_kg_mentah'] ?? '' }}
                            @endif
                        </div>
                        <div class="isian-sayur-cell ">kg</div>
                        <div class="isian-sayur-cell align-right">
                            @if(is_numeric($dataKalkulasi['isian_sayur_2_kg_matang'] ?? ''))
                                {{ number_format((float)($dataKalkulasi['isian_sayur_2_kg_matang'] ?? ''), 2) }}
                            @else
                                {{ $dataKalkulasi['isian_sayur_2_kg_matang'] ?? '' }}
                            @endif
                        </div>
                        <div class="isian-sayur-cell">kg</div>
                        <div class="isian-sayur-cell green-bg-percent">
                            @if(is_numeric($dataKalkulasi['isian_sayur_2_persen'] ?? ''))
                                {{ number_format((float)($dataKalkulasi['isian_sayur_2_persen'] ?? ''), 0) }}%
                            @else
                                {{ $dataKalkulasi['isian_sayur_2_persen'] ?? '' }}
                            @endif
                        </div>
                        <div class="isian-sayur-cell">{{ $dataKalkulasi['isian_sayur_2_jenis'] ?? '' }}</div>
                    </div>
                    <div class="isian-sayur-row">
                        <div class="isian-sayur-cell ">{{ $dataKalkulasi['isian_sayur_3_nama'] ?? '' }}:</div>
                        <div class="isian-sayur-cell align-right">
                            @if(is_numeric($dataKalkulasi['isian_sayur_3_kg_mentah'] ?? ''))
                                {{ number_format((float)($dataKalkulasi['isian_sayur_3_kg_mentah'] ?? ''), 2) }}
                            @else
                                {{ $dataKalkulasi['isian_sayur_3_kg_mentah'] ?? '' }}
                            @endif
                        </div>
                        <div class="isian-sayur-cell ">kg</div>
                        <div class="isian-sayur-cell align-right">
                            @if(is_numeric($dataKalkulasi['isian_sayur_3_kg_matang'] ?? ''))
                                {{ number_format((float)($dataKalkulasi['isian_sayur_3_kg_matang'] ?? ''), 2) }}
                            @else
                                {{ $dataKalkulasi['isian_sayur_3_kg_matang'] ?? '' }}
                            @endif
                        </div>
                        <div class="isian-sayur-cell">kg</div>
                        <div class="isian-sayur-cell green-bg-percent">
                            @if(is_numeric($dataKalkulasi['isian_sayur_3_persen'] ?? ''))
                                {{ number_format((float)($dataKalkulasi['isian_sayur_3_persen'] ?? ''), 0) }}%
                            @else
                                {{ $dataKalkulasi['isian_sayur_3_persen'] ?? '' }}
                            @endif
                        </div>
                        <div class="isian-sayur-cell">{{ $dataKalkulasi['isian_sayur_3_jenis'] ?? '' }}</div>
                    </div>
                    <div class="isian-sayur-row">
                        <div class="isian-sayur-cell no-top-border no-bottom-border"></div>
                        <div class="isian-sayur-cell no-top-border no-bottom-border"></div>
                        <div class="isian-sayur-cell no-top-border no-bottom-border">kg</div>
                        <div class="isian-sayur-cell no-top-border no-bottom-border"></div>
                        <div class="isian-sayur-cell no-top-border no-bottom-border">kg</div>
                        <div class="isian-sayur-cell no-top-border no-bottom-border"></div>
                        <div class="isian-sayur-cell no-top-border no-bottom-border"></div>
                    </div>
                    <div class="isian-sayur-row">
                        <div class="isian-sayur-cell bold">Total:</div>
                        <div class="isian-sayur-cell align-right bold">
                            @if(is_numeric($dataKalkulasi['isian_sayur_total_kg_mentah']))
                                {{ number_format((float)$dataKalkulasi['isian_sayur_total_kg_mentah'], 2) }}
                            @else
                                {{ $dataKalkulasi['isian_sayur_total_kg_mentah'] }}
                            @endif
                        </div>
                        <div class="isian-sayur-cell bold">kg</div>
                        <div class="isian-sayur-cell align-right bold">
                            @if(is_numeric($dataKalkulasi['isian_sayur_total_kg_matang']))
                                {{ number_format((float)$dataKalkulasi['isian_sayur_total_kg_matang'], 2) }}
                            @else
                                {{ $dataKalkulasi['isian_sayur_total_kg_matang'] }}
                            @endif
                        </div>
                        <div class="isian-sayur-cell bold">kg</div>
                        <div class="isian-sayur-cell green-bg-percent bold">
                            @if(is_numeric($dataKalkulasi['isian_sayur_total_persen']))
                                {{ number_format((float)$dataKalkulasi['isian_sayur_total_persen'], 0) }}%
                            @else
                                {{ $dataKalkulasi['isian_sayur_total_persen'] }}
                            @endif
                        </div>
                        <div class="isian-sayur-cell"></div>
                    </div>
                </div>
            </div>

            <div class="info-box">
                <div class="info-row">
                    <span class="info-label">Kebutuhan Bahan Baku:</span>
                    <span class="info-value">
                        @if(is_numeric($dataKalkulasi['kebutuhan_bahan_baku_kg']))
                            {{ number_format((float)$dataKalkulasi['kebutuhan_bahan_baku_kg'], 0) }}
                        @else
                            {{ $dataKalkulasi['kebutuhan_bahan_baku_kg'] }}
                        @endif
                    </span>
                    <span class="info-unit small-text">kg</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Kebutuhan Bahan Baku Matang:</span>
                    <span class="info-value">
                        @if(is_numeric($dataKalkulasi['kebutuhan_bahan_baku_matang_kali_masak']))
                            {{ number_format((float)$dataKalkulasi['kebutuhan_bahan_baku_matang_kali_masak'], 0) }}
                        @else
                            {{ $dataKalkulasi['kebutuhan_bahan_baku_matang_kali_masak'] }}
                        @endif
                    </span>
                    <span class="info-unit small-text">kg</span>
                </div>
                <div class="info-row">
                    
                    <span class="info-label">kali masak:</span>
                    <span class="info-value">
                        @if(is_numeric($dataKalkulasi['Jumlah_masak']))
                            {{ number_format((float)$dataKalkulasi['Jumlah_masak'], 2) }} x masak
                        @else
                            {{ $dataKalkulasi['Jumlah_masak'] ?? '' }}
                        @endif

                    </span>
                    
                    <span class="info-unit"></span>
                </div>
               
            </div>
        </div>
    </div>

    <div class="clearfix" style="margin-top: 15px;">
        <div class="table-container left">
            <h4>Resep</h4>
            <table>
                <thead>
                    <tr>
                        <th style="width: 10%;">No</th>
                        <th style="width: 60%;">Sayuran</th>
                        <th style="width: 15%;">Jumlah</th>
                        <th style="width: 15%;">Satuan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($resepPenerimaan as $item)
                    <tr>
                        <td>{{ $item->no }}</td>
                        <td>{{ $item->sayuran }}</td>
                        <td class="align-right">
                            @if(is_numeric($item->jumlah))
                                {{ number_format((float)$item->jumlah, 2) }}
                            @else
                                {{ $item->jumlah }}
                            @endif
                        </td>
                        <td>{{ $item->satuan_resep }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="table-container">
            <h4>Penerimaan</h4>
            <table>
                <thead>
                    <tr>
                        <th style="width: 20%;">Jml Box</th>
                        <th style="width: 20%;">Total Box</th>
                        <th style="width: 30%;">Isi Box (kg)</th>
                        <th style="width: 10%;">%</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($resepPenerimaan as $item)
                    <tr>
                        <td class="align-right">
                            @if(is_numeric($item->jml_box))
                                {{ number_format((float)$item->jml_box, 0) }}
                            @else
                                {{ $item->jml_box }}
                            @endif
                        </td>
                        <td class="align-right">
                            @if(is_numeric($item->total))
                                {{ number_format((float)$item->total, 3) }}
                            @else
                                {{ $item->total }}
                            @endif
                        </td>
                        <td class="align-right">
                            @if(is_numeric($item->isi_box_kg))
                                {{ number_format((float)$item->isi_box_kg, 2) }}
                            @else
                                {{ $item->isi_box_kg }}
                            @endif
                        </td>
                        <td class="align-right">
                            @if ($item->persentase_isi_box === '#VALUE!')
                                #VALUE!
                            @elseif(is_numeric($item->persentase_isi_box))
                                {{ number_format((float)$item->persentase_isi_box, 0) . '%' }}
                            @else
                                {{ $item->persentase_isi_box }}
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>


    <div class="signatures-section">
        <div class="signature-column">
            <span class="signature-label">Dibuat oleh,</span>
            <span class="signature-position">Asisten Dapur</span>
        </div>
        <div class="signature-column">
            <span class="signature-label">Disetujui oleh,</span>
            <span class="signature-position">Kepala Dapur</span>
        </div>
    </div>

</body>
</html>