<!DOCTYPE html>
<html>
<head>
    <title>Rekap Pendamping</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9px; line-height: 1.2; padding: 2px; } /* line-height 1.2 sudah cukup baik */
        table { width: 100%; border-collapse: collapse; margin-bottom: 8px; font-size: 9px; }
        th, td { border: 1px solid #000; padding: 2px 3px; text-align: left; vertical-align: top; } /* Perbaiki typo "2x 3x" menjadi "2px 3px" */
        th { background-color: #f2f2f2; font-weight: bold; }

        .header-title { text-align: center; font-size: 14px; font-weight: bold; margin-bottom: 10px; } /* Mengurangi margin-bottom */

        /* CSS Baru untuk Header */
        .report-header {
            margin-bottom: 10px; /* Jarak keseluruhan header dengan konten di bawahnya */
        }
        .report-header h3 {
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

        .info-box { border: 1px solid #ccc; padding: 2px; margin-bottom: 5px; }
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

        .col-float { float: left; width: 49%; padding: 0 3px; box-sizing: border-box; }
        .col-float.right { margin-left: 2%; }
        .clearfix::after { content: ""; clear: both; display: table; }

        /* Specific styling for the 'Isian Pendamping' inner table-like structure */
        .isian-pendamping-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }
        .isian-pendamping-row {
            display: table-row;
        }
        .isian-pendamping-cell {
            display: table-cell;
            padding: 2px 3px;
            border: 1px solid #000;
            vertical-align: middle;
        }
        /* Custom widths for isian pendamping cells based on image */
        .isian-pendamping-cell:nth-child(1) { width: 30%; } /* Nama Pendamping */
        .isian-pendamping-cell:nth-child(2) { width: 15%; text-align: right;} /* Mentah (jumlah) */
        .isian-pendamping-cell:nth-child(3) { width: 10%; } /* Satuan Mentah */
        .isian-pendamping-cell:nth-child(4) { width: 15%; text-align: right;} /* Matang (jumlah) */
        .isian-pendamping-cell:nth-child(5) { width: 10%; } /* Satuan Matang */
        .isian-pendamping-cell:nth-child(6) { width: 10%; text-align: center;} /* % (tidak ada di gambar) */
        .isian-pendamping-cell:nth-child(7) { width: 10%; } /* Jenis */

        /* Remove border for specific cells in isian-pendamping-grid to match image */
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
            margin-bottom: 50px; /* Space for signature line */
            display: block;
        }
        .signature-name {
            font-weight: bold;
            text-decoration: underline;
        }
        .signature-position {
            font-size: 8px;
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
                            <th style="width: 25%;">Pilihan Nama Pendamping</th>
                            <th style="width: 15%;">Berat Bahan Baku per Box</th>
                            <th style="width: 10%;">Satuan</th>
                            <th style="width: 15%;">Ket</th>
                            <th style="width: 20%;">Hasil Matang Setelah Penyusutan (kg)</th>
                            <th style="width: 5%;">%</th>
                        </tr>
                    </thead>
                <tbody>
                    @foreach($pendampingMasters as $item)
                    <tr>
                        <td>{{ $item->no }}</td>
                        <td>{{ $item->nama_pendamping }}</td>
                        <td class="align-right">
                            @if(is_numeric($item->berat_bahan_baku_per_box))
                                {{ number_format((float)$item->berat_bahan_baku_per_box, 0) }}
                            @else
                                {{ $item->berat_bahan_baku_per_box }}
                            @endif
                        </td>
                        <td>{{ $item->satuan }}</td>
                        <td>{{ $item->ket }}</td>
                        <td class="align-right">
                            @if(is_numeric($item->hasil_matang_setelah_penyusutan_kg))
                                {{ number_format((float)$item->hasil_matang_setelah_penyusutan_kg, 2) . ' kg' }}
                            @else
                                {{ $item->hasil_matang_setelah_penyusutan_kg }}
                            @endif
                        </td>
                        <td class="align-right">
                            @if(is_numeric($item->persentase_penyusutan))
                                {{ number_format((float)$item->persentase_penyusutan, 0) . '%' }}
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
                    <span class="info-unit small-text">{{ $dataKalkulasi['pemorsian_unit'] ?? '' }}</span>
                </div>
            </div>

            <div class="info-box">
                <div class="info-box-title">Menu Pendamping</div>
                <div class="isian-pendamping-grid">
                    <div class="isian-pendamping-row">
                        <div class="isian-pendamping-cell no-border-right"></div>
                        <div class="isian-pendamping-cell no-border-right">Mentah</div>
                        <div class="isian-pendamping-cell no-border-right"></div>
                        <div class="isian-pendamping-cell no-border-right">Matang</div>
                        <div class="isian-pendamping-cell no-border-right"></div>
                        <div class="isian-pendamping-cell no-border-right"></div>
                        <div class="isian-pendamping-cell">Jenis</div>
                    </div>
                    <div class="isian-pendamping-row">
                        <div class="isian-pendamping-cell ">{{ $dataKalkulasi['isian_pendamping_tempe_nama'] }}</div>
                        <div class="isian-pendamping-cell align-right">
                            @if(is_numeric($dataKalkulasi['isian_pendamping_tempe_jumlah_mentah']))
                                {{ number_format((float)$dataKalkulasi['isian_pendamping_tempe_jumlah_mentah'], 0) }}
                            @else
                                {{ $dataKalkulasi['isian_pendamping_tempe_jumlah_mentah'] }}
                            @endif
                        </div>
                        <div class="isian-pendamping-cell">{{ $dataKalkulasi['isian_pendamping_tempe_satuan_mentah'] }}</div>
                        <div class="isian-pendamping-cell align-right">
                            @if(is_numeric($dataKalkulasi['isian_pendamping_tempe_jumlah_matang']))
                                {{ number_format((float)$dataKalkulasi['isian_pendamping_tempe_jumlah_matang'], 0) }}
                            @else
                                {{ $dataKalkulasi['isian_pendamping_tempe_jumlah_matang'] }}
                            @endif
                        </div>
                        <div class="isian-pendamping-cell">{{ $dataKalkulasi['isian_pendamping_tempe_satuan_matang'] }}</div>
                        <div class="isian-pendamping-cell"></div>
                        <div class="isian-pendamping-cell">{{ $dataKalkulasi['isian_pendamping_tempe_jenis'] }}</div>
                    </div>
                    <div class="isian-pendamping-row">
                        <div class="isian-pendamping-cell no-border-top no-border-bottom"></div>
                        <div class="isian-pendamping-cell no-border-top no-border-bottom"></div>
                        <div class="isian-pendamping-cell no-border-top no-border-bottom"></div>
                        <div class="isian-pendamping-cell no-border-top no-border-bottom"></div>
                        <div class="isian-pendamping-cell no-border-top no-border-bottom"></div>
                        <div class="isian-pendamping-cell no-border-top no-border-bottom"></div>
                        <div class="isian-pendamping-cell no-border-top no-border-bottom"></div>
                    </div>
                    <div class="isian-pendamping-row">
                        <div class="isian-pendamping-cell bold">Total</div>
                        <div class="isian-pendamping-cell align-right bold">
                            @if(is_numeric($dataKalkulasi['isian_pendamping_total_jumlah_mentah']))
                                {{ number_format((float)$dataKalkulasi['isian_pendamping_total_jumlah_mentah'], 0) }}
                            @else
                                {{ $dataKalkulasi['isian_pendamping_total_jumlah_mentah'] }}
                            @endif
                        </div>
                        <div class="isian-pendamping-cell bold">Pcs</div>
                        <div class="isian-pendamping-cell align-right bold">
                            @if(is_numeric($dataKalkulasi['isian_pendamping_total_jumlah_matang']))
                                {{ number_format((float)$dataKalkulasi['isian_pendamping_total_jumlah_matang'], 0) }}
                            @else
                                {{ $dataKalkulasi['isian_pendamping_total_jumlah_matang'] }}
                            @endif
                        </div>
                        <div class="isian-pendamping-cell bold">Pcs</div>
                        <div class="isian-pendamping-cell"></div>
                        <div class="isian-pendamping-cell"></div>
                    </div>
                </div>
            </div>

            <div class="info-box">
                <div class="info-row">
                    <span class="info-label">Kebutuhan Bahan Baku:</span>
                    <span class="info-value">
                        @if(is_numeric($dataKalkulasi['kebutuhan_bahan_baku_total']))
                            {{ number_format((float)$dataKalkulasi['kebutuhan_bahan_baku_total'], 0) }}
                        @else
                            {{ $dataKalkulasi['kebutuhan_bahan_baku_total'] }}
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
                    <span class="info-unit small-text">kali masak</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Kebutuhan tilting:</span>
                    <span class="info-value">
                        @if(is_numeric($dataKalkulasi['kebutuhan_titino_persen']))
                            {{ number_format((float)$dataKalkulasi['kebutuhan_titino_persen'], 2) . '%' }}
                        @else
                            {{ $dataKalkulasi['kebutuhan_titino_persen'] ?? '' }}
                        @endif
                    </span>
                    <span class="info-unit"></span>
                </div>
                 @if ($dataKalkulasi['disamakan_maksimal_selisih_text'])
                    <div style="font-size: 8px; text-align: right; margin-top: 5px;">
                        {{ $dataKalkulasi['disamakan_maksimal_selisih_text'] }}
                    </div>
                 @endif
                 @if (isset($dataKalkulasi['disamakan_maksimal_selisih_value']) && is_numeric($dataKalkulasi['disamakan_maksimal_selisih_value']))
                    <div style="font-size: 9px; text-align: right; margin-top: 3px;">
                        {{ number_format((float)$dataKalkulasi['disamakan_maksimal_selisih_value'], 1) }}
                    </div>
                 @endif
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
                        <th style="width: 50%;">Pendamping</th>
                        <th style="width: 20%;">Jumlah</th>
                        <th style="width: 20%;">Satuan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($resepPenerimaan as $item)
                    <tr>
                        <td>{{ $item->no_resep }}</td>
                        <td>{{ $item->pendamping }}</td>
                        <td class="align-right">
                            @if(is_numeric($item->jumlah))
                                {{ number_format((float)$item->jumlah, 0) }}
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
                        <th style="width: 25%;">Jml Box</th>
                        <th style="width: 25%;">Total Box</th>
                        <th style="width: 30%;">Isi box (Pcs)</th>
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
                            @if(is_numeric($item->total_box))
                                {{ number_format((float)$item->total_box, 0) }}
                            @else
                                {{ $item->total_box }}
                            @endif
                        </td>
                        <td class="align-right">
                            @if(is_numeric($item->isi_box_unit))
                                {{ number_format((float)$item->isi_box_unit, 0) }} {{ $item->isi_box_satuan }}
                            @else
                                {{ $item->isi_box_unit }} {{ $item->isi_box_satuan }}
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