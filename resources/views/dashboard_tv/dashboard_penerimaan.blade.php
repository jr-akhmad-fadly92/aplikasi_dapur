<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">
<head>
    <title>{{ $header }}</title>
    @include('Template.head')
    <script>
        // Reload halaman setiap 30 detik
        setInterval(function() {
            location.reload();
        }, 30000);
    </script>
    <style>
        .content-title {
            text-align: center;
            width: 100%;
        }
    </style>
</head>
<body class="sidebar-mini sidebar-collapse sidebar-closed">
    <div class="wrapper">

        <!-- Navbar -->
        @include('Template.navbar')
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        @include('Template.left-sidebar-dashboard')

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
                        <!-- Box: Jumlah Ompreng Diluar -->
                        <div class="col-lg-4 col-md-6 col-sm-12">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>0</h3>
                                    <p>Jumlah Ompreng Diluar</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-bag"></i>
                                </div>
                                <a href="{{ route('ompreng.formOmprengMasuk') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        
                        <!-- Box: PO Hari Ini -->
                        <div class="col-lg-4 col-md-6 col-sm-12">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>{{ $jumlah_po_datang }}</h3>
                                    <p>PO Hari ini</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-stats-bars"></i>
                                </div>
                                <a href="{{ route('penerimaan_bahan.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        
                        <!-- Box: BHP Hari Ini >
                        <div-- class="col-lg-4 col-md-6 col-sm-12">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>44</h3>
                                    <p>BHP Hari ini</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-person-add"></i>
                                </div>
                                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div-->
                    </div>
                </div>
            </section>
            
            <!-- Section: Data Kontainer -->
            <section class="content">
                <h1 class="content-title">Data Kontainer</h1>
                <div class="container-fluid">
                    <div class="row">
                        <!-- Box: Jumlah Total -->
                        <div class="col-lg-4 col-md-6 col-sm-12">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ $wadah_total }}</h3>
                                    <p>Jumlah Total</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-bag"></i>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Box: Jumlah Terpakai -->
                        <div class="col-lg-4 col-md-6 col-sm-12">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>{{ $wadah_terpakai }}</h3>
                                    <p>Jumlah Terpakai</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-stats-bars"></i>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Box: Jumlah Tidak Terpakai -->
                        <div class="col-lg-4 col-md-6 col-sm-12">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>{{ $wadah_belum_terpakai }}</h3>
                                    <p>Jumlah Tidak Terpakai</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-person-add"></i>
                                </div>
                           </div>
                        </div>
                    </div>
                </div>
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
    setInterval(function() {
        location.reload();
    }, 30000); // 30 detik
</script>
</body>
</html>
