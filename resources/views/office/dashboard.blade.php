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
                    <!-- Third Row for Table -->
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 ><i class="fas fa-calendar-alt mr-2"></i>Daftar Menu Periode {{ $keterangan_periode ?? 0 }}</h4>
                                
                            </div>
                            <div class="card-body">
                                <table id="table-periode-1" class="table table-bordered table-striped" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Tanggal</th>
                                            <th>Hari</th>
                                            <th>Karbo</th>
                                            <th>Protein</th>
                                            <th>Sayur</th>
                                            <th>Buah</th>
                                            <th>Susu</th>
                                            <th>Porsi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Data akan dimuat via DataTables -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End of Third Row -->
                   <div class="row mt-4">
                        <div class="col-12">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title" style="font-weight: bold">
                                        <i class="fas fa-utensils mr-2"></i>
                                         <span id="ompreng-line-title">Ompreng per Line - {{ date('d F Y') }} {{ $menu->tanggal_kirim ?? '-' }}</span>
                                    </h3>
                                    
                                </div>
                                <div class="card-body">
                                    <!-- Line Production Cards -->
                                    <div class="row">
                                        <!-- LINE 1 -->
                                        <div class="col-lg-3 col-6">
                                            <div class="small-box bg-primary">
                                                <div class="inner text-center">
                                                    <h5>LINE 1</h5>
                                                    <h3 id="ompreng-line-1">{{ $ompreng_line_1 ?? 0 }}</h3>
                                                    <p>Total Ompreng</p>
                                                </div>
                                                <div class="icon">
                                                    <i class="fas fa-chart-bar"></i>
                                                </div>
                                                <div class="small-box-footer">
                                                    <span class="badge badge-success">
                                                        <i class="fas fa-check-circle"></i> AKTIF
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- LINE 2 -->
                                        <div class="col-lg-3 col-6">
                                            <div class="small-box bg-success">
                                                <div class="inner text-center">
                                                    <h5>LINE 2</h5>
                                                    <h3 id="ompreng-line-2">{{ $ompreng_line_2 ?? 0 }}</h3>
                                                    <p>Total Ompreng</p>
                                                </div>
                                                <div class="icon">
                                                    <i class="fas fa-chart-bar"></i>
                                                </div>
                                                <div class="small-box-footer">
                                                    <span class="badge badge-success">
                                                        <i class="fas fa-check-circle"></i> AKTIF
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- LINE 3 -->
                                        <div class="col-lg-3 col-6">
                                            <div class="small-box bg-info">
                                                <div class="inner text-center">
                                                    <h5>LINE 3</h5>
                                                    <h3 id="ompreng-line-3">{{ $ompreng_line_3 ?? 0 }}</h3>
                                                    <p>Total Ompreng</p>
                                                </div>
                                                <div class="icon">
                                                    <i class="fas fa-chart-bar"></i>
                                                </div>
                                                <div class="small-box-footer">
                                                    <span class="badge badge-success">
                                                        <i class="fas fa-check-circle"></i> AKTIF
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- LINE 4 -->
                                        <div class="col-lg-3 col-6">
                                            <div class="small-box bg-warning">
                                                <div class="inner text-center">
                                                    <h5>LINE 4</h5>
                                                    <h3 id="ompreng-line-4">{{ $ompreng_line_4 ?? 0 }}</h3>
                                                    <p>Total Ompreng</p>
                                                </div>
                                                <div class="icon">
                                                    <i class="fas fa-chart-bar"></i>
                                                </div>
                                                <div class="small-box-footer">
                                                    <span class="badge badge-success">
                                                        <i class="fas fa-check-circle"></i> AKTIF
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Summary Cards - 6 Kotak Layout -->
                                    <div class="row mt-3">
                                        <!-- Total Ompreng dari semua line -->
                                        <div class="col-lg-4 col-6">
                                            <div class="small-box bg-secondary">
                                                <div class="inner text-center">
                                                    <h5>TOTAL OMPRENG</h5>
                                                    <h3 id="ompreng-lines-total">{{ $total_ompreng_lines ?? 0 }}</h3>
                                                    <p>Total dari semua line</p>
                                                </div>
                                                <div class="icon">
                                                    <i class="fas fa-chart-line"></i>
                                                </div>
                                                <div class="small-box-footer">
                                                    <span class="badge badge-light">
                                                        <i class="fas fa-calculator"></i> TOTAL PRODUKSI
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Total Porsi dari rincian_sekolah -->
                                        <div class="col-lg-4 col-6">
                                            <div class="small-box bg-dark">
                                                <div class="inner text-center">
                                                    <h5>TOTAL PORSI</h5>
                                                    <h3>{{ number_format($total_porsi ?? 0) }}</h3>
                                                    <p>Dari rincian sekolah</p>
                                                </div>
                                                <div class="icon">
                                                    <i class="fas fa-users"></i>
                                                </div>
                                                <div class="small-box-footer">
                                                    <span class="badge badge-light">
                                                        <i class="fas fa-school"></i> PENERIMA SEKOLAH
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Spacer untuk layout 6 kotak (2 baris, 3 kolom) -->
                                        <div class="col-lg-4 col-6">
                                            <div class="small-box bg-danger">
                                                <div class="inner text-center">
                                                    <h5>STATUS KELUAR</h5>
                                                    <h3>{{ $ompreng_keluar ?? 0 }}</h3>
                                                    <p>Ompreng sedang keluar</p>
                                                </div>
                                                <div class="icon">
                                                    <i class="fas fa-truck"></i>
                                                </div>
                                                <div class="small-box-footer">
                                                    <span class="badge badge-light">
                                                        <i class="fas fa-shipping-fast"></i> DALAM PERJALANAN
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> 
                <!-- End of First Row -->
                

                
                    <!-- Management Ompreng Section -->
                    
                    <div class="row">
                    <div class="col-12">
                        <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title" style="font-weight: bold">
                            <i class="fas fa-user mr-2"></i>
                            Data Penerima Manfaat Gizi ( Total Jumlah Sekolah | Total Jumlah Siswa )
                            </h3>
                        </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-6">
                        <!-- small box -->
                        <div class="small-box bg-info">
                            <div class="inner text-center">
                                <h5>Sekolah</h5>
                                <h3>{{ $jumlah_sekolah_all }} | {{ $penerima_sekolah_all }} <i class="fa fa-solid fa-user"></i></h3>
                            </div>
                            <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-6">
                        <!-- small box -->
                        <div class="small-box bg-info">
                            <div class="inner text-center">
                                <h5>TK - SD 3</h5>
                                <h3>{{ $jumlah_sekolah_tk }} | {{ $penerima_sekolah_tk }} <i class="fa fa-solid fa-user"></i></h3>
                            </div>
                            <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-6">
                        <!-- small box -->
                        <div class="small-box bg-info">
                            <div class="inner text-center">
                                <h5>SD 4 - 6</h5>
                                <h3>{{ $jumlah_sekolah_sd }} | {{ $penerima_sekolah_sd }} <i class="fa fa-solid fa-user"></i></h3>
                            </div>
                            <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-6">
                        <!-- small box -->
                        <div class="small-box bg-info">
                            <div class="inner text-center">
                                <h5>SMP</h5>
                                <h3>{{ $jumlah_sekolah_smp }} | {{ $penerima_sekolah_smp }} <i class="fa fa-solid fa-user"></i></h3>
                            </div>
                            <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-6">
                        <!-- small box -->
                        <div class="small-box bg-info">
                            <div class="inner text-center">
                                <h5>SMA</h5>
                                <h3>{{ $jumlah_sekolah_sma }} | {{ $penerima_sekolah_sma }} <i class="fa fa-solid fa-user"></i></h3>
                            </div>
                            <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-6">
                        <!-- small box -->
                        <div class="small-box bg-info">
                            <div class="inner text-center">
                                <h5>IBU</h5>
                                <h3>{{ $jumlah_sekolah_ibu }} | {{ $penerima_sekolah_ibu }} <i class="fa fa-solid fa-user"></i></h3>
                            </div>
                            <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>
                        </div>
                    </div>
                    <!-- ./col --> 
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

    <!-- DataTables Scripts -->
    <script src="{{ asset('AdminLte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('AdminLte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('AdminLte/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>

    <!-- SweetAlert2 -->
    <script src="{{ asset('AdminLte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            // Dapatkan informasi periode dari backend untuk menghindari duplikasi logic
            var today = new Date();
            var currentDay = today.getDate();

            // Simplified frontend logic - let backend handle the complex period determination
            var tableTitle = '<i class="fas fa-calendar-alt mr-2"></i>Laporan Menu Harian';
            var badgeText = 'Menampilkan data berdasarkan tanggal akses (tgl ' + currentDay + ')';

            $('#table-title').html(tableTitle);
            $('#periode-badge').html(badgeText).removeClass('badge-info').addClass('badge-info');

            // Simplified debug info
            console.log('Dashboard Filter Active - Current Day: ' + currentDay);

            // Inisialisasi DataTable dengan filter periode otomatis berdasarkan tanggal akses
            var table1 = $('#table-periode-1').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('laporan.menu.periode1') }}",
                    data: function (d) {
                        // Parameter untuk mengaktifkan filter periode otomatis di controller
                        d.dashboard_filter = true;
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'tanggal', name: 'tanggal' },
                    { data: 'hari', name: 'hari' },
                    { data: 'karbohidrat', name: 'karbo' },
                    { data: 'protein', name: 'protein' },
                    { data: 'sayur', name: 'sayur' },
                    { data: 'buah', name: 'buah' },
                    { data: 'susu', name: 'susu' },
                    { data: 'porsi_formatted', name: 'porsi' },
                ],
                responsive: true,
                autoWidth: false,
                language: {
                    processing: "Memproses...",
                    search: "Cari:",
                   //info: "Menampilkan START sampai END dari TOTAL data",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                    infoFiltered: "(disaring dari MAX total data)",
                    loadingRecords: "Memuat...",
                    zeroRecords: "Tidak ada data yang cocok",
                    emptyTable: "Tidak ada data tersedia",
                    paginate: {
                        first: "Pertama",
                        previous: "Sebelumnya",
                        next: "Selanjutnya",
                        last: "Terakhir"
                    }
                }
            });
            // Load data pertama kali
            table1.ajax.reload();

            function refreshOmprengPerLine() {
                $.ajax({
                    url: "{{ route('dashboard_office.ajax_ompreng_per_line') }}",
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (!response || !response.success) {
                            return;
                        }

                        $('#ompreng-line-1').text(response.ompreng_line_1 ?? 0);
                        $('#ompreng-line-2').text(response.ompreng_line_2 ?? 0);
                        $('#ompreng-line-3').text(response.ompreng_line_3 ?? 0);
                        $('#ompreng-line-4').text(response.ompreng_line_4 ?? 0);
                        $('#ompreng-lines-total').text(response.total_ompreng_lines ?? 0);
                        $('#ompreng-line-title').text('Ompreng per Line - ' + (response.date_label || '-') + ' ' + (response.tanggal_kirim || '-'));
                    }
                });
            }

            setInterval(refreshOmprengPerLine, 5000);
        });
    </script>
</body>
</html>
