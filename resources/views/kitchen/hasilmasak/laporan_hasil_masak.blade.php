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
                            <div class="card-body">
                                <div class="form-group row">
                                    <label for="tanggal" class="col-sm-1 col-form-label">Tanggal</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control form-control-sm datepicker" id="tanggal" name="tanggal" value="{{ date('d-m-Y') }}" >
                                    </div>
                                    <div class="col-sm-1">
                                        <button type="button" class="btn btn-primary btn-sm" id="btn_tanggal" >Update</button>
                                      
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-6 col-xs-9">
                                    <h3 class="card-title">{{$header}}</h3>
                                </div>
                                <div class="col-md-6 col-xs-12 text-right"> 
                                        <button onclick="downloadLaporan()" class="btn btn-primary btn-xs mr-1"> Download Laporan</button>
                                        <button onclick="downloadLaporanExcel()" class="btn btn-primary btn-xs mr-1"> Download Laporan Excel</button>
                                </div>
                            </div>
                         </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            
                            <table id="table_laporan" class="table table-bordered table-hover table-sm" style="width: 100%">
                                <thead>
                                    <tr>
                                    
                                    <th rowspan="2">Nama Masakan</th>
                                    
                                    <th colspan="6">Hasil Produksi (Matang)</th>
                                    <th colspan="2">Realisasi Pax</th>
                                    </tr>
                                    <tr>
                                    <th>Qty</th>
                                    <th>Satuan</th>
                                    <th>Qty</th>
                                    <th>Satuan</th>
                                    <th>Jumlah Gastronom</th>
                                    <th>gramasi | waktu</th>
                                    <th>Qty</th>
                                    <th>Satuan</th>
                                    <th>Keterangan</th>
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
       
        var checklist_penerimaan = $('#table_laporan').DataTable({
            processing: false,
            serverSide: false,
            ajax: {
                url: '{{ url("/dt_global_hasil_masak") }}',
                data: function (d) {
                    d.tanggal = $('#tanggal').val();
                },
                error: function(xhr, error, thrown) {
                    console.warn('AJAX error suppressed:', thrown); // Bisa kamu log kalau mau debugging
                }
                
            },
            
            columns: [
                { title: 'Nama Masakan' },
                { title: 'Qty' },
                { title: 'Satuan' },
                { title: 'Qty' },
                { title: 'Satuan' },
                { title: 'Jumlah Gastronom' },
                { title: 'Keterangan' },
                { title: 'Qty' },
                { title: 'Satuan' },
                { title: 'Keterangan' }
            ],
            
            responsive: true,
            stateSave: true,
            stateDuration: 60*30,
        
    
        });
        $(document).ready(function() {
            $('.datepicker').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                locale: {
                    format: 'DD-MM-YYYY' // Format tanggal
                }
            });
        });
        $('#btn_tanggal').on('click', function() {
            checklist_penerimaan.ajax.reload();
        });
        
        
    </script>
    <script>
        function downloadLaporan() {
            var tanggal = moment($("#tanggal").val(), 'DD-MM-YYYY').format('YYYY-MM-DD');
            window.location.href = "{{url('/laporan-hasil-masak')}}?tanggal=" + tanggal;
        }
        function downloadLaporanExcel() {
            var tanggal = moment($("#tanggal").val(), 'DD-MM-YYYY').format('YYYY-MM-DD');
            window.location.href = "{{url('/hasil_matang/excel_pangan')}}?tanggal=" + tanggal;
        }
    </script>
</body>
</html>
