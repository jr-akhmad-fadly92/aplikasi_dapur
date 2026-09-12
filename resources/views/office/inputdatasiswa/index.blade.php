<!DOCTYPE html>
<html lang="en">

<head>
    <title>{{ $header }}</title>
    @include('Template.head')
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
                            <h1 class="m-0 text-dark">
                                {{ $header }}
                                @if(isset($sekolah) && $sekolah)
                                <br><small class="text-muted">{{ $sekolah->nama_sekolah }}</small>
                                @endif
                            </h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                                <li class="breadcrumb-item active">{{ $header }}</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="d-flex justify-content-end">
                                                <a href="{{ url()->previous() }}" class="btn btn-default btn-sm mr-2">
                                                    <i class="fa fa-arrow-left"></i> Kembali
                                                </a>
                                                <button class="btn btn-primary btn-sm mr-2" onclick="showImportModal()">
                                                    <i class="fa fa-upload"></i> Import Data
                                                </button>
                                                <button class="btn btn-warning btn-sm mr-2" onclick="showExportModal()">
                                                    <i class="fa fa-download"></i> Export Data
                                                </button>
                                                <button class="btn btn-warning btn-sm mr-2"
                                                    onclick="showExportModalPresensi()">
                                                    <i class="fa fa-download"></i> Export Data presensi
                                                </button>
                                                <button class="btn btn-success btn-sm" onclick="formSiswa()">
                                                    <i class="fa fa-plus"></i> Tambah Data Siswa
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <!-- Filter Form -->
                                    <div class="row mb-3">
                                        <div class="col-md-{{ isset($sekolah) && $sekolah ? '4' : '3' }}">
                                            <div class="form-group">
                                                <label for="filter_nisn">Filter NISN:</label>
                                                <input type="text" class="form-control form-control-sm" id="filter_nisn"
                                                    placeholder="Masukkan NISN">
                                            </div>
                                        </div>
                                        <div class="col-md-{{ isset($sekolah) && $sekolah ? '4' : '3' }}">
                                            <div class="form-group">
                                                <label for="filter_nama">Filter Nama:</label>
                                                <input type="text" class="form-control form-control-sm" id="filter_nama"
                                                    placeholder="Masukkan nama siswa">
                                            </div>
                                        </div>
                                        <div class="col-md-{{ isset($sekolah) && $sekolah ? '4' : '3' }}">
                                            <div class="form-group">
                                                <label>&nbsp;</label>
                                                <div>
                                                    <button class="btn btn-primary btn-sm" onclick="filterData()">
                                                        <i class="fa fa-search"></i> Filter
                                                    </button>
                                                    <button class="btn btn-secondary btn-sm" onclick="resetFilter()">
                                                        <i class="fa fa-refresh"></i> Reset
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <table id="dt_siswa" class="table table-bordered table-striped table-hover"
                                        cellspacing="0" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>NISN</th>
                                                <th>Nama Siswa</th>
                                                <th>Sekolah</th>
                                                <th>Kelas</th>
                                                <th>Jenis Kelamin</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Data  via AJAX -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        @include('Template.footer')
    </div>

    <!-- REQUIRED SCRIPTS -->
    @include('Template.script')

    <script type="text/javascript">
        var Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });

        // function untuk handle error 
        function handleAjaxError(xhr) {
            var response = JSON.parse(xhr.responseText);
            var message = response.message || 'Terjadi kesalahan';

            if (response.errors) {
                message += "<ul>";
                $.each(response.errors, function (field, errors) {
                    $.each(errors, function (index, error) {
                        message += "<li>" + error + "</li>";
                    });
                });
                message += "</ul>";
            }

            $.alert({
                title: 'Error',
                content: message,
                type: 'red',
                backgroundDismiss: true,
                columnClass: 'col-xs-10 col-xs-offset-1 col-md-8 col-md-offset-2'
            });
        }

        // function close all dialogs
        function closeAllDialogs() {
            $.each(jconfirm.instances, function (index, instance) {
                instance.close();
            });
        }

        // Filter functions
        function filterData() { table.ajax.reload(); }
        function resetFilter() {
            $('#filter_nisn, #filter_nama').val('');
            table.ajax.reload();
        }

        // DataTable Configuration
        var table = $('#dt_siswa').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ url("inputdatasiswa/dt_dataSiswa") }}',
                data: function (d) {
                    @if (isset($sekolah) && $sekolah)
                        d.sekolah_id = '{{ $sekolah->id }}';
                    @endif
                    d.nisn = $('#filter_nisn').val();
                    d.nama = $('#filter_nama').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'nisn', name: 'nisn' },
                { data: 'nama', name: 'nama' },
                { data: 'nama_sekolah', name: 'nama_sekolah' },
                { data: 'kelas', name: 'kelas' },
                { data: 'jenis_kelamin_text', name: 'jenis_kelamin' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            responsive: true,
            order: [[1, 'asc']]
        });

        // Function form adding new student
        function formSiswa(id = null) {
            var title = id ? 'Edit Data Siswa' : 'Tambah Data Siswa';
            var url = '{{ url("inputdatasiswa/form_dataSiswa") }}';
            if (id) {
                url += '?id=' + id;
            }
            @if (isset($sekolah) && $sekolah)
                url += id ? '&sekolah_id={{ $sekolah->id }}' : '?sekolah_id={{ $sekolah->id }}';
            @endif

            $.confirm({
                title: title,
                columnClass: 'col-md-8 col-md-offset-2 col-xs-12 col-sm-12',
                content: 'url:' + url,
                type: 'blue',
                buttons: {
                    simpan: {
                        text: "<i class='fa fa-save'></i> Simpan",
                        btnClass: 'btn-success',
                        action: function () {
                            $.confirm({
                                title: 'Konfirmasi',
                                content: 'Apakah anda yakin ingin menyimpan data ini?',
                                type: 'orange',
                                buttons: {
                                    simpan: {
                                        text: 'Ya, Simpan',
                                        btnClass: 'btn-success',
                                        action: function () {
                                            var formData = new FormData(document.getElementById('form_data_siswa'));
                                            // Tambahkan token CSRF
                                            formData.append('_token', '{{ csrf_token() }}');

                                            $.ajax({
                                                type: 'POST',
                                                url: '{{ url("inputdatasiswa/ajax_simpanSiswa") }}',
                                                data: formData,
                                                dataType: 'json',
                                                contentType: false,
                                                cache: false,
                                                processData: false,
                                                success: function (response) {
                                                    Toast.fire({
                                                        icon: 'success',
                                                        title: response.message
                                                    });
                                                    table.ajax.reload(null, false);
                                                    closeAllDialogs();
                                                },
                                                error: handleAjaxError
                                            });
                                        }
                                    },
                                    batal: {
                                        text: 'Batal',
                                        btnClass: 'btn-default'
                                    }
                                }
                            });
                            return false;
                        }
                    },
                    batal: {
                        text: 'Batal',
                        btnClass: 'btn-default'
                    }
                }
            });
        }

        // Event handler edit button
        $(document).on('click', '.edit-siswa', function () {
            var id = $(this).data('id');
            formSiswa(id);
        });

        // Event handler detail button
        $(document).on('click', '.detail-siswa', function () {
            var id = $(this).data('id');

            $.confirm({
                title: 'Detail Siswa',
                columnClass: 'col-md-10 col-md-offset-1 col-xs-12 col-sm-12',
                content: 'url:{{ url("inputdatasiswa/detail_siswa") }}?id=' + id,
                type: 'blue',
                buttons: {
                    tutup: {
                        text: 'Tutup',
                        btnClass: 'btn-default'
                    }
                }
            });
        });

        // Event handler delete button
        $(document).on('click', '.delete-siswa', function () {
            var id = $(this).data('id');

            $.confirm({
                title: 'Konfirmasi Hapus',
                content: 'Apakah anda yakin ingin menghapus data siswa ini?',
                type: 'red',
                buttons: {
                    hapus: {
                        text: 'Ya, Hapus',
                        btnClass: 'btn-danger',
                        action: function () {
                            $.ajax({
                                type: 'POST',
                                url: '{{ url("inputdatasiswa/ajax_deleteSiswa") }}',
                                data: {
                                    id: id,
                                    _token: '{{ csrf_token() }}'
                                },
                                dataType: 'json',
                                success: function (response) {
                                    Toast.fire({
                                        icon: 'success',
                                        title: response.message
                                    });
                                    table.ajax.reload(null, false);
                                },
                                error: function (xhr) {
                                    var response = JSON.parse(xhr.responseText);
                                    Toast.fire({
                                        icon: 'error',
                                        title: response.message || 'Terjadi kesalahan'
                                    });
                                }
                            });
                        }
                    },
                    batal: {
                        text: 'Batal',
                        btnClass: 'btn-default'
                    }
                }
            });
        });

        // Function Import/Export modal
        function showImportModal() {
            $.confirm({
                title: 'Import Data Siswa',
                columnClass: 'col-md-8 col-md-offset-2 col-xs-12 col-sm-12',
                content: 'url:{{ url("inputdatasiswa/form_import") }}',
                type: 'blue',
                buttons: { tutup: { text: 'Tutup', btnClass: 'btn-default' } }
            });
        }

        function showExportModalPresensi() {
            var exportUrl = '{{ url("inputdatasiswa/form_export_presensi") }}';
            @if (isset($sekolah) && $sekolah)
                exportUrl += '?sekolah_id={{ $sekolah->id }}';
            @endif

            $.confirm({
                title: 'Export Data Siswa',
                columnClass: 'col-md-6 col-md-offset-3 col-xs-12 col-sm-12',
                content: 'url:' + exportUrl,
                type: 'green',
                buttons: { tutup: { text: 'Tutup', btnClass: 'btn-default' } }
            });
        }

        // key handler filter
        $('#filter_nisn, #filter_nama').on('change keypress', function (e) {
            if (e.type === 'change' || e.which == 13) {
                filterData();
            }
        });
        function showExportModal() {
            var exportUrl = '{{ url("inputdatasiswa/form_export") }}';
            @if(isset($sekolah) && $sekolah)
                exportUrl += '?sekolah_id={{ $sekolah->id }}';
            @endif

            $.confirm({
                title: 'Export Data Siswa',
                columnClass: 'col-md-6 col-md-offset-3 col-xs-12 col-sm-12',
                content: 'url:' + exportUrl,
                type: 'green',
                buttons: { tutup: { text: 'Tutup', btnClass: 'btn-default' } }
            });
        }

        // key handler filter
        $('#filter_nisn, #filter_nama').on('change keypress', function (e) {
            if (e.type === 'change' || e.which == 13) {
                filterData();
            }
        });
    </script>
</body>

</html>