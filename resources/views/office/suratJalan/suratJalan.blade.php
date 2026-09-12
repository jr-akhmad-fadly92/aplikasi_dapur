<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">
<head>
    <title>{{ $header }}</title>
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
                    <div class="col-12">
                        <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-9 col-xs-12">
                                    <h3 class="card-title">Data Surat Jalan</h3>
                                </div>
                                <div class="col-md-3 col-xs-12"> 
                                    <a href="{{ url('suratJalan/formSuratJalan') }}" class="btn btn-primary btn-xs float-right"><i class="fas fa-plus"></i> Tambah Surat Jalan</a>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="surat_jalan" class="table table-bordered table-hover table-sm">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Referensi</th>
                                        <th>No. Surat Jalan</th>
                                        <th>Menu</th>
                                        <th>Rencana Kirim</th>
                                        <th>Status</th>
                                        <th>Tgl. Kirim</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                            
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
        var surat_jalan = $('#surat_jalan').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ url("suratJalan/dt_suratJalan") }}',
                
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'referensi', name: 'referensi'},
                {data: 'no_surat_jalan', name: 'no_surat_jalan'},
                {data: 'menu', name: 'menu'},
                {data: 'rencana_kirim', name: 'rencana_kirim'},
                {data: 'status', name: 'status'},
                {data: 'published_at', name: 'published_at'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            
            responsive: true,
            stateSave: true,
            stateDuration: 60*30,
        
    
        });
    </script>
</body>
</html>
