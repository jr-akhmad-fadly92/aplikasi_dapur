<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">
<head>
    <title>{{ $header }}</title>
    @include('Template.head') <!-- Menyertakan file head -->
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">

        <!-- Navbar -->
        @include('Template.navbar') <!-- Menyertakan navbar -->
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        @include('Template.left-sidebar') <!-- Menyertakan sidebar kiri -->

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
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
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h1>{{ $header }}</h1>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <h3 class="card-title">
                                        <a href="{{ route('resep.create') }}" class="edit btn btn-primary btn-sm" id="btn-edit-post">Tambah</a>
                                        <a href="{{ route('pdf_laporan_master_resep') }}" class="edit btn btn-primary btn-sm" id="btn-edit-post">Download</a>
                                        <a href="{{ route('dataresep.cetak') }}" class="edit btn btn-primary btn-sm" id="btn-edit-post">Download Excel</a>
                                    </h3>
                                    
                                    <!-- Tabel data resep -->
                                    <table id="tbl_list_master_menu" class="table table-bordered table-hover" style="width: 100%">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Menu</th>
                                                <th>Komponen Sehat</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                    <!-- /.tabel -->
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
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <div class="p-3">
                <h5>Title</h5>
                <p>Sidebar content</p>
            </div>
        </aside>
        <!-- /.control-sidebar -->

        <!-- Main Footer -->
        @include('Template.footer') <!-- Menyertakan footer -->
    </div>
    <!-- ./wrapper -->

    <!-- REQUIRED SCRIPTS -->
    @include('Template.script') <!-- Menyertakan script -->
    
    <script type="text/javascript">
        $(document).ready(function () {
            $('#tbl_list_master_menu').DataTable({
                ajax: '{{ url()->current() }}',
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'nama_resep', name: 'nama_resep' },
                    { data: 'nama_komponen', name: 'nama_komponen' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }, // Kolom aksi (tombol)
                ]
            });
        });
    </script>
    
    <!-- SweetAlert untuk notifikasi sukses/gagal -->
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
</body>
</html>
