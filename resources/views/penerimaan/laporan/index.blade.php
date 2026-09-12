<!DOCTYPE html>
<html lang="en">

<head>
    <title>{{ $header }}</title>
    @include('Template.head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        @include('Template.navbar')
        @include('Template.left-sidebar')

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 text-dark">{{ $header }}</h1>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between flex-wrap" style="gap: 8px;">
                                <form method="GET" action="{{ route('laporan_penerimaan') }}" class="form-inline">
                                    <label for="tanggal" class="mr-2">Tanggal</label>
                                    <input type="date" id="tanggal" name="tanggal" class="form-control mr-2" value="{{ $tanggal }}">
                                    <button type="submit" class="btn btn-primary">Search</button>
                                </form>
                                <div class="d-flex align-items-center" style="gap:8px;">
                                    <a href="{{ route('stok_opnam_pdf', ['tanggal' => $tanggal]) }}" class="btn btn-secondary">Stok Opnam</a>
                                    @if ($poChecklistId)
                                        <a href="{{ route('cheklist_penerimaan_bgn', $poChecklistId) }}" class="btn btn-info">Checklist Penerimaan</a>
                                    @else
                                        <button type="button" class="btn btn-info" disabled>Checklist Penerimaan</button>
                                    @endif
                                    <a href="{{ route('mastermenu.print-center', ['tanggal' => $tanggal, 'tanggal_awal' => $tanggal, 'tanggal_akhir' => $tanggal, 'id_po' => $poChecklistId]) }}" class="btn btn-dark">Pusat Cetak</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ number_format($totalDirencanakan, 0, ',', '.') }}</h3>
                                    <p>Total Direncanakan (tanggal dipilih)</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>{{ number_format($totalSudahDatang, 0, ',', '.') }}</h3>
                                    <p>Total Sudah Datang (tanggal dipilih)</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>{{ number_format($persentaseDatang, 2, ',', '.') }}%</h3>
                                    <p>Persentase Bahan Datang</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">1) Kedatangan Akan Datang (Belum Datang)</h3>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table id="tbl_akan_datang" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>No PO</th>
                                        <th>Nama Bahan</th>
                                        <th>Qty Pesan</th>
                                        <th>Sudah Datang</th>
                                        <th>Satuan</th>
                                        <th>Waktu Kedatangan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($akanDatang as $index => $row)
                                        @php
                                            $sisaDatang = max(0, (float) $row->jumlah_bahan - (float) $row->total_datang);
                                        @endphp
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $row->nomor_po }}</td>
                                            <td>{{ $row->nama_bahan }}</td>
                                            <td>{{ number_format($row->jumlah_bahan, 0, ',', '.') }}</td>
                                            <td>{{ number_format($row->total_datang, 0, ',', '.') }}</td>
                                            <td>{{ $row->satuan ?? '-' }}</td>
                                            <td>{{ $row->tanggal_kedatangan }}</td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-primary openModalBtn"
                                                    data-idbahan="{{ $row->id }}"
                                                    data-idrincian="{{ $row->id_rincian_bahan }}"
                                                    data-jumlahbahan="{{ number_format($sisaDatang, 0, ',', '.') }} {{ $row->satuan ?? '' }}"
                                                    data-jumlahdatang="0">
                                                    Input Penerimaan
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">2) Kedatangan Sudah Datang</h3>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table id="tbl_sudah_datang" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>No PO</th>
                                        <th>Nama Bahan</th>
                                        <th>Qty Pesan</th>
                                        <th>Sudah Datang</th>
                                        <th>Satuan</th>
                                        <th>Waktu Kedatangan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sudahDatang as $index => $row)
                                        @php
                                            $sisaDatang = max(0, (float) $row->jumlah_bahan - (float) $row->total_datang);
                                        @endphp
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $row->nomor_po }}</td>
                                            <td>{{ $row->nama_bahan }}</td>
                                            <td>{{ number_format($row->jumlah_bahan, 0, ',', '.') }}</td>
                                            <td>{{ number_format($row->total_datang, 0, ',', '.') }}</td>
                                            <td>{{ $row->satuan ?? '-' }}</td>
                                            <td>{{ $row->tanggal_kedatangan }}</td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-primary openModalBtn"
                                                    data-idbahan="{{ $row->id }}"
                                                    data-idrincian="{{ $row->id_rincian_bahan }}"
                                                    data-jumlahbahan="{{ number_format($sisaDatang, 0, ',', '.') }} {{ $row->satuan ?? '' }}"
                                                    data-jumlahdatang="0"
                                                    @if ($sisaDatang <= 0) disabled @endif>
                                                    Input Penerimaan
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">3) Report Jumlah Total Datang pada Tanggal Dipilih (Detail Penerimaan)</h3>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table id="tbl_detail_penerimaan" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>No PO</th>
                                        <th>Nama Bahan</th>
                                        <th>QR Code Wadah</th>
                                        <th>Jumlah Datang</th>
                                        <th>Satuan</th>
                                        <th>Status</th>
                                        <th>Nama Penerima</th>
                                        <th>Waktu Input</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($detailPenerimaanPerHari as $index => $row)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $row->nomor_po }}</td>
                                            <td>{{ $row->nama_bahan }}</td>
                                            <td>{{ $row->qr_code_wadah ?? '-' }}</td>
                                            <td>{{ number_format($row->jumlah_datang, 0, ',', '.') }}</td>
                                            <td>{{ $row->satuan ?? '-' }}</td>
                                            <td>
                                                @if ((int) $row->status === 1)
                                                    Lolos
                                                @elseif ((int) $row->status === 2)
                                                    Kurang
                                                @else
                                                    Tidak
                                                @endif
                                            </td>
                                            <td>{{ $row->nama_penerima ?? '-' }}</td>
                                            <td>{{ \Carbon\Carbon::parse($row->created_at)->format('d-m-Y H:i') }}</td>
                                            <td>
                                                @if (auth()->check() && in_array(auth()->user()->level, ['backoffice', 'admin'], true))
                                                    <button type="button" class="btn btn-sm btn-warning openEditModalBtn"
                                                        data-idpenerimaan="{{ $row->id_penerimaan }}"
                                                        data-idbahan="{{ $row->id_barang_po }}"
                                                        data-idrincian="{{ $row->id_rincian_bahan }}"
                                                        data-jumlahbahan="{{ number_format($row->jumlah_bahan, 0, ',', '.') }} {{ $row->satuan ?? '' }}"
                                                        data-jumlahdatang="{{ $row->jumlah_datang }}"
                                                        data-qrcode="{{ $row->qr_code_wadah }}"
                                                        data-status="{{ $row->status }}"
                                                        data-keterangan="{{ $row->keterangan }}"
                                                        data-namapenerima="{{ $row->nama_penerima }}">
                                                        Koreksi
                                                    </button>
                                                @endif
                                                @if ((int) $row->sudah_masuk_gudang === 0)
                                                    <button type="button" class="btn btn-sm btn-danger btnDeletePenerimaan" data-id="{{ $row->id_penerimaan }}">
                                                        Hapus
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">4) Masuk Gudang</h3>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table id="tbl_masuk_gudang" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kode Wadah</th>
                                        <th>No PO</th>
                                        <th>Nama Barang</th>
                                        <th>Stok Barang</th>
                                        <th>Satuan</th>
                                        <th>Tanggal Masuk</th>
                                        <th>Tanggal Akan Keluar</th>
                                        <th>Lokasi</th>
                                        <th>Keterangan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($masukGudang as $index => $row)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $row->kode_wadah ?? '-' }}</td>
                                            <td>{{ $row->nomor_po ?? '-' }}</td>
                                            <td>
                                                @if (!empty($row->nama_barang))
                                                    {{ $row->nama_barang }}
                                                    @if ($row->show_group_actions && $row->group_size > 1)
                                                        <span class="badge badge-info">{{ $row->group_size }} item</span>
                                                    @endif
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ number_format($row->jumlah, 0, ',', '.') }}</td>
                                            <td>{{ $row->satuan ?? '-' }}</td>
                                            <td>{{ $row->tanggal_masuk ?? '-' }}</td>
                                            <td>{{ $row->tanggal_akan_keluar ?? '-' }}</td>
                                            <td>{{ $row->lokasi ?? '-' }}</td>
                                            <td>{{ $row->keterangan ?? '-' }}</td>
                                            <td>
                                                @if (auth()->check() && in_array(auth()->user()->level, ['backoffice', 'admin'], true))
                                                    <button
                                                        type="button"
                                                        class="btn btn-sm btn-warning btnEditStokGudang"
                                                        data-id="{{ $row->id }}"
                                                        data-namabarang="{{ $row->nama_barang }}"
                                                        data-jumlah="{{ $row->jumlah }}"
                                                        data-satuan="{{ $row->satuan }}">
                                                        Edit Stok
                                                    </button>
                                                    @if ($row->show_group_actions)
                                                        <button
                                                            type="button"
                                                            class="btn btn-sm btn-primary btnEditSatuanGudang mt-1"
                                                            data-itemids="{{ $row->group_item_ids }}"
                                                            data-namabarang="{{ $row->nama_barang }}"
                                                            data-idsatuan="{{ $row->id_satuan }}">
                                                            Edit Satuan
                                                        </button>
                                                    @endif
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalLabel">Input Penerimaan</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="updateForm">
                        <div class="modal-body">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" id="id_penerimaan" name="id_penerimaan">
                            <input type="hidden" id="idbahan" name="idbahan">
                            <input type="hidden" id="idrincian" name="idrincian">

                            <div class="form-group">
                                <label>QR Code Wadah</label>
                                <input type="text" class="form-control" id="qrcode_wadah" name="qrcode_wadah" required>
                                <div class="mt-2">
                                    <button type="button" id="btnStartScan" class="btn btn-sm btn-outline-primary">Scan Barcode via HP</button>
                                    <button type="button" id="btnStopScan" class="btn btn-sm btn-outline-secondary" style="display:none;">Stop Scan</button>
                                </div>
                                <div id="qr-reader" style="width:100%; display:none; margin-top:10px;"></div>
                            </div>
                            <div class="form-group">
                                <label>Jumlah Bahan Yang Dipesan</label>
                                <input type="text" class="form-control" id="jumlah_bahan" name="jumlah_bahan" readonly>
                            </div>
                            <div class="form-group">
                                <label>Jumlah Bahan Yang Datang</label>
                                <input type="number" class="form-control" id="jumlah_datang" name="jumlah_datang" required>
                            </div>
                            <div class="form-group" hidden>
                                <label>Jumlah Berat Bahan</label>
                                <input type="number" class="form-control" id="jumlah_berat" name="jumlah_berat" value="0" required>
                            </div>
                            <div class="form-group" hidden>
                                <label>Satuan Berat Bahan</label>
                                <select class="form-control" id="satuan_berat" name="satuan_berat" required>
                                    @foreach ($satuan as $data)
                                        <option value="{{ $data->id }}">{{ $data->satuan }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Status</label>
                                <select class="form-control" id="status" name="status" required>
                                    <option value="1">Lolos</option>
                                    <option value="0">Tidak</option>
                                    <option value="2">Kurang</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Keterangan</label>
                                <textarea class="form-control" id="keterangan" name="keterangan"></textarea>
                            </div>
                            <div class="form-group">
                                <label>Nama Penerima</label>
                                <input type="text" class="form-control" id="nama_penerima" name="nama_penerima" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="editStokGudangModal" tabindex="-1" aria-labelledby="editStokGudangLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editStokGudangLabel">Edit Stok Gudang</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="editStokGudangForm">
                        <div class="modal-body">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" id="edit_stok_id" name="id">
                            <div class="form-group">
                                <label>Nama Barang</label>
                                <input type="text" class="form-control" id="edit_stok_nama_barang" readonly>
                            </div>
                            <div class="form-group">
                                <label>Jumlah / Stok Barang</label>
                                <input type="number" step="0.01" min="0" class="form-control" id="edit_stok_jumlah" name="jumlah" required>
                            </div>
                            <div class="form-group">
                                <label>Satuan Saat Ini</label>
                                <input type="text" class="form-control" id="edit_stok_satuan" readonly>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="editSatuanGudangModal" tabindex="-1" aria-labelledby="editSatuanGudangLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editSatuanGudangLabel">Edit Satuan Gudang</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="editSatuanGudangForm">
                        <div class="modal-body">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" id="edit_satuan_item_ids" name="item_ids">
                            <div class="form-group">
                                <label>Nama Barang</label>
                                <input type="text" class="form-control" id="edit_satuan_nama_barang" readonly>
                            </div>
                            <div class="form-group">
                                <label>Satuan</label>
                                <select class="form-control" id="edit_satuan_id_satuan" name="id_satuan" required>
                                    @foreach ($satuan as $data)
                                        <option value="{{ $data->id }}">{{ $data->satuan }}</option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Perubahan ini berlaku untuk semua stok aktif dengan nama barang yang sama pada grup ini.</small>
                                <small class="form-text text-muted">Edit satuan tidak mengubah angka stok. Koreksi jumlah tetap dilakukan per item.</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @include('Template.footer')
    </div>

    @include('Template.script')
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script src="{{ asset('AdminLte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            let html5QrCode = null;
            let isScanning = false;
            let formMode = 'create';

            function initLaporanTable(selector) {
                if (!$.fn.DataTable.isDataTable(selector)) {
                    $(selector).DataTable({
                        pageLength: 10,
                        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                        searching: true,
                        ordering: false,
                        info: true,
                        autoWidth: false,
                        language: {
                            search: 'Search:',
                            lengthMenu: 'Tampilkan _MENU_ data',
                            info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                            infoEmpty: 'Menampilkan 0 sampai 0 dari 0 data',
                            zeroRecords: 'Data tidak ditemukan',
                            paginate: {
                                first: 'Pertama',
                                last: 'Terakhir',
                                next: 'Berikutnya',
                                previous: 'Sebelumnya'
                            }
                        }
                    });
                }
            }

            initLaporanTable('#tbl_akan_datang');
            initLaporanTable('#tbl_sudah_datang');
            initLaporanTable('#tbl_detail_penerimaan');
            initLaporanTable('#tbl_masuk_gudang');

            $('#edit_satuan_id_satuan').select2({
                width: '100%',
                dropdownParent: $('#editSatuanGudangModal'),
                placeholder: '-- Pilih Satuan --'
            });

            function stopScanner() {
                if (html5QrCode && isScanning) {
                    html5QrCode.stop().then(function () {
                        isScanning = false;
                        $('#btnStartScan').show();
                        $('#btnStopScan').hide();
                        $('#qr-reader').hide().empty();
                    }).catch(function () {
                        isScanning = false;
                        $('#btnStartScan').show();
                        $('#btnStopScan').hide();
                        $('#qr-reader').hide().empty();
                    });
                }
            }

            $('#btnStartScan').on('click', function () {
                if (typeof Html5Qrcode === 'undefined') {
                    Swal.fire('Info', 'Fitur scan kamera belum termuat. Coba refresh halaman.', 'info');
                    return;
                }

                if (isScanning) {
                    return;
                }

                $('#qr-reader').show();
                $('#btnStartScan').hide();
                $('#btnStopScan').show();

                html5QrCode = new Html5Qrcode('qr-reader');
                html5QrCode.start(
                    { facingMode: 'environment' },
                    { fps: 10, qrbox: { width: 250, height: 250 } },
                    function (decodedText) {
                        $('#qrcode_wadah').val(decodedText).trigger('change');
                        stopScanner();
                    },
                    function () {}
                ).then(function () {
                    isScanning = true;
                }).catch(function () {
                    $('#btnStartScan').show();
                    $('#btnStopScan').hide();
                    $('#qr-reader').hide().empty();
                    Swal.fire('Gagal', 'Kamera tidak bisa diakses. Izinkan kamera di browser HP.', 'error');
                });
            });

            $('#btnStopScan').on('click', function () {
                stopScanner();
            });

            $(document).on('click', '.openModalBtn', function () {
                formMode = 'create';
                $('#modalLabel').text('Input Penerimaan');
                $('#id_penerimaan').val('');
                $('#idbahan').val($(this).data('idbahan'));
                $('#idrincian').val($(this).data('idrincian'));
                $('#jumlah_bahan').val($(this).data('jumlahbahan'));
                $('#jumlah_datang').val($(this).data('jumlahdatang'));
                $('#qrcode_wadah').val('');
                $('#status').val('1');
                $('#keterangan').val('');
                $('#nama_penerima').val('');
                $('#updateModal').modal('show');
            });

            $(document).on('click', '.openEditModalBtn', function () {
                formMode = 'edit';
                $('#modalLabel').text('Koreksi Penerimaan');
                $('#id_penerimaan').val($(this).data('idpenerimaan'));
                $('#idbahan').val($(this).data('idbahan'));
                $('#idrincian').val($(this).data('idrincian'));
                $('#jumlah_bahan').val($(this).data('jumlahbahan'));
                $('#jumlah_datang').val($(this).data('jumlahdatang'));
                $('#qrcode_wadah').val($(this).data('qrcode'));
                $('#status').val(String($(this).data('status')));
                $('#keterangan').val($(this).data('keterangan'));
                $('#nama_penerima').val($(this).data('namapenerima'));
                $('#updateModal').modal('show');
            });

            $(document).on('click', '.btnEditStokGudang', function () {
                $('#edit_stok_id').val($(this).data('id'));
                $('#edit_stok_nama_barang').val($(this).data('namabarang'));
                $('#edit_stok_jumlah').val($(this).data('jumlah'));
                $('#edit_stok_satuan').val($(this).data('satuan'));
                $('#editStokGudangModal').modal('show');
            });

            $(document).on('click', '.btnEditSatuanGudang', function () {
                $('#edit_satuan_item_ids').val($(this).data('itemids'));
                $('#edit_satuan_nama_barang').val($(this).data('namabarang'));
                $('#edit_satuan_id_satuan').val(String($(this).data('idsatuan'))).trigger('change');
                $('#editSatuanGudangModal').modal('show');
            });

            $('#updateModal').on('hidden.bs.modal', function () {
                stopScanner();
            });

            $('#updateForm').submit(function (e) {
                e.preventDefault();
                const submitUrl = formMode === 'edit'
                    ? "{{ route('koreksi_penerimaan_bahan') }}"
                    : "{{ route('simpan_penerimaan_bahan') }}";

                $.ajax({
                    url: submitUrl,
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function (response) {
                        Swal.fire('Berhasil!', response.message, 'success');
                        $('#updateModal').modal('hide');
                        $('#updateForm')[0].reset();
                        window.location.reload();
                    },
                    error: function (xhr) {
                        let msg = 'Terjadi kesalahan saat menyimpan data.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        Swal.fire('Gagal!', msg, 'error');
                    }
                });
            });

            $('#editStokGudangForm').submit(function (e) {
                e.preventDefault();

                $.ajax({
                    url: "{{ route('laporan_penerimaan.update_stok_gudang') }}",
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function (response) {
                        Swal.fire('Berhasil!', response.message, 'success');
                        $('#editStokGudangModal').modal('hide');
                        window.location.reload();
                    },
                    error: function (xhr) {
                        let msg = 'Terjadi kesalahan saat memperbarui stok barang.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        Swal.fire('Gagal!', msg, 'error');
                    }
                });
            });

            $('#editSatuanGudangForm').submit(function (e) {
                e.preventDefault();

                $.ajax({
                    url: "{{ route('laporan_penerimaan.update_satuan_gudang') }}",
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function (response) {
                        Swal.fire('Berhasil!', response.message, 'success');
                        $('#editSatuanGudangModal').modal('hide');
                        window.location.reload();
                    },
                    error: function (xhr) {
                        let msg = 'Terjadi kesalahan saat memperbarui satuan barang.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        Swal.fire('Gagal!', msg, 'error');
                    }
                });
            });

            $(document).on('click', '.btnDeletePenerimaan', function () {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Yakin hapus data penerimaan?',
                    text: 'Data yang dihapus tidak bisa dikembalikan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('/destroy-bahan') }}/" + id,
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function () {
                                Swal.fire('Berhasil!', 'Data penerimaan berhasil dihapus', 'success').then(() => {
                                    window.location.reload();
                                });
                            },
                            error: function () {
                                Swal.fire('Gagal!', 'Gagal menghapus data penerimaan', 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
</body>

</html>
