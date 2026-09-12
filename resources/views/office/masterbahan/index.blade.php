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
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">Starter Page</li>
                            </ol>
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
                            <h1>{{ $header }}</h1>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <!-- Action & Filter Section - Single Row -->
                            <div class="row mb-3">
                                <div class="col-md-2">
                                    <label>&nbsp;</label>
                                    <a href="{{ route('master_bahan.create') }}" class="btn btn-primary form-control btn-sm d-flex justify-content-center align-items-center">Tambah</a>
                                </div>
                                <div class="col-md-2">
                                    <label>&nbsp;</label>
                                    <a href="{{ route('pdf_laporan_master_bahan') }}" class="btn btn-primary form-control btn-sm d-flex justify-content-center align-items-center">PDF</a>
                                </div>
                                <div class="col-md-2">
                                    <label>&nbsp;</label>
                                    <a href="{{ route('databahan.cetak') }}" class="btn btn-primary form-control btn-sm d-flex justify-content-center align-items-center">Excel</a>
                                </div>
                                <div class="col-md-2">
                                    <label for="filter_jenis" class="mb-2 d-block"><small>Jenis:</small></label>
                                    <select id="filter_jenis" class="form-control form-control-sm">
                                        <option value="">Semua</option>
                                        <option value="1">Karbo</option>
                                        <option value="2">Lauk</option>
                                        <option value="3">Sayur</option>
                                        <option value="4">Buah</option>
                                        <option value="5">Suplemen</option>
                                        <option value="6">Bumbu</option>
                                        <option value="7">Penunjang</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="filter_satuan" class="mb-2 d-block"><small>Satuan:</small></label>
                                    <select id="filter_satuan" class="form-control form-control-sm">
                                        <option value="">Semua</option>
                                        @php
                                            $satuanList = \App\Models\TbSatuan::all();
                                        @endphp
                                        @foreach($satuanList as $sat)
                                            <option value="{{ $sat->id }}">{{ $sat->satuan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label>&nbsp;</label>
                                    <button id="btn_reset_filter" class="btn btn-secondary form-control btn-sm d-flex justify-content-center align-items-center">Reset</button>
                                </div>
                            </div>
                            
                            <table id="tbl_list_master_bahan" class="table table-bordered table-hover" style="width: 100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>bahan</th>
                                    <th>satuan_bahan</th>
                                    <th>Jenis Bahan</th>
                                    <th>Spesifikasi</th>
                                    <th>action</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                        </div>
                        <!-- /.card -->

                        <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">DataTable with default features</h3>
                        </div>
                        <!-- /.card-header -->
                        
                        <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </div>
                <div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalLabel">Input Penerimaan</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form id="updateForm">
                            <div class="modal-body">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                                <input type="hidden" id="idbahan" name="idbahan">
                                <div class="form-group">
                                    <label>Keterangan</label>
                                    <input type="text" class="form-control" id="keterangan" name="keterangan" required>
                                </div>
                                
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
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
        var table = $('#tbl_list_master_bahan').DataTable({
            ajax: {
                url: '{{ url()->current() }}',
                data: function (d) {
                    d.filter_jenis = $('#filter_jenis').val();
                    d.filter_satuan = $('#filter_satuan').val();
                }
            },
            pageLength: 6,
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'bahan', name: 'bahan' },
                { data: 'nama_satuan_bahan', name: 'nama_satuan_bahan' },
                { data: 'jenis_bahan', name: 'jenis_bahan' },
                { data: 'Spesifikasi_bahan', name: 'Spesifikasi_bahan' },
                {data: 'action', name: 'action', orderable: false, searchable: false}, // Aksi (tombol)
            ]
        });

        // Reload table when filters change
        $('#filter_jenis, #filter_satuan').on('change', function () {
            table.ajax.reload();
        });

        // Reset filters
        $('#btn_reset_filter').on('click', function () {
            $('#filter_jenis').val('');
            $('#filter_satuan').val('');
            table.ajax.reload();
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
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $(document).on('click', '.openModalBtn', function () {
                var idbahan     = $(this).data('idbahan');
                var keterangan = $(this).data('keterangan');
                
                $('#idbahan').val(idbahan);
                $('#keterangan').val(keterangan);
                
                $('#updateModal').modal('show');
            });

            $('#updateForm').submit(function (e) {
                e.preventDefault();

                let formData = $(this).serialize();
                console.log(formData); // Debug: Lihat data sebelum dikirim

                $.ajax({
                    url: '/simpan_spesifikasi_bahan_baku',
                    type: 'POST',
                    data: formData,
                    success: function (response) {
                        Swal.fire('Berhasil!', response.message, 'success');
                        $('#updateModal').modal('hide');
                        $('#updateModal form')[0].reset();
                        $('#tbl_list_master_bahan').DataTable().ajax.reload();
                    },
                    error: function (xhr) {
                        console.error(xhr.responseText); // Debug: Lihat error detail di console
                        Swal.fire('Gagal!', 'Terjadi kesalahan: ' + xhr.responseJSON.message, 'error');
                    }
                });
            });
       }); 
    </script>
    <!-- jQuery -->
</body>
</html>
