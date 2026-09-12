<!DOCTYPE html>
<html>
<head>
    <title>Rekap Lauk</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9px; line-height: 1.2; padding: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 8px; font-size: 9px; }
        th, td { border: 1px solid #000; padding: 3px 5px; text-align: left; vertical-align: top; }
        th { background-color: #f2f2f2; font-weight: bold; }

        .header-title { text-align: center; font-size: 14px; font-weight: bold; margin-bottom: 10px; } /* Dikurangi dari 15px */

        /* CSS Baru untuk Header Dokumen */
        .report-header {
            margin-bottom: 10px; /* Jarak keseluruhan header dengan konten di bawahnya */
        }
        .report-header h3 {
            margin-top: 0; /* Pastikan tidak ada margin atas */
            margin-bottom: 2px; /* Mengurangi jarak di bawah H3 */
            font-size: 12px; /* Mungkin ingin sedikit lebih besar */
            text-align: left;
        }
        .report-header p {
            margin: 0; /* Menghilangkan margin default pada p */
            padding: 0; /* Menghilangkan padding default pada p */
            line-height: 1.2; /* Mengatur tinggi baris agar lebih rapat */
            font-size: 9px;
            text-align: left;
        }


        .info-box { border: 1px solid #ccc; padding: 5px; margin-bottom: 5px; }
        .info-box-title { font-weight: bold; margin-bottom: 3px; }
        .info-row { display: table; width: 100%; }
        .info-label, .info-value, .info-unit { display: table-cell; vertical-align: middle; padding: 1px 0; }
        .info-label { width: 40%; }
        .info-value { width: 30%; text-align: right; }
        .info-unit { width: 10%; text-align: left; padding-left: 2px; }

        .info-value.yellow-bg { background-color: #fff3cd; }

        .align-right { text-align: right; }
        .bold { font-weight: bold; }
        .small-text { font-size: 8px; }
        .cell-yellow { background-color: #fff3cd; }
        .cell-green { background-color: #d4edda; }

        .col-float { float: left; width: 49%; padding: 0 5px; box-sizing: border-box; }
        .col-float.right { margin-left: 2%; }
        .clearfix::after { content: ""; clear: both; display: table; }

        /* Specific styling for the 'Isian Lauk' inner table-like structure */
        .isian-lauk-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }
        .isian-lauk-row {
            display: table-row;
        }
        .isian-lauk-cell {
            display: table-cell;
            padding: 2px 3px;
            border: 1px solid #000;
            vertical-align: middle;
        }
        /* Custom widths for isian lauk cells based on image */
        .isian-lauk-cell:nth-child(1) { width: 30%; } /* Nama Lauk */
        .isian-lauk-cell:nth-child(2) { width: 15%; text-align: right;} /* Mentah kg (jika ada) */
        .isian-lauk-cell:nth-child(3) { width: 10%; } /* Satuan Mentah (kg) */
        .isian-lauk-cell:nth-child(4) { width: 15%; text-align: right;} /* Matang kg */
        .isian-lauk-cell:nth-child(5) { width: 10%; } /* Satuan Matang (kg) */
        .isian-lauk-cell:nth-child(6) { width: 10%; text-align: center;} /* % (tidak ada di gambar lauk) */
        .isian-lauk-cell:nth-child(7) { width: 10%; } /* Jenis */

        /* Remove border for specific cells in isian-lauk-grid to match image */
        .no-border-top { border-top: none; }
        .no-border-bottom { border-bottom: none; }
        .no-border-left { border-left: none; }
        .no-border-right { border-right: none; }

        /* New styles for separate tables */
        .table-container {
            width: 49%; /* Half width for two tables side-by-side */
            float: left;
            box-sizing: border-box;
            padding: 0 5px;
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

        /* Styles for Dibuat Oleh / Disetujui Oleh section */
        .signatures-section {
            width: 100%;
            margin-top: 30px; /* Space from tables above */
            display: table;
            table-layout: fixed; /* Ensures columns are equally wide */
        }
        .signature-column {
            display: table-cell;
            width: 50%; /* Each column takes half width */
            text-align: center;
            vertical-align: top;
            padding: 0 10px;
        }
        .signature-label {
            font-weight: bold;
            margin-bottom: 50px; /* Space for signature line. Diatur untuk garis manual. */
            display: block;
        }
        .signature-name {
            font-weight: bold;
            /* text-decoration: underline; */ /* Hapus jika ingin garis manual di bawah */
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
        <p>Perhitungan Memasak Nasi dengan Steamer 2 Pintu (12 Tray)</p>
        <p>Prepared by : Joko Priyo</p>
        <p>Printed on : {{ date('d-m-Y H:i:s') }}</p>
    </div>

    <div class="clearfix">
        <div class="col-float">
            <div class="table-title bold"></div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 20%;">Pilihan Nama Lauk</th>
                        <th style="width: 15%;">Berat Bahan Baku per Box</th>
                        <th style="width: 10%;">Satuan</th>
                        <th style="width: 20%;">Hasil Matang Setelah Penyusutan (kg)</th>
                        <th style="width: 5%;">%</th>
                    </tr>
                </thead>
                <tbody>
                @php
                $i = 0;
                @endphp
                    @foreach($laukmasters_rev as $item)
                    @php
                    $i++;
                    @endphp
                    <tr>
                        <td>{{ $i }}</td>
                        <td>{{ $item->bahan }}</td>
                        {{-- Perbaikan number_format dan pengecekan is_numeric --}}
                        <td class="align-right">
                                {{ number_format($item->isi_per_box, 0,0) }}
                            
                        </td>
                        <td>{{ $item->satuan }}</td>
                        {{-- Perbaikan number_format dan pengecekan is_numeric --}}
                        <td class="align-right">
                            {{ $item->hasil_matang }}
                        </td>
                        {{-- Perbaikan number_format dan pengecekan is_numeric --}}
                        <td class="align-right">
                            
                                {{ $item->penyusutan }}
                            
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
                    <span class="info-label">Jumlah Pax (dan Buffer 1%):</span>
                    <span class="info-value">
                        @if(is_numeric($dataKalkulasi['jumlah_pax_buffer']))
                            {{ number_format((float)$dataKalkulasi['jumlah_pax_buffer'], 0) }}
                        @else
                            {{ $dataKalkulasi['jumlah_pax_buffer'] }}
                        @endif
                    </span>
                    <span class="info-unit small-text">pax</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Pemorsian:</span>
                    <span class="info-value">
                        @if(is_numeric($dataKalkulasi['pemorsian_gram']))
                            {{ number_format((float)$dataKalkulasi['pemorsian_gram'], 0) }}
                        @else
                            {{ $dataKalkulasi['pemorsian_gram'] ?? '' }}
                        @endif
                    </span>
                    <span class="info-unit small-text">gram</span>
                </div>
            </div>

            <div class="info-box">
                <div class="info-box-title">Isian Lauk</div>
                <div class="isian-lauk-grid">
                    <div class="isian-lauk-row">
                        <div class="isian-lauk-cell no-border-right">Bahan</div>
                        <div class="isian-lauk-cell no-border-right">Mentah</div>
                        <div class="isian-lauk-cell no-border-right"></div>
                        <div class="isian-lauk-cell no-border-right">Matang</div>
                        <div class="isian-lauk-cell no-border-right"></div>
                        <div class="isian-lauk-cell no-border-right"></div>
                        <div class="isian-lauk-cell">Jenis</div>
                    </div>
                    <div class="isian-lauk-row">
                        <div class="isian-lauk-cell ">{{ $dataKalkulasi['isian_lauk_ayam_bone_nama'] }}</div>
                        <div class="isian-lauk-cell align-right">
                            @if(is_numeric($dataKalkulasi['isian_lauk_ayam_bone_kg_mentah'] ?? ''))
                                {{ number_format((float)($dataKalkulasi['isian_lauk_ayam_bone_kg_mentah'] ?? ''), 0) }}
                            @endif
                        </div>
                        <div class="isian-lauk-cell ">Kg / Potong</div>
                        <div class="isian-lauk-cell align-right">
                            @if(is_numeric($dataKalkulasi['isian_lauk_ayam_bone_kg_matang']))
                                {{ number_format((float)$dataKalkulasi['isian_lauk_ayam_bone_kg_matang'], 0) }}
                            @else
                                {{ $dataKalkulasi['isian_lauk_ayam_bone_kg_matang'] }}
                            @endif
                        </div>
                        <div class="isian-lauk-cell">Kg / Potong</div>
                        <div class="isian-lauk-cell"></div>
                        <div class="isian-lauk-cell"></div>
                    </div>
                    <div class="isian-lauk-row">
                        <div class="isian-lauk-cell ">{{ $dataKalkulasi['isian_lauk_ayam_giling_nama'] }}</div>
                        <div class="isian-lauk-cell align-right">
                            @if(is_numeric($dataKalkulasi['isian_lauk_ayam_giling_kg_mentah'] ?? ''))
                                {{ number_format((float)($dataKalkulasi['isian_lauk_ayam_giling_kg_mentah'] ?? ''), 0) }}
                            @endif
                        </div>
                        <div class="isian-lauk-cell ">Kg / Potong</div>
                        <div class="isian-lauk-cell align-right">
                            @if(is_numeric($dataKalkulasi['isian_lauk_ayam_giling_kg_matang']))
                                {{ number_format((float)$dataKalkulasi['isian_lauk_ayam_giling_kg_matang'], 0) }}
                            @else
                                {{ $dataKalkulasi['isian_lauk_ayam_giling_kg_matang'] }}
                            @endif
                        </div>
                        <div class="isian-lauk-cell">Kg / Potong</div>
                        <div class="isian-lauk-cell"></div>
                        <div class="isian-lauk-cell"></div>
                    </div>
                    <div class="isian-lauk-row">
                        <div class="isian-lauk-cell no-border-top no-border-bottom"></div>
                        <div class="isian-lauk-cell no-border-top no-border-bottom"></div>
                        <div class="isian-lauk-cell no-border-top no-border-bottom">Kg / Potong</div>
                        <div class="isian-lauk-cell no-border-top no-border-bottom"></div>
                        <div class="isian-lauk-cell no-border-top no-border-bottom"></div>
                        <div class="isian-lauk-cell no-border-top no-border-bottom"></div>
                        <div class="isian-lauk-cell no-border-top no-border-bottom"></div>
                    </div>
                    <div class="isian-lauk-row">
                        <div class="isian-lauk-cell no-border-top no-border-bottom bold">Pilihan Nama Sayuran</div>
                        <div class="isian-lauk-cell no-border-top no-border-bottom"></div>
                        <div class="isian-lauk-cell no-border-top no-border-bottom"></div>
                        <div class="isian-lauk-cell no-border-top no-border-bottom"></div>
                        <div class="isian-lauk-cell no-border-top no-border-bottom"></div>
                        <div class="isian-lauk-cell no-border-top no-border-bottom"></div>
                        <div class="isian-lauk-cell no-border-top no-border-bottom"></div>
                    </div>
                    <div class="isian-lauk-row">
                        <div class="isian-lauk-cell bold">Total</div>
                        <div class="isian-lauk-cell align-right bold">
                            @if(is_numeric($dataKalkulasi['isian_lauk_total_kg_mentah']))
                                {{ number_format((float)$dataKalkulasi['isian_lauk_total_kg_mentah'], 0) }}
                            @else
                                {{ $dataKalkulasi['isian_lauk_total_kg_mentah'] }}
                            @endif
                        </div>
                        <div class="isian-lauk-cell bold">{{ $satuan_bahan_1_fix->satuan  }}</div>
                        <div class="isian-lauk-cell align-right bold">
                            @if(is_numeric($dataKalkulasi['isian_lauk_total_kg_matang']))
                                {{ number_format((float)$dataKalkulasi['isian_lauk_total_kg_matang'], 0) }}
                            @else
                                {{ $dataKalkulasi['isian_lauk_total_kg_matang'] }}
                            @endif
                        </div>
                        <div class="isian-lauk-cell bold">{{ $satuan_bahan_1_fix->satuan  }}</div>
                        <div class="isian-lauk-cell"></div>
                        <div class="isian-lauk-cell"></div>
                    </div>
                </div>
            </div>

            <div class="info-box">
                <div class="info-row">
                    <span class="info-label">Kebutuhan Bahan Baku:</span>
                    <span class="info-value">
                        @if(is_numeric($dataKalkulasi['kebutuhan_bahan_baku_kg']))
                            {{ number_format((float)$dataKalkulasi['kebutuhan_bahan_baku_kg'], 0) }} {{ $satuan_bahan_1_fix->satuan  }}
                        @else
                            {{ $dataKalkulasi['kebutuhan_bahan_baku_kg'] }} {{ $satuan_bahan_1_fix->satuan  }}
                        @endif
                    </span>
                  
                </div>
                <div class="info-row">
                    <span class="info-label">Kebutuhan Bahan Baku Matang:</span>
                    <span class="info-value">
                        @if(is_numeric($dataKalkulasi['kebutuhan_bahan_baku_matang_kali_masak']))
                            {{ number_format((float)$dataKalkulasi['kebutuhan_bahan_baku_matang_kali_masak'], 0) }} {{ $satuan_bahan_1_fix->satuan  }}
                        @else
                            {{ $dataKalkulasi['kebutuhan_bahan_baku_matang_kali_masak'] }} {{ $satuan_bahan_1_fix->satuan  }}
                        @endif
                    </span>
                    
                </div>
                <div class="info-row">
                    <span class="info-label">kali masak :</span>
                    <span class="info-value">
                       {{ $data_rumus_protein_rev->jumlah_masak }}
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
                        <th style="width: 60%;">Lauk</th>
                        <th style="width: 15%;">Jumlah</th>
                        <th style="width: 15%;">Satuan</th>
                    </tr>
                </thead>
                <tbody>
                    <--bahan 1-->
                    <tr>
                        <th style="width: 10%;">1</th>
                        <th style="width: 60%;">{{ $nama1 }}</th>
                        <th style="width: 15%;">{{ number_format(($jumlah_bahan_1_fix->jumlah ?? 0 ) / ($data_rumus_protein_rev->jumlah_masak  ?? 1)) }}</th>
                        <th style="width: 15%;">{{ $satuan_bahan_1_fix->satuan ?? 0 }}</th>
                    </tr>
                    <--bahan 2-->
                    <tr>
                        <th style="width: 10%;">1</th>
                        <th style="width: 60%;">{{ $nama2 }}</th>
                        <th style="width: 15%;">{{ number_format(($jumlah_bahan_2_fix->jumlah ?? 0 ) / ($data_rumus_protein_rev->jumlah_masak  ?? 1)) }}</th>
                        <th style="width: 15%;">{{ $satuan_bahan_2_fix->satuan ?? 0 }}</th>
                    </tr>
                   
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
                        <th style="width: 30%;">Isi box ({{ $satuan_bahan_1_fix->satuan  }} )</th>
                    </tr>
                </thead>
                <tbody>
                    
                    <tr>
                        <td class="align-right">
                            {{ $data_rumus_protein_rev->jumlah_masak }}
                        </td>
                        <td class="align-right">
                            {{ $data_rumus_protein_rev->jumlah_masak }}
                        </td>
                        <td class="align-right">
                            {{ ($jumlah_bahan_1_fix->jumlah ?? 0 ) / (number_format($data_rumus_protein_rev->jumlah_masak  ?? 1)) }}
                        </td>
                    </tr>
                    <tr>
                        <td class="align-right">
                            {{ $data_rumus_protein_rev->jumlah_masak }}
                        </td>
                        <td class="align-right">
                            {{ $data_rumus_protein_rev->jumlah_masak }}
                        </td>
                        <td class="align-right">
                            {{ ($jumlah_bahan_2_fix->jumlah ?? 0 ) / (number_format($data_rumus_protein_rev->jumlah_masak  ?? 1)) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="signatures-section">
        <div class="signature-column">
            <span class="signature-label">Dibuat Oleh:</span>
            <span class="signature-position">Asisten Dapur</span>
        </div>
        <div class="signature-column">
            <span class="signature-label">Disetujui Oleh:</span>
            <span class="signature-position">Kepala Dapur</span>
        </div>    
    </div>

</body>
</html>