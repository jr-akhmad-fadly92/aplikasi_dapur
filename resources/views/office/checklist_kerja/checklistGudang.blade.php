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
                                    <button onclick="cetakChecklistGudangKeluar()" class="btn btn-primary btn-xs float-right"> Download Checklist</button>
                                </div>
                                 
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="checklist_gudang_keluar" class="table table-bordered table-hover table-sm">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kode penyimpanan</th>
                                        <th>Jenis</th>
                                        <th>Nama barang</th>
                                        <th>Jumlah</th>
                                        <th>Satuan</th>
                                        <th>Tanggal Keluar</th>
                                        <th>Status</th>
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
        var checklist_gudang_keluar = $('#checklist_gudang_keluar').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ url("checklistGudang/dt_checklistGudangKeluar") }}',
                data: function (d) {
                    d.tanggal = $('#tanggal').val();
                }
                
            },
            
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'kode_wadah', name: 'kode_wadah'},
                {data: 'jenis', name: 'jenis'},
                {data: 'nama_barang', name: 'nama_barang'},
                {data: 'jumlah', name: 'jumlah'},
                {data: 'satuan', name: 'satuan'},
                {data: 'tanggal_keluar', name: 'tanggal_keluar'},
                {data: 'status', name: 'status'},
                
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
            checklist_gudang_keluar.ajax.reload();
        });
        function cetakChecklistGudangKeluar(){
                var tanggal = moment($("#tanggal").val(), 'DD-MM-YYYY').format('YYYY-MM-DD');
                window.open("{{url('/checklistKerja/pdfChecklistGudangKeluar')}}?tanggal="+tanggal, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,location=no,width=720");
        }
        function cetakChecklisthasilmasak(){
                var tanggal = moment($("#tanggal").val(), 'DD-MM-YYYY').format('YYYY-MM-DD');
                window.open("{{url('/hasil_matang/excel_pangan')}}?tanggal="+tanggal, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,location=no,width=720");
        }
    </script>
</body>
</html>
