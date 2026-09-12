<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">
<head>
    <title>{{ $header }}</title>
    @include('Template.head')
    <style>
        .form-control {
            height: 40px; /* Sesuaikan tinggi */
            width: 100%; /* Pastikan width full */
        }
        
        .select2-container .select2-selection--single {
            height: 40px !important; /* Samakan dengan input lainnya */
            padding: 5px;
            display: flex;
            align-items: center;
        }
        
        .select2-selection__rendered {
            line-height: 30px !important;
        }

        /* AI Suggestion Styling */
        .alert-suggestion {
            background-color: #e7f3ff;
            border: 1px solid #b3d9ff;
            border-radius: 5px;
            padding: 12px;
        }

        .alert-suggestion .badge-primary {
            font-size: 0.75em;
            padding: 4px 8px;
            text-transform: uppercase;
        }

        #add-suggestion-qty {
            color: #28a745;
            font-weight: bold;
        }

        .btn-success {
            transition: all 0.3s ease;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
    </style>
    <!-- Tambahkan di dalam <head> pada file blade Anda -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">

        <!-- Navbar -->
        @include('Template.navbar')
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        @include('Template.left-sidebar')

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 text-dark" id="currentTime">Starter Page</h1>
                        </div><!-- /.col -->
                        
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                    <div class="col-12">
                        <div class="card">
                        <div class="card-header">
                             
                          <div class="row invoice-info">
                                <div class="col-sm-6 ">
                                <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b>Dapur</b> <a class="float-right"> {{ $dapur->nama_dapur }}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Tanggal pengajuan</b> <a class="float-right">{{ $tanggal_pengajuan }}</a>
                                </li>
                              
                                </ul>
                                <h3 class="card-title">
                                <!-- button -->
                                 <a href="{{ route('pengajuan_po.index') }}"  class="btn btn-primary flex-fill" id="btn-edit-post">Kembali</a>
                                </h3>
                                
                                </div>
                                <!-- /.col -->
                                <div class="col-sm-6 invoice-col">
                                <ul class="list-group list-group-unbordered mb-3">
                                
                                <li class="list-group-item">
                                    <b>Nama Supplier </b> <a class="float-right">{{ $supplier->nama_supplier }}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Tanggal Menu </b> <a class="float-right">{{ $tanggal_menu	  }}</a>
                                </li>

                                </ul>
                                
                                </div>
                                <!-- /.col -->
                                
                                <!-- /.col -->
                            </div>

                            <br>
                            <div class="row invoice-info">
                                
                                <!-- /.col -->
                                <div class="col-sm-4 invoice-col">
                                    <ul class="list-group list-group-unbordered mb-3">
                                    
                                        <li class="list-group-item">
                                            <b>Karbohidrat </b> <a class="float-right">{{ $karbohidrat }}</a>
                                        </li>
                                    
                                    </ul>
                                
                                </div>
                                <div class="col-sm-4 invoice-col">
                                    <ul class="list-group list-group-unbordered mb-3">
                                    
                                        <li class="list-group-item">
                                            <b>Lauk </b> <a class="float-right">{{ $protein }}</a>
                                        </li>
                                    
                                    </ul>
                                
                                </div>
                                <div class="col-sm-4 invoice-col">
                                    <ul class="list-group list-group-unbordered mb-3">
                                    
                                        <li class="list-group-item">
                                            <b>Sayur </b> <a class="float-right">{{ $sayur }}</a>
                                        </li>
                                    
                                    </ul>
                                
                                </div>
                                <div class="col-sm-4 invoice-col">
                                    <ul class="list-group list-group-unbordered mb-3">
                                    
                                        <li class="list-group-item">
                                            <b>Buah </b> <a class="float-right">{{ $buah }}</a>
                                        </li>
                                    
                                    </ul>
                                
                                </div>
                                <div class="col-sm-4 invoice-col">
                                    <ul class="list-group list-group-unbordered mb-3">
                                    
                                        <li class="list-group-item">
                                            <b>Pendamping </b> <a class="float-right">{{ $susu }}</a>
                                        </li>
                                    
                                    </ul>
                                
                                </div>
                                <!-- /.col -->
                                
                                <!-- /.col -->
                            </div>
                        </div>
                        <!-- /.card-header -->
                       
                        </div>
                        <!-- /.card -->
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h1 class="mb-0">{{ $header }}</h1>
                                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#addModal">
                                        <i class="fas fa-plus"></i> Tambah Bahan
                                    </button>
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body table-responsive">
                                
                                <table id="tbl_list_barang" class="table table-bordered table-hover " style="width: 100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Bahan</th>
                                        <th>Jumlah Bahan</th>
                                        <th>Jumlah PO</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                </table>
                            </div>
                        <!-- /.card-body -->
                        </div>
                        
                    </div>
                    <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.container-fluid -->
                </section>
        </div>
        <!-- /.content-wrapper -->
        <!-- Modal Tambah -->
        <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addModalLabel">Tambah Bahan PO</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="addForm">
                            <input type="hidden" id="add-id-po" value="{{ $po->id }}">
                            <div class="mb-3">
                                <label class="form-label">Bahan</label>
                                <select class="form-control select2" id="add-bahan" name="add_bahan" required>
                                    <option value="">-- Pilih Bahan --</option>
                                    @foreach($bahan_list as $bahan)
                                        <option value="{{ $bahan->id }}" data-satuan="{{ $bahan->satuan_bahan }}">{{ $bahan->bahan }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @if($is_po_bahan_pangan)
                            <input type="hidden" id="add-id-menu-harian" value="{{ $id_menu_harian }}">
                            <div class="mb-3">
                                <label class="form-label">Menu</label>
                                <select class="form-control select2" id="add-menu-tipe" name="add_menu_tipe" {{ empty($menu_tipe_options) ? '' : 'required' }}>
                                    <option value="">-- Pilih Menu --</option>
                                    @foreach($menu_tipe_options as $menuOption)
                                        <option value="{{ $menuOption['key'] }}"
                                                data-resep-id="{{ $menuOption['resep_id'] }}"
                                                data-tanggal-kedatangan="{{ $menuOption['tanggal_kedatangan'] }}"
                                                data-tanggal-digunakan="{{ $menuOption['tanggal_digunakan'] }}">
                                            {{ $menuOption['label'] }}
                                        </option>
                                    @endforeach
                                </select>
                                @if(empty($menu_tipe_options))
                                    <small class="text-danger">Data menu tidak ditemukan untuk PO ini, data tetap bisa disimpan sebagai non rincian menu.</small>
                                @endif
                            </div>
                            @endif
                            <!-- AI Quantity Suggestion Container -->
                            <div id="add-suggestion-container" class="mb-3" style="display: none;">
                                <div class="alert alert-info alert-suggestion" role="alert">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div style="flex: 1;">
                                            <strong><i class="fas fa-lightbulb"></i> Saran AI:</strong>
                                            <p class="mb-2">
                                                <span class="badge badge-primary" id="add-suggestion-confidence">-</span>
                                                Saran jumlah: <strong id="add-suggestion-qty" style="font-size: 1.2em; color: #28a745;">-</strong>
                                            </p>
                                            <small id="add-suggestion-reason" class="text-muted d-block mb-2">-</small>
                                            <div id="add-suggestion-details" style="font-size: 0.9em; margin-top: 8px; display: none;">
                                                <small class="d-block">Analisis: <span id="add-suggestion-analysis-text">-</span></small>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-success ml-2" id="add-apply-suggestion" onclick="applyAddSuggestion()">
                                            Terapkan
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Jumlah Bahan</label>
                                        <input type="number" step="0.01" class="form-control" id="add-jumlah-bahan" name="add_jumlah_bahan" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Satuan</label>
                                        <select class="form-control select2" id="add-satuan" name="add_satuan" required>
                                            @foreach($satuan_list as $sat)
                                                <option value="{{ $sat->id }}">{{ $sat->satuan }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Jumlah Box</label>
                                <input type="number" class="form-control" id="add-jumlah-box" name="add_jumlah_box" value="1" min="1" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Harga Satuan</label>
                                <input type="number" class="form-control" id="add-harga" name="add_harga" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Total Harga</label>
                                <input type="number" class="form-control" id="add-jumlah-po" name="add_jumlah_po" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tanggal Kedatangan</label>
                                <input type="datetime-local" class="form-control" id="add-tanggal-kedatangan" name="add_tanggal_kedatangan" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tanggal Digunakan</label>
                                <input type="date" class="form-control" id="add-tanggal-digunakan" name="add_tanggal_digunakan" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Keterangan</label>
                                <input type="text" class="form-control" id="add-keterangan" name="add_keterangan">
                            </div>
                            <button type="submit" class="btn btn-success">Simpan Data</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Edit -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Bahan PO</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editForm">
                            <input type="hidden" id="edit-id">
                            <div class="mb-3">
                                <label class="form-label">Bahan</label>
                                <input type="text" class="form-control" id="edit-bahan" name="edit_bahan" required readonly>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Jumlah Bahan</label>
                                        <input type="number" step="0.01" class="form-control" id="edit-jumlah-bahan" name="edit_jumlah_bahan" required>
                                        <input type="hidden" id="edit-jumlah-bahan-base">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Satuan</label>
                                        <select class="form-control select2" id="edit-satuan" name="edit_satuan" required>
                                            @foreach($satuan_list as $sat)
                                                <option value="{{ $sat->id }}">{{ $sat->satuan }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Jumlah Box</label>
                                <input type="number" class="form-control" id="edit-jumlah-box" name="edit_jumlah_box" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Harga Satuan</label>
                                <input type="number" class="form-control" id="edit-harga" name="edit_harga" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Total Harga</label>
                                <input type="number" class="form-control" id="edit-jumlah-po" name="edit_jumlah_po" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tanggal Kedatangan</label>
                                <input type="datetime-local" class="form-control" id="edit-tanggal-kedatangan" name="edit_tanggal_kedatangan" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tanggal Digunakan</label>
                                <input type="date" class="form-control" id="edit-tanggal-digunakan" name="edit_tanggal_digunakan" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Keterangan</label>
                                <input type="text" class="form-control" id="edit-keterangan" name="edit_keterangan" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
            <div class="p-3">
                <h5>Title</h5>
                <p>Sidebar content</p>
            </div>
        </aside>
        <!-- /.control-sidebar -->
        
        
        <!-- Main Footer -->
        @include('Template.footer')
    </div>
    <!-- ./wrapper -->

    <!-- REQUIRED SCRIPTS -->
  
    @include('Template.script')
   <script src="{{ asset('AdminLte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script type="text/javascript">
        var Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });

        var table = $('#tbl_list_barang').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ url("pengajuan_po/edit/dt_bahan_po") }}'+'/'+{{ $po->id }},
                
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
               
                {data: 'Bahan_baku', name: 'Bahan_baku'},
                {data: 'Jumlah_Bahan_baku', name: 'Jumlah_Bahan_baku'},
                {data: 'Jumlah_PO_bahan_baku', name: 'Jumlah_PO_bahan_baku'},
                {data: 'action', name: 'action', orderable: false, searchable: false}, // Aksi (tombol)
            ]
        
    
        });

    // Inisialisasi Select2 untuk dropdown di modal
    $('#add-bahan').select2({
        dropdownParent: $('#addModal'),
        width: '100%',
        placeholder: '-- Pilih Bahan --'
    });

    $('#add-satuan').select2({
        dropdownParent: $('#addModal'),
        width: '100%'
    });

    if ($('#add-menu-tipe').length) {
        $('#add-menu-tipe').select2({
            dropdownParent: $('#addModal'),
            width: '100%',
            placeholder: '-- Pilih Menu --'
        });

        $('#add-menu-tipe').on('change', function() {
            var selectedOption = $(this).find(':selected');
            var tanggalKedatangan = selectedOption.data('tanggal-kedatangan');
            var tanggalDigunakan = selectedOption.data('tanggal-digunakan');

            if (tanggalKedatangan) {
                $('#add-tanggal-kedatangan').val(tanggalKedatangan);
            }
            if (tanggalDigunakan) {
                $('#add-tanggal-digunakan').val(tanggalDigunakan);
            }
        });
    }

    $('#edit-satuan').select2({
        dropdownParent: $('#editModal'),
        width: '100%'
    });

    function updateAddTotalHarga() {
        var jumlahBahan = parseFloat($('#add-jumlah-bahan').val()) || 0;
        var hargaSatuan = parseFloat($('#add-harga').val()) || 0;
        var totalHarga = jumlahBahan * hargaSatuan;
        $('#add-jumlah-po').val(Math.round(totalHarga));
    }

    $('#add-jumlah-bahan, #add-harga').on('input', function() {
        updateAddTotalHarga();
    });

    $('#add-bahan').on('change', function() {
        var defaultSatuan = $(this).find(':selected').data('satuan');
        if (defaultSatuan) {
            $('#add-satuan').val(defaultSatuan).trigger('change');
        }
    });

    $('#addForm').on('submit', function(e) {
        e.preventDefault();

        var selectedMenuOption = $('#add-menu-tipe').length ? $('#add-menu-tipe').find(':selected') : null;

        var formData = {
            id_po: $('#add-id-po').val(),
            id_bahan: $('#add-bahan').val(),
            jumlah_bahan: $('#add-jumlah-bahan').val(),
            satuan: $('#add-satuan').val(),
            harga: $('#add-harga').val(),
            jumlah_box: $('#add-jumlah-box').val(),
            tanggal_kedatangan: $('#add-tanggal-kedatangan').val(),
            tanggal_digunakan: $('#add-tanggal-digunakan').val(),
            keterangan: $('#add-keterangan').val(),
            id_menu_harian: $('#add-id-menu-harian').length ? $('#add-id-menu-harian').val() : null,
            menu_tipe: $('#add-menu-tipe').length ? $('#add-menu-tipe').val() : null,
            menu_resep_id: selectedMenuOption ? selectedMenuOption.data('resep-id') : null,
        };

        $.ajax({
            url: '/pengajuan_po/store_data',
            method: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#addModal').modal('hide');
                $('#addForm')[0].reset();
                $('#add-jumlah-po').val('');
                Swal.fire({
                    icon: 'success',
                    title: 'BERHASIL',
                    text: 'Data bahan PO berhasil ditambahkan',
                    showConfirmButton: false,
                    timer: 2000
                });
                $('#tbl_list_barang').DataTable().ajax.reload();
            },
            error: function(xhr) {
                let message = 'Terjadi kesalahan saat menambahkan data';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                Swal.fire('Error', message, 'error');
            }
        });
    });

    // Helper: kompatibel untuk SweetAlert2 versi lama dan baru
    function isSwalConfirmed(result) {
        return (typeof result.isConfirmed !== 'undefined' && result.isConfirmed) || result.value === true;
    }

    // Helper: ambil pesan error terbaik dari response AJAX
    function getAjaxErrorMessage(xhr, defaultMessage) {
        if (xhr.responseJSON && (xhr.responseJSON.error || xhr.responseJSON.message)) {
            return xhr.responseJSON.error || xhr.responseJSON.message;
        }
        return defaultMessage;
    }

    // Aksi delete bahan: konfirmasi SweetAlert -> hapus via AJAX -> reload DataTables saja
    $(document).on('click', '.btn-delete-bahan', function(e) {
        e.preventDefault();

        var $button = $(this);
        var deleteUrl = $button.attr('href');

        if (!deleteUrl) {
            Swal.fire('Error', 'URL hapus tidak ditemukan', 'error');
            return;
        }

        Swal.fire({
            title: 'Hapus bahan?',
            text: 'Data bahan akan dihapus permanen.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (!isSwalConfirmed(result)) {
                return;
            }

            $button.addClass('disabled').attr('aria-disabled', 'true');

            $.ajax({
                url: deleteUrl,
                type: 'GET',
                headers: {
                    'Accept': 'application/json'
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message || 'Data bahan berhasil dihapus',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    table.ajax.reload(null, false);
                },
                error: function(xhr) {
                    Swal.fire('Error', getAjaxErrorMessage(xhr, 'Gagal menghapus data bahan'), 'error');
                },
                complete: function() {
                    $button.removeClass('disabled').removeAttr('aria-disabled');
                }
            });
        });
    });

    // Fungsi untuk membuka modal dan memuat data ke dalam form
    function editModal(id) {
        // Menggunakan AJAX untuk mengambil data berdasarkan ID
        $.ajax({
            url: '/pengajuan_po//edit/get_data/' + id, // Ganti dengan URL endpoint yang sesuai
            method: 'GET',
            success: function(response) {
                // Memuat data ke dalam form modal
                $('#edit-id').val(response.id);
                $('#edit-bahan').val(response.bahan);
                $('#edit-jumlah-bahan').val(parseFloat(response.jumlah_bahan).toFixed(2));
                $('#edit-jumlah-bahan-base').val(response.jumlah_bahan); // Simpan nilai asli
                $('#edit-jumlah-po').val(response.jumlah_po);
                $('#edit-tanggal-kedatangan').val(response.tanggal_kedatangan);
                $('#edit-tanggal-digunakan').val(response.tanggal_digunakan);
                $('#edit-harga').val(response.harga);
                $('#edit-jumlah-box').val(response.jumlah_box);
                $('#edit-satuan').val(response.satuan).trigger('change');
                $('#edit-keterangan').val(response.keterangan);
                
                // Menampilkan modal
                $('#editModal').modal('show');
            },
            error: function(xhr, status, error) {
                alert("Terjadi kesalahan saat mengambil data!");
            }
        });
    }

    // Konversi satuan mapping
    const unitConversions = {
        // gram to kg
        'gram': { 'kg': 0.001, 'gram': 1 },
        'kg': { 'gram': 1000, 'kg': 1 },
        // ml to liter
        'ml': { 'liter': 0.001, 'ml': 1 },
        'mililiter': { 'liter': 0.001, 'mililiter': 1 },
        'liter': { 'ml': 1000, 'mililiter': 1000, 'liter': 1 },
        // satuan yang tidak ada konversi
        'pcs': { 'pcs': 1 },
        'potong': { 'potong': 1 },
        'buah': { 'buah': 1 },
        'butir': { 'butir': 1 },
        'karung': { 'karung': 1 }
    };

    // Function untuk mendapatkan nama satuan dari ID
    function getSatuanName(satuanId) {
        const satuanOptions = $('#edit-satuan option');
        return satuanOptions.filter('[value="' + satuanId + '"]').text().toLowerCase();
    }

    // Function untuk melakukan konversi
    function convertUnit(value, fromUnit, toUnit) {
        fromUnit = fromUnit.toLowerCase();
        toUnit = toUnit.toLowerCase();
        
        // Jika satuan sama, tidak perlu konversi
        if (fromUnit === toUnit) {
            return value;
        }
        
        // Cek apakah ada konversi untuk satuan ini
        if (unitConversions[fromUnit] && unitConversions[fromUnit][toUnit]) {
            return value * unitConversions[fromUnit][toUnit];
        }
        
        // Jika tidak ada konversi, kembalikan nilai asli
        return value;
    }

    // Handle perubahan satuan
    let currentSatuan = null;
    
    $('#editModal').on('shown.bs.modal', function() {
        currentSatuan = $('#edit-satuan').val();
    });

    $('#edit-satuan').on('change', function() {
        const baseValue = parseFloat($('#edit-jumlah-bahan-base').val()) || 0;
        const baseSatuan = getSatuanName(currentSatuan);
        const newSatuan = getSatuanName($(this).val());
        
        // Konversi nilai dari satuan awal ke satuan baru
        const convertedValue = convertUnit(baseValue, baseSatuan, newSatuan);
        $('#edit-jumlah-bahan').val(convertedValue.toFixed(2));
        
        // Update nilai base untuk konversi berikutnya
        $('#edit-jumlah-bahan-base').val(convertedValue);
        currentSatuan = $(this).val();
        
        // Hitung ulang total harga
        updateTotalHarga();
    });

    // Function untuk update total harga
    function updateTotalHarga() {
        var jumlahBahan = parseFloat($('#edit-jumlah-bahan').val()) || 0;
        var hargaSatuan = parseFloat($('#edit-harga').val()) || 0;
        var totalHarga = jumlahBahan * hargaSatuan;
        $('#edit-jumlah-po').val(Math.round(totalHarga));
    }

    // Auto-calculate total harga ketika jumlah bahan atau harga satuan diubah
    $('#edit-jumlah-bahan, #edit-harga').on('input', function() {
        updateTotalHarga();
    });

   // Menangani submit form edit
    $('#editForm').on('submit', function(e) {
        e.preventDefault();

        // Mengambil data dari form
        var formData = {
            id: $('#edit-id').val(),
            bahan: $('#edit-bahan').val(),
            jumlah_bahan: $('#edit-jumlah-bahan').val(),
            jumlah_po: $('#edit-jumlah-po').val(),
            satuan: $('#edit-satuan').val(),
            tanggal_kedatangan: $('#edit-tanggal-kedatangan').val(),
            tanggal_digunakan: $('#edit-tanggal-digunakan').val(),
            harga: $('#edit-harga').val(),
            jumlah_box: $('#edit-jumlah-box').val(),
            keterangan: $('#edit-keterangan').val(),
        };

        // Menambahkan CSRF token ke header
        $.ajax({
            url: '/pengajuan_po/update_data', // Ganti dengan URL endpoint untuk update
            method: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Mengambil CSRF token
            },
            success: function(response) {
                // Menutup modal dan memperbarui data di DataTables
                $('#editModal').modal('hide');
               Swal.fire({
                icon: 'success',
                title: 'BERHASIL',
                text: 'Data berhasil diperbarui',
                showConfirmButton: false,
                timer: 2000
            });
                $('#tbl_list_barang').DataTable().ajax.reload();
            },
            error: function(xhr, status, error) {
                console.log("Error Status: " + status);
                console.log("Error Message: " + error);
                console.log("Error Response: " + xhr.responseText);
                alert("Terjadi kesalahan saat memperbarui data! Lihat console untuk detail.");
            }
        });
    });

    // ================================== AI SUGGESTION FUNCTIONS ================================== //

    /**
     * Fetch AI suggestion ketika bahan dipilih
     */
    $('#add-bahan').on('change', function() {
        var bahanId = $(this).val();
        var defaultSatuan = $(this).find(':selected').data('satuan');

        // Set default satuan
        if (defaultSatuan) {
            $('#add-satuan').val(defaultSatuan).trigger('change');
        }

        // Jika bahan dipilih, fetch suggestion
        if (bahanId) {
            fetchAddQuantitySuggestion(bahanId);
        } else {
            // Hide suggestion jika bahan kosong
            $('#add-suggestion-container').slideUp();
        }
    });

    /**
     * Fetch quantity suggestion dari AI service
     * 
     * @param int bahanId
     */
    function fetchAddQuantitySuggestion(bahanId) {
        var rincianMenuId = $('#add-id-menu-harian').val() || null;
        var kontrakId = '{{ $po->id_kontrak }}' || null;

        // Show loading state
        showAddSuggestionLoading();

        $.ajax({
            url: '/api/po/ai/suggest-quantity',
            method: 'POST',
            data: {
                bahan_id: bahanId,
                rincian_menu_id: rincianMenuId,
                current_qty: parseFloat($('#add-jumlah-bahan').val()) || null,
                kontrak_id: kontrakId,
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            },
            success: function(response) {
                if (response.success) {
                    displayAddSuggestion(response);
                } else {
                    hideAddSuggestion();
                    console.warn('Suggestion failed:', response.error);
                }
            },
            error: function(xhr) {
                hideAddSuggestion();
                console.error('AI Suggestion Error:', xhr.responseJSON);
            }
        });
    }

    /**
     * Display suggestion di UI
     * 
     * @param object response API response dengan suggestion data
     */
    function displayAddSuggestion(response) {
        var confidenceClass = 'badge-primary';
        var confidence = response.confidence_level || 'low';

        if (confidence === 'high') {
            confidenceClass = 'badge-success';
        } else if (confidence === 'medium') {
            confidenceClass = 'badge-warning';
        }

        // Update suggestion UI
        $('#add-suggestion-confidence')
            .removeClass('badge-success badge-warning badge-primary')
            .addClass(confidenceClass)
            .text(confidence.toUpperCase() + ' (' + response.historical_analysis.data_points + ' data)');

        $('#add-suggestion-qty').text(response.suggested_quantity.toFixed(2));
        $('#add-suggestion-reason').text(response.reason);

        // Build analysis details
        var analysisText = '';
        var analysis = response.historical_analysis || {};
        if (analysis.data_points > 0) {
            analysisText = 'Rata-rata: ' + analysis.average_usage.toFixed(2) + 
                          ' | Min: ' + analysis.min_usage.toFixed(2) + 
                          ' | Max: ' + analysis.max_usage.toFixed(2) + 
                          ' | Tren: ' + (analysis.usage_trend || '-');
        } else {
            analysisText = 'Tidak ada data historis - gunakan estimasi Anda';
        }

        $('#add-suggestion-analysis-text').text(analysisText);

        // Show container
        $('#add-suggestion-container').slideDown();

        // Store suggestion untuk reference
        window.lastAddSuggestion = response;
    }

    /**
     * Hide suggestion container
     */
    function hideAddSuggestion() {
        $('#add-suggestion-container').slideUp();
        window.lastAddSuggestion = null;
    }

    /**
     * Show loading state
     */
    function showAddSuggestionLoading() {
        $('#add-suggestion-container').slideDown();
        $('#add-suggestion-confidence').html('<span class="spinner-border spinner-border-sm mr-2"></span>Loading...');
        $('#add-suggestion-qty').text('-');
        $('#add-suggestion-reason').text('Menganalisis data historis...');
    }

    /**
     * Apply suggestion ke form
     */
    function applyAddSuggestion() {
        if (!window.lastAddSuggestion) {
            Swal.fire('Error', 'Tidak ada saran yang tersedia', 'error');
            return;
        }

        var suggestion = window.lastAddSuggestion;
        $('#add-jumlah-bahan').val(suggestion.suggested_quantity.toFixed(2));

        // Trigger update calculation
        updateAddTotalHarga();

        // Show feedback
        Toast.fire({
            icon: 'success',
            title: 'Saran diterapkan!',
            text: 'Jumlah diatur ke ' + suggestion.suggested_quantity.toFixed(2)
        });
    }

    // ================================== END AI SUGGESTION FUNCTIONS ================================== //

    </script>


    <!-- jQuery -->
</body>
</html>
