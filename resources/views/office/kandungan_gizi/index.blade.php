<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
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
                           <h1 >{{ $header }}</h1>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <h3 class="card-title"><button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalTambah">Tambah</button></h3>
                            <table id="tbl_list_master_kandungan_gizi" class="table table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kandungan</th>
                                    <th>action</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
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
        </div>
        <!-- /.content-wrapper -->
        <!-- Modal Tambah Data -->
        <div class="modal fade" id="modalTambah" tabindex="-1" role="dialog" aria-labelledby="modalTambahLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTambahLabel">Tambah Kandungan Gizi</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="formTambah">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="kandungan">Kandungan</label>
                                <input type="text" class="form-control" id="kandungan" name="kandungan" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Edit Data -->
        <div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-labelledby="modalEditLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalEditLabel">Edit Kandungan Gizi</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="formEdit">
                        <div class="modal-body">
                            <input type="hidden" id="edit_id" name="id">
                            <div class="form-group">
                                <label for="edit_kandungan">Kandungan</label>
                                <input type="text" class="form-control" id="edit_kandungan" name="kandungan" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
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
   
    <script type="text/javascript">
    $(document).ready(function () {
    $('#tbl_list_master_kandungan_gizi').DataTable({
            
            ajax: '{{ url()->current() }}',
            columns: [
                 { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'kandungan', name: 'kandungan' },
                
                {data: 'action', name: 'action', orderable: false, searchable: false}, // Aksi (tombol)


            ]
        });
    });
    </script>
     <script src="{{ asset('AdminLte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
     <script>
        //message with sweetalert
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
    $(document).ready(function() {
        // Tambah Data
        $('#formTambah').submit(function(e) {
            e.preventDefault();
            $.ajax({
                url: "{{ route('master-gizi.store') }}",
                type: "POST",
                data: $(this).serialize(),
                 headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#modalTambah').modal('hide');
                    Swal.fire("Sukses", "Data berhasil ditambahkan", "success");
                    $('#tbl_list_master_kandungan_gizi').DataTable().ajax.reload();
                }
            });
        });

        // Edit Data
        $(document).on('click', '.btn-edit', function() {
            let id = $(this).data('id');
            $.get("{{ url('master-gizi') }}/" + id + "/edit", function(data) {
                $('#edit_id').val(data.id);
                $('#edit_kandungan').val(data.kandungan);
                $('#modalEdit').modal('show');
            });
        });
       $(document).on('click', '.btn-delete', function() {
            let id = $(this).data('id');
            console.log("Tombol delete diklik, ID:", id);

            Swal.fire({
                title: "Yakin ingin menghapus?",
                text: "Data yang dihapus tidak bisa dikembalikan!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Ya, Hapus!",
                cancelButtonText: "Batal"
            }).then((result) => {
                console.log("Hasil konfirmasi:", result);

                // Pakai `result.value` bukan `result.isConfirmed`
                if (result.value) {
                    console.log("Konfirmasi OK, lanjut delete...");

                    Swal.fire({
                        title: "Menghapus...",
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: "/master-gizi/" + id,
                        type: "DELETE",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            console.log("Delete sukses:", response);
                            Swal.fire("Terhapus!", "Data berhasil dihapus.", "success");
                            $('#tbl_list_master_kandungan_gizi').DataTable().ajax.reload();
                        },
                        error: function(xhr) {
                            console.log("Delete gagal:", xhr.responseText);
                            Swal.fire("Gagal!", "Terjadi kesalahan saat menghapus data.", "error");
                        }
                    });
                } else {
                    console.log("Penghapusan dibatalkan oleh user.");
                }
            });
        });


        $('#formEdit').submit(function(e) {
            e.preventDefault();
            let id = $('#edit_id').val();
            $.ajax({
                url: "{{ url('master-gizi') }}/" + id,
                type: "PUT",
                data: $(this).serialize(),
                 headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#modalEdit').modal('hide');
                    Swal.fire("Sukses", "Data berhasil diperbarui", "success");
                    $('#tbl_list_master_kandungan_gizi').DataTable().ajax.reload();
                }
            });
        });
    });
    </script>
    
    <!-- jQuery -->
</body>
</html>
