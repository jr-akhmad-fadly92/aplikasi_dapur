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
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h2>Menu untuk {{ $keterangan_menu }} ({{ $tanggalKirimFormatted }})</h2>
                                </div>
                                <div class="card-body">
                                    <div class="row row-cols-2 row-cols-md-5 gap-4">
                                        @foreach ([
                                            ['img' => 'nasi.png', 'title' => 'Karbohidrat', 'desc' => $menus->nama_karbohidrat ?? '-', 'kode_a' => $histori_masak_a->hasil_porsi_karbohidrat ?? 0, 'kode_b' => $histori_masak_b->hasil_porsi_karbohidrat ?? 0],
                                            ['img' => 'protein.png', 'title' => 'Protein', 'desc' => $menus->nama_protein ?? '-', 'kode_a' => $histori_masak_a->hasil_porsi_protein ?? 0, 'kode_b' => $histori_masak_b->hasil_porsi_protein ?? 0],
                                            ['img' => 'sayur.png', 'title' => 'Sayur', 'desc' => $menus->nama_sayur ?? '-', 'kode_a' => $histori_masak_a->hasil_porsi_sayur ?? 0, 'kode_b' => $histori_masak_b->hasil_porsi_sayur ?? 0],
                                            ['img' => 'buah.png', 'title' => 'Buah', 'desc' => $menus->nama_buah ?? '-', 'kode_a' => $histori_masak_a->hasil_porsi_buah ?? 0, 'kode_b' => $histori_masak_b->hasil_porsi_buah ?? 0],
                                            ['img' => 'susu.png', 'title' => 'Pelengkap', 'desc' => $menus->nama_susu ?? '-', 'kode_a' => $histori_masak_a->hasil_porsi_susu ?? 0, 'kode_b' => $histori_masak_b->hasil_porsi_susu ?? 0]
                                        ] as $item)
                                            <div class="col">
                                                <div class="card bg-soft-primary h-100 text-center">
                                                    <div class="card-body">
                                                        <img src="{{ asset('image/' . $item['img']) }}" alt="" style="width: 60px; height: auto;">
                                                        <h4>{{ $item['desc'] }}</h4>
                                                      
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Jumlah Hari Ini & Sudah Packing -->
                    <div class="row mt-4 gap-4">
                        <div class="col">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h2>Jumlah Hari Ini</h2>
                                    <h2><span>{{ $total }} Pack</span></h2>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    <div class="row">
                    <div class="col-12">
                        <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-8 col-xs-12">
                                    <h2>Bahan yang yang digunakan: {{ now()->setTimezone('Asia/Jakarta')->locale('id')->translatedFormat('l, d F Y') }}</h2>

                                </div>
                                <div class="col-md-4 col-xs-12" style="text-align: right;">
                                    <a href="{{ route('v_formWarehouse', ['type' => 'out']) }}" class="btn btn-sm btn-success"><i class="fa fa-plus-circle"></i> barang keluar</a>

                                </div>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="dt_list_bahan_menu" class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>Resep</th>
                                <th>Bahan</th>
                                
                            </tr>
                            </thead>
                             
                            </table>
                            
                        </div>
                        <!-- /.card-body -->
                        </div>
                        <!-- /.card -->

                        <!-- /.card -->
                        
                        <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-8 col-xs-12">
                                    <h2>Bahan yang harus dikeluarkan hari ini : {{ now()->setTimezone('Asia/Jakarta')->locale('id')->translatedFormat('l, d F Y') }}</h2>

                                </div>
                                <div class="col-md-4 col-xs-12" style="text-align: right;">
                                    <a href="{{ route('v_formWarehouse', ['type' => 'out']) }}" class="btn btn-sm btn-success"><i class="fa fa-plus-circle"></i> barang keluar</a>

                                </div>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="dt_warehouse_must_out" class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Bahan Baku</th>
                                <th>Jumlah</th>
                                <th>Satuan</th>
                                <th>Waktu Tersimpan</th>
                                <th>Status</th>
                                
                            </tr>
                            </thead>
                            
                            </table>
                        </div>
                        <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                        <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-8 col-xs-12">
                                    <h1 >{{ $header }}</h1>
                                </div>
                                <div class="col-md-4 col-xs-12" style="text-align: right;">
                                    <div class="form-group">
                                        <a href="{{ route('v_formWarehouse', ['type' => 'in']) }}" class="btn btn-sm btn-success"><i class="fa fa-plus-circle"></i> barang masuk</a>
                                        <a href="{{ route('v_formWarehouse', ['type' => 'out']) }}" class="btn btn-sm btn-success"><i class="fa fa-plus-circle"></i> barang keluar</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="dt_warehouse_in_stock" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Bahan Baku</th>
                                    <th>Jumlah</th>
                                    <th>Satuan</th>
                                    <th>Waktu Tersimpan</th>
                                    <th>Status</th>
                                    
                                </tr>
                            </thead>
                           
                            </table>
                        </div>
                        <!-- /.card-body -->
                        </div>
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
        var warehouseInStock = $('#dt_warehouse_in_stock').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ url("warehouse/dt_warehouseInStock") }}',
                
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'nama_barang', name: 'nama_barang'},
                {data: 'jumlah', name: 'jumlah'},
                {data: 'nama_satuan', name: 'nama_satuan'},
                {data: 'lama_penyimpanan', name: 'lama_penyimpanan'},
                {data: 'status', name: 'status'},
            ]
        
    
        });
        var warehouseMustOut = $('#dt_warehouse_must_out').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ url("warehouse/dt_warehouseMustOut") }}',
                
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'nama_barang', name: 'nama_barang'},
                {data: 'jumlah', name: 'jumlah'},
                {data: 'id_satuan', name: 'id_satuan'},
                {data: 'lama_penyimpanan', name: 'lama_penyimpanan'},
                {data: 'status', name: 'status'},
            ]
        
    
        });
        var warehouseMustOut = $('#dt_list_bahan_menu').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ url("warehouse/dt_list_bahan") }}',
                
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'resep', name: 'resep'},
                {data: 'jumlah_satuan', name: 'jumlah_satuan'},
               
            ]
        
    
        });
    </script>
    
</body>
</html>
