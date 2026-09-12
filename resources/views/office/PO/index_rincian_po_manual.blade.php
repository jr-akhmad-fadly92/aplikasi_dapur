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
                                    <b>Id dapur</b> <a class="float-right"></a>
                                </li>
                                <li class="list-group-item">
                                    <b>Tanggal pengajuan</b> <a class="float-right"></a>
                                </li>
                              
                                </ul>
                                <h3 class="card-title">
                                    <a href="{{ route('pengajuan_po.index') }}" class="btn btn-primary flex-fill" id="btn-edit-post">Selesai</a>
                                <button type="button" class="btn btn-primary flex-fill" data-toggle="modal" data-target="#modalTambah">
                                    Tambah Rincian Menu
                                </button>                       
                                </h3>
                                
                                </div>
                                <!-- /.col -->
                                <div class="col-sm-6 invoice-col">
                                <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b>Jumlah</b> <a class="float-right">Pax</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Tanggal kirim</b> <a class="float-right"></a>
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
                                
                                <table id="tbl_list_barang" class="table table-bordered table-hover " style="width: 100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Bahan</th>
                                        <th>Jumlah Bahan</th>
                                        <th>Jumlah PO</th>
                                        <th>Aksi</th>
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
                    <input type="hidden" class="form-control" id="id_po" name="id_po" value="{{ $po->id }}" required>
                    <input type="hidden" class="form-control" id="id_kontrak" name="id_kontrak" value="{{ $po->id_kontrak }}" required>
                    <input type="hidden" class="form-control" id="id_rincian_kontrak" name="id_rincian_kontrak" value="">
               
                <div class="form-group">
                    <label for="id_bahan">Bahan</label>
                    <select class="form-control select2" id="id_bahan" name="id_bahan" style="width: 100%">
                    @foreach ($bahan as $data)
                        <option value={{ $data->id }} data-satuan="{{ $data->satuan_bahan }}"> {{$data->bahan }} </option>
                    @endforeach
                    </select>
                </div>

               

                <div class="form-group">
                    <label for="jumlah">Jumlah Bahan</label>
                    <input type="number" class="form-control" id="jumlah_bahan" name="jumlah_bahan" required>
                </div>

                 <div class="form-group">
                    <label for="bahan_id">Satuan</label>
                    <select class="form-control select2" id="satuan" name="satuan" style="width: 100%">
                    @foreach ($satuan as $data)
                        <option value={{ $data->id }}> {{$data->satuan }} </option>
                    @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="jumlah_po">Harga Satuan <span id="label_harga_unit" class="badge badge-secondary ml-1" style="font-size:0.75rem;"></span></label>
                    <input type="number" class="form-control" id="jumlah_po" name="jumlah_po" required>
                </div>

                <div class="form-group">
                    <label for="tanggal_kedatangan">Tanggal Kedatangan</label>
                    <input type="datetime-local" class="form-control" id="tanggal_kedatangan" name="tanggal_kedatangan" required>
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
    $('#tbl_list_barang').DataTable({
            
            ajax: '{{ url()->current() }}',
            columns: [
                 { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
              
                { data: 'bahan', name: 'bahan' },
                { data: 'jumlah_yang_dipesan', name: 'jumlah_yang_dipesan' },
                { data: 'jumlah_yang_dibayar', name: 'jumlah_yang_dibayar' },
                { data: 'action', name: 'action' },
               

            ]
        });
        // AJAX Delete tanpa reload page
        $(document).on('click', '.btn-delete', function () {
            var url = $(this).data('url');
            Swal.fire({
                title: 'Hapus data ini?',
                text: 'Data tidak dapat dikembalikan setelah dihapus.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then(function (result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: 'GET',
                        success: function (res) {
                            $('#tbl_list_barang').DataTable().ajax.reload(null, false);
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: res.message,
                                showConfirmButton: false,
                                timer: 1500
                            });
                        },
                        error: function () {
                            Swal.fire('Gagal', 'Terjadi kesalahan saat menghapus data.', 'error');
                        }
                    });
                }
            });
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
                $('#formTambah').submit(function (e) {
                e.preventDefault();
                var formData = {
                    id_po : $('#id_po').val(),
                    id_kontrak : $('#id_kontrak').val(),
                    id_rincian_kontrak : $('#id_rincian_kontrak').val(),
                    id_bahan : $('#id_bahan').val(),
                    jumlah_bahan : $('#jumlah_bahan').val(),
                    satuan : $('#satuan').val(),
                    jumlah_po: $('#jumlah_po').val(),
                    tanggal_kedatangan: $('#tanggal_kedatangan').val(),
                    _token: '{{ csrf_token() }}'
                };

                $.ajax({
                    url: '{{ route("tambah_barang_po_manual") }}',
                    type: 'POST',
                    data: formData,
                    success: function (response) {
                        if(response.success) {
                            $('#modalTambah').modal('hide');
                            $('#formTambah')[0].reset();
                            $('#id_rincian_kontrak').val('');
                            $('#id_bahan, #satuan').val(null).trigger('change');
                            $('#tbl_list_barang').DataTable().ajax.reload(); // Refresh DataTable
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
                        alert(xhr.responseJSON?.message || 'Terjadi kesalahan dalam menyimpan data');
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
                        alert('Terjadi kesalahan, coba lagi!');
                    }
                });
            });
        });
        
    </script>
    <script>
        $(document).ready(function() {
            $('#id_bahan').select2({
                placeholder: "Pilih Bahan",
                allowClear: true
            });
             $('#satuan').select2({
                placeholder: "Pilih satuan",
                allowClear: true
            });

            // Update badge label harga berdasarkan satuan terpilih
            function updateLabelHarga() {
                var satuanText = $('#satuan option:selected').text().trim().toLowerCase();
                if (satuanText === 'gram') {
                    $('#label_harga_unit').text('per kg');
                } else if (satuanText === 'ml') {
                    $('#label_harga_unit').text('per liter');
                } else {
                    $('#label_harga_unit').text('');
                }
            }

            // Auto-select satuan + harga dari rincian kontrak saat bahan dipilih
            function syncBahanInfo() {
                var idBahan   = $('#id_bahan').val();
                var idKontrak = $('#id_kontrak').val();

                if (idBahan) {
                    $.get('{{ route("get_harga_bahan_po_manual") }}', {
                        id_bahan: idBahan,
                        id_kontrak: idKontrak
                    }, function(res) {
                        $('#id_rincian_kontrak').val(res.id_rincian_kontrak || '');

                        if (res.satuan_bahan) {
                            $('#satuan').val(res.satuan_bahan).trigger('change');
                            updateLabelHarga();
                        }

                        if (res.harga_bahan > 0) {
                            $('#jumlah_po').val(res.harga_bahan);
                        } else {
                            $('#jumlah_po').val('');
                        }
                    });
                } else {
                    $('#id_rincian_kontrak').val('');
                    $('#jumlah_po').val('');
                }
            }

            // Update label jika satuan diubah manual
            $('#satuan').on('change', updateLabelHarga);

            $('#id_bahan').on('change', syncBahanInfo);

            $('#jumlah_po').on('blur', function () {
                var harga     = $(this).val();
                var idBahan   = $('#id_bahan').val();
                var idKontrak = $('#id_kontrak').val();
                var satuan    = $('#satuan').val();

                if (!idBahan || !idKontrak || harga === '') {
                    return;
                }

                $.ajax({
                    url: '{{ route("update_harga_bahan_po_manual") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id_bahan: idBahan,
                        id_kontrak: idKontrak,
                        harga_bahan: harga,
                        satuan_bahan: satuan
                    },
                    success: function (response) {
                        if (response && response.id_rincian_kontrak) {
                            $('#id_rincian_kontrak').val(response.id_rincian_kontrak);
                        }
                    },
                    error: function () {
                        // silent
                    }
                });
            });

            // Trigger saat modal dibuka agar langsung default
            $('#modalTambah').on('shown.bs.modal', function () {
                syncBahanInfo();
            });
        });
    </script>
    


    <!-- jQuery -->
</body>
</html>
