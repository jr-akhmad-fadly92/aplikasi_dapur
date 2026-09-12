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
                           <h1 >{{ $header }}</h1>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body"><div class="table-responsive py-4"><table class="table "><thead><tr><th>Tanggal</th> <th>Nama Truk</th> <th>Nama Sekolah</th> <th>Jumlah Porsi</th> <th>Status</th> <td>Aksi</td></tr></thead> <tbody><tr><td>22 Agustus 2024</td> <td>Truk 001</td> <td>SD N 1 Gunung Pati</td> <td>100</td> <td><span class="badge font-size-12 bg-soft-success">Packing Selesai</span></td> <td></td></tr> <tr><td>22 Agustus 2024</td> <td>Truk 002</td> <td>SD N 2 Gunung Pati</td> <td>100</td> <td><span class="badge font-size-12 bg-soft-warning">Belum diproses</span></td> <td><div class="dropdown b-dropdown btn-group" id="__BVID__38"><!----><button aria-haspopup="true" aria-expanded="false" type="button" class="btn dropdown-toggle btn-white font-size-16 text-body p-0" id="__BVID__38__BV_toggle_"><i class="uil uil-ellipsis-v"></i></button><ul role="menu" tabindex="-1" class="dropdown-menu dropdown-menu-right" aria-labelledby="__BVID__38__BV_toggle_"> <a href="#" class="dropdown-item">Proses Packing</a></ul></div></td></tr></tbody></table></div></div>
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
  
    @include('Template.script')
   
    <script type="text/javascript">
    $(document).ready(function () {
    $('#tbl_list_master_satuan').DataTable({
            
            ajax: '{{ url()->current() }}',
            columns: [
                 { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'satuan', name: 'satuan' },
                
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
    
    <!-- jQuery -->
</body>
</html>
