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
                            <h3 class="card-title"><button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalTambah">Tambah</button>
                            <a class="btn btn-primary btn-sm" href="{{ route('master_bahan.index') }}">kembali</a></h3>
                            
                            <table id="tbl_list_master_rincian_bahan" class="table table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kemasan</th>
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
                        <h5 class="modal-title" id="modalTambahLabel">Tambah Kemasan</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="formTambah">
                        <div class="modal-body"> 
                            
                                
                            <input type="hidden" class="form-control" id="id_bahan" name="id_bahan" value="{{ $id_bahan }}">
                            
                            
                            <div class="form-group">
                                <label for="kandungan">Jumlah</label>
                                <input type="number" class="form-control" id="jumlah_tambah" name="jumlah_tambah" required>
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
                                <label for="jumlah">Jumlah</label>
                                <input type="number" class="form-control" id="jumlah_update" name="jumlah_update" required>
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
    $('#tbl_list_master_rincian_bahan').DataTable({
            
            ajax: '{{ url()->current() }}',
            columns: [
                 { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                
                { data: 'jumlah_bahan', name: 'jumlah_bahan' },
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
                url: "{{ route('kemasan-materials.store') }}",
                type: "POST",
                data: $(this).serialize(),
                 headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#modalTambah').modal('hide');
                    Swal.fire("Sukses", "Data berhasil ditambahkan", "success");
                    $('#tbl_list_master_rincian_bahan').DataTable().ajax.reload();
                }
            });
        });

        // Edit Data
        $(document).on('click', '.btn-edit', function() {
            let id = $(this).data('id');
            $.get("{{ url('kemasan-materials') }}/" + id + "/edit", function(data) {
                $('#edit_id').val(data.id);
                $('#jumlah_update').val(data.jumlah);
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
                        url: "/kemasan-materials/" + id,
                        type: "DELETE",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            console.log("Delete sukses:", response);
                            Swal.fire("Terhapus!", "Data berhasil dihapus.", "success");
                            $('#tbl_list_master_rincian_bahan').DataTable().ajax.reload();
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
            let formData = $(this).serialize();
            
            console.log("Data yang dikirim:", formData); // Debugging

            $.ajax({
                url: "/rincian-kandungan-gizi/" + id,
                type: "PUT",
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    console.log("Response:", response);
                    $('#modalEdit').modal('hide');
                    Swal.fire("Sukses", "Data berhasil diperbarui", "success");
                    $('#tbl_list_master_rincian_bahan').DataTable().ajax.reload();
                },
                error: function(xhr) {
                    console.log("Error:", xhr.responseText); // Debugging
                    Swal.fire("Error", "Terjadi kesalahan", "error");
                }
            });
        });

    });
    </script>
    <!-- jQuery -->
</body>
</html>
