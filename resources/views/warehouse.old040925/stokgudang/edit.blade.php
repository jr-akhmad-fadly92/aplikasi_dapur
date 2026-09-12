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
                            <form action="{{ route('stokgudang.update', $stokgudang->id) }}" method="POST" enctype="multipart/form-data">
                        
                                @csrf
                                @method('PUT')

                                

                                <div class="form-group mb-3">
                                    <label>Nama Barang</label>
                                    <select name="nama_barang" class="form-control" required readonly>
                                        @foreach($bahan as $bahan)
                                            <option value="{{ $bahan->id }}" 
                                                {{ old('nama_barang', $stokgudang->nama_barang) == $bahan->id ? 'selected' : '' }}>
                                                {{ $bahan->bahan }}
                                            </option>
                                        @endforeach
                                    </select>
                                    
                                    @error('nama_barang')
                                        <div class="alert alert-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Jumlah</label>
                                    <input name="jumlah" class="form-control" value="{{ $stokgudang->jumlah }}" required>

                                    @error('jumlah')
                                        <div class="alert alert-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label>Satuan</label>
                                    <select name="id_satuan" class="form-control" required readonly>
                                        @foreach($satuan as $satuan)
                                            <option value="{{ $satuan->id }}" 
                                                {{ old('id_satuan', $stokgudang->id_satuan) == $satuan->id ? 'selected' : '' }}>
                                                {{ $satuan->satuan }}
                                            </option>
                                        @endforeach
                                    </select>
                                    
                                    @error('id_satuan')
                                        <div class="alert alert-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                <label class="font-weight-bold">Kategori Bahan</label>
                                    <select name="kategori_bahan" class="form-control" required>
                                        <option value="Matang" {{ old('kategori_bahan', $stokgudang->kategori_bahan) == 'Matang' ? 'selected' : '' }}>Matang</option>
                                        <option value="Mentah" {{ old('kategori_bahan', $stokgudang->kategori_bahan) == 'Mentah' ? 'selected' : '' }}>Mentah</option>
                                    </select>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="font-weight-bold">Status Bahan</label>
                                    <select name="status_bahan" class="form-control" required>
                                        <option value="Aman" {{ old('status_bahan', $stokgudang->status_bahan) == 'Aman' ? 'selected' : '' }}>Aman</option>
                                        <option value="Sedikit" {{ old('status_bahan', $stokgudang->status_bahan) == 'Sedikit' ? 'selected' : '' }}>Sedikit</option>
                                        <option value="Habis" {{ old('status_bahan', $stokgudang->status_bahan) == 'Habis' ? 'selected' : '' }}>Habis</option>
                                    </select>
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
