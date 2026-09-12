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
                            <form action="{{ route('datadapur.update', $dapur->id) }}" method="POST" enctype="multipart/form-data">
                        
                                @csrf
                                @method('PUT')

                                

                                <!-- pemili Dapur-->
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Pemilik Dapur</label>
                                            <input type="text" class="form-control @error('pemilik') is-invalid @enderror" name="pemilik" value="{{ $dapur->pemilik }}" step="any" placeholder="pemilik">
                                            @error('pemilik')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Nama Dapur-->
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Nama Dapur</label>
                                            <input type="text" class="form-control @error('nama_dapur') is-invalid @enderror" name="nama_dapur" value="{{ $dapur->nama_dapur }}" step="any" placeholder="Nama Dapur">
                                            @error('nama_dapur')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>


                                        
                                        <!-- Nomor Dapur -->
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Nomor Dapur</label>
                                            <input type="text" class="form-control @error('nomor_dapur') is-invalid @enderror" name="nomor_dapur" value="{{ $dapur->nomor_dapur }}" step="any" placeholder="---">
                                            @error('nomor_dapur')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">IP Dapur</label>
                                            <input type="text" class="form-control @error('ip_dapur') is-invalid @enderror" name="ip_dapur" value="{{ $dapur->ip_dapur ?? '' }}" placeholder="Contoh: 192.168.1.10">
                                            @error('ip_dapur')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- telepon -->
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Telepon</label>
                                            <input type="text" class="form-control @error('no_telp') is-invalid @enderror" name="no_telp" value="{{ $dapur->no_telp }}" step="any" placeholder="---">
                                             @error('no_telp')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Email -->
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Email</label>
                                            <input type="text" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $dapur->email }}" step="any" placeholder="---">
                                             @error('email')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Alamat Dapur -->
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Alamat Dapur</label>
                                            <input type="text" class="form-control @error('alamat_dapur') is-invalid @enderror" name="alamat_dapur" value="{{ $dapur->alamat_dapur }}" step="any" placeholder="Alamat Dapur"></input>
                                            @error('alamat_dapur')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- kelurahan -->
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">kelurahan</label>
                                            <input type="text" class="form-control @error('kelurahan') is-invalid @enderror" name="kelurahan" value="{{ $dapur->kelurahan }}" step="any" placeholder="---">
                                             @error('kelurahan')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- kecamatan -->
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Kecamatan</label>
                                           <input type="text" class="form-control @error('kecamatan') is-invalid @enderror" name="kecamatan" step="any" placeholder="---" value="{{ $dapur->kecamatan }}" >
                                            
                                            @error('kecamatan')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Kota -->
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Kota</label>
                                            <input type="text" class="form-control @error('kota') is-invalid @enderror" name="kota" value="{{ $dapur->kota }}" step="any" placeholder="---">
                                             @error('kota')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Provinsi -->
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Provinsi</label>
                                           <input type="text" class="form-control @error('provinsi') is-invalid @enderror" name="provinsi" value="{{ $dapur->provinsi }}" step="any" placeholder="---">
                                            
                                            @error('provinsi')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Kepala Dapur -->
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Kepala Dapur</label>
                                           <input type="text" class="form-control @error('kepala_dapur') is-invalid @enderror" name="kepala_dapur" value="{{ $dapur->kepala_dapur ?? '' }}" step="any" placeholder="---">
                                            
                                            @error('kepala_dapur')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Admin Dapur -->
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Admin Dapur</label>
                                           <input type="text" class="form-control @error('admin_dapur') is-invalid @enderror" name="admin_dapur" value="{{ $dapur->admin_dapur ?? '' }}" step="any" placeholder="---">
                                            
                                            @error('kepala_dapur')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Ahli Gizi -->
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Ahli Gizi</label>
                                           <input type="text" class="form-control @error('ahli_gizi') is-invalid @enderror" name="ahli_gizi" value="{{ $dapur->ahli_gizi ?? '' }}" step="any" placeholder="---">
                                            
                                            @error('ahli_gizi')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Ahli Akuntan -->
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Ahli Akuntan</label>
                                           <input type="text" class="form-control @error('ahli_akuntan') is-invalid @enderror" name="ahli_akuntan" value="{{ $dapur->ahli_akuntan ?? '' }}" step="any" placeholder="---">
                                            
                                            @error('ahli_akuntan')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>

                                <button type="submit" class="btn btn-md btn-primary me-3">UPDATE</button>
                                <a type="reset" href="{{ route('datadapur.index') }}" class="btn btn-warning">Kembali</a>


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
