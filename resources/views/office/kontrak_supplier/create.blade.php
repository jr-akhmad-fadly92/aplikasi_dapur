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
                            <h1 >{{ $header }}</h1>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <form action="{{ route('kontrak.store') }}" method="POST" enctype="multipart/form-data">
                        
                                @csrf
                                

                                

                                <div class="form-group mb-3">
                                    <label class="font-weight-bold">Nomor Kontrak</label>
                                    <input type="text" class="form-control @error('nomor_kontrak') is-invalid @enderror" name="nomor_kontrak" value="{{ $nomor_kontrak }}" placeholder="---">
                                
                                    <!-- error message untuk name -->
                                    @error('nomor_kontrak')
                                        <div class="alert alert-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                        
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold">Suplier</label>
                                    <select class="form-control select2" id="supplier" name="supplier">
                                        @foreach ($Supplier as $data)
                                            <option value="{{ $data->id }}">{{ $data->nama_supplier }}</option>
                                        @endforeach
                                    </select>
                                    @error('supplier')
                                    <div class="alert alert-danger mt-2">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold">Awal Kontrak</label>
                                    <input type="date" class="form-control @error('awal_kontrak') is-invalid @enderror"  value="{{ old('awal_kontrak', date('Y-m-d')) }}" name="awal_kontrak">
                                    @error('awal_kontrak')
                                    <div class="alert alert-danger mt-2">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label class="font-weight-bold">Akhir Kontrak</label>
                                    <input type="date" class="form-control @error('akhir_kontrak') is-invalid @enderror"  value="{{ old('akhir_kontrak', date('Y-m-d')) }}" name="akhir_kontrak">
                                    @error('akhir_kontrak')
                                    <div class="alert alert-danger mt-2">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label class="font-weight-bold">Suplier</label>
                                    <select class="form-control select2" id="cara_pembayaran" name="cara_pembayaran">
                                       
                                            <option value="cash">cash</option>
                                            <option value="tempo 3 hari">tempo 3 hari</option>
                                            <option value="tempo 1 minggu">tempo 1 minggu</option>
                                            <option value="tempo 2 minggu">tempo 2 minggu</option>
                                            <option value="tempo 3 minggu">tempo 2 minggu</option>
                                            <option value="tempo 1 bulan">tempo 1 bulan</option>
                                      
                                    </select>
                                    @error('supplier')
                                    <div class="alert alert-danger mt-2">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold">Bank Rekening</label>
                                    <input type="text" class="form-control @error('bank') is-invalid @enderror"  value="" name="bank">
                                    @error('bank')
                                    <div class="alert alert-danger mt-2">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label class="font-weight-bold">Nomor Rekening</label>
                                    <input type="number" class="form-control @error('nomor_rekening_pembayaran') is-invalid @enderror"  value="{{ old('nomor_rekening_pembayaran', date('Y-m-d')) }}" name="nomor_rekening_pembayaran">
                                    @error('nomor_rekening_pembayaran')
                                    <div class="alert alert-danger mt-2">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label class="font-weight-bold">Nama Rekening</label>
                                    <input type="text" class="form-control @error('nama_rekening') is-invalid @enderror"  value="" name="nama_rekening">
                                    @error('nama_rekening')
                                    <div class="alert alert-danger mt-2">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-md btn-primary me-3">Simpan</button>
                                <a type="reset" href="{{ route('kontrak.index') }}" class="btn btn-md btn-warning">Kembali</a>

                            </form> 
                        </div>
                        <!-- /.card-body -->
                        </div>
                        <!-- /.card -->

                       >
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
    <script>
        $(document).ready(function() {
            $('#supplier').select2({
                placeholder: "Pilih Supplier",
                allowClear: true
            });
        });
        $(document).ready(function() {
            $('#cara_pembayaran').select2({
                placeholder: "Pilih Pembayaran",
                allowClear: true
            });
        });
    </script>
    <!-- jQuery -->
</body>
</html>
