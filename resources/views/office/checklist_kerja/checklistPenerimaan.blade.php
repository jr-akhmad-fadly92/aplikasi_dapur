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
                                <div class="form-group row d-none">
                                    <label for="jam_pelayanan" class="col-sm-1 col-form-label">Jam</label>
                                    <div class="col-sm-11">
                                        <select class="form-control form-control-sm" id="jam_pelayanan" name="jam_pelayanan">
                                            <option value="semua" selected>Semua</option>
                                            <option value="06:00">06:00</option>
                                            <option value="09:00">09:00</option>
                                            <option value="12:00">12:00</option>
                                            <option value="15:00">15:00</option>
                                            <option value="18:00">18:00</option>
                                        </select>
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
                                    <button onclick="cetakFormPenerimaanAll()" class="btn btn-primary btn-xs mr-1">Form Penerimaan All excel</button>
                                    <button hidden onclick="cetakFormPenerimaan1()" class="btn btn-primary btn-xs mr-1">Form Penerimaan 1 excel</button>
                                    <button hidden onclick="cetakChecklistPenerimaan2()" class="btn btn-primary btn-xs mr-1">Checklist Penerimaan excel</button>
                                    <button onclick="cetakChecklistPenerimaanNonPangan()" class="btn btn-primary btn-xs mr-1">Checklist Non Pangan excel</button>
                                    <button onclick="cetakGudangMasuk()" class="btn btn-primary btn-xs mr-1">Gudang Masuk excel</button>
                                    <button onclick="cetakGudangKeluar()" class="btn btn-primary btn-xs mr-1">Gudang Keluar excel</button>
                                    <button hidden onclick="cetakChecklistPenerimaan()" class="btn btn-primary btn-xs">Download Checklist</button>
                                </div>

                                
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="checklist_penerimaan" class="table table-bordered table-hover table-sm">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Jam Datang</th>
                                        <th>Nama Barang</th>
                                        <th>No. PO</th>
                                        <th>Supplier</th>
                                        <th>Jumlah</th>
                                        <th>Satuan</th>
                                        
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
        var checklist_penerimaan = $('#checklist_penerimaan').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ url("checklistPenerimaan/dt_checklistPenerimaan") }}',
                data: function (d) {
                    d.tanggal = $('#tanggal').val();
                    d.jam_pelayanan = $('#jam_pelayanan').val();
                }
                
            },
            
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'tanggal_kedatangan', name: 'tanggal_kedatangan'},
                {data: 'nama_bahan', name: 'nama_bahan'},
                {data: 'nomor_po', name: 'nomor_po'},
                {data: 'supplier', name: 'supplier'},
                {data: 'jumlah_bahan', name: 'jumlah_bahan'},
                {data: 'satuan', name: 'satuan'},
                
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
        $('#jam_pelayanan').on('change', function() {
            checklist_penerimaan.ajax.reload();
        });
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
        function cetakGudangMasuk(){
            var tanggal = moment($("#tanggal").val(), 'DD-MM-YYYY').format('YYYY-MM-DD');
            window.open("{{ route('excel_penerimaan_checklist_1_1') }}?tanggal="+tanggal, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,location=no,width=720");
        }
        function cetakGudangKeluar(){
            var tanggal = moment($("#tanggal").val(), 'DD-MM-YYYY').format('YYYY-MM-DD');
            var jam = $('#jam_pelayanan').val();
            var url = "{{ route('excel_penerimaan_checklist_2_1') }}?tanggal=" + tanggal;
            if (jam && jam !== 'semua') {
                url += '&jam_pelayanan=' + encodeURIComponent(jam);
            }
            window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,location=no,width=720");
        }
        function cetakFormPenerimaan1(){
            var tanggal = moment($("#tanggal").val(), 'DD-MM-YYYY').format('YYYY-MM-DD');
            window.open("{{ route('Excel_form_Penerimaan_1') }}?tanggal="+tanggal, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,location=no,width=720");
        }
        function cetakFormPenerimaanAll(){
            var tanggal = moment($("#tanggal").val(), 'DD-MM-YYYY').format('YYYY-MM-DD');
            window.open("{{ route('excel_penerimaan_all') }}?tanggal="+tanggal, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,location=no,width=720");
        }
    </script>
</body>
</html>
