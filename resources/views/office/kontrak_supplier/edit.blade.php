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
                            <h1 >{{ $header }}</h1>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <form action="{{ route('kontrak.update', $kontrak->id) }}" method="POST" enctype="multipart/form-data">
                        
                                @csrf
                                @method('PUT')

                                

                                <div class="form-group mb-3">
                                    <label class="font-weight-bold">Nomor Kontrak</label>
                                    <input type="text" class="form-control @error('nomor_kontrak') is-invalid @enderror" name="nomor_kontrak" value="{{ old('nomor_kontrak', $kontrak->nomor_kontrak) }}" placeholder="nomor_kontrak">
                                
                                    <!-- error message untuk nomor_kontrak -->
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
                                            
                                            <option value="{{ $data->id }}" @if($kontrak->id_supplier == $data->id) selected @endif >{{ $data->nama_supplier }}</option>
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
                                    <input type="date" class="form-control @error('awal_kontrak') is-invalid @enderror"  value="{{ $kontrak->awal_kontrak }}" name="awal_kontrak">
                                    @error('awal_kontrak')
                                    <div class="alert alert-danger mt-2">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                 
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold">Akhir Kontrak</label>
                                    <input type="date" class="form-control @error('akhir_kontrak') is-invalid @enderror"  value="{{ $kontrak->akhir_kontrak }}" name="akhir_kontrak">
                                    @error('akhir_kontrak')
                                    <div class="alert alert-danger mt-2">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label class="font-weight-bold">Cara Pembayaran</label>
                                    <select class="form-control select2" id="cara_pembayaran" name="cara_pembayaran">
                                       
                                            <option value="cash"  @if($kontrak->cara_pembayaran == "cash") selected @endif >cash</option>
                                            <option value="tempo 3 hari" @if($kontrak->cara_pembayaran == "tempo 3 hari") selected @endif>tempo 3 hari</option>
                                            <option value="tempo 1 minggu" @if($kontrak->cara_pembayaran == "tempo 1 minggu") selected @endif>tempo 1 minggu</option>
                                            <option value="tempo 2 minggu" @if($kontrak->cara_pembayaran == "tempo 2 minggu") selected @endif>tempo 2 minggu</option>
                                            <option value="tempo 3 minggu" @if($kontrak->cara_pembayaran == "tempo 3 minggu") selected @endif>tempo 2 minggu</option>
                                            <option value="tempo 1 bulan" @if($kontrak->cara_pembayaran == "tempo 1 bulan") selected @endif>tempo 1 bulan</option>
                                      
                                    </select>
                                    @error('supplier')
                                    <div class="alert alert-danger mt-2">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold">Bank Rekening</label>
                                    <input type="test" class="form-control @error('bank') is-invalid @enderror"  value="{{ $kontrak->bank }} " name="bank">
                                    @error('bank')
                                    <div class="alert alert-danger mt-2">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label class="font-weight-bold">Nomor Rekening</label>
                                    <input type="test" class="form-control @error('nomor_rekening_pembayaran') is-invalid @enderror"  value="{{ $kontrak->nomor_rekening_pembayaran }} " name="nomor_rekening_pembayaran">
                                    @error('nomor_rekening_pembayaran')
                                    <div class="alert alert-danger mt-2">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label class="font-weight-bold">Nama Rekening</label>
                                    <input type="text" class="form-control @error('nama_rekening') is-invalid @enderror"  value="{{ $kontrak->nama_rekening }} " name="nama_rekening">
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
    
    <!-- jQuery -->
</body>
</html>
