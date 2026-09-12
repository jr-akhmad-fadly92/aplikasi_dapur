<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">
<head>
    <title>{{ $header }}</title>
    @include('Template.head')
    <style>
        .form-control {
            height: 40px; /* Sesuaikan tinggi */
            width: 100%; /* Pastikan width full */
        }
        
        .select2-container .select2-selection--single {
            height: 40px !important; /* Samakan dengan input lainnya */
            padding: 5px;
            display: flex;
            align-items: center;
        }
        
        .select2-selection__rendered {
            line-height: 30px !important;
        }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
                                <h3 class="card-title">
                                    <a href="{{ route('mastermenu.index') }}" class="btn btn-primary flex-fill" id="btn-edit-post">Selesai</a>
                                <button type="button" class="btn btn-primary flex-fill" data-toggle="modal" data-target="#modalTambah">
                                    Tambah Rincian Menu
                                </button>                       
                                </h3>
                                
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
                            <div class="card-body table-responsive">
                                
                                <table id="tbl_list_master_menu" class="table table-bordered table-hover " style="width: 100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Resep</th>
                                        <th>Bahan</th>
                                        <th>Jumlah</th>
                                        <th>Satuan</th>
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
        <!-- Modal Tambah bumbu -->
        <div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahLabel">Tambah Rincian Menu Harian</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formTambah">
                <div class="form-group" hidden>
                    <label for="id_menu">id menu</label>
                    <input type="text" class="form-control" id="id_menu" name="id_menu" value="{{ $idmenu }}" required>
                </div>
                <div class="form-group">
                    <label for="bahan_id">Bahan</label>
                    <select class="form-control select2" id="bahan_id" name="bahan_id" style="width: 100%">
                    @foreach ($bumbu as $data)
                        <option value={{ $data->id }}>{{ $data->bahan }} ({{$data->satuan }})</option>
                    @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="jumlah">Jumlah</label>
                    <input type="number" class="form-control" id="jumlah" name="jumlah" required>
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
            </div>
        </div>
        </div>
        
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
                { data: 'resep', name: 'resep' },
                { data: 'bahan', name: 'bahan' },
                { data: 'jumlah_bahan', name: 'jumlah_bahan' },
                { data: 'satuan', name: 'satuan' },
               

            ]
        });
        // Event submit form untuk insert data
        
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
                $('#formTambah').submit(function (e) {
                e.preventDefault();
                var formData = {
                    id_menu : $('#id_menu').val(),
                    bahan_id: $('#bahan_id').val(),
                    jumlah: $('#jumlah').val(),
                    _token: '{{ csrf_token() }}'
                };

                $.ajax({
                    url: '{{ route("tambahan_rincian_menu_harian.store") }}',
                    type: 'POST',
                    data: formData,
                    success: function (response) {
                        if(response.success) {
                            $('#modalTambah').modal('hide');
                            $('#formTambah')[0].reset();
                            $('#tbl_list_master_menu').DataTable().ajax.reload(); // Refresh DataTable
                            Swal.fire({
                                icon: "success",
                                title: "BERHASIL",
                                text: "{{ session('success') }}",
                                showConfirmButton: false,
                                timer: 2000
                            });
                            
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function (xhr) {
                        alert('Terjadi kesalahan dalam menyimpan data');
                    }
                });
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // Event saat tombol Update diklik
            $(document).on('click', '.update-jumlah', function() {
                let id = $(this).data('id');
                let jumlah = $(this).closest('tr').find('.jumlah').val();
               
                $.ajax({
                    url: "{{ route('rincian_bahan.update_jumlah') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: id,
                        jumlah : jumlah
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
                        alert(jumlah);
                    }
                });
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#bahan_id').select2({
                placeholder: "Pilih Bahan",
                allowClear: true
            });
        });
    </script>
    <!-- jQuery -->
</body>
</html>
