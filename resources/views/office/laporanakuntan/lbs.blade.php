<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">
<head>
    <title>Laporan Biaya Infrastruktur dan Peralatan</title>
    <style>
    .btn-custom {
        margin-right: 8px; /* kasih spasi default */
    }
    </style>
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
                                <h4>Laporan Biaya Infrastruktur dan Peralatan</h4><br>
                                <form method="GET" action="{{ route('lbs.index') }}" >
                                <div class="form-group row">
                                    
                                     <label for="tanggal" class="col-sm-1 col-form-label">Tanggal</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control form-control-sm datepicker" id="tanggal_mulai" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}" >
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control form-control-sm datepicker" id="tanggal_selesai" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}" >
                                    </div>
                                    <div class="col-sm-3 d-flex">
                                        <button type="submit" class="btn btn-primary btn-custom">Cari Data </button>
                                        <a href="{{ route('lbs.cetak2', request()->all()) }}" class="btn btn-primary btn-custom">Cetak Data</a>
                                    </div>
                                    
                                </div>
                                </form>
                            </div>
                        </div>
                        <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-9 col-xs-12">
                                    <h3 class="card-title">Laporan Biaya Infrastruktur dan Peralatan</h3>
                                    
                                </div>
                               
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="5%">No.</th>
                                        <th width="15%">Tanggal</th>
                                        <th width="40%">Uraian</th>
                                        <th width="20%" class="text-end">Nominal (Rp)</th>
                                        <th width="15%">Keterangan</th>
                                    </tr>
                                </thead>
                        
                                <tbody>
                                    @php
                                    $i=0;    
                                    @endphp
                                    @foreach ($data as $row)
                                    @php
                                    $i=$i+1;    
                                    @endphp
                                        <tr>
                                            <td class="text-center">{{ $i }}</td>
                                            <td class="text-center">{{ \Carbon\Carbon::parse($row->tanggal)->format('d-m-Y') }}</td>
                                            <td>{{ $row->deskripsi }}</td>
                                            <td class="text-end">{{ number_format($row->jumlah, 0, ',', '.') }}</td>
                                            <td class="text-center">
                                                @if($row->status == 1)
                                                    ACC
                                                @else
                                                    Revisi
                                                @endif
                                            </td>
                                        </tr>
                                    
                                      
                                    @endforeach
                                    
                                </tbody>

                                    @if($data->count() > 0)
                                        <tfoot>
                                            <tr>
                                                <th colspan="3" class="text-end">TOTAL</th>
                                                <th class="text-end">{{ number_format($total, 0, ',', '.') }}</th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    @endif
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
                url: '{{ url("dt_Laporan") }}',
                data: function (d) {
                    d.tanggal = $('#tanggal').val();
                }
                
            },
            
            columns: [
                { title: 'Keterangan' },
                { title: 'Aksi' }
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
</body>
</html>
