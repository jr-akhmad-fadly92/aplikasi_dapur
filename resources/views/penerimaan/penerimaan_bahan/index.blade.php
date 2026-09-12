<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ $header }}</title>
    @include('Template.head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">

        @include('Template.navbar')
        @include('Template.left-sidebar')

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 text-dark" id="currentTime">Starter Page</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">{{ $header }}</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header p-2" style=" width: 100%;flex: 1 1 auto;text-align: center;">
                                    <ul class="nav nav-pills">
                                        <li class="nav-item" style="width: 33%;flex: 1 1 auto;text-align: center;"><a class="nav-link active" href="#penerimaan" data-toggle="tab">Penerimaan</a></li>
                                        <li class="nav-item" style="width: 33%;flex: 1 1 auto;text-align: center;"><a class="nav-link " href="#sudah_diterima" data-toggle="tab">Sudah Diterima</a></li>
                                        <li class="nav-item" style="width: 34%;flex: 1 1 auto;text-align: center;"><a class="nav-link " href="#gudang" data-toggle="tab">Gudang</a></li>
                                    </ul>
                                </div><!-- /.card-header -->
                                <div class="card-header">
                                    <h3 class="card-title">Rencana Bahan Masuk</h3>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content">
                                        <div class="active tab-pane" id="penerimaan">
                                            <table id="tbl_list_tb_penerimaan" class="table table-bordered table-hover" style="width: 100%">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Nomor PO</th>
                                                        <th>Nama Bahan</th>
                                                        <th>tanggal_kedatangan</th>
                                                        <th>Jumlah Sudah Datang</th>
                                                        <th>Jumlah Belum Datang</th>
                                                        <th>Keterangan</th>
                                                        
                                                        <th>Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>        
                                        </div><!-- /.tab-activity -->
                                        <div class="tab-pane" id="sudah_diterima">
                                            
                                            <table id="tbl_list_tb_sudah_diterima" class="table table-bordered table-hover" style="width: 100%">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Kode Box</th>
                                                        <th>Nama Bahan</th>
                                                        <th>tanggal_kedatangan</th>
                                                        <th>Jumlah</th>
                                                        <th>Keterangan</th>
                                                        
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>        
                                        </div><!-- /.tab-activity -->
                                        <div class="tab-pane" id="gudang">
                                            <table id="tbl_list_tb_gudang" class="table table-bordered table-hover" style="width: 100%">
                                                <thead>
                                                    <tr>
                                                         <th>No</th>
                                                        <th>Kode Box</th>
                                                        <th>Nama Bahan</th>
                                                        <th>tanggal_kedatangan</th>
                                                        <th>Jumlah</th>
                                                        <th>Keterangan</th>
                                                        
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>        
                                        </div><!-- /.tab-activity -->
                                        <!-- /.tab-pane -->
                                        
                                    </div><!-- /.tab-content -->
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <!-- Tambahkan Modal di View -->
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
                            <input type="hidden" id="idrincian" name="idrincian">
                            <div class="form-group" >
                                <label>QR Code Wadah</label>
                                <input type="text" class="form-control" id="qrcode_wadah" name="qrcode_wadah" value="" required>
                            </div>
                            <div class="form-group">
                                <label>Jumlah Bahan Yang Dipesan</label>
                                <input type="text" class="form-control" id="jumlah_bahan" name="jumlah_bahan" readonly>
                            </div>
                            <div class="form-group">
                                <label>Jumlah Bahan Yang Datang</label>
                                <input type="number" class="form-control" id="jumlah_datang" name="jumlah_datang" required>
                            </div>
                            <div class="form-group" hidden>
                                <label>Jumlah Berat Bahan</label>
                                <input type="number" class="form-control" id="jumlah_berat" name="jumlah_berat" value="0" required>
                            </div>
                            <div class="form-group" hidden>
                                <label>Satuan Berat Bahan</label>
                                <select class="form-control" id="satuan_berat" name="satuan_berat" required>
                                    @foreach ($satuan as $data)
                                        <option value="{{ $data->id }}">{{ $data->satuan }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>Status</label>
                                <select class="form-control" id="status" name="status" required>
                                    <option value="1">Lolos</option>
                                    <option value="0">Tidak</option>
                                    <option value="2">Kurang</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Keterangan</label>
                                <textarea class="form-control" id="keterangan" name="keterangan"></textarea>
                            </div>
                            <div class="form-group">
                                <label>Nama Penerima</label>
                                <input type="text" class="form-control" id="nama_penerima" name="nama_penerima" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @include('Template.footer')
    </div>

    @include('Template.script')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#tbl_list_tb_penerimaan').DataTable({
                ajax: '{{ url()->current() }}',
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'nomor_po_datang', name: 'nomor_po_datang' },
                    { data: 'nama_bahan', name: 'nama_bahan' },
                    { data: 'tanggal_kedatangan', name: 'tanggal_kedatangan' },
                    { data: 'jumlah_yang_sudah_datang', name: 'jumlah_yang_sudah_datang' },
                    { data: 'jumlah_dan_satuan', name: 'jumlah_dan_satuan' },
                    { data: 'keterangan', name: 'keterangan' },
                    
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });

            $('#tbl_list_tb_sudah_diterima').DataTable({
                ajax: '{{ route('penerimaan.dt_sudah_diterima') }}',
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'qr_code_wadah', name: 'qr_code_wadah' },
                    { data: 'bahan', name: 'bahan' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'jumlah_satuan', name: 'jumlah_satuan' },
                    { data: 'keterangan', name: 'keterangan' },
                    
                ]
            });

            $('#tbl_list_tb_gudang').DataTable({
                ajax: '{{ route('penerimaan.dt_gudang') }}',
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'kode_wadah', name: 'kode_wadah' },
                    { data: 'nama_barang', name: 'nama_barang' },
                    { data: 'tanggal_masuk', name: 'tanggal_masuk' },
                    { data: 'tanggal_akan_keluar', name: 'tanggal_akan_keluar' },
                    { data: 'jumlah_satuan', name: 'jumlah_satuan' },
                    
                ]
            });
        });
    </script>
    <script src="{{ asset('AdminLte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script>
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
                var jumlahbahan = $(this).data('jumlahbahan');
                var idrincian   = $(this).data('idrincian');
                var jumlah_datang   = $(this).data('jumlahdatang');
                $('#idbahan').val(idbahan);
                $('#jumlah_bahan').val(jumlahbahan);
                
                $('#idrincian').val(idrincian);
                $('#jumlah_datang').val(jumlah_datang);
                $('#updateModal').modal('show');
            });

            $('#updateForm').submit(function (e) {
                e.preventDefault();

                let formData = $(this).serialize();
                console.log(formData); // Debug: Lihat data sebelum dikirim

                $.ajax({
                    url: '/simpan_penerimaan_bahan',
                    type: 'POST',
                    data: formData,
                    success: function (response) {
                        Swal.fire('Berhasil!', response.message, 'success');
                        $('#updateModal').modal('hide');
                        $('#updateModal form')[0].reset();
                        $('#tbl_list_tb_penerimaan').DataTable().ajax.reload();
                        $('#tbl_list_tb_sudah_diterima').DataTable().ajax.reload();
                        $('#tbl_list_tb_gudang').DataTable().ajax.reload();
                    },
                    error: function (xhr) {
                        console.error(xhr.responseText); // Debug: Lihat error detail di console
                        Swal.fire('Gagal!', 'Terjadi kesalahan: ' + xhr.responseJSON.message, 'error');
                    }
                });
            });
       }); 
    </script>
</body>
</html>
