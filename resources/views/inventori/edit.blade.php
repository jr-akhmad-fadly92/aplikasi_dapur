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
                            <h1 >{{ $header }}</h1>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <form action="{{ route('inventori.update', $inventori->id) }}" method="POST" enctype="multipart/form-data">
                        
                                @csrf
                                @method('PUT')


                                <div class="form-group mb-3">
                                    <label>Kode Barang</label>
                                    <input type="text" name="kode_barang" class="form-control" value="{{ $inventori->kode_barang }}" required readonly>
                                    
                                    @error('kode_barang')
                                        <div class="alert alert-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label>Nama Barang</label>
                                    <input type="text" name="nama_barang" class="form-control" value="{{ $inventori->nama_barang }}" required>
                                    
                                    @error('nama_barang')
                                        <div class="alert alert-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label class="font-weight-bold">Jenis Barang</label>
                                    <select name="jenis_barang" class="form-control" required>
                                        <option value="Rantang" {{ old('jenis_barang', $inventori->jenis_barang) == 'Rantang' ? 'selected' : '' }}>Rantang</option>
                                        <option value="Ompreng" {{ old('jenis_barang', $inventori->jenis_barang) == 'Ompreng' ? 'selected' : '' }}>Ompreng</option>
                                        <option value="BHP" {{ old('jenis_barang', $inventori->jenis_barang) == 'BHP' ? 'selected' : '' }}>BHP</option>
                                        <option value="Alat Masak" {{ old('jenis_barang', $inventori->jenis_barang) == 'Alat Masak' ? 'selected' : '' }}>Alat Masak</option>
                                        <option value="Lain Lain" {{ old('jenis_barang', $inventori->jenis_barang) == 'Lain Lain' ? 'selected' : '' }}>Lain Lain</option>
                                    </select>

                                    @error('jenis_barang')
                                        <div class="alert alert-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                

                                <div class="form-group mb-3">
                                    <label>Jumlah Stok</label>
                                    <input type="number" name="jumlah_stok" class="form-control" value="{{ $inventori->jumlah_stok }}" required>
                                    
                                    @error('jumlah_stok')
                                        <div class="alert alert-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                
                                        
                                

                                <button type="submit" class="btn btn-md btn-primary me-3">UPDATE</button>
                                <button type="reset" class="btn btn-md btn-warning">RESET</button>

                            </form> 
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
  
    @include('Template.script')
    
    <!-- jQuery -->
</body>
</html>
