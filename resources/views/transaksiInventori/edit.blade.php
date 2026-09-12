<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">
<head>
    <title>Edit Transaksi</title>
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
                            <h1 >Edit Transaksi</h1>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                        <form action="{{ route('transaksiinventori.update', $transaksi->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label>Kode Barang</label>
                                <input type="text" class="form-control" name="kode_barang" value="{{ $transaksi->kode_barang }}" readonly>
                            </div>

                            <div class="form-group">
                                <label>Kondisi</label>
                                <input type="text" class="form-control" name="kondisi" value="{{ $transaksi->kondisi }}" required>
                            </div>

                            <div class="form-group">
                                <label>PIC</label>
                                <input type="text" class="form-control" name="pic" value="{{ $transaksi->pic }}" required>
                            </div>

                            <div class="form-group">
                                <label>Jumlah</label>
                                <input type="number" class="form-control" name="jumlah" value="{{ $transaksi->jumlah }}" required>
                            </div>

                            <div class="form-group">
                                <label>Status</label>
                                <input type="text" class="form-control" name="status" value="{{ $transaksi->status }}" required>
                            </div>

                            <button type="submit" class="btn btn-success">Update</button>
                        </form>
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
  
    @include('Template.script')
    
    <!-- jQuery -->
</body>
</html>
