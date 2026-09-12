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
                        <div class="card-header">
                          <div class="row invoice-info">
                                <div class="col-sm-6 ">
                                <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b>Id dapur</b> <a class="float-right">{{ $dapur->nomor_dapur }}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Tanggal pengajuan</b> <a class="float-right">{{ \Carbon\Carbon::parse($data_menu_harian->tanggal_pengajuan)->translatedFormat('l, d-m-Y') }}</a>
                                </li>
                              
                                </ul>
                                <h3 class="card-title"><a href="{{ route('update_rincian_sekolah_harian',$idmenu) }}" class="edit btn btn-primary  " id="btn-edit-post">Lanjutkan</a></h3>
                                
                                </div>
                                <!-- /.col -->
                                <div class="col-sm-6 invoice-col">
                                <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b>Jumlah</b> <a class="float-right">{{ number_format($jumlah, 0, ',', '.') }} Pax</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Tanggal kirim</b> <a class="float-right">{{ \Carbon\Carbon::parse($data_menu_harian->tanggal_kirim)->translatedFormat('l, d-m-Y') }}</a>
                                </li>
                                
                                </ul>
                                
                                </div>
                                <!-- /.col -->
                                
                                <!-- /.col -->
                            </div>
                        </div>
                        <!-- /.card-header -->
                       
                        </div>
                        <!-- /.card -->
                        <div class="card">
                            <div class="card-header">
                           <h1 >{{ $header }}</h1>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                
                                <table id="tbl_list_master_menu" class="table table-bordered table-hover" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Sekolah</th>
                                        <th>Jumlah Penerima total</th>
                                        <th>Jumlah Penerima a</th>
                                        <th>Jumlah Penerima b</th>
                                        <th>Update</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
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
  
    @include('Template.script')
   
    <script type="text/javascript">
    $(document).ready(function () {
    $('#tbl_list_master_menu').DataTable({
            
            ajax: '{{ url()->current() }}',
            columns: [
                 { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'nama_dan_tingkat_sekolah', name: 'nama_dan_tingkat_sekolah' },
                { data: 'input_jumlah', name: 'input_jumlah' },
                { data: 'input_jumlah_a', name: 'input_jumlah_a' },
                { data: 'input_jumlah_b', name: 'input_jumlah_b' },
                {data: 'action', name: 'action', orderable: false, searchable: false}, // Aksi (tombol)


            ]
        });
    });
    </script>
     <script src="{{ asset('AdminLte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
     <script>
        //message with sweetalert
        @if(session('success'))
            Swal.fire({
                icon: "success",
                title: "BERHASIL",
                text: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 2000
            });
        @elseif(session('error'))
            Swal.fire({
                icon: "error",
                title: "GAGAL!",
                text: "{{ session('error') }}",
                showConfirmButton: false,
                timer: 2000
            });
        @endif
            
    </script>
    <script>
        $(document).ready(function() {
            // Event saat tombol Update diklik
            $(document).on('click', '.update-jumlah', function() {
                let id = $(this).data('id');
                let jumlaha = $(this).closest('tr').find('.jumlaha').val();
                let jumlahb = $(this).closest('tr').find('.jumlahb').val();

                $.ajax({
                    url: "{{ route('rincian_sekolah.update_jumlah') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: id,
                        jumlaha : jumlaha,
                        jumlahb : jumlahb
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: "success",
                            title: "BERHASIL",
                            text: "{{ session('success') }}",
                            showConfirmButton: false,
                            timer: 2000
                        });
                        $('#tbl_list_master_menu').DataTable().ajax.reload();
                    },
                    error: function(xhr) {
                        alert('Terjadi kesalahan, coba lagi!');
                    }
                });
            });
        });
    </script>

    <!-- jQuery -->
</body>
</html>
