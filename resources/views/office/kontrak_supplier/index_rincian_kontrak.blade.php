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
        .select2-container .select2-selection--single {
            height: calc(2.25rem + 2px); /* Sama dengan form-control */
            padding: 0.375rem 0.75rem; /* Sesuaikan padding */
        }
    </style>
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
                                    <b>Nomor Kontrak</b> <a class="float-right">{{ $kontrak->nomor_kontrak }}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Supplier</b> <a class="float-right">{{ $supplier->nama_supplier }}</a>
                                </li>
                                

                                </ul>
                                <h3 class="card-title"><a hidden href="{{ route('kontrak.index') }}" class="edit btn btn-primary  " id="btn-edit-post">Kembali</a></h3>
                                
                                </div>
                                <!-- /.col -->
                                <div class="col-sm-6 invoice-col">
                                <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b>Kontrak</b> <a class="float-right">{{ \Carbon\Carbon::parse($kontrak->awal_kontrak)->translatedFormat('l, d-m-Y') }} - {{ \Carbon\Carbon::parse($kontrak->akhir_kontrak)->translatedFormat('l, d-m-Y') }}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>PIC | Telp</b> <a class="float-right">{{ $supplier->nama_PIC }} | {{ $supplier->no_telp }}</a>
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
                           <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalTambahBahan">
                                + Tambah Bahan
                            </button>
                            <a href="{{ route('rincian-kontrak.template-harga', $kontrak->id) }}" class="btn btn-info ml-2">
                                Export Template Harga
                            </a>
                            <button type="button" class="btn btn-primary ml-2" data-toggle="modal" data-target="#modalUploadHarga">
                                Upload Harga Excel
                            </button>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                
                                <table id="tbl_list_Bahan" class="table table-bordered table-hover" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Bahan</th>
                                        <th>Harga Satuan</th>
                                        <th>action</th>
                                        
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
        <!-- Modal Tambah Bahan -->
        <div class="modal fade" id="modalTambahBahan" tabindex="-1" role="dialog" aria-labelledby="modalTambahBahanLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTambahBahanLabel">Tambah Bahan</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="formTambahBahan">
                            @csrf
                            <div class="form-group" hidden>
                                <label for="id_kontrak">id kontrak</label>
                                <input type="text" class="form-control" id="id_kontrak" name="id_kontrak" value="{{ $kontrak->id }}" required>
                            </div>
                            <div class="form-group">
                                <label for="bahan">Nama Bahan</label><br>
                                <select class="form-control select2" style="width: 100%" id="id_bahan" name="id_bahan">
                                        @foreach ($bahan as $data)
                                            <option value="{{ $data->id }}">{{ $data->bahan }}</option>
                                        @endforeach
                                </select>
                            </div>
                            <div class="form-group" hidden>
                                <label for="merek_bahan">Merek Bahan</label>
                                <input type="text" class="form-control" id="merek_bahan" name="merek_bahan" value="-" required>
                            </div>
                            <div class="form-group">
                                <label for="harga_bahan">Harga Satuan</label>
                                <input type="text" class="form-control" id="harga_bahan" name="harga_bahan" required>
                            </div>
                            <div class="form-group" hidden>
                                <label for="jumlah_bahan">Jumlah</label>
                                <input type="number" class="form-control" id="jumlah_bahan" name="jumlah_bahan" value="1" required>
                            </div>

                            <div class="form-group" >
                                <label for="bahan">Satuan</label><br>
                                <select class="form-control select2" style="width: 100%" id="satuan_bahan" name="satuan_bahan">
                                        @foreach ($satuan as $data)
                                            <option value="{{ $data->id }}">{{ $data->satuan }}</option>
                                        @endforeach
                                </select>
                            </div>
                            <div class="form-group" hidden>
                                <label for="bahan">Kemasan</label><br>
                                <select class="form-control select2" style="width: 100%" id="kemasan" name="kemasan">
                                        @foreach ($satuan as $data)
                                            <option value="{{ $data->satuan }}">{{ $data->satuan }}</option>
                                        @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!--modal update-->

        <div class="modal fade" id="modalEditBox" tabindex="-1">
            <div class="modal-dialog">
                <form id="formEditBox">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title">Ubah Harga</h5>
                </div>
                        <div class="modal-body">
                            <input type="hidden" id="edit_id">
                            

                            <div class="form-group">
                                <label for="edit_harga_bahan">Harga</label>
                                <input type="number" id="edit_harga_bahan" name="edit_harga_bahan" class="form-control" required>
                            </div>
                           
                            
                        </div>
                    <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </div>
                </form>
            </div>
        </div>

        <div class="modal fade" id="modalUploadHarga" tabindex="-1" role="dialog" aria-labelledby="modalUploadHargaLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalUploadHargaLabel">Upload Update Harga Kontrak</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('rincian-kontrak.import-harga', $kontrak->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="alert alert-info">
                                Alur: 1) Export template, 2) ubah kolom <b>harga_baru</b>, 3) upload kembali file.
                            </div>

                            <div class="form-group">
                                <label for="file_harga">File Excel</label>
                                <input type="file" name="file_harga" id="file_harga" class="form-control" accept=".xlsx,.xls,.csv" required>
                                <small class="text-muted">Format yang didukung: .xlsx, .xls, .csv</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Upload & Proses</button>
                        </div>
                    </form>
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
    $('#tbl_list_Bahan').DataTable({
            
            ajax: '{{ url()->current() }}',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'nama_bahan', name: 'nama_bahan' },
                { data: 'nama_satuan', name: 'nama_satuan' },
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
        document.getElementById("harga_bahan").addEventListener("input", function (e) {
            let value = e.target.value.replace(/\D/g, ""); // Hanya angka, hapus karakter selain digit
            if (parseInt(value) > 1000000) {
                value = "1000000"; // Batasi maksimum 1.000.000
            }
            e.target.value = new Intl.NumberFormat("id-ID").format(value); // Format angka dengan titik
        });
        document.getElementById("formTambahBahan").addEventListener("submit", function () {
            let inputHarga = document.getElementById("harga_bahan");
            inputHarga.value = inputHarga.value.replace(/\./g, ""); // Hapus titik sebelum dikirim
        });
        $(document).ready(function() {
            $('#formTambahBahan').on('submit', function(event) {
                event.preventDefault();

                $.ajax({
                    url: "{{ route('rincian-kontraks.store') }}", // Sesuaikan dengan route penyimpanan
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(response) {
                         if (response.error) {
                            Swal.fire("Gagal!", response.error, "warning");
                        } else {
                            $('#modalTambahBahan').modal('hide');
                            Swal.fire("Berhasil!", "Data bahan berhasil ditambahkan.", "success");
                            $('#tbl_list_Bahan').DataTable().ajax.reload();
                        }
                        
                    },
                    error: function(xhr) {
                        Swal.fire("Gagal!", "Terjadi kesalahan, coba lagi.", "error");
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
        });
        $(document).ready(function() {
            $('#satuan_bahan').select2({
                placeholder: "Pilih Satuan",
                allowClear: true
            });
        });
        $(document).ready(function() {
            $('#kemasan').select2({
                placeholder: "Pilih kemasan",
                allowClear: true
            });
        });
    </script>
    <script>
        // Menampilkan modal dan isi data saat klik tombol edit
        $(document).on('click', '.btn-edit-box', function () {
            const id = $(this).data('id');
            const harga = $(this).data('hargabahan');
    
            $('#edit_id').val(id);
            $('#edit_harga_bahan').val(harga);
            $('#modalEditBox').modal('show');
        });
    
        // Submit form edit
        document.getElementById('formEditBox').addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('edit_id').value;
            const harga_bahan = document.getElementById('edit_harga_bahan').value;
            fetch(`/rincian-kontraks/update/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ harga_bahan :harga_bahan })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        $('#tbl_list_Bahan').DataTable().ajax.reload(null, false);
                        $('#modalEditBox').modal('hide');
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: data.message
                    });
                }
            });
        });
    </script>
    <!-- jQuery -->
</body>
</html>
