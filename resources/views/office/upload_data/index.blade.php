<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">
<head>
    <title>upload-data</title>
    @include('Template.head')
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
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-primary">
                                    <h3 class="card-title">Manajemen Upload Data</h3>
                                    <div class="card-tools">
                                        <a href="{{ route('form-checklist-harian.export') }}" class="btn btn-sm btn-success mr-2" target="_blank">
                                            <i class="fas fa-file-excel"></i> Download Form Checklist Harian
                                        </a>
                                        <button type="button" class="btn btn-sm btn-light" data-toggle="modal" data-target="#createModal">
                                            <i class="fas fa-plus"></i> Tambah Data
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <table id="uploadTable" class="table table-striped table-bordered table-hover">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th width="5%">No</th>
                                                <th width="12%">Tanggal Pelayanan</th>
                                                <th width="15%">Menu</th>
                                                <th width="50%">Dokumen</th>
                                                <th width="18%">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Create/Edit Modal -->
                <div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title" id="createModalLabel">Tambah Data Upload</h5>
                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form id="uploadForm" enctype="multipart/form-data">
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="tanggal_pelayanan">Tanggal Pelayanan *</label>
                                        <input type="date" class="form-control" id="tanggal_pelayanan" name="tanggal_pelayanan" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="id_menu">Menu (Opsional)</label>
                                        <select class="form-control" id="id_menu" name="id_menu">
                                            <option value="">-- Pilih Menu --</option>
                                            @foreach($menus as $menu)
                                                <option value="{{ $menu->id }}">{{ $menu->menu }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="data_menu">
                                                    <i class="fas fa-file-pdf text-danger"></i> Menu
                                                </label>
                                                <input type="file" class="form-control-file" id="data_menu" name="data_menu" accept=".pdf" >
                                                <small class="form-text text-muted">Opsional - Max 10MB</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="data_po">
                                                    <i class="fas fa-file-pdf text-danger"></i> PO
                                                </label>
                                                <input type="file" class="form-control-file" id="data_po" name="data_po" accept=".pdf" >
                                                <small class="form-text text-muted">Opsional - Max 10MB</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="data_sj_kp">
                                                    <i class="fas fa-file-pdf text-danger"></i> SJ/KP
                                                </label>
                                                <input type="file" class="form-control-file" id="data_sj_kp" name="data_sj_kp" accept=".pdf">
                                                <small class="form-text text-muted">Opsional - Max 10MB</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="data_invoice">
                                                    <i class="fas fa-file-pdf text-danger"></i> Invoice
                                                </label>
                                                <input type="file" class="form-control-file" id="data_invoice" name="data_invoice" accept=".pdf" >
                                                <small class="form-text text-muted">Opsional - Max 10MB</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="data_penerimaan_pangan">
                                                    <i class="fas fa-file-pdf text-danger"></i> Penerimaan Pangan
                                                </label>
                                                <input type="file" class="form-control-file" id="data_penerimaan_pangan" name="data_penerimaan_pangan" accept=".pdf" >
                                                <small class="form-text text-muted">Opsional - Max 10MB</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="data_penerimaan_non_pangan">
                                                    <i class="fas fa-file-pdf text-danger"></i> Penerimaan Non-Pangan
                                                </label>
                                                <input type="file" class="form-control-file" id="data_penerimaan_non_pangan" name="data_penerimaan_non_pangan" accept=".pdf">
                                                <small class="form-text text-muted">Opsional - Max 10MB</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="data_gudang">
                                                    <i class="fas fa-file-pdf text-danger"></i> Gudang
                                                </label>
                                                <input type="file" class="form-control-file" id="data_gudang" name="data_gudang" accept=".pdf">
                                                <small class="form-text text-muted">Opsional - Max 10MB</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="data_hasil_masak">
                                                    <i class="fas fa-file-pdf text-danger"></i> Hasil Masak
                                                </label>
                                                <input type="file" class="form-control-file" id="data_hasil_masak" name="data_hasil_masak" accept=".pdf" >
                                                <small class="form-text text-muted">Opsional - Max 10MB</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="data_sj_sekolah">
                                                    <i class="fas fa-file-pdf text-danger"></i> SJ Sekolah
                                                </label>
                                                <input type="file" class="form-control-file" id="data_sj_sekolah" name="data_sj_sekolah" accept=".pdf" >
                                                <small class="form-text text-muted">Opsional - Max 10MB</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="data_counter_ompreng">
                                                    <i class="fas fa-file-pdf text-danger"></i> Counter Ompreng
                                                </label>
                                                <input type="file" class="form-control-file" id="data_counter_ompreng" name="data_counter_ompreng" accept=".pdf" >
                                                <small class="form-text text-muted">Opsional - Max 10MB</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="data_uji_organoleptik">
                                                    <i class="fas fa-file-pdf text-danger"></i> Uji Organoleptik
                                                </label>
                                                <input type="file" class="form-control-file" id="data_uji_organoleptik" name="data_uji_organoleptik" accept=".pdf" >
                                                <small class="form-text text-muted">Opsional - Max 10MB</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary" id="submitBtn">
                                        <i class="fas fa-save"></i> Simpan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Edit Modal -->
                <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true"></div>

                <!-- Delete Modal -->
                <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title" id="deleteModalLabel">Hapus Data</h5>
                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                Apakah Anda yakin ingin menghapus data upload ini?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.container-fluid -->
                </section>
        </div>
        <!-- /.content-wrapper -->

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

    <!-- jQuery -->
    @include('Template.script')

    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
<script>
$(document).ready(function() {
    let uploadTable = $('#uploadTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('upload-data.index') }}",
            type: 'GET'
        },
        columns: [
            { data: 'id', searchable: false, orderable: false, render: function(data, type, row, meta) {
                return meta.row + 1;
            }},
            { data: 'tanggal_pelayanan' },
            { data: 'menu' },
            { data: 'documents', searchable: false, orderable: false, render: function(data, type, row) {
                let badges = '';
                data.forEach(function(doc) {
                    if(doc.file) {
                        badges += '<span class="badge badge-success mr-1" title="' + doc.label + '"><i class="fas fa-check"></i> ' + doc.label + '</span>';
                    } else {
                        badges += '<span class="badge badge-secondary mr-1" title="' + doc.label + '"><i class="fas fa-times"></i> ' + doc.label + '</span>';
                    }
                });
                return badges;
            }},
            { data: 'actions', searchable: false, orderable: false }
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        language: {
            url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        }
    });

    // Form submit untuk create
    $('#uploadForm').on('submit', function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        let url = "{{ route('upload-data.store') }}";
        let uploadId = $('#uploadForm').data('upload-id');

        if(uploadId) {
            formData.append('_method', 'PUT');
            url = "{{ route('upload-data.index') }}" + '/' + uploadId;
        }

        // Add CSRF token to FormData
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

        $('#submitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');

        $.ajax({
            type: uploadId ? 'POST' : 'POST',
            url: url,
            data: formData,
            contentType: false,
            processData: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if(response.success) {
                    Swal.fire('Sukses', response.message, 'success');
                    $('#createModal').modal('hide');
                    $('#uploadForm').trigger('reset');
                    $('#uploadForm').removeData('upload-id');
                    uploadTable.ajax.reload();
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function(xhr) {
                let message = 'Terjadi kesalahan';
                if(xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                Swal.fire('Error', message, 'error');
            },
            complete: function() {
                $('#submitBtn').prop('disabled', false).html('<i class="fas fa-save"></i> Simpan');
            }
        });
    });

    // Fetch menus berdasarkan tanggal pelayanan
    $('#tanggal_pelayanan').on('change', function() {
        let selectedDate = $(this).val();
        let $menuSelect = $('#id_menu');

        if (!selectedDate) {
            $menuSelect.html('<option value="">-- Pilih Menu --</option>');
            return;
        }

        $.ajax({
            url: "{{ route('upload-data.get-menus-by-date') }}",
            type: 'GET',
            data: { date: selectedDate },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                let options = '<option value="">-- Pilih Menu (Opsional) --</option>';
                if (response.data && response.data.length > 0) {
                    response.data.forEach(function(menu) {
                        options += '<option value="' + menu.id + '">' + menu.menu + '</option>';
                    });
                    // Auto-select first menu if available
                    $menuSelect.html(options);
                    if (response.data.length === 1) {
                        $menuSelect.val(response.data[0].id);
                    }
                } else {
                    options += '<optgroup label="Tidak ada menu untuk tanggal ini"></optgroup>';
                    $menuSelect.html(options);
                }
            },
            error: function() {
                let options = '<option value="">-- Error loading menus --</option>';
                $menuSelect.html(options);
            }
        });
    });

    // Edit button
    $(document).on('click', '.btn-edit', function() {
        let id = $(this).data('id');
        $.ajax({
            url: "{{ route('upload-data.index') }}" + '/' + id + '/edit',
            type: 'GET',
            success: function(response) {
                $('#editModal').html(response);
                $('#editModal').modal('show');
                
                // Populate menu dropdown based on current date in edit form
                let currentDate = $('#edit_tanggal_pelayanan').val();
                let currentMenuId = $('#current-menu-id').val();
                
                if (currentDate) {
                    $.ajax({
                        url: "{{ route('upload-data.get-menus-by-date') }}",
                        type: 'GET',
                        data: { date: currentDate },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            let options = '<option value="">-- Pilih Menu (Opsional) --</option>';
                            if (response.data && response.data.length > 0) {
                                response.data.forEach(function(menu) {
                                    let selected = (currentMenuId && menu.id == currentMenuId) ? 'selected' : '';
                                    options += '<option value="' + menu.id + '" ' + selected + '>' + menu.menu + '</option>';
                                });
                            } else {
                                options += '<optgroup label="Tidak ada menu untuk tanggal ini"></optgroup>';
                            }
                            $('#edit_id_menu').html(options);
                        }
                    });
                }
                
                // Handle edit form submit
                $('#editForm').off('submit').on('submit', function(e) {
                    e.preventDefault();
                    let formData = new FormData(this);
                    let uploadId = $('#edit-id').val();
                    formData.append('_method', 'PUT');
                    
                    $('#editSubmitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
                    
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('upload-data.index') }}" + '/' + uploadId,
                        data: formData,
                        contentType: false,
                        processData: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if(response.success) {
                                Swal.fire('Sukses', response.message, 'success');
                                $('#editModal').modal('hide');
                                uploadTable.ajax.reload();
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('Error', 'Gagal menyimpan perubahan', 'error');
                        },
                        complete: function() {
                            $('#editSubmitBtn').prop('disabled', false).html('<i class="fas fa-save"></i> Simpan Perubahan');
                        }
                    });
                });
            },
            error: function() {
                Swal.fire('Error', 'Gagal memuat data', 'error');
            }
        });
    });

    // Edit button date change handler
    $(document).on('change', '#edit_tanggal_pelayanan', function() {
        let selectedDate = $(this).val();
        let $menuSelect = $('#edit_id_menu');

        if (!selectedDate) {
            $menuSelect.html('<option value="">-- Pilih Menu (Opsional) --</option>');
            return;
        }

        $.ajax({
            url: "{{ route('upload-data.get-menus-by-date') }}",
            type: 'GET',
            data: { date: selectedDate },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                let currentMenuId = $('#current-menu-id').val();
                let options = '<option value="">-- Pilih Menu (Opsional) --</option>';
                if (response.data && response.data.length > 0) {
                    response.data.forEach(function(menu) {
                        let selected = (currentMenuId && menu.id == currentMenuId) ? 'selected' : '';
                        options += '<option value="' + menu.id + '" ' + selected + '>' + menu.menu + '</option>';
                    });
                } else {
                    options += '<optgroup label="Tidak ada menu untuk tanggal ini"></optgroup>';
                }
                $menuSelect.html(options);
            },
            error: function() {
                let options = '<option value="">-- Error loading menus --</option>';
                $menuSelect.html(options);
            }
        });
    });

    // Delete confirm
    let deleteId = null;
    $(document).on('click', '.btn-delete', function() {
        deleteId = $(this).data('id');
        $('#deleteModal').modal('show');
    });

    $('#confirmDeleteBtn').on('click', function() {
        $.ajax({
            type: 'DELETE',
            url: "{{ route('upload-data.index') }}" + '/' + deleteId,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if(response.success) {
                    Swal.fire('Sukses', response.message, 'success');
                    $('#deleteModal').modal('hide');
                    uploadTable.ajax.reload();
                }
            },
            error: function(xhr) {
                Swal.fire('Error', 'Gagal menghapus data', 'error');
            }
        });
    });

    // Download file
    $(document).on('click', '.btn-download', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        let field = $(this).data('field');
        window.location.href = "{{ route('upload-data.index') }}" + '/' + id + '/download/' + field;
    });

    // Reset form on modal hide
    $('#createModal').on('hidden.bs.modal', function() {
        $('#uploadForm').trigger('reset');
        $('#uploadForm').removeData('upload-id');
        $('#createModalLabel').text('Tambah Data Upload');
    });
});
</script>
</body>
</html>


@section('content')

@endsection

@section('scripts')

@endsection
