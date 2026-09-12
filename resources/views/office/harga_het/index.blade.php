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
                        <h1 class="m-0 text-dark">{{ $header }}</h1>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <a href="{{ route('harga-het.template') }}" class="btn btn-info">
                            Export Template Excel
                        </a>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalUploadHargaHet">
                            Upload Excel Harga HET
                        </button>
                    </div>
                    <div class="card-body">
                        <table id="tbl_harga_het" class="table table-bordered table-hover" style="width: 100%">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>ID Bahan</th>
                                <th>Nama Bahan</th>
                                <th>Tanggal Update</th>
                                <th>Harga HET</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="modal fade" id="modalEditHargaHet" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="formEditHargaHet">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Update Manual Harga HET</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="id_bahan" name="id_bahan">

                        <div class="form-group">
                            <label>Nama Bahan</label>
                            <input type="text" id="nama_bahan" class="form-control" readonly>
                        </div>

                        <div class="form-group">
                            <label for="tanggal_update">Tanggal Update</label>
                            <input type="date" id="tanggal_update" name="tanggal_update" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="harga_het">Harga HET</label>
                            <input type="number" id="harga_het" name="harga_het" class="form-control" min="1" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="modalUploadHargaHet" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Excel Harga HET</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('harga-het.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info">
                            Alur: 1) Export template, 2) edit kolom <b>harga_het</b>, 3) upload kembali file.<br>
                            <b>Catatan:</b> kolom <b>tanggal_update</b> otomatis mengikuti tanggal saat template di-download.
                        </div>
                        <div class="form-group">
                            <label for="file_het">File Excel</label>
                            <input type="file" name="file_het" id="file_het" class="form-control" accept=".xlsx,.xls,.csv" required>
                            <small class="text-muted">Format: .xlsx, .xls, .csv</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Upload & Proses</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('Template.footer')
</div>

@include('Template.script')
<script src="{{ asset('AdminLte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<script>
    @if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: '{{ session('success') }}'
    });
    @elseif(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Gagal',
        text: '{{ session('error') }}'
    });
    @endif

    $(document).ready(function () {
        const table = $('#tbl_harga_het').DataTable({
            ajax: '{{ route('harga-het.index') }}',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'id_bahan', name: 'id_bahan' },
                { data: 'nama_bahan', name: 'nama_bahan' },
                { data: 'tanggal_update_format', name: 'tanggal_update_format', orderable: false, searchable: false },
                { data: 'harga_het_format', name: 'harga_het_format', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        $(document).on('click', '.btn-edit-harga-het', function () {
            $('#id_bahan').val($(this).data('id_bahan'));
            $('#nama_bahan').val($(this).data('nama_bahan'));
            $('#tanggal_update').val($(this).data('tanggal_update'));
            $('#harga_het').val($(this).data('harga_het'));
            $('#modalEditHargaHet').modal('show');
        });

        $('#formEditHargaHet').on('submit', function (e) {
            e.preventDefault();

            fetch('{{ route('harga-het.upsert') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    id_bahan: $('#id_bahan').val(),
                    tanggal_update: $('#tanggal_update').val(),
                    harga_het: $('#harga_het').val()
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            $('#modalEditHargaHet').modal('hide');
                            table.ajax.reload(null, false);
                        });
                    } else {
                        Swal.fire('Gagal', 'Tidak bisa memperbarui data.', 'error');
                    }
                })
                .catch(() => {
                    Swal.fire('Gagal', 'Terjadi kesalahan saat update.', 'error');
                });
        });
    });
</script>
</body>
</html>
