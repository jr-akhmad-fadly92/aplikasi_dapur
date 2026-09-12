<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ $header }}</title>
    @include('Template.head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

    <!-- Navbar -->
    @include('Template.navbar')

    <!-- Sidebar -->
    @include('Template.left-sidebar')

    <!-- Content Wrapper -->
    <div class="content-wrapper">

        <!-- Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark" id="currentTime">Starter Page</h1>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <!-- Card utama data hari libur -->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h1>{{ $header }}</h1>
                                <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#modallibur">
                                    Tambah Hari Libur
                                </button>
                            </div>
                            <div class="card-body">
                                <!-- Tabel Data Hari Libur -->
                                <table class="table table-bordered w-100" id="tableKbm">
                                    <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Keterangan Libur</th>
                                        <th>Jenis Libur</th>
                                        <th>Aksi</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>

                        <!-- Card contoh kosong -->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">DataTable with default features</h3>
                            </div>
                            <div class="card-body">
                                <!-- Bisa diisi konten tambahan -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Modal Tambah Hari Libur -->
    <div class="modal fade" id="modallibur" tabindex="-1" role="dialog" aria-labelledby="modalKbmLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="formlibur">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalKbmLabel">Tambah Hari Libur</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Input tanggal -->
                        <div class="mb-3">
                            <label>Tanggal</label>
                            <input type="date" name="tanggal" class="form-control">
                        </div>
                        <!-- Input keterangan -->
                        <div class="mb-3">
                            <label>Keterangan Libur</label>
                            <input type="text" name="keterangan" class="form-control">
                        </div>
                        <!-- Pilihan jenis libur -->
                        <div class="mb-3">
                            <label>Jenis Libur</label>
                            <select name="jenis" class="form-control">
                                <option value="Cuti">Cuti</option>
                                <option value="Libur Hari Besar" selected>Libur Hari Besar</option>
                                <option value="Semesteran">Semesteran</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div class="modal fade" id="modalDelete" tabindex="-1" aria-labelledby="modalDeleteLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="modalDeleteLabel">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Apakah yakin ingin menghapus data ini?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger btn-sm" id="btnConfirmDelete">Ya, Hapus</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <div class="p-3">
            <h5>Title</h5>
            <p>Sidebar content</p>
        </div>
    </aside>

    <!-- Footer -->
    @include('Template.footer')

</div>

<!-- Script -->
@include('Template.script')
<script>
$(document).ready(function(){

    // 1. Inisialisasi DataTable
    var table = $('#tableKbm').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('master_libur.data') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'tanggal_libur', name: 'tanggal_libur' },
            { data: 'keterangan_libur', name: 'keterangan_libur' },
            { data: 'jenis', name: 'jenis' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });

    // 2. Tambah Hari Libur via AJAX
    $('#formlibur').on('submit', function(e){
        e.preventDefault();
        $.ajax({
            url: "{{ route('master_libur.simpan') }}",
            type: "POST",
            data: $(this).serialize(),
            success: function(res){
                $('#modallibur').modal('hide');
                $('#formlibur')[0].reset();
                table.ajax.reload(null, false);
                Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message });
            },
            error: function(xhr){
                if(xhr.status === 422){
                    let errors = xhr.responseJSON.errors;
                    let msg = "";
                    $.each(errors, function(key, val){ msg += val[0] + "<br>"; });
                    Swal.fire({ icon: 'error', title: 'Validasi Gagal', html: msg });
                } else {
                    Swal.fire({ icon: 'error', title: 'Terjadi Kesalahan', text: 'Silakan coba lagi.' });
                }
            }
        });
    });

    // 3. Hapus Hari Libur
    let deleteId = null;

    // Klik tombol hapus → buka modal konfirmasi
    $(document).on('click', '.btnDelete', function() {
        deleteId = $(this).data('id');
        $('#modalDelete').modal('show');
    });

    // Konfirmasi hapus
    $('#btnConfirmDelete').on('click', function() {
        if (deleteId) {
            $.ajax({
                url: "{{ url('master_libur') }}/" + deleteId,
                type: "DELETE",
                data: { _token: $('meta[name="csrf-token"]').attr('content') },
                success: function(response) {
                    $('#modalDelete').modal('hide');
                    table.ajax.reload(null, false);
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: response.success });
                },
                error: function(xhr) {
                    $('#modalDelete').modal('hide');
                    Swal.fire({ icon: 'error', title: 'Terjadi Kesalahan', text: xhr.responseText });
                }
            });
        }
    });

});
</script>
</body>
</html>
