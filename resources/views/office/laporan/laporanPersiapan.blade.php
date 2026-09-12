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
                                <div class="col-md-9 col-xs-12">
                                    <h3 class="card-title">{{$header}}</h3>
                                </div>
                                <div class="col-md-3 col-xs-12"> 
                                    <button onclick="downloadLaporan()" class="btn btn-primary btn-xs float-right"> Download Laporan</button>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body" id="laporan_persiapan">
                            
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
        
        $(document).ready(function() {
            $('.datepicker').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                locale: {
                    format: 'DD-MM-YYYY' // Format tanggal
                }
            });

            $('#btn_tanggal').click(function() {
                // Ambil tanggal yang dipilih
                var tanggal = $('#tanggal').val();

                // Kirim request AJAX ke controller
                $.ajax({
                    url: '{{ url("/laporanPersiapan/ajax_updateLaporanPersiapan") }}', // Pastikan URL sudah sesuai
                    method: 'POST',
                    data: {
                        tanggal: tanggal,
                        _token: '{{ csrf_token() }}' // Pastikan CSRF token disertakan
                    },
                    success: function(response) {
                        // Update konten laporan_persiapan dengan view yang diterima
                        $('#laporan_persiapan').html(response);
                    },
                    error: function(xhr, status, error) {
                        console.error("Error: " + error);
                    }
                });
            });
        });
        function downloadLaporan() {
            var tanggal = moment($("#tanggal").val(), 'DD-MM-YYYY').format('YYYY-MM-DD');
            window.location.href = "{{url('/testlaporan')}}?tanggal=" + tanggal;
        }
        
    </script>
</body>
</html>
