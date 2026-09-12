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
                            <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#modalKbm">
                            Tambah KBM
                            </button>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                              <table class="table table-bordered" id="tableKbm">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Tahun Ajaran</th>
                                            <th>Semester</th>
                                            <th>Tanggal Mulai</th>
                                            <th>Tanggal Selesai</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                </table>
                        </div>
                        <!-- /.card-body -->
                        </div>
                        <!-- /.card -->

                        <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">DataTable with default features</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            
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
        
        <div class="modal fade" id="modalKbm" tabindex="-1" role="dialog" aria-labelledby="modalKbmLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="formKbm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="modalKbmLabel">Tambah KBM</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                @if($kbm)
                <div class="mb-3">
                    <label>Tahun Ajaran</label>
                    <input type="text" name="tahun_ajaran" class="form-control" placeholder="2025/2026" value="{{ $kbm->tahun_ajaran ?? ''}}">
                </div>
                <div class="mb-3">
                    <label>Semester</label>
                    <select name="semester" class="form-control">
                        <option value="">-- Pilih Semester --</option>
                        <option value="ganjil" @if($kbm->semester == 'ganjil') selected @endif >Ganjil</option>
                        <option value="genap" @if($kbm->semester == 'genap') selected @endif >Genap</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Tanggal Mulai KBM</label>
                    <input type="date" name="tanggal_mulai_kbm" class="form-control" value="{{ $kbm->tanggal_mulai_kbm ?? '' }}">
                </div>
                <div class="mb-3">
                    <label>Tanggal Selesai KBM</label>
                    <input type="date" name="tanggal_selesai_kbm" class="form-control" value="{{ $kbm->tanggal_selesai_kbm ?? '' }}">
                </div>
                @else
                <div class="mb-3">
                    <label>Tahun Ajaran</label>
                    <input type="text" name="tahun_ajaran" class="form-control" placeholder="2025/2026" value="">
                </div>
                <div class="mb-3">
                    <label>Semester</label>
                    <select name="semester" class="form-control">
                        <option value="">-- Pilih Semester --</option>
                        <option value="ganjil">Ganjil</option>
                        <option value="genap">Genap</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Tanggal Mulai KBM</label>
                    <input type="date" name="tanggal_mulai_kbm" class="form-control">
                </div>
                <div class="mb-3">
                    <label>Tanggal Selesai KBM</label>
                    <input type="date" name="tanggal_selesai_kbm" class="form-control">
                </div>
                @endif
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
            </form>
        </div>
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
    <script>
    $(document).ready(function(){

        // definisikan datatable dan simpan ke variabel
        var table = $('#tableKbm').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('kbm.data') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'tahun_ajaran', name: 'tahun_ajaran' },
                { data: 'semester', name: 'semester' },
                { data: 'tanggal_mulai_kbm', name: 'tanggal_mulai_kbm' },
                { data: 'tanggal_selesai_kbm', name: 'tanggal_selesai_kbm' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        // Submit form via AJAX
        $('#formKbm').on('submit', function(e){
            e.preventDefault();

            $.ajax({
                url: "{{ route('kbm.store') }}",
                type: "POST",
                data: $(this).serialize(),
                success: function(res){
                    $('#modalKbm').modal('hide'); // tutup modal
                    $('#formKbm')[0].reset(); // reset form

                    // reload datatable setelah insert
                    table.ajax.reload(null, false);

                    // tampilkan sweetalert
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message,
                    });
                },
                error: function(xhr){
                    if(xhr.status === 422){
                        let errors = xhr.responseJSON.errors;
                        let msg = "";
                        $.each(errors, function(key, val){
                            msg += val[0] + "<br>";
                        });
                        Swal.fire({
                            icon: 'error',
                            title: 'Validasi Gagal',
                            html: msg,
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan',
                            text: 'Silakan coba lagi.',
                        });
                    }
                }
            });
        });

    });
</script>


</body>
</html>
