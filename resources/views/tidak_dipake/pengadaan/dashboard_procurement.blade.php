<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ $header }}</title>
    @include('Template.head')
    <style>
        .scrollable-table {
            max-height: 300px; /* You can adjust this height as needed */
            overflow-y: auto;  /* Enables vertical scrolling */
        }
        
        .card-body {
            min-height: 400px; /* Menambahkan tinggi minimum agar card lebih panjang ke bawah */
        }

        .card-header h3 {
            text-align: center;
        }

        /* Mengatur agar teks di dalam tabel rata kiri */
        .table th, .table td {
            text-align: left;
        }
    </style>
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
                            <h1 class="m-0 text-dark">{{ $header }}</h1>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <!-- Kotak 1: Bahan Dipesan -->
                        <div class="col-lg-4 col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3>Bahan DiOrder</h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body scrollable-table">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Nama Bahan</th>
                                                    <th>Supplier</th>
                                                    <th>Tanggal Pesan</th>
                                                    <th>Status</th>
                                                    <th>Detail</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>1</td>
                                                    <td>Beras</td>
                                                    <td>PT Abu</td>
                                                    <td>22-10-2023</td>
                                                    <td>Sedikit</td>
                                                    <td>Detail</td>
                                                </tr>
                                                <!-- Add more rows here as needed -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                        </div>

                        <!-- Kotak 2: Stok Bahan -->
                        <div class="col-lg-4 col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3>Stok Bahan</h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body scrollable-table">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Nama Barang</th>
                                                    <th>Jumlah</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>1</td>
                                                    <td>Pisang</td>
                                                    <td>100</td>
                                                </tr>
                                                <!-- Add more rows here as needed -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                        </div>

                        <!-- Kotak 3: Stok Habis -->
                        <div class="col-lg-4 col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3>Stok Habis</h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body scrollable-table">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Nama Barang</th>
                                                    <th>Jumlah</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>1</td>
                                                    <td>Jeruk</td>
                                                    <td>0</td>
                                                </tr>
                                                <!-- Add more rows here as needed -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                        </div>
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.container-fluid -->
            </section>
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
        @include('Template.footer')
    </div>
    <!-- ./wrapper -->

    <!-- REQUIRED SCRIPTS -->
    @include('Template.script')
</body>
</html>
