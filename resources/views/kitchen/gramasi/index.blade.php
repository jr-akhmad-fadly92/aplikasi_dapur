<!DOCTYPE html>
<!-- Halaman template starter untuk proyek baru -->
<html lang="en">
<head>
    <title>{{ $header }}</title>
    @include('Template.head') <!-- Menyertakan file head -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        
        <!-- Navbar -->
        @include('Template.navbar')
        <!-- /.navbar -->

        <!-- Sidebar Kiri -->
        @include('Template.left-sidebar')
        <!-- /.sidebar -->

        <!-- Konten Utama -->
        <div class="content-wrapper">
            <!-- Header Konten -->
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

            <!-- Isi Konten -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h1>{{ $header }}</h1>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <h3 class="card-title">
                                        <!--button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#createModal">
                                            Tambah
                                        </!--button-->
                                    </h3>
                                    <table id="tbl_list_master_gramasi" class="table table-bordered table-hover" style="width: 100%">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Kode</th>
                                                <th>Karbohidrat</th>
                                                <th>Protein</th>
                                                <th>Sayur</th>
                                                <th>Buah</th>
                                                <th>Susu</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.container-fluid -->
            </section>
            <!-- Modal Tambah Data -->
            <div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="createModalLabel">Tambah Data Gramasi</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="createForm">
                                @csrf
                                <div class="form-group">
                                    <label for="kode">Kode</label>
                                    <select class="form-control" id="kode" name="kode" required>
                                        <option value="">-- Pilih Kode --</option>
                                        <option value="A">A</option>
                                        <option value="B">B</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="karbohidrat">Karbohidrat</label>
                                    <input type="number" class="form-control" id="karbohidrat" name="karbohidrat" required>
                                </div>
                                <div class="form-group">
                                    <label for="protein">Protein</label>
                                    <input type="number" class="form-control" id="protein" name="protein" required>
                                </div>
                                <div class="form-group">
                                    <label for="sayur">Sayur</label>
                                    <input type="number" class="form-control" id="sayur" name="sayur" required>
                                </div>
                                <div class="form-group">
                                    <label for="buah">Buah</label>
                                    <input type="number" class="form-control" id="buah" name="buah" required>
                                </div>
                                <div class="form-group">
                                    <label for="susu">Susu</label>
                                    <input type="number" class="form-control" id="susu" name="susu" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal Edit Data -->
            <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel">Edit Data Gramasi</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="editForm">
                                @csrf
                                <input type="hidden" id="edit_id" name="id">
                                <div class="form-group">
                                    <label for="edit_kode">Kode</label>
                                    <select class="form-control" id="edit_kode" name="kode" required>
                                        <option value="">-- Pilih Kode --</option>
                                        <option value="A">A</option>
                                        <option value="B">B</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="edit_karbohidrat">Karbohidrat</label>
                                    <input type="number" class="form-control" id="edit_karbohidrat" name="karbohidrat" required>
                                </div>
                                <div class="form-group">
                                    <label for="edit_protein">Protein</label>
                                    <input type="number" class="form-control" id="edit_protein" name="protein" required>
                                </div>
                                <div class="form-group">
                                    <label for="edit_sayur">Sayur</label>
                                    <input type="number" class="form-control" id="edit_sayur" name="sayur" required>
                                </div>
                                <div class="form-group">
                                    <label for="edit_buah">Buah</label>
                                    <input type="number" class="form-control" id="edit_buah" name="buah" required>
                                </div>
                                <div class="form-group">
                                    <label for="edit_susu">Susu</label>
                                    <input type="number" class="form-control" id="edit_susu" name="susu" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
        <div class="modal fade" id="editModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Data Gramasi</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="editForm">
                            @csrf
                            @method('PUT')
                            <input type="hidden" id="edit_id">
                            <div class="form-group">
                                <label for="edit_kode">Kode</label>
                                <select class="form-control" id="edit_kode" name="kode" required>
                                    <option value="">-- Pilih Kode --</option>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="edit_karbohidrat">Karbohidrat</label>
                                <input type="number" class="form-control" id="edit_karbohidrat" name="karbohidrat" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_protein">Protein</label>
                                <input type="number" class="form-control" id="edit_protein" name="protein" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_sayur">Sayur</label>
                                <input type="number" class="form-control" id="edit_sayur" name="sayur" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_buah">Buah</label>
                                <input type="number" class="form-control" id="edit_buah" name="buah" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_susu">Susu</label>
                                <input type="number" class="form-control" id="edit_susu" name="susu" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Kontrol -->
        <aside class="control-sidebar control-sidebar-dark">
            <div class="p-3">
                <h5>Title</h5>
                <p>Sidebar content</p>
            </div>
        </aside>
        <!-- /.control-sidebar -->

        <!-- Footer -->
        @include('Template.footer')
        <!-- /.footer -->
    </div>
    <!-- ./wrapper -->

    <!-- Skrip yang diperlukan -->
    @include('Template.script')
    
    <script type="text/javascript">
        $(document).ready(function () {
            $('#tbl_list_master_gramasi').DataTable({
                ajax: '{{ url()->current() }}',
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'kode', name: 'kode' },
                    { data: 'karbohidrat', name: 'karbohidrat' },
                    { data: 'protein', name: 'protein' },
                    { data: 'sayur', name: 'sayur' },
                    { data: 'buah', name: 'buah' },
                    { data: 'susu', name: 'susu' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });
        });
    </script>
    
    <!-- SweetAlert untuk notifikasi -->
    <script src="{{ asset('AdminLte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script>
        @if(session('success'))
            Swal.fire({
                icon: "success",
                title: "BERHASIL",
                text: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 2000
            });
        @elseif(session('error'))
            Swal.fire({
                icon: "error",
                title: "GAGAL!",
                text: "{{ session('error') }}",
                showConfirmButton: false,
                timer: 2000
            });
        @endif
    </script>
    <script>
    $(document).ready(function () {
        $('#createForm').submit(function (e) {
            e.preventDefault(); // Hindari reload halaman

            let formData = new FormData(this);

            $.ajax({
                url: "{{ route('gramasi.store') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            icon: "success",
                            title: "Sukses",
                            text: response.message,
                            showConfirmButton: false,
                            timer: 2000
                        });

                        $('#createModal').modal('hide'); // Tutup modal
                        $('#createForm')[0].reset(); // Reset form
                        $('#tbl_list_master_gramasi').DataTable().ajax.reload(); // Reload tabel
                    }
                },
                error: function (xhr) {
                    let errors = xhr.responseJSON.errors;
                    let errorMessage = "Terjadi kesalahan!";
                    if (errors) {
                        errorMessage = Object.values(errors).join("\n");
                    }
                    Swal.fire({
                        icon: "error",
                        title: "Gagal!",
                        text: errorMessage,
                    });
                }
            });
        });
    });
    </script>
    <script>
        $(document).ready(function () {
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });
            // Tampilkan Data di Modal Edit
            $(document).on('click', '.edit', function () {
                var id = $(this).data('id');
                $.get('/gramasi/' + id + '/edit', function (data) {
                    $('#edit_id').val(data.id);
                    $('#edit_kode').val(data.kode);
                    $('#edit_karbohidrat').val(data.karbohidrat);
                    $('#edit_protein').val(data.protein);
                    $('#edit_sayur').val(data.sayur);
                    $('#edit_buah').val(data.buah);
                    $('#edit_susu').val(data.susu);
                    $('#editModal').modal('show');
                });
            });

            // Update Data
            $('#editForm').submit(function (e) {
                e.preventDefault();
                var id = $('#edit_id').val();
                $.ajax({
                    url: '/gramasi/' + id,
                    method: 'PUT',
                    data: $(this).serialize(),
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                icon: "success",
                                title: "Sukses",
                                text: response.message,
                                showConfirmButton: false,
                                timer: 2000
                            });

                            $('#editModal').modal('hide');
                            $('#tbl_list_master_gramasi').DataTable().ajax.reload(); // Reload tabel
                        }
                    },
                    error: function (xhr) {
                        let errors = xhr.responseJSON.errors;
                        let errorMessage = "Terjadi kesalahan!";
                        if (errors) {
                            errorMessage = Object.values(errors).join("\n");
                        }
                        Swal.fire({
                            icon: "error",
                            title: "Gagal!",
                            text: errorMessage,
                        });
                    }
                    
                });
            });

            // Hapus Data
            $(document).on('click', '.delete', function () {
                var id = $(this).data('id');
                if (confirm('Yakin ingin menghapus?')) {
                    $.ajax({
                        url: '/gramasi/' + id,
                        method: 'DELETE',
                         success: function (response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Sukses",
                                    text: response.message,
                                    showConfirmButton: false,
                                    timer: 2000
                                });

                                  $('#tbl_list_master_gramasi').DataTable().ajax.reload(); // Reload tabel
                            }
                        },
                        error: function (xhr) {
                            let errors = xhr.responseJSON.errors;
                            let errorMessage = "Terjadi kesalahan!";
                            if (errors) {
                                errorMessage = Object.values(errors).join("\n");
                            }
                            Swal.fire({
                                icon: "error",
                                title: "Gagal!",
                                text: errorMessage,
                            });
                        }
                        
                    });
                }
            });
        });

    </script>
</body>
</html>
