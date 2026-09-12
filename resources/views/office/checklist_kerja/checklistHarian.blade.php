
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
                                    <h3 class="card-title">Checklist</h3>
                                </div>
                                <div class="col-md-3 col-xs-12"> 
                                 
                                </div>
                                 
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table class="table table-bordered table-hover table-sm">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Form / Checklist</th>
                                        <th style="text-align: center">Tombol</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th>1</th>
                                        <th>Cheklist Gudang Keluar</th>
                                        <th style="text-align: center"><button onclick="cetakChecklistGudangKeluar()" class="btn btn-primary btn-xs "> Download </button></th>
                                    </tr>
                                    <tr>
                                        <th>2</th>
                                        <th>Checklist Penerimaan 1</th>
                                        <th style="text-align: center"><button onclick="cetakChecklistPenerimaan()" class="btn btn-primary btn-xs "> Download</button></th>
                                    </tr>
                                    <tr>
                                        <th>3</th>
                                        <th>Checklist Penerimaan 2</th>
                                        <th style="text-align: center"> <button onclick="cetakChecklistPenerimaan2()" class="btn btn-primary btn-xs mr-1">Download</button>
                                    </th>
                                    </tr>
                                    <tr>
                                        <th>4</th>
                                        <th>Checklist Penerimaan Non Pangan</th>
                                        <th style="text-align: center"> <button onclick="cetakChecklistPenerimaanNonPangan()" class="btn btn-primary btn-xs mr-1">Download</button></th>
                                    </tr>
                                    <tr>
                                        <th>5</th>
                                        <th>Form Keluar non pangan </th>
                                        <th style="text-align: center"> <button onclick="cetak_excel_form_pengeluaran_non_pangan()" class="btn btn-primary btn-xs mr-1"> Download</button></th>
                                    </tr>
                                   
                                    <tr>
                                        <th>6</th>
                                        <th>Form Stok Opnam</th>
                                        <th style="text-align: center"> <button onclick="cetak_excel_Non_Pangan_stok_opnam()" class="btn btn-primary btn-xs mr-1"> Download</button></th>
                                    </tr>
                                    
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                        </div>
                        <!-- /.card -->

                        <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-9 col-xs-12">
                                    <h3 class="card-title">Laporan</h3>
                                </div>
                                <div class="col-md-3 col-xs-12"> 
                                 
                                </div>
                                 
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table class="table table-bordered table-hover table-sm">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Form / Checklist</th>
                                        <th style="text-align: center">Tombol</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                    <tr>
                                        <th>1</th>
                                        <th>Laporan Timbangan Hasil Masak</th>
                                        <th style="text-align: center"> <button onclick="cetak_excel_pangan_hasil_matang()" class="btn btn-primary btn-xs mr-1"> Download </button></th>
                                    </tr>
                                   
                                    <tr>
                                        <th>2</th>
                                        <th>Laporan Harian Dapur</th>
                                        <th style="text-align: center"> <button onclick="cetak_excel_laporan_operasional()" class="btn btn-primary btn-xs mr-1"> Download</button></th>
                                    </tr>
                                </tbody>
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
        function cetakChecklistPenerimaan(){
                var tanggal = moment($("#tanggal").val(), 'DD-MM-YYYY').format('YYYY-MM-DD');
                window.open("{{url('/checklistKerja/pdfChecklistPenerimaan_2')}}?tanggal="+tanggal, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,location=no,width=720");
        }
        function cetakChecklistPenerimaan2(){
                var tanggal = moment($("#tanggal").val(), 'DD-MM-YYYY').format('YYYY-MM-DD');
                window.open("{{url('/checklistKerja/excelChecklistPenerimaan')}}?tanggal="+tanggal, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,location=no,width=720");
        }
        function cetakChecklistPenerimaanNonPangan(){
                var tanggal = moment($("#tanggal").val(), 'DD-MM-YYYY').format('YYYY-MM-DD');
                window.open("{{url('/checklistKerja/excel_checklistKerja_Penerimaan_non_pangan')}}?tanggal="+tanggal, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,location=no,width=720");
        }

        function cetak_excel_form_pengeluaran_non_pangan(){
                var tanggal = moment($("#tanggal").val(), 'DD-MM-YYYY').format('YYYY-MM-DD');
                window.open("{{url('/Form_pengeluaran/excel_form_pengeluaran_non_pangan')}}?tanggal="+tanggal, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,location=no,width=720");
        }

        function cetak_excel_pangan_hasil_matang(){
                var tanggal = moment($("#tanggal").val(), 'DD-MM-YYYY').format('YYYY-MM-DD');
                window.open("{{url('/hasil_matang/excel_pangan')}}?tanggal="+tanggal, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,location=no,width=720");
        }

        function cetak_excel_Non_Pangan_stok_opnam(){
                var tanggal = moment($("#tanggal").val(), 'DD-MM-YYYY').format('YYYY-MM-DD');
                window.open("{{url('/Stok_opnam/excel_Non_Pangan')}}?tanggal="+tanggal, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,location=no,width=720");
        }

        function cetak_excel_laporan_operasional(){
                var tanggal = moment($("#tanggal").val(), 'DD-MM-YYYY').format('YYYY-MM-DD');
                window.open("{{url('/laporan_harian_dapur/excel')}}?tanggal="+tanggal, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,location=no,width=720");
        }
    </script>
</body>
</html>
