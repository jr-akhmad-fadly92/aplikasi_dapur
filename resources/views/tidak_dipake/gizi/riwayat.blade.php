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
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">Starter Page</li>
                            </ol>
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
                            <h1>{{ $header }}</h1>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <h4>Bahan Baku</h4> 
                            <div class="table-responsive">
                                <table class="table ">
                                    <thead>
                                        <tr>
                                            <th>Tangal</th> 
                                            <th>Nomor PO</th> 
                                            <th>Kategori</th> 
                                            <th>Nama Barang</th> 
                                            <th>Jumlah</th> 
                                            <th>Satuan</th> 
                                            <th>pH</th> 
                                            <th>Kadar Air (%)</th> 
                                            <th>Kadar Protein (mg)</th> 
                                            <th>Kadar Formalin (mg/l)</th>
                                        </tr>
                                    </thead> 
                                    <tbody>
                                        <tr>
                                            <td>19 Agustus 2024</td> 
                                            <td>PO-0001</td> 
                                            <td>Bahan Baku</td> 
                                            <td>Ayam</td> 
                                            <td>200</td> 
                                            <td>Kg</td> 
                                            <td>7</td> 
                                            <td>10</td> 
                                            <td>10</td> 
                                            <td>0</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /.card-body -->
                        </div>
                        <!-- /.card -->

                        <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">DataTable with default features</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body"><h4>Masakan</h4> <div class="table-responsive"><table class="table"><thead class="align-middle"><tr><th rowspan="2">Tangal</th> <th rowspan="2">Batch</th> <th rowspan="2">Resep</th> <th colspan="4" class="text-center">Karbohidrat</th> <th colspan="4" class="text-center">Sayur</th> <th colspan="4" class="text-center">Protein</th></tr> <tr><th>pH</th> <th>Kadar Air (%)</th> <th>Kadar Protein (mg)</th> <th>Kadar Formalin (mg/l)</th> <th>pH</th> <th>Kadar Air (%)</th> <th>Kadar Protein (mg)</th> <th>Kadar Formalin (mg/l)</th> <th>pH</th> <th>Kadar Air (%)</th> <th>Kadar Protein (mg)</th> <th>Kadar Formalin (mg/l)</th></tr></thead> <tbody><tr><td>19 Agustus 2024</td> <td>1</td> <td>Resep 1</td> <td>7</td> <td>10</td> <td>10</td> <td>0</td> <td>7</td> <td>10</td> <td>10</td> <td>0</td> <td>7</td> <td>10</td> <td>10</td> <td>0</td></tr></tbody></table></div></div>
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
    $('#tbl_list_master_bahan').DataTable({
            
            ajax: '{{ url()->current() }}',
            columns: [
                 { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'bahan', name: 'bahan' },
                { data: 'gramasi', name: 'gramasi' },
                { data: 'nama_satuan_gudang', name: 'nama_satuan_gudang' },
                { data: 'nama_satuan_bahan', name: 'nama_satuan_bahan' },
                
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
